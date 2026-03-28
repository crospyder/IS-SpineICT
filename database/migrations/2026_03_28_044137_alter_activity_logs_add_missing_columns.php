<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('activity_logs')) {
            Schema::create('activity_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->string('subject_type');
                $table->unsignedBigInteger('subject_id');
                $table->string('event', 50);
                $table->string('entity_type', 50)->nullable();
                $table->string('title')->nullable();
                $table->text('message');
                $table->json('old_values')->nullable();
                $table->json('new_values')->nullable();
                $table->timestamps();

                $table->index(['subject_type', 'subject_id']);
                $table->index(['event']);
                $table->index(['entity_type']);
                $table->index(['created_at']);
            });

            return;
        }

        Schema::table('activity_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('activity_logs', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('id');
            }

            if (!Schema::hasColumn('activity_logs', 'subject_type')) {
                $table->string('subject_type')->nullable()->after('user_id');
            }

            if (!Schema::hasColumn('activity_logs', 'subject_id')) {
                $table->unsignedBigInteger('subject_id')->nullable()->after('subject_type');
            }

            if (!Schema::hasColumn('activity_logs', 'event')) {
                $table->string('event', 50)->nullable()->after('subject_id');
            }

            if (!Schema::hasColumn('activity_logs', 'entity_type')) {
                $table->string('entity_type', 50)->nullable()->after('event');
            }

            if (!Schema::hasColumn('activity_logs', 'title')) {
                $table->string('title')->nullable()->after('entity_type');
            }

            if (!Schema::hasColumn('activity_logs', 'message')) {
                $table->text('message')->nullable()->after('title');
            }

            if (!Schema::hasColumn('activity_logs', 'old_values')) {
                $table->json('old_values')->nullable()->after('message');
            }

            if (!Schema::hasColumn('activity_logs', 'new_values')) {
                $table->json('new_values')->nullable()->after('old_values');
            }

            if (!Schema::hasColumn('activity_logs', 'created_at')) {
                $table->timestamp('created_at')->nullable()->after('new_values');
            }

            if (!Schema::hasColumn('activity_logs', 'updated_at')) {
                $table->timestamp('updated_at')->nullable()->after('created_at');
            }
        });

        try {
            DB::statement('CREATE INDEX IF NOT EXISTS activity_logs_subject_idx ON activity_logs(subject_type, subject_id)');
        } catch (\Throwable $e) {
        }

        try {
            DB::statement('CREATE INDEX IF NOT EXISTS activity_logs_event_idx ON activity_logs(event)');
        } catch (\Throwable $e) {
        }

        try {
            DB::statement('CREATE INDEX IF NOT EXISTS activity_logs_entity_type_idx ON activity_logs(entity_type)');
        } catch (\Throwable $e) {
        }

        try {
            DB::statement('CREATE INDEX IF NOT EXISTS activity_logs_created_at_idx ON activity_logs(created_at)');
        } catch (\Throwable $e) {
        }
    }

    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            if (Schema::hasColumn('activity_logs', 'user_id')) {
                $table->dropColumn('user_id');
            }

            if (Schema::hasColumn('activity_logs', 'subject_type')) {
                $table->dropColumn('subject_type');
            }

            if (Schema::hasColumn('activity_logs', 'subject_id')) {
                $table->dropColumn('subject_id');
            }

            if (Schema::hasColumn('activity_logs', 'event')) {
                $table->dropColumn('event');
            }

            if (Schema::hasColumn('activity_logs', 'entity_type')) {
                $table->dropColumn('entity_type');
            }

            if (Schema::hasColumn('activity_logs', 'title')) {
                $table->dropColumn('title');
            }

            if (Schema::hasColumn('activity_logs', 'message')) {
                $table->dropColumn('message');
            }

            if (Schema::hasColumn('activity_logs', 'old_values')) {
                $table->dropColumn('old_values');
            }

            if (Schema::hasColumn('activity_logs', 'new_values')) {
                $table->dropColumn('new_values');
            }
        });
    }
};