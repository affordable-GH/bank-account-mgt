<?php

namespace App\Http\Controllers;

use App\Models\Entry;
use App\Http\Requests\StoreEntryRequest;
use App\Http\Requests\UpdateEntryRequest;
use App\Models\Account;
use App\Models\AccountLocation;
use App\Models\EntryType;
use Illuminate\Http\Request;

class EntryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(int $location, Request $request)
    {
        $account_location = AccountLocation::findOrFail($location);
        if ($request->filled('account')) {
            $account = Account::findOrFail($request->account);
            $entries = $account_location->accounts->entries->orderBy('created_at', 'DESC')->get();
        } else {
            $entries = Entry::belongsToAccounts($account_location->accounts->pluck('id'))->orderBy('created_at', 'DESC')->get();
        }
        return view('entries.index', compact('entries', 'account_location'));
    }

    /**
     * Show the form for creating a new resource.
     */

    public function create(int $location, Request $request)
    {
        $page_title = 'create account';
        $account_location = AccountLocation::findOrFail($location);
        $entry_types = EntryType::all();
        if ($request->filled('account')) {
            $accounts = AccountLocation::findOrFail($location)->openAccounts()->where('id', $request->account)->get();
        } else {
            $accounts = $account_location->openAccounts()->orderBy('updated_at', 'desc')->get();
        }
        return view('entries.create', compact('account_location', 'page_title', 'entry_types', 'accounts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(int $location, StoreEntryRequest $request)
    {
        $account = AccountLocation::findOrFail($location)->accounts()->findOrFail($request->account);
        $account->entries()->create([
            'entry_type_id' => $request->entry_type,
            'description' => $request->description,
            'amount' => $request->amount,
            'value_date' => $request->value_date ?? now(),
            'reference_number' => $request->reference_number,
            'date' => $request->date ?? now(),
        ]);
        $routeName = $request->has('exist') ? 'entries.index' : 'entries.create';
        return redirect()->to(route($routeName, $location))->with('success', 'Entry created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Entry $entry)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $location, Entry $entry)
    {
        $account_location = AccountLocation::findOrFail($location);
        if ($entry->account->accountLocation->id !== $location) {
            abort(403, 'Entry does not belongs to this location.');
        }
        $page_title = 'edit entry';
        $entry_types = EntryType::all();
        return view('entries.edit', compact('entry', 'account_location', 'page_title', 'entry_types'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(int $location, UpdateEntryRequest $request, Entry $entry)
    {
        // return $entry;
        if ($entry->account->accountLocation->id !== $location) {
            abort(403, 'Account does not belongs to this location.');
        }
        // dd($request->all());
        if ($entry->is_reconciled) {
            $fields = [
                'entry_type' => $request->entry_type,
                'description' => $request->description,
                'value_date' => $request->input('value-date') ?? now(),
                'date' => $request->input('date') ?? now(),
            ];
        } else {
            $fields = [
                'entry_type_id' => $request->entry_type,
                'description' => $request->description,
                'amount' => $request->amount,
                'value_date' => $request->input('value-date') ?? now(),
                'reference_number' => $request->reference_number,
                'date' => $request->input('date') ?? now(),
            ];
        }
        // Update entry
        $entry->update($fields);

        return redirect()->back()->with('success', 'Entry updated successfully');
        // return response()->json(['success' => true, 'url' => $url]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $location, Request $request, Entry $entry)
    {
        // // Authorization check
        // if ($location && $entry->account->accountLocation->id !== $location) {
        //     abort(403, 'Account does not belong to this location.');
        // }
        $accountLocation = AccountLocation::find($location);
        if (!$accountLocation) {
            abort(404, 'Account location not found.');
        }
        // Delete single entry or multiple entries
        $deletedCount = 0;
        if ($request->filled('entries')) {
            $entries = $request->input('entries');
            Entry::destroy($entries);
            $deletedCount = count($entries);
        } else {
            $entry->delete();
            $deletedCount = 1;
        }

        // Return success response
        $url = redirect()->back()->with('success', $deletedCount . ' entry/entries deleted successfully.')->getTargetUrl();
        return response()->json(['success' => true, 'url' => $url]);
    }

    public function reconcile(int $location, Request $request)
    {
        // Validation
        $request->validate([
            'entries' => 'required|array',
            'entries.*' => 'exists:entries,id',
        ]);

        // Authorization
        $accountLocation = AccountLocation::find($location);
        if (!$accountLocation) {
            abort(404, 'Account location not found.');
        }
        // Reconcile entries
        $entry_ids = $request->input('entries');
        Entry::whereIn('id', $entry_ids)
            ->whereHas('account.accountLocation', function ($query) use ($location) {
                $query->where('id', $location);
            })->get()->each(function ($entry) {
                $entry->account->updateBalance($entry->amount, $entry->entryType->type);
                if ($entry->is_transfer) {
                    $transferEntries = Entry::where('transfer_id', $entry->transfer->id)->pluck('id')->toArray();
                    $entry->reconcile($transferEntries);
                }
                $entry->update(['is_reconciled' => true]);
            });
        // Return success response
        $url = redirect()->back()->with('success', 'Entries reconciled successfully')->getTargetUrl();
        return response()->json(['success' => true, 'url' => $url]);
    }
}
