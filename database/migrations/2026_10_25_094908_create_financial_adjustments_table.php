<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_adjustments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('case_id')
                ->constrained('cases')
                ->restrictOnDelete();

            $table->foreignId('payment_id')
                ->nullable()
                ->constrained('payments')
                ->restrictOnDelete();

            $table->foreignId('bail_bond_id')
                ->nullable()
                ->constrained('bail_bonds')
                ->restrictOnDelete();

            $table->foreignId('recorded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('reference_number')->unique();
            $table->string('adjustment_type');
            $table->decimal('amount', 12, 2);
            $table->text('reason');
            $table->dateTime('processed_at')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->index(['case_id', 'adjustment_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_adjustments');
    }
};
