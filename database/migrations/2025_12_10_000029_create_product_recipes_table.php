<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->decimal('yield_quantity', 16, 4)->default(1);
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('subscriber_id')->nullable()->index();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('product_recipe_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipe_id')->constrained('product_recipes')->cascadeOnDelete();
            $table->foreignId('component_product_id')->constrained('products')->cascadeOnDelete();
            $table->decimal('quantity', 16, 4)->default(1);
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->unsignedBigInteger('subscriber_id')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_recipe_items');
        Schema::dropIfExists('product_recipes');
    }
};
