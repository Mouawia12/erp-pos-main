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
        Schema::create('companies', function (Blueprint $table) {
            $table->id(); 
            $table->integer('group_id');  // 1 => biller , 2=> customer , 3=> supplier
            $table->string('group_name')->nullable(); // biller , customer , supplier
            $table->integer('customer_group_id')->nullable();
            $table->string('customer_group_name')->nullable();
            $table->string('name');
            $table->string('company')->nullable();
            $table->string('vat_no');
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state');
            $table->string('postal_code')->nullable();
            $table->string('country')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('invoice_footer')->nullable();
            $table->text('logo')->nullable();
            $table->double('award_points')->default(0);
            $table->double('deposit_amount')->default(0); //customer or supplier current balance
            $table->double('opening_balance')->default(0);
            $table->integer('account_id')->default(0);
            $table->double('credit_amount')->default(0);
            $table->integer('stop_sale')->default(0); // 1=> stop sale if customer balance reaches credit amount
            $table->integer('representative_id_')->default(0);
            $table->integer('user_id')->default(0);
            $table->boolean('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
