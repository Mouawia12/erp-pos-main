<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('status')->default('active'); // active | inactive | ended
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('subscriber_id')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });

        Schema::create('promotion_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('promotion_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('variant_id')->nullable();
            $table->string('variant_color')->nullable();
            $table->string('variant_size')->nullable();
            $table->string('variant_barcode')->nullable();
            $table->integer('min_qty')->default(1);
            $table->decimal('discount_value', 12, 2)->default(0);
            $table->string('discount_type')->default('percent'); // percent | amount
            $table->string('special_barcode')->nullable();
            $table->integer('max_qty')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotion_items');
        Schema::dropIfExists('promotions');
    }
};
