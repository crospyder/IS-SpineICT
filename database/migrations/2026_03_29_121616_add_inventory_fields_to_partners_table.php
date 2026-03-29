<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('partners', function (Blueprint $table) {
            if (!Schema::hasColumn('partners', 'inventory_enabled')) {
                $table->boolean('inventory_enabled')->default(false);
            }

            if (!Schema::hasColumn('partners', 'inventory_mode')) {
                $table->string('inventory_mode')->nullable(); // manual, agent, hybrid
            }

            if (!Schema::hasColumn('partners', 'inventory_partner_key')) {
                $table->string('inventory_partner_key')->nullable()->unique();
            }

            if (!Schema::hasColumn('partners', 'is_internal')) {
                $table->boolean('is_internal')->default(false);
            }
        });
    }

    public function down(): void
    {
        Schema::table('partners', function (Blueprint $table) {
            $table->dropColumn([
                'inventory_enabled',
                'inventory_mode',
                'inventory_partner_key',
                'is_internal',
            ]);
        });
    }
};