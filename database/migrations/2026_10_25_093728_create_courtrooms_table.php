
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courtrooms', function (Blueprint $table) {
            $table->id();
            $table->string('room_number')->unique();
            $table->string('court_name');
            $table->unsignedInteger('capacity')->nullable();
            $table->string('location')->nullable();
            $table->string('status')->default('available');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courtrooms');
    }
};
