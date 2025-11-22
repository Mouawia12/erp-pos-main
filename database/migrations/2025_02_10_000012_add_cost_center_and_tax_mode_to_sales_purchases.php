<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (!Schema::hasColumn('sales', 'cost_center')) {
                $table->string('cost_center')->nullable()->after('invoice_type');
            }
            if (!Schema::hasColumn('sales', 'tax_mode')) {
                $table->string('tax_mode')->default('inclusive')->after('cost_center');
            }
        });

        Schema::table('purchases', function (Blueprint $table) {
            if (!Schema::hasColumn('purchases', 'cost_center')) {
                $table->string('cost_center')->nullable()->after('invoice_no');
            }
            if (!Schema::hasColumn('purchases', 'tax_mode')) {
                $table->string('tax_mode')->default('inclusive')->after('cost_center');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (Schema::hasColumn('sales', 'cost_center')) {
                $table->dropColumn('cost_center');
            }
            if (Schema::hasColumn('sales', 'tax_mode')) {
                $table->dropColumn('tax_mode');
            }
        });

        Schema::table('purchases', function (Blueprint $table) {
            if (Schema::hasColumn('purchases', 'cost_center')) {
                $table->dropColumn('cost_center');
            }
            if (Schema::hasColumn('purchases', 'tax_mode')) {
                $table->dropColumn('tax_mode');
            }
        });
    }
};
