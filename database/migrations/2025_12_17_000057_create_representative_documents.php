<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('representative_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('representative_id');
            $table->string('title')->nullable();
            $table->string('document_type')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('file_path');
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->timestamps();

            $table->foreign('representative_id')
                ->references('id')
                ->on('representatives')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('representative_documents');
    }
};
