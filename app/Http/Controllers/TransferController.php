<?php

namespace App\Http\Controllers;

use App\Models\Transfer;
use App\Http\Requests\StoreTransferRequest;
use App\Http\Requests\UpdateTransferRequest;
use App\Models\Account;
use App\Models\AccountLocation;
use App\Models\Entry;
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
        return view('transfers.create', compact('account_location', 'page_title', 'accounts'));
    }

    private function generateRef(): int
    {
        return (int) now()->format('Ymdhisv');
    }
    private function createEntry(int $account_id, int $transferId, int $entryType, float $amount, $date, string $description)
    {
        Account::find($account_id)->entries()->create([
            'entry_type_id' => $entryType,
            'description' => $description,
            'amount' => $amount,
            'reference_number' => $this->generateRef(),
            'value_date' => $date ?? now(),
            'is_transfer' => true,
            'transfer_id' => $transferId,
            'created_at' => $date ?? now(),
        ]);
    }
    private function updateEntry(int $account_id, int $entryType, float $amount, $date, string $description)
    {

        Entry::where('id', $account_id)->update([
            'entry_type_id' => $entryType,
            'description' => $description,
            'amount' => $amount,
            'reference_number' => $this->generateRef(),
            'value_date' => $date ?? now(),
            'is_transfer' => true,
            'created_at' => $date ?? now(),
        ]);
    }

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

        DB::transaction(function () use ($from_account_id, $to_account_id, $amount, $transfer_type, $notes, $date, $request, $to_account, $from_account) {
            // Update sender's balance
            // $fromAccount = Account::find($from_account_id);
            // $fromAccount->balance -= $amount;
            // $fromAccount->save();

            // // Update recipient's balance
            // $toAccount = Account::find($to_account_id);
            // $toAccount->balance += $amount;
            // $toAccount->save();

            // Create transfer record
            $transfer = Transfer::create([
                'from_account_id' => $from_account_id,
                'to_account_id' => $to_account_id,
                'amount' => $amount,
                'notes' => $notes,
                'transfer_type_id' => $transfer_type,
                'transfer_date' => $date ?? now(),
            ]);
            $this->createEntry($from_account_id, $transfer->id, EntryType::DEBIT_ID, $request->amount, $request->date, 'Transfer to ' . $to_account->name);
            $this->createEntry($to_account_id, $transfer->id, EntryType::CREDIT_ID, $request->amount, $request->date, 'Transfer from ' . $from_account->name);
        });
        $route = $request->has('exit') ? 'transfers.index' : 'transfers.create';
        return redirect()->to(route($route, ['location' => $location]))->with('success', 'Transfer created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(int $location, Transfer $transfer)
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $location, Transfer $transfer)
    {
        $page_title = 'edit transfer';
        $account_location = AccountLocation::findOrFail($location);
        $accounts = Account::open()->orderBy('updated_at', 'desc')->get();
        return view('transfers.edit', compact('transfer', 'account_location', 'page_title', 'accounts'));
    }

    /**
     * Update the specified resource in storage.
     */
    // public function update(int $location, UpdateTransferRequest $request, Transfer $transfer)
    // {
    //     $account_location = AccountLocation::findOrFail($location);
    //     $transfer_type = ($account_location->accounts()->whereIn('id', [$request->from_account, $request->to_account])->count() > 1)
    //         ? TransferType::INTERNAL_ID
    //         : TransferType::EXTERNAL_ID;

    //     $from_account = Account::findOrFail($request->from_account);
    //     $to_account = Account::findOrFail($request->to_account);
    //     $amount = $request->amount;
    //     $from_account_id = $from_account->id;
    //     $to_account_id = $to_account->id;
    //     $notes = $request->notes;
    //     $date = $request->date;
    //     // Validate balance before transfer
    //     if ($from_account->balance < $amount) {
    //         return back()->with('error', 'Insufficient Account Balance');
    //     }
    //     $this->updateEntry($from_account_id, EntryType::DEBIT_ID, $request->amount, $request->date, 'Transfer to ' . $to_account->name);
    //     $this->updateEntry($to_account_id, EntryType::CREDIT_ID, $request->amount, $request->date, 'Transfer from ' . $from_account->name);

    //     DB::transaction(function () use ($transfer, $from_account_id, $to_account_id, $amount, $transfer_type, $notes, $date) {
    //         // Update sender's balance
    //         $previous_amount = $transfer->amount;
    //         $fromAccount = Account::find($from_account_id);
    //         $fromAccount->balance += $previous_amount;
    //         $fromAccount->save();

    //         $toAccount = Account::find($to_account_id);
    //         $toAccount->balance -= $previous_amount;
    //         $toAccount->save();

    //         $fromAccountUpdate = $fromAccount->fresh();
    //         $toAccountUpdate = $toAccount->fresh();


    //         $fromAccountUpdate->balance -= $amount;
    //         $fromAccountUpdate->save();
    //         $toAccountUpdate->balance += $amount;
    //         $toAccountUpdate->save();

    //         // update transfer record
    //         $transfer->update([
    //             'from_account_id' => $from_account_id,
    //             'to_account_id' => $to_account_id,
    //             'amount' => $amount,
    //             'notes' => $notes,
    //             'transfer_type_id' => $transfer_type,
    //             'transfer_date' => $date ?? now(),
    //         ]);
    //     });
    // }
    public function update(int $location, UpdateTransferRequest $request, Transfer $transfer)
    {
        abort(403);
        $accountLocation = AccountLocation::findOrFail($location);
        $transferType = $this->determineTransferType($accountLocation, $request);

        $fromAccount = Account::findOrFail($request->from_account);
        $toAccount = Account::findOrFail($request->to_account);

        if (!$this->hasSufficientBalance($fromAccount, $request->amount)) {
            return redirect()->back()->with('error', 'Insufficient Account Balance');
        }

        DB::transaction(function () use ($transfer, $fromAccount, $toAccount, $request, $transferType) {
            $this->updateAccounts($fromAccount, $toAccount, $request->amount, $transfer->amount);
            $this->updateTransferRecord($transfer, $fromAccount, $toAccount, $request, $transferType);
            $this->createTransferEntries($fromAccount, $toAccount, $request);
        });
        return redirect()->back()->with('success', 'Transfer updated successfully');
    }

    private function determineTransferType(AccountLocation $accountLocation, UpdateTransferRequest $request)
    {
        return ($accountLocation->accounts()->whereIn('id', [$request->from_account, $request->to_account])->count() > 1)
            ? TransferType::INTERNAL_ID
            : TransferType::EXTERNAL_ID;
    }

    private function hasSufficientBalance(Account $account, float $amount)
    {
        return $account->balance >= $amount;
    }

    private function updateAccounts(Account $fromAccount, Account $toAccount, float $amount, float $previousAmount)
    {
        $fromAccount->balance += $previousAmount;
        $fromAccount->save();
        $toAccount->balance -= $previousAmount;
        $toAccount->save();

        $fromAccount->balance -= $amount;
        $fromAccount->save();
        $toAccount->balance += $amount;
        $toAccount->save();
    }

    private function updateTransferRecord(Transfer $transfer, Account $fromAccount, Account $toAccount, UpdateTransferRequest $request, int $transferType)
    {
        $transfer->update([
            'from_account_id' => $fromAccount->id,
            'to_account_id' => $toAccount->id,
            'amount' => $request->amount,
            'notes' => $request->notes,
            'transfer_type_id' => $transferType,
            'transfer_date' => $request->date ?? now(),
        ]);
    }

    private function createTransferEntries(Account $fromAccount, Account $toAccount, UpdateTransferRequest $request)
    {
        $this->updateEntry($fromAccount->id, EntryType::DEBIT_ID, $request->amount, $request->date, 'Transfer to ' . $toAccount->name);
        $this->updateEntry($toAccount->id, EntryType::CREDIT_ID, $request->amount, $request->date, 'Transfer from ' . $fromAccount->name);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $location, Transfer $transfer)
    {
        return $transfer;
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

        DB::transaction(function () use ($from_account_id, $to_account_id, $amount, $transfer_type, $notes, $date, $request, $to_account, $from_account) {
            // Update sender's balance
            // $fromAccount = Account::find($from_account_id);
            // $fromAccount->balance -= $amount;
            // $fromAccount->save();

            // // Update recipient's balance
            // $toAccount = Account::find($to_account_id);
            // $toAccount->balance += $amount;
            // $toAccount->save();

            // Create transfer record
            $transfer = Transfer::create([
                'from_account_id' => $from_account_id,
                'to_account_id' => $to_account_id,
                'amount' => $amount,
                'notes' => $notes,
                'transfer_type_id' => $transfer_type,
                'transfer_date' => $date ?? now(),
            ]);
            $this->createEntry($from_account_id, $transfer->id, EntryType::DEBIT_ID, $request->amount, $request->date, 'Transfer to ' . $to_account->name);
            $this->createEntry($to_account_id, $transfer->id, EntryType::CREDIT_ID, $request->amount, $request->date, 'Transfer from ' . $from_account->name);
        });
        $route = $request->has('exit') ? 'transfers.index' : 'transfers.create';
        return redirect()->to(route($route, ['location' => $location]))->with('success', 'Transfer created successfully');
  
    }
}
