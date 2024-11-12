<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountType extends Model
{
    use HasFactory;
    protected $fillable = ['type'];
    public const SAVINGS_ID = 1;
    public const CURRENT_ID = 2;
    public const SAVINGS = 'savings';
    public const CURRENT = 'current';
}
