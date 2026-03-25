<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('credentials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_id')->constrained()->cascadeOnDelete();
            $table->foreignId('asset_id')->nullable()->constrained()->nullOnDelete();

            $table->string('credential_type', 50);
            $table->string('title');

            $table->string('username')->nullable();
            $table->text('password')->nullable();
            $table->text('secret_note')->nullable();

            $table->string('url')->nullable();
            $table->string('remote_id')->nullable();

            $table->date('valid_until')->nullable();
            $table->boolean('is_active')->default(true);

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['partner_id', 'credential_type']);
            $table->index(['asset_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('credentials');
    }
};