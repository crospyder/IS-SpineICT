<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_scans', function (Blueprint $table) {
            $table->id();

            $table->foreignId('inventory_item_id')->constrained()->cascadeOnDelete();

            $table->string('agent_device_id')->nullable();
            $table->string('scan_type')->nullable();

            $table->string('agent_version')->nullable();

            $table->string('scan_started_at')->nullable();
            $table->string('scan_completed_at')->nullable();
            $table->integer('scan_duration_ms')->nullable();

            $table->longText('payload_json');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_scans');
    }
};