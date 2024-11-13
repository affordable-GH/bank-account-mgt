<?php

namespace Database\Seeders;

use App\Models\AccountStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccountStatusSeeder extends Seeder
{
    const STATUSES = ['open', 'closed'];
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (self::STATUSES as $status) {
            if (!AccountStatus::where('status', $status)->exists()) {
                AccountStatus::create(['status' => $status]);
            }
        }
    }
}
