<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partner_contract_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contract_service_type_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['partner_id', 'contract_service_type_id'], 'partner_contract_service_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partner_contract_services');
    }
};