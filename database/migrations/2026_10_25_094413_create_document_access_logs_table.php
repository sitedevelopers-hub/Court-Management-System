<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_access_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('case_document_id')
                ->constrained('case_documents')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('action');
            $table->string('ip_address', 45)->nullable();
            $table->text('details')->nullable();
            $table->timestamp('accessed_at')->useCurrent();
            $table->timestamps();

            $table->index(['case_document_id', 'accessed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_access_logs');
    }
};
