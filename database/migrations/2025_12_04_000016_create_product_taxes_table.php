<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_taxes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('tax_rate_id');
            $table->timestamps();

            $table->unique(['product_id','tax_rate_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_taxes');
    }
};
