<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('stock_count_items', function (Blueprint $table) {
            if (!Schema::hasColumn('stock_count_items', 'batch_no')) {
                $table->string('batch_no')->nullable()->after('variant_barcode');
            }
            if (!Schema::hasColumn('stock_count_items', 'production_date')) {
                $table->date('production_date')->nullable()->after('batch_no');
            }
            if (!Schema::hasColumn('stock_count_items', 'expiry_date')) {
                $table->date('expiry_date')->nullable()->after('production_date');
            }
        });
    }

    public function down(): void
    {
        Schema::table('stock_count_items', function (Blueprint $table) {
            if (Schema::hasColumn('stock_count_items', 'expiry_date')) {
                $table->dropColumn('expiry_date');
            }
            if (Schema::hasColumn('stock_count_items', 'production_date')) {
                $table->dropColumn('production_date');
            }
            if (Schema::hasColumn('stock_count_items', 'batch_no')) {
                $table->dropColumn('batch_no');
            }
        });
    }
};
