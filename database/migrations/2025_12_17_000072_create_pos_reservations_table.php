<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pos_reservations', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->string('customer_phone')->nullable();
            $table->dateTime('reservation_time')->nullable();
            $table->unsignedInteger('guests')->nullable();
            $table->string('status')->default('booked');
            $table->unsignedBigInteger('pos_section_id')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('subscriber_id')->nullable();
            $table->unsignedBigInteger('sale_id')->nullable();
            $table->string('session_location')->nullable();
            $table->string('session_type')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->index(['reservation_time', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_reservations');
    }
};
