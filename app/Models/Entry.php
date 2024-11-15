<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Entry extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'account_id',
        'entry_type_id',
        'amount',
        'description',
        'reference_number',
        'is_reconciled',
        'is_transfer',
        'value_date',
        'transfer_id'
    ];
    protected $orderBy = ['created_at', 'desc'];

    protected $casts = [
        'value_date' => 'date',
        'is_reconciled' => 'boolean', // Assuming the column is of boolean type in the database table
        'is_transfer' => 'boolean' // Assuming the column is of boolean type in the database table
    ];
    public function account()
    {
        return $this->belongsTo(Account::class);  // Assuming Account Model has a foreign key 'account_id'
    }
    public function scopeEntriesToReconcile($query)
    {
        return $query->where('is_reconciled', false);  // Assuming the column is of boolean type in the database table
    }
    public function entryType()
    {
        return $this->belongsTo(EntryType::class);
    }
    public function transactionType()
    {
        return $this->belongsTo(TransactionType::class);
    }
    public function scopeBelongsToAccounts($query, $accountIds)
    {
        return $query->whereIn('account_id', $accountIds);
    }
    public function transfer()
    {
        return $this->belongsTo(Transfer::class);  // Assuming Transfer Model has a foreign key 'transfer_id'
    }
    public function reconcile(int|array $id)
    {
        if (is_array($id)) {
            return $this->whereIn('id', $id)->update(['is_reconciled' => true]);
        }
        return $this->where('id', $id)->update(['is_reconciled' => true]);
    }
}
