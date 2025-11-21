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
        Schema::create('catch_recipts', function (Blueprint $table) {
            $table->id();
            $table->integer('branch_id');   
            $table->string('docNumber');   
            $table->date('date');  
            $table->integer('from_account');   
            $table->integer('to_account');   
            $table->string('client'); 
            $table->decimal('amount');   
            $table->text('notes');   
            $table->integer('payment_type')->nullable() ->default(0);  
            $table->integer('user_id'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catch_recipts');
    }
};
