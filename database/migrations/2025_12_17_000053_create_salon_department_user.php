<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salon_department_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('salon_department_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->unique(['salon_department_id', 'user_id']);
            $table->foreign('salon_department_id')->references('id')->on('salon_departments')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salon_department_user');
    }
};
