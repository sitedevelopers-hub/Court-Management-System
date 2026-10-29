<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('case_fee_id')
                ->constrained('case_fees')
                ->restrictOnDelete();

            $table->foreignId('received_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('receipt_number')->unique();
            $table->decimal('amount', 12, 2);
            $table->string('payment_method');
            $table->string('transaction_reference')->nullable()->index();
            $table->dateTime('paid_at');
            $table->string('status')->default('completed');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['case_fee_id', 'paid_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
