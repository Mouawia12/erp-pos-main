<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_assemblies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipe_id')->constrained('product_recipes')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->decimal('quantity', 16, 4);
            $table->foreignId('warehouse_id')->constrained('warehouses')->cascadeOnDelete();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('subscriber_id')->nullable()->index();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('product_assembly_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assembly_id')->constrained('product_assemblies')->cascadeOnDelete();
            $table->foreignId('component_product_id')->constrained('products')->cascadeOnDelete();
            $table->decimal('quantity', 16, 4);
            $table->unsignedBigInteger('subscriber_id')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_assembly_items');
        Schema::dropIfExists('product_assemblies');
    }
};
