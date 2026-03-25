<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('project_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_catalog_id')->nullable()->constrained('service_catalog')->nullOnDelete();

            $table->string('item_type', 30)->default('service');
            $table->string('name');
            $table->text('description')->nullable();

            $table->decimal('quantity', 10, 2)->default(1);
            $table->boolean('purchase_has_vat')->default(true);

            $table->decimal('cost_price_net', 12, 2)->nullable();
            $table->decimal('sell_price_net', 12, 2)->nullable();
            $table->decimal('profit_net', 12, 2)->nullable();

            $table->string('sale_status', 30)->nullable();
            $table->boolean('is_manual_cost')->default(false);

            $table->integer('sort_order')->default(0);
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['project_id', 'item_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_items');
    }
};