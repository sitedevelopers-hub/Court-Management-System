<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bail_bonds', function (Blueprint $table) {
            $table->id();

            $table->foreignId('case_id')
                ->constrained('cases')
                ->restrictOnDelete();

            $table->foreignId('participant_id')
                ->constrained('participants')
                ->restrictOnDelete();

            $table->foreignId('recorded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('reference_number')->unique();
            $table->string('bond_type');
            $table->decimal('ordered_amount', 12, 2);
            $table->decimal('deposited_amount', 12, 2)->default(0);
            $table->date('order_date')->nullable();
            $table->string('status')->default('ordered');
            $table->text('conditions')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['case_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bail_bonds');
    }
};
