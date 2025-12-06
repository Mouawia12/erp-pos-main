<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('warehouse_movements', function (Blueprint $table) {
            // using raw SQL keeps us from requiring doctrine/dbal just to alter the column
            DB::statement('ALTER TABLE warehouse_movements MODIFY invoice_no VARCHAR(255) NOT NULL');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('warehouse_movements', function (Blueprint $table) {
            DB::statement('ALTER TABLE warehouse_movements MODIFY invoice_no INT NOT NULL');
        });
    }
};
