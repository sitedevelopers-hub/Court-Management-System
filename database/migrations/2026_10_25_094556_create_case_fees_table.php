<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('case_fees', function (Blueprint $table) {
            $table->id();

            $table->foreignId('case_id')
                ->constrained('cases')
                ->restrictOnDelete();

            $table->foreignId('participant_id')
                ->nullable()
                ->constrained('participants')
                ->nullOnDelete();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('fee_reference')->unique();
            $table->string('fee_type');
            $table->string('description')->nullable();
            $table->decimal('amount', 12, 2);
            $table->decimal('amount_paid', 12, 2)->default(0);
            $table->date('due_date')->nullable();
            $table->string('status')->default('unpaid');
            $table->timestamps();

            $table->index(['case_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('case_fees');
    }
};
