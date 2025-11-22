<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('cr_number')->nullable();
            $table->string('tax_number')->nullable();
            $table->string('responsible_person')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('address')->nullable();
            $table->string('system_url')->nullable();
            $table->unsignedInteger('users_limit')->default(1);
            $table->date('subscription_start')->nullable();
            $table->date('subscription_end')->nullable();
            $table->enum('status', ['active', 'near_expiry', 'expired'])->default('active');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('subscriber_renewals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscriber_id')->constrained('subscribers')->cascadeOnDelete();
            $table->date('previous_end_date')->nullable();
            $table->date('new_end_date')->nullable();
            $table->integer('added_days')->default(0);
            $table->integer('added_months')->default(0);
            $table->integer('added_years')->default(0);
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('renewed_by')->nullable();
            $table->timestamps();
        });

        Schema::create('subscriber_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscriber_id')->constrained('subscribers')->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->string('file_path');
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriber_documents');
        Schema::dropIfExists('subscriber_renewals');
        Schema::dropIfExists('subscribers');
    }
};
