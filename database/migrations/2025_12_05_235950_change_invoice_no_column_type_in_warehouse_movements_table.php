<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }
        if (!Schema::hasTable('warehouse_movements') || !Schema::hasColumn('warehouse_movements', 'invoice_no')) {
            return;
        }

        // using raw SQL keeps us from requiring doctrine/dbal just to alter the column
        DB::statement('ALTER TABLE warehouse_movements MODIFY invoice_no VARCHAR(255) NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }
        if (!Schema::hasTable('warehouse_movements') || !Schema::hasColumn('warehouse_movements', 'invoice_no')) {
            return;
        }

        DB::statement('ALTER TABLE warehouse_movements MODIFY invoice_no INT NOT NULL');
    }
};
