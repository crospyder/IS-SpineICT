<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('work_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_id')->constrained()->cascadeOnDelete();
            $table->foreignId('partner_contact_id')->nullable()->constrained('partner_contacts')->nullOnDelete();
            $table->foreignId('service_catalog_id')->nullable()->constrained('service_catalog')->nullOnDelete();
            $table->foreignId('partner_service_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('obligation_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('asset_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->date('work_date');
            $table->string('entry_type', 30)->default('work');

            $table->decimal('hours', 6, 2)->default(0);
            $table->decimal('unit_price', 10, 2)->nullable();
            $table->decimal('amount', 10, 2)->default(0);

            $table->string('title');
            $table->text('description')->nullable();

            $table->boolean('is_billable')->default(true);
            $table->boolean('is_billed')->default(false);

            $table->timestamps();

            $table->index(['partner_id', 'work_date']);
            $table->index(['project_id', 'work_date']);
            $table->index(['is_billable', 'is_billed']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_logs');
    }
};