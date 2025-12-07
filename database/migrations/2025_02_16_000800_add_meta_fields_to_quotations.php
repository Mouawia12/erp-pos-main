<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('quotations')) {
            return;
        }

        Schema::table('quotations', function (Blueprint $table) {
            if (! Schema::hasColumn('quotations', 'invoice_type')) {
                $table->string('invoice_type')->default('simplified_tax_invoice')->after('quotation_no');
            }
            if (! Schema::hasColumn('quotations', 'payment_method')) {
                $table->string('payment_method')->default('cash')->after('status');
            }
            if (! Schema::hasColumn('quotations', 'cost_center')) {
                $table->string('cost_center')->nullable()->after('note');
            }
            if (! Schema::hasColumn('quotations', 'representative_id')) {
                $table->unsignedBigInteger('representative_id')->nullable()->after('warehouse_id');
            }
            if (! Schema::hasColumn('quotations', 'customer_name')) {
                $table->string('customer_name')->nullable()->after('customer_id');
            }
            if (! Schema::hasColumn('quotations', 'customer_phone')) {
                $table->string('customer_phone')->nullable()->after('customer_name');
            }
            if (! Schema::hasColumn('quotations', 'customer_address')) {
                $table->string('customer_address')->nullable()->after('customer_phone');
            }
            if (! Schema::hasColumn('quotations', 'customer_tax_number')) {
                $table->string('customer_tax_number')->nullable()->after('customer_address');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('quotations')) {
            return;
        }

        Schema::table('quotations', function (Blueprint $table) {
            foreach (['customer_tax_number','customer_address','customer_phone','customer_name','representative_id','cost_center','payment_method','invoice_type'] as $column) {
                if (Schema::hasColumn('quotations', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
