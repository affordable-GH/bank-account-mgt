<?php

namespace Database\Seeders;

use App\Models\EntryType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EntryTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    const ENTRY_TYPES = ['credit', 'debit'];
    public function run(): void
    {
        foreach (self::ENTRY_TYPES as $type) {
            if (!EntryType::where('type', $type)->exists()) {
                EntryType::create(['type' => $type]);
            }
        }
    }
}
