<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('procurement_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('procurement_id')->constrained()->cascadeOnDelete();

            $table->unsignedInteger('sort_order')->default(0);
            $table->string('item_type', 30)->default('goods');

            $table->string('name');
            $table->text('description')->nullable();

            $table->decimal('quantity', 12, 3)->default(1);

            $table->string('supplier_origin', 20)->default('domestic');
            $table->string('supplier_name')->nullable();

            $table->string('purchase_currency', 3)->default('EUR');
            $table->string('sale_currency', 3)->default('EUR');

            $table->decimal('purchase_net_unit', 15, 4)->default(0);
            $table->decimal('sale_net_unit', 15, 4)->default(0);

            $table->decimal('purchase_vat_rate', 5, 2)->default(25.00);
            $table->decimal('sale_vat_rate', 5, 2)->default(25.00);

            $table->boolean('is_optional')->default(false);
            $table->string('status_flag', 30)->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['procurement_id', 'sort_order']);
            $table->index(['procurement_id', 'item_type']);
            $table->index(['procurement_id', 'supplier_origin']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('procurement_items');
    }
};