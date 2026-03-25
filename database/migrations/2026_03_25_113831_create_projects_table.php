<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_id')->constrained()->cascadeOnDelete();
            $table->foreignId('partner_contact_id')->nullable()->constrained('partner_contacts')->nullOnDelete();
            $table->foreignId('owner_user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('name');
            $table->text('description')->nullable();
            $table->string('status', 30)->default('draft');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            $table->decimal('planned_cost_total', 12, 2)->nullable();
            $table->decimal('planned_sell_total', 12, 2)->nullable();
            $table->decimal('actual_cost_total', 12, 2)->nullable();
            $table->decimal('actual_sell_total', 12, 2)->nullable();

            $table->string('currency', 3)->default('EUR');
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['partner_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};