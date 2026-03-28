<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {

            if (!Schema::hasColumn('activity_logs', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained()->nullOnDelete();
            }

            if (!Schema::hasColumn('activity_logs', 'entity_type')) {
                $table->string('entity_type')->nullable();
            }

            if (!Schema::hasColumn('activity_logs', 'title')) {
                $table->string('title')->nullable();
            }

            if (!Schema::hasColumn('activity_logs', 'message')) {
                $table->text('message')->nullable();
            }

            if (!Schema::hasColumn('activity_logs', 'old_values')) {
                $table->json('old_values')->nullable();
            }

            if (!Schema::hasColumn('activity_logs', 'new_values')) {
                $table->json('new_values')->nullable();
            }
        });
    }

    public function down(): void
    {
        // nema rollbacka jer ne želimo riskirati brisanje
    }
};