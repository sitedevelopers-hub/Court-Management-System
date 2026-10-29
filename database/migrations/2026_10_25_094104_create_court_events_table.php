<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('court_events', function (Blueprint $table) {
            $table->id();

            $table->foreignId('case_id')
                ->nullable()
                ->constrained('cases')
                ->nullOnDelete();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('title');
            $table->text('description')->nullable();
            $table->string('event_type')->default('deadline');
            $table->dateTime('starts_at');
            $table->dateTime('ends_at')->nullable();
            $table->dateTime('remind_at')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->index(['starts_at', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('court_events');
    }
};
