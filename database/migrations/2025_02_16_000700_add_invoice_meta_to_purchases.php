<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            if (! Schema::hasColumn('purchases', 'invoice_type')) {
                $table->string('invoice_type')->default('tax_invoice')->after('invoice_no');
            }

            if (! Schema::hasColumn('purchases', 'payment_method')) {
                $table->string('payment_method')->default('credit')->after('payment_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            if (Schema::hasColumn('purchases', 'payment_method')) {
                $table->dropColumn('payment_method');
            }

            if (Schema::hasColumn('purchases', 'invoice_type')) {
                $table->dropColumn('invoice_type');
            }
        });
    }
};
