<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salon_reservation_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('salon_reservation_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('variant_id')->nullable();
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->decimal('unit_factor', 18, 6)->default(1);
            $table->decimal('quantity', 18, 4);
            $table->text('note')->nullable();
            $table->unsignedBigInteger('subscriber_id')->nullable();
            $table->timestamps();
            $table->index(['salon_reservation_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salon_reservation_items');
    }
};
