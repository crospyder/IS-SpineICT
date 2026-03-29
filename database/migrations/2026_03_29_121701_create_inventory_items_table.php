<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_id')->constrained()->cascadeOnDelete();

            $table->string('source')->default('manual'); // manual | agent
            $table->string('agent_device_id')->nullable()->index();

            $table->string('hostname')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('manufacturer')->nullable();
            $table->string('model')->nullable();

            $table->string('cpu')->nullable();
            $table->integer('cpu_cores')->nullable();
            $table->integer('cpu_threads')->nullable();
            $table->integer('ram_gb')->nullable();
            $table->string('gpu')->nullable();

            $table->string('system_drive')->nullable();
            $table->integer('disk_total_gb')->nullable();
            $table->integer('disk_free_gb')->nullable();

            $table->string('os_caption')->nullable();
            $table->string('os_version')->nullable();
            $table->string('os_build_number')->nullable();
            $table->string('os_architecture')->nullable();
            $table->string('os_install_date')->nullable();
            $table->string('os_last_boot_time')->nullable();

            $table->boolean('tpm_present')->nullable();
            $table->boolean('bitlocker_enabled')->nullable();
            $table->boolean('windows_activated')->nullable();

            $table->boolean('vpn_detected')->nullable();
            $table->string('primary_adapter_name')->nullable();
            $table->string('primary_mac_address')->nullable();
            $table->string('primary_ip_address')->nullable();

            $table->boolean('is_domain_joined')->nullable();
            $table->string('domain_name')->nullable();
            $table->boolean('is_azure_ad_joined')->nullable();

            $table->boolean('windows_update_service_running')->nullable();
            $table->string('windows_update_service_status')->nullable();

            $table->string('current_user')->nullable();
            $table->string('agent_version')->nullable();

            $table->timestamp('last_seen_at')->nullable();

            $table->text('raw_payload_json')->nullable();

            $table->string('status')->default('active');
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};