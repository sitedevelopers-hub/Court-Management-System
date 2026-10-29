<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('case_participants', function (Blueprint $table) {
            $table->id();

            $table->foreignId('case_id')
                ->constrained('cases')
                ->cascadeOnDelete();

            $table->foreignId('participant_id')
                ->constrained('participants')
                ->cascadeOnDelete();

            $table->string('role');
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->unique(
                ['case_id', 'participant_id', 'role'],
                'case_participants_unique_role'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('case_participants');
    }
};
