<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('procurements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('partner_id')->constrained()->cascadeOnDelete();

            $table->string('title');
            $table->string('reference_no')->nullable();

            $table->string('status', 50)->default('draft');

            $table->date('offer_date')->nullable();
            $table->date('valid_until')->nullable();

            $table->string('default_sale_currency', 3)->default('EUR');
            $table->string('default_purchase_currency', 3)->default('EUR');

            $table->decimal('fx_eur_to_usd', 12, 6)->default(1.000000);
            $table->decimal('fx_usd_to_eur', 12, 6)->default(1.000000);

            $table->decimal('vat_rate', 5, 2)->default(25.00);

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['partner_id', 'status']);
            $table->index('offer_date');
            $table->index('valid_until');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('procurements');
    }
};