<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catch_recipt_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('catch_recipt_id');
            $table->unsignedBigInteger('account_id');
            $table->decimal('amount', 15, 4);
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catch_recipt_details');
    }
};
