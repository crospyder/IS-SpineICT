<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('procurement_costs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('procurement_id')->constrained()->cascadeOnDelete();

            $table->string('cost_type', 30)->default('other');
            $table->string('description');

            $table->decimal('quantity', 12, 3)->default(1);
            $table->string('unit', 50)->nullable();

            $table->string('currency', 3)->default('EUR');

            $table->decimal('net_amount', 15, 4)->default(0);
            $table->decimal('vat_rate', 5, 2)->default(25.00);

            $table->string('supplier_origin', 20)->default('domestic');

            $table->boolean('include_in_offer')->default(false);
            $table->boolean('include_in_margin')->default(true);

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['procurement_id', 'cost_type']);
            $table->index(['procurement_id', 'supplier_origin']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('procurement_costs');
    }
};