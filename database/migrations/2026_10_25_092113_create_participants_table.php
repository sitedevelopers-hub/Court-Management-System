
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('participants', function (Blueprint $table) {
            $table->id();

            $table->string('participant_number')->unique();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('participant_type');
            $table->string('email')->nullable()->index();
            $table->string('phone')->nullable();
            $table->string('national_id')->nullable()->unique();
            $table->string('organization')->nullable();
            $table->string('address')->nullable();
            $table->string('status')->default('active');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('participants');
    }
};
