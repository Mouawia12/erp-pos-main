<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('account_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('account_settings', 'customer_control_account')) {
                $table->integer('customer_control_account')->default(0)->after('sales_tax_excise_account');
            }
            if (! Schema::hasColumn('account_settings', 'supplier_control_account')) {
                $table->integer('supplier_control_account')->default(0)->after('customer_control_account');
            }
        });
    }

    public function down(): void
    {
        Schema::table('account_settings', function (Blueprint $table) {
            if (Schema::hasColumn('account_settings', 'supplier_control_account')) {
                $table->dropColumn('supplier_control_account');
            }
            if (Schema::hasColumn('account_settings', 'customer_control_account')) {
                $table->dropColumn('customer_control_account');
            }
        });
    }
};
