<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_document_counters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('doc_type', 100);
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('next_number')->default(1);
            $table->string('prefix')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'doc_type', 'branch_id'], 'user_doc_branch_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_document_counters');
    }
};
