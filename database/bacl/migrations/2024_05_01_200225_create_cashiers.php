<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cashiers', function (Blueprint $table) {
            $table->id();
            $table->string('company')->nullable();   
            $table->string('name')->nullable();   
            $table->string('address')->nullable();  
            $table->string('commercial_register')->nullable();   
            $table->string('license')->nullable();   
            $table->string('tax_number')->nullable(); 
            $table->string('email')->nullable();   
            $table->string('bill_holder1')->nullable();   
            $table->string('bill_holder2')->nullable();  
            $table->string('phone')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cashiers');
    }
};
