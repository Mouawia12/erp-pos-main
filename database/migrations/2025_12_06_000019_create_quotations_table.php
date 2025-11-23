<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date')->nullable();
            $table->string('quotation_no')->unique();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('warehouse_id')->nullable();
            $table->text('note')->nullable();
            $table->double('total')->default(0);
            $table->double('discount')->default(0);
            $table->double('tax')->default(0);
            $table->double('net')->default(0);
            $table->string('status')->default('draft'); // draft | converted | cancelled
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('subscriber_id')->nullable();
            $table->timestamps();
        });

        Schema::create('quotation_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quotation_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('variant_id')->nullable();
            $table->string('variant_color')->nullable();
            $table->string('variant_size')->nullable();
            $table->string('variant_barcode')->nullable();
            $table->double('quantity');
            $table->double('price_unit');
            $table->double('tax')->default(0);
            $table->double('total')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotation_details');
        Schema::dropIfExists('quotations');
    }
};
