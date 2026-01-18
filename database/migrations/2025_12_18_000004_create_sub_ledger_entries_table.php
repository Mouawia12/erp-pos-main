<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sub_ledger_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sub_ledger_id');
            $table->unsignedBigInteger('journal_id');
            $table->date('date');
            $table->double('debit')->default(0);
            $table->double('credit')->default(0);
            $table->string('notes')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('subscriber_id')->nullable();
            $table->timestamps();

            $table->index(['sub_ledger_id', 'journal_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sub_ledger_entries');
    }
};
