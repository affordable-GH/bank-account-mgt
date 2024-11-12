<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountStatus extends Model
{
    use HasFactory;
    public const OPEN_ID = 1;
    public const CLOSED_ID = 2;
    public const OPEN = 'open';
    public const CLOSED = 'closed';
    protected $fillable = ['status'];
}
