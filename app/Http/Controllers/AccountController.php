<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Http\Requests\StoreAccountRequest;
use App\Http\Requests\UpdateAccountRequest;
use App\Models\AccountLocation;
use App\Models\AccountStatus;
use App\Models\AccountType;
use App\Models\EntryType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(int $location)
    {
        $account_location =  AccountLocation::findOrFail($location);
        $accounts = $account_location->accounts()->orderBy('created_at', 'DESC')->get();
        $page_title = $account_location->name;
        return view('accounts.index', compact('accounts', 'account_location', 'page_title'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(int $location)
    {
        $page_title = 'create account';
        $account_location = AccountLocation::findOrFail($location);
        $account_types = AccountType::all();
        $account_statuses = AccountStatus::all();
        return view('accounts.create', compact('account_location', 'page_title', 'account_types', 'account_statuses'));
    }
    private
    function getAcronym($str)
    {
        $words = explode(' ', trim($str));
        $acronym = '';
        foreach ($words as $word) {
            if (strlen($word) > 3) {
                $acronym .= strtoupper($word[0]);
            }
        }
        return $acronym;
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(int $location, StoreAccountRequest $request)
    {
        $account_location = AccountLocation::findOrFail($location);
        $account = $account_location->accounts()->create([
            'account_number' => $request->account_number,
            'bank_name' => $request->bank_name,
            'name' => $this->getAcronym($request->bank_name),
            'account_type_id' => $request->account_type,
            'account_status_id' => $request->account_status,
            'account_description' => $request->account_description,
            'account_address' => $account_location->name . ' - ' . $this->getAcronym($request->bank_name),
            'initial_amount' => $request->initial_amount ?? 0,
            'balance' => $request->initial_amount ?? 0,
            'created_at' => $request->created_at ?? now(),
        ]);
        //create an entry with description initial deposit
        $account->entries()->create([
            'entry_type_id' => EntryType::CREDIT_ID,
            'amount' => $request->initial_amount ?? 0,
            'description' => 'intial deposit',
            'reference_number' => now()->format('Ymdhisv'),
            'value_date' => $request->created_at ?? now(),
        ]);
        $request->initial_amount > 0 && $account->updateBalance($request->initial_amount ?? 0, 'credit');
        $routeName = $request->has('exist') ? 'account.home' : 'account.create';
        return redirect()->to(route($routeName, $location))->with('success', 'account created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(int $location, Account $account)
    {
        $page_title = 'account';
        $account_location = AccountLocation::findOrFail($location);
        if ($account->accountLocation->id !== $location) {
            abort(403, 'Account does not belongs to this location.');
        }
        $account->load('entries');
        return view('accounts.show', compact('account', 'account_location', 'page_title'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $location, Account $account)
    {
        $page_title = 'edit account';
        $account_location = AccountLocation::findOrFail($location);
        if ($account->accountLocation->id !== $location) {
            abort(403, 'Account does not belongs to this location.');
        }
        $account_types = AccountType::all();
        $account_statuses = AccountStatus::all();
        return view('accounts.edit', compact('account', 'account_location', 'page_title', 'account_types', 'account_statuses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(int $location, UpdateAccountRequest $request, Account $account)
    {
        if ($account->accountLocation->id !== $location) {
            abort(403, 'Account does not belongs to this location.');
        }

        $previousInitialAmount = $account->initial_amount;
        $account->update([
            'account_number' => $request->account_number,
            'bank_name' => $request->bank_name,
            'name' => $this->getAcronym($request->bank_name),
            'account_type_id' => $request->account_type,
            'account_status_id' => $request->account_status,
            'account_description' => $request->account_description,
            'account_address' => $account->accountLocation->name . ' - ' . $this->getAcronym($request->bank_name),
            'initial_amount' => $request->initial_amount,
            'created_at' => $request->created_at ?? now(),
        ]);

        if ($request->initial_amount !== $previousInitialAmount) {
            $difference = $request->initial_amount - $previousInitialAmount;
            $account->updateBalance($difference, 'credit');
        }

        return back()->with('success', 'Account updated successfully');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $location, Account $account)
    {
        // return $account;
        if ($account->accountLocation->id !== $location) {
            abort(403, 'Account does not belongs to this location.');
        }
        $account->delete();
        $url = back()->with('warning', 'Account deleted successfully')->getTargetUrl();
        return response()->json(['success' => true, 'url' => $url]);
    }
    private function generateAccountNumber(): int
    {
        return (int) now()->format('Ymdhisv');
    }

    public function cloneAccounts(int $location)
    {
        $accountLocation = AccountLocation::findOrFail($location);
        $clonedAccountLocation = $accountLocation->replicate();
        $clonedAccountLocation->name .= ' - Cloned';
        $clonedAccountLocation->save();

        foreach ($accountLocation->accounts as $account) {
            $clonedAccount = $account->replicate();
            $clonedAccount->account_location_id = $clonedAccountLocation->id;
            $clonedAccount->account_number = $this->generateAccountNumber();
            $clonedAccount->save();
        }

        $url = redirect()->route('account.home', $clonedAccountLocation->id)
            ->with('success', 'Accounts cloned successfully')->getTargetUrl();
        return response()->json(['success' => true, 'url' => $url]);
    }
}
