<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tables = [
        'account_settings',
        'advance_payments',
        'advance_payment_months',
        'brands',
        'cashiers',
        'catch_recipts',
        'categories',
        'company_infos',
        'currencies',
        'customer_groups',
        'deductions',
        'employers',
        'employer_categories',
        'expenses_categories',
        'invoice_term_templates',
        'pos_settings',
        'product_taxes',
        'product_variants',
        'promotion_items',
        'quotation_details',
        'representative_clients',
        'rewards',
        'salary_docs',
        'salary_doc_details',
        'stock_count_items',
        'tax_excise',
        'tax_rates',
        'units',
        'update_quntities',
        'update_quntity_details',
        'user_document_counters',
        'user_groups',
        'vendor_movements',
        'warehouse_movements',
        'warehouse_transfer_items',
        'visits',
    ];

    public function up(): void
    {
        foreach ($this->tables as $tableName) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (Schema::hasColumn($tableName, 'subscriber_id')) {
                    return;
                }

                $afterColumn = Schema::hasColumn($tableName, 'branch_id') ? 'branch_id' : 'id';
                $table->unsignedBigInteger('subscriber_id')->nullable()->after($afterColumn);
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $tableName) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (! Schema::hasColumn($tableName, 'subscriber_id')) {
                    return;
                }

                $table->dropColumn('subscriber_id');
            });
        }
    }
};
