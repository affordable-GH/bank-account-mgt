<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transfer extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'to_account_id',
        'from_account_id',
        'amount',
        'notes',
        'transfer_type_id',
    ];

    public function scopeInternalTransfers($query)
    {
        return $query->where('transaction_type_id', TransferType::INTERNAL_ID);
    }
    public function scopeExternalTransfers($query)
    {
        return $query->where('transaction_type_id', TransferType::EXTERNAL_ID);
    }
    public function scopeBelongsToAccounts($query, $accountIds)
    {
        return $query->whereIn('to_account_id', $accountIds)->orWhereIn('from_account_id', $accountIds)->orderBy('created_at', 'desc')->get();
    }
    public function fromAccount()
    {
        return $this->belongsTo(Account::class, 'from_account_id');
    }
    public function toAccount()
    {
        return $this->belongsTo(Account::class, 'to_account_id');
    }

    public function transferType()
    {
        return $this->belongsTo(TransferType::class);  // Assuming TransferType Model has a foreign key 'transfer_type_id'
    }
    
}
