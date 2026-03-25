<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('partner_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_catalog_id')->nullable()->constrained('service_catalog')->nullOnDelete();
            $table->foreignId('partner_contact_id')->nullable()->constrained('partner_contacts')->nullOnDelete();

            $table->string('service_type', 50)->index();
            $table->string('name');
            $table->string('domain_name')->nullable();

            $table->string('provider')->nullable();
            $table->string('registrar')->nullable();

            $table->string('status', 50)->default('active');
            $table->string('renewal_period', 50)->nullable();
            $table->boolean('auto_renew')->default(false);

            $table->date('starts_on')->nullable();
            $table->date('expires_on')->nullable();
            $table->date('renewal_date')->nullable();

            $table->string('admin_link')->nullable();
            $table->string('renewal_method')->nullable();
            $table->boolean('resolved')->default(false);
            $table->timestamp('last_alarm_sent_at')->nullable();

            $table->decimal('cost_price', 10, 2)->nullable();
            $table->decimal('sell_price', 10, 2)->nullable();
            $table->string('currency', 3)->default('EUR');

            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(['partner_id', 'service_type']);
            $table->index(['expires_on', 'status']);
            $table->index(['provider', 'registrar']);
            $table->index(['auto_renew', 'resolved']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partner_services');
    }
};