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
        Schema::create('account_settings', function (Blueprint $table) {
            $table->id();
            $table->integer('safe_account');
            $table->integer('bank_account');  
            $table->integer('sales_account');
            $table->integer('purchase_account'); 
            $table->integer('return_sales_account');
            $table->integer('return_purchase_account');  
            $table->integer('stock_account');
            $table->integer('sales_discount_account');  
            $table->integer('purchase_discount_account');
            $table->integer('cost_account');  
            $table->integer('reverse_profit_account');
            $table->integer('profit_account');  
            $table->integer('sales_tax_account');
            $table->integer('purchase_tax_account');  
            $table->integer('sales_tax_excise_account');
            $table->integer('warehouse_id')->nullable();   
            $table->integer('branch_id');   
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_settings');
    }
};
