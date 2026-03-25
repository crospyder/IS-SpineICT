<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('obligations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_id')->constrained()->cascadeOnDelete();
            $table->foreignId('partner_service_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('partner_contact_id')->nullable()->constrained('partner_contacts')->nullOnDelete();
            $table->foreignId('asset_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('assigned_user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('title');
            $table->text('description')->nullable();

            $table->string('type', 50)->default('other');
            $table->string('priority', 20)->default('normal');
            $table->string('status', 30)->default('open');

            $table->date('due_date')->nullable();
            $table->date('completed_date')->nullable();

            $table->boolean('is_recurring')->default(false);
            $table->string('recurrence_type', 30)->nullable();
            $table->unsignedSmallInteger('remind_days_before')->default(7);

            $table->timestamp('last_reminder_sent_at')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['partner_id', 'status', 'due_date']);
            $table->index(['type', 'priority']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('obligations');
    }
};