
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hearings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('case_id')
                ->constrained('cases')
                ->restrictOnDelete();

            $table->foreignId('courtroom_id')
                ->nullable()
                ->constrained('courtrooms')
                ->nullOnDelete();

            $table->dateTime('scheduled_start');
            $table->dateTime('scheduled_end')->nullable();
            $table->string('hearing_type')->default('hearing');
            $table->string('status')->default('scheduled');
            $table->text('outcome')->nullable();
            $table->text('adjournment_reason')->nullable();
            $table->dateTime('rescheduled_from')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['courtroom_id', 'scheduled_start']);
            $table->index(['case_id', 'scheduled_start']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hearings');
    }
};
