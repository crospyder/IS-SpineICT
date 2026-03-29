<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('partners', function (Blueprint $table) {
            $table->boolean('is_contract_client')->default(false)->after('is_active');
            $table->string('contract_status')->nullable()->after('is_contract_client');
            $table->date('contract_start_date')->nullable()->after('contract_status');
            $table->date('contract_end_date')->nullable()->after('contract_start_date');
            $table->text('contract_notes')->nullable()->after('contract_end_date');
        });
    }

    public function down(): void
    {
        Schema::table('partners', function (Blueprint $table) {
            $table->dropColumn([
                'is_contract_client',
                'contract_status',
                'contract_start_date',
                'contract_end_date',
                'contract_notes',
            ]);
        });
    }
};