<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('document_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_document_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('version');
            $table->string('stored_path');
            $table->string('original_filename');
            $table->string('mime')->nullable();
            $table->unsignedBigInteger('size_kb')->nullable();
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
            $table->text('note')->nullable();
            $table->timestamps();
            $table->index('candidate_document_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_versions');
    }
};
