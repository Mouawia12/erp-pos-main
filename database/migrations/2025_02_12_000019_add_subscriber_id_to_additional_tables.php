<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'items',
            'karats',
            'exit_old_details',
            'exit_olds',
            'exit_old_tax_details',
            'exit_olds_tax',
            'exit_work_tax_details',
            'sales_tax',
            'enter_old_details',
            'enter_olds',
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
            'items',
            'karats',
            'exit_old_details',
            'exit_olds',
            'exit_old_tax_details',
            'exit_olds_tax',
            'exit_work_tax_details',
            'sales_tax',
            'enter_old_details',
            'enter_olds',
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
