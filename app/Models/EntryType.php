<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntryType extends Model
{
    use HasFactory;
    public const CREDIT_ID = 1;
    public const DEBIT_ID = 2;
    public const TYPE_CREDIT = 'credit';
    public const TYPE_DEBIT = 'debit';
    protected $fillable = ['type'];
}
