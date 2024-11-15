<?php

namespace Database\Seeders;

use App\Models\AccountType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccountTypeSeeder extends Seeder
{
    const TYPES = ['current', 'savings'];
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (self::TYPES as $type) {
            if (!AccountType::where('type', $type)->exists()) {
                AccountType::create(['type' => $type]);
            }
        }
    }
}
