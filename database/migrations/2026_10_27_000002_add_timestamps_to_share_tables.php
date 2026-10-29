<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('report_shares', function (Blueprint $table) {
            $table->timestamps();
        });

        Schema::table('template_shares', function (Blueprint $table) {
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::table('report_shares', function (Blueprint $table) {
            $table->dropTimestamps();
        });

        Schema::table('template_shares', function (Blueprint $table) {
            $table->dropTimestamps();
        });
    }
};
