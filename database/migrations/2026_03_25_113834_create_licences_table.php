<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('licences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_id')->constrained()->cascadeOnDelete();
            $table->foreignId('asset_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('partner_service_id')->nullable()->constrained()->nullOnDelete();

            $table->string('name');
            $table->string('licence_type', 50)->nullable();
            $table->text('licence_key')->nullable();

            $table->string('assigned_to')->nullable();
            $table->string('status', 30)->default('active');

            $table->date('purchased_on')->nullable();
            $table->date('installed_on')->nullable();
            $table->date('valid_until')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['partner_id', 'licence_type']);
            $table->index(['valid_until', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('licences');
    }
};