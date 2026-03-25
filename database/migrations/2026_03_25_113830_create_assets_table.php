<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_asset_id')->nullable()->constrained('assets')->nullOnDelete();

            $table->string('asset_type', 50);
            $table->string('name');
            $table->string('hostname')->nullable();
            $table->string('domain_name')->nullable();

            $table->string('ip_address')->nullable();
            $table->string('local_ip')->nullable();
            $table->string('public_ip')->nullable();
            $table->string('mac_address')->nullable();

            $table->string('manufacturer')->nullable();
            $table->string('model')->nullable();
            $table->string('serial_number')->nullable();

            $table->string('os_name')->nullable();
            $table->string('os_version')->nullable();

            $table->string('cpu')->nullable();
            $table->unsignedInteger('ram_gb')->nullable();
            $table->string('storage_summary')->nullable();

            $table->string('location')->nullable();
            $table->string('status', 30)->default('active');

            $table->date('purchased_on')->nullable();
            $table->date('installed_on')->nullable();
            $table->date('warranty_until')->nullable();

            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['partner_id', 'asset_type']);
            $table->index(['hostname', 'ip_address']);
            $table->index(['serial_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};