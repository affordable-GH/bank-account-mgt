<?php

namespace Database\Seeders;

use App\Models\TransferType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TransferTypeSeeder extends Seeder
{
    const TYPES = ['internal', 'external'];
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (self::TYPES as $type) {
            if (!TransferType::where('type', $type)->exists()) {
                TransferType::create(['type' => $type]);
            }
        }
    }
}
