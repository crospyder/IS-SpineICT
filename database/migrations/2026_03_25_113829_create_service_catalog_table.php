<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('service_catalog', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 50)->unique();
            $table->string('service_type', 50);
            $table->text('description')->nullable();
            $table->string('billing_type', 50)->nullable();
            $table->decimal('default_unit_price', 10, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['service_type', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_catalog');
    }
};