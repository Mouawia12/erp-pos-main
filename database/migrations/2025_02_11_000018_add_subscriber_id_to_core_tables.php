<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'companies', 'branches', 'warehouses', 'products', 'sales', 'sale_details',
            'purchases', 'purchase_details', 'payments', 'expenses'
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'subscriber_id')) {
                    $afterColumn = Schema::hasColumn($tableName, 'branch_id') ? 'branch_id' : 'id';
                    $table->unsignedBigInteger('subscriber_id')->nullable()->after($afterColumn);
                }
            });
        }
    }

    public function down(): void
    {
        $tables = [
            'companies', 'branches', 'warehouses', 'products', 'sales', 'sale_details',
            'purchases', 'purchase_details', 'payments', 'expenses'
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (Schema::hasColumn($tableName, 'subscriber_id')) {
                    $table->dropColumn('subscriber_id');
                }
            });
        }
    }
};
