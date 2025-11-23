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
        Schema::table('representatives', function (Blueprint $table) {
            if (!Schema::hasColumn('representatives', 'document_name')) {
                $table->string('document_name')->nullable()->after('notes');
            }
            if (!Schema::hasColumn('representatives', 'document_number')) {
                $table->string('document_number')->nullable()->after('document_name');
            }
            if (!Schema::hasColumn('representatives', 'document_expiry_date')) {
                $table->date('document_expiry_date')->nullable()->after('document_number');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('representatives', function (Blueprint $table) {
            if (Schema::hasColumn('representatives', 'document_expiry_date')) {
                $table->dropColumn('document_expiry_date');
            }
            if (Schema::hasColumn('representatives', 'document_number')) {
                $table->dropColumn('document_number');
            }
            if (Schema::hasColumn('representatives', 'document_name')) {
                $table->dropColumn('document_name');
            }
        });
    }
};
