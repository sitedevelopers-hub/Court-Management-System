<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('case_documents', function (Blueprint $table) {
            $table->id();

            $table->foreignId('case_id')
                ->constrained('cases')
                ->restrictOnDelete();

            $table->foreignId('uploaded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('document_title');
            $table->string('category');
            $table->string('original_filename');
            $table->string('stored_filename')->unique();
            $table->string('storage_path');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->text('description')->nullable();
            $table->string('confidentiality')->default('restricted');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['case_id', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('case_documents');
    }
};
