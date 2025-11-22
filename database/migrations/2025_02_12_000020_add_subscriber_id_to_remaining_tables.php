<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'accounts_trees',
            'account_movements',
            'account_settings',
            'accounting_closing',
            'journal_details',
            'journals',
            'inventorys',
            'inventory_details',
            'warehouse_products',
            'product_units',
            'vendor_movements',
            'representatives',
        ];

        foreach ($tables as $tableName) {
            if (!Schema::hasTable($tableName)) {
                continue;
            }
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'subscriber_id')) {
                    $after = Schema::hasColumn($tableName, 'branch_id') ? 'branch_id' : 'id';
                    $table->unsignedBigInteger('subscriber_id')->nullable()->after($after);
                }
            });
        }
    }

    public function down(): void
    {
        $tables = [
            'accounts_trees',
            'account_movements',
            'account_settings',
            'accounting_closing',
            'journal_details',
            'journals',
            'inventorys',
            'inventory_details',
            'warehouse_products',
            'product_units',
            'vendor_movements',
            'representatives',
        ];

        foreach ($tables as $tableName) {
            if (!Schema::hasTable($tableName)) {
                continue;
            }
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (Schema::hasColumn($tableName, 'subscriber_id')) {
                    $table->dropColumn('subscriber_id');
                }
            });
        }
    }
};
