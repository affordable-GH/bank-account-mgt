<?php

namespace App\Http\Controllers;

use App\Models\Transfer;
use App\Http\Requests\StoreTransferRequest;
use App\Http\Requests\UpdateTransferRequest;
use App\Models\Account;
use App\Models\AccountLocation;
use App\Models\EntryType;
use App\Models\TransferType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransferController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(int $location)
    {
        $account_location = AccountLocation::findOrFail($location);
        $transfers = Transfer::belongsToAccounts($account_location->accounts->pluck('id'));
        return view('transfers.index', compact('transfers', 'account_location'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(int $location)
    {
        $page_title = 'create transfer';
        $account_location = AccountLocation::findOrFail($location);
        $accounts = Account::open()->orderBy('updated_at', 'desc')->get();
        $transfer_types = TransferType::all();
        if ($transfer_types->isEmpty()) {
            TransferType::create([
                'id' => 1,
                'type' => 'internal',
            ]);
            TransferType::create([
                'id' => 2,
                'type' => 'external',
            ]);
        }
        return view('transfers.create', compact('account_location', 'page_title', 'accounts'));
    }

    private function generateRef(): int
    {
        return (int) now()->format('Ymdhisv');
    }
    private function createEntry($account_id, $entryType, $amount, $date, $description)
    {
        Account::find($account_id)->entries()->create([
            'entry_type_id' => $entryType,
            'description' => $description,
            'amount' => $amount,
            'date' => $date,
            'reference_number' => $this->generateRef(),
            'value_date' => $date ?? now(),
            'is_transfer' => true,
            'created_at' => $date ?? now(),
        ]);
    }
    public function updateEntry() {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(int $location, StoreTransferRequest $request)
    {
        $account_location = AccountLocation::findOrFail($location);
        $transfer_type = ($account_location->accounts()->whereIn('id', [$request->from_account, $request->to_account])->count() > 1)
            ? TransferType::INTERNAL_ID
            : TransferType::EXTERNAL_ID;

        $from_account = Account::findOrFail($request->from_account);
        $to_account = Account::findOrFail($request->to_account);
        $amount = $request->amount;
        $from_account_id = $from_account->id;
        $to_account_id = $to_account->id;
        $notes = $request->notes;
        $date = $request->date;
        // Validate balance before transfer
        if ($from_account->balance < $amount) {
            return back()->with('error', 'Insufficient Account Balance');
        }
        // dd($request->all(), $from_account, $to_account, $transfer_type);
        $this->createEntry($from_account_id, EntryType::DEBIT_ID, $request->amount, $request->date, 'Transfer to ' . $to_account->name);
        $this->createEntry($to_account_id, EntryType::CREDIT_ID, $request->amount, $request->date, 'Transfer from ' . $from_account->name);

        DB::transaction(function () use ($from_account_id, $to_account_id, $amount, $transfer_type, $notes, $date) {
            // Update sender's balance
            $fromAccount = Account::find($from_account_id);
            $fromAccount->balance -= $amount;
            $fromAccount->save();

            // Update recipient's balance
            $toAccount = Account::find($to_account_id);
            $toAccount->balance += $amount;
            $toAccount->save();

            // Create transfer record
            Transfer::create([
                'from_account_id' => $from_account_id,
                'to_account_id' => $to_account_id,
                'amount' => $amount,
                'notes' => $notes,
                'transfer_type_id' => $transfer_type,
                'transfer_date' => $date ?? now(),
            ]);
        });
        $route = $request->has('exit') ? 'transfers.index' : 'transfers.create';
        return redirect()->to(route($route, ['location' => $location]))->with('success', 'Transfer created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Transfer $transfer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $location, Transfer $transfer)
    {
        return $transfer;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(int $location, UpdateTransferRequest $request, Transfer $transfer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $location, Transfer $transfer)
    {
        //
    }
}
