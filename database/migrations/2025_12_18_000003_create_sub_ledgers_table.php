<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sub_ledgers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('control_account_id');
            $table->string('type');
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('subscriber_id')->nullable();
            $table->timestamps();

            $table->index(['company_id', 'control_account_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sub_ledgers');
    }
};
