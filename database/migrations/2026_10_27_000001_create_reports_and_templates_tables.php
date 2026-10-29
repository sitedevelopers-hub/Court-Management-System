<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('generated_reports', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('report_type', 60)->default('financial');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('file_path');               // saved report file (docx/pdf)
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('report_shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('generated_report_id')->constrained('generated_reports')->cascadeOnDelete();
            $table->foreignId('shared_with')->constrained('users')->cascadeOnDelete();
            $table->string('note', 500)->nullable();
            $table->timestamp('shared_at')->useCurrent();
        });

        Schema::create('work_templates', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->longText('content');               // plain text typed by the user
            $table->string('file_path')->nullable();   // generated .docx file
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('template_shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_template_id')->constrained('work_templates')->cascadeOnDelete();
            $table->foreignId('shared_with')->constrained('users')->cascadeOnDelete();
            $table->string('note', 500)->nullable();
            $table->timestamp('shared_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('template_shares');
        Schema::dropIfExists('work_templates');
        Schema::dropIfExists('report_shares');
        Schema::dropIfExists('generated_reports');
    }
};
