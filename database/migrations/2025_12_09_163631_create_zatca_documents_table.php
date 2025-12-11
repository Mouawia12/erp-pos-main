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
        Schema::create('zatca_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscriber_id')->nullable()->constrained('subscribers')->nullOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('sale_id')->nullable()->constrained('sales')->cascadeOnDelete();
            $table->unsignedBigInteger('icv');
            $table->uuid('uuid');
            $table->string('invoice_number');
            $table->string('invoice_type')->default('simplified_tax_invoice');
            $table->string('previous_hash')->nullable();
            $table->string('hash')->nullable();
            $table->longText('xml')->nullable();
            $table->longText('response')->nullable();
            $table->text('error_message')->nullable();
            $table->boolean('sent_to_zatca')->default(false);
            $table->string('sent_to_zatca_status')->nullable();
            $table->timestamp('signing_time')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->string('qr_value')->nullable();
            $table->timestamps();

            $table->unique('sale_id');
            $table->unique(['subscriber_id', 'branch_id', 'icv'], 'zatca_documents_scope_icv_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zatca_documents');
    }
};
