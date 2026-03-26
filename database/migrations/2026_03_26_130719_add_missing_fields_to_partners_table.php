<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('partners', 'legal_name')) {
            Schema::table('partners', function (Blueprint $table) {
                $table->string('legal_name')->nullable()->after('name');
            });
        }

        if (!Schema::hasColumn('partners', 'oib')) {
            Schema::table('partners', function (Blueprint $table) {
                $table->string('oib')->nullable()->after('legal_name');
            });
        }

        if (!Schema::hasColumn('partners', 'email')) {
            Schema::table('partners', function (Blueprint $table) {
                $table->string('email')->nullable()->after('oib');
            });
        }

        if (!Schema::hasColumn('partners', 'phone')) {
            Schema::table('partners', function (Blueprint $table) {
                $table->string('phone')->nullable()->after('email');
            });
        }

        if (!Schema::hasColumn('partners', 'website')) {
            Schema::table('partners', function (Blueprint $table) {
                $table->string('website')->nullable()->after('phone');
            });
        }

        if (!Schema::hasColumn('partners', 'address')) {
            Schema::table('partners', function (Blueprint $table) {
                $table->string('address')->nullable()->after('website');
            });
        }

        if (!Schema::hasColumn('partners', 'city')) {
            Schema::table('partners', function (Blueprint $table) {
                $table->string('city')->nullable()->after('address');
            });
        }

        if (!Schema::hasColumn('partners', 'postal_code')) {
            Schema::table('partners', function (Blueprint $table) {
                $table->string('postal_code')->nullable()->after('city');
            });
        }

        if (!Schema::hasColumn('partners', 'country')) {
            Schema::table('partners', function (Blueprint $table) {
                $table->string('country')->nullable()->after('postal_code');
            });
        }

        if (!Schema::hasColumn('partners', 'notes')) {
            Schema::table('partners', function (Blueprint $table) {
                $table->text('notes')->nullable()->after('country');
            });
        }

        if (!Schema::hasColumn('partners', 'is_active')) {
            Schema::table('partners', function (Blueprint $table) {
                $table->boolean('is_active')->default(true)->after('notes');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('partners', 'legal_name')) {
            Schema::table('partners', function (Blueprint $table) {
                $table->dropColumn('legal_name');
            });
        }

        if (Schema::hasColumn('partners', 'email')) {
            Schema::table('partners', function (Blueprint $table) {
                $table->dropColumn('email');
            });
        }

        if (Schema::hasColumn('partners', 'phone')) {
            Schema::table('partners', function (Blueprint $table) {
                $table->dropColumn('phone');
            });
        }

        if (Schema::hasColumn('partners', 'website')) {
            Schema::table('partners', function (Blueprint $table) {
                $table->dropColumn('website');
            });
        }

        if (Schema::hasColumn('partners', 'address')) {
            Schema::table('partners', function (Blueprint $table) {
                $table->dropColumn('address');
            });
        }

        if (Schema::hasColumn('partners', 'city')) {
            Schema::table('partners', function (Blueprint $table) {
                $table->dropColumn('city');
            });
        }

        if (Schema::hasColumn('partners', 'postal_code')) {
            Schema::table('partners', function (Blueprint $table) {
                $table->dropColumn('postal_code');
            });
        }

        if (Schema::hasColumn('partners', 'country')) {
            Schema::table('partners', function (Blueprint $table) {
                $table->dropColumn('country');
            });
        }

        if (Schema::hasColumn('partners', 'notes')) {
            Schema::table('partners', function (Blueprint $table) {
                $table->dropColumn('notes');
            });
        }

        if (Schema::hasColumn('partners', 'is_active')) {
            Schema::table('partners', function (Blueprint $table) {
                $table->dropColumn('is_active');
            });
        }
    }
};