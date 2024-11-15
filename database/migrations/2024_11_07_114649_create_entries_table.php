<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entry_type_id')->constrained();
            $table->foreignId('account_id')->constrained();
            $table->unsignedBigInteger('transfer_id')->nullable()->index('transfer_id');
            $table->foreign('transfer_id')->references('id')->on('transfers')->cascadeOnDelete();
            $table->date('value_date');
            $table->decimal('amount', 10, 2);
            $table->text('description')->nullable();
            $table->boolean('is_reconciled')->default(false);
            $table->boolean('is_transfer')->default(false);
            $table->string('reference_number', 55)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entries');
    }
};
