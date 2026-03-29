<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partner_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('category', 50)->default('ostalo');
            $table->date('document_date')->nullable();
            $table->text('notes')->nullable();
            $table->string('file_path');
            $table->string('original_filename');
            $table->string('mime_type', 150)->nullable();
            $table->unsignedBigInteger('file_size')->default(0);
            $table->timestamps();

            $table->index(['partner_id', 'document_date']);
            $table->index(['partner_id', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partner_documents');
    }
};