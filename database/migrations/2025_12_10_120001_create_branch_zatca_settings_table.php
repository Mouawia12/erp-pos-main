<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branch_zatca_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->string('zatca_stage')->default('developer-portal');
            $table->string('invoice_type')->nullable();
            $table->string('business_category')->nullable();
            $table->string('egs_serial_number')->nullable();
            $table->boolean('is_simulation')->default(false);
            $table->longText('cnf')->nullable();
            $table->longText('private_key')->nullable();
            $table->longText('public_key')->nullable();
            $table->longText('csr_request')->nullable();
            $table->longText('certificate')->nullable();
            $table->string('secret')->nullable();
            $table->string('csid')->nullable();
            $table->longText('production_certificate')->nullable();
            $table->string('production_secret')->nullable();
            $table->string('production_csid')->nullable();
            $table->timestamp('requested_at')->nullable();
            $table->longText('last_payload')->nullable();
            $table->timestamps();

            $table->unique('branch_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branch_zatca_settings');
    }
};
