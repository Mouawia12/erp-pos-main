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
        Schema::table('purchases', function (Blueprint $table) {
            if (!Schema::hasColumn('purchases', 'supplier_invoice_no')) {
                $table->string('supplier_invoice_no')->nullable()->after('invoice_no');
            }
            if (!Schema::hasColumn('purchases', 'supplier_invoice_copy')) {
                $table->string('supplier_invoice_copy')->nullable()->after('supplier_invoice_no');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            if (Schema::hasColumn('purchases', 'supplier_invoice_no')) {
                $table->dropColumn('supplier_invoice_no');
            }
            if (Schema::hasColumn('purchases', 'supplier_invoice_copy')) {
                $table->dropColumn('supplier_invoice_copy');
            }
        });
    }
};
