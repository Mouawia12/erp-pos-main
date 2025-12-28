<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory_details', function (Blueprint $table) {
            if (!Schema::hasColumn('inventory_details', 'batch_no')) {
                $table->string('batch_no')->nullable()->after('unit');
            }
            if (!Schema::hasColumn('inventory_details', 'production_date')) {
                $table->date('production_date')->nullable()->after('batch_no');
            }
            if (!Schema::hasColumn('inventory_details', 'expiry_date')) {
                $table->date('expiry_date')->nullable()->after('production_date');
            }
            if (!Schema::hasColumn('inventory_details', 'is_counted')) {
                $table->unsignedTinyInteger('is_counted')->default(0)->after('new_quantity');
            }
        });
    }

    public function down(): void
    {
        Schema::table('inventory_details', function (Blueprint $table) {
            if (Schema::hasColumn('inventory_details', 'batch_no')) {
                $table->dropColumn('batch_no');
            }
            if (Schema::hasColumn('inventory_details', 'production_date')) {
                $table->dropColumn('production_date');
            }
            if (Schema::hasColumn('inventory_details', 'expiry_date')) {
                $table->dropColumn('expiry_date');
            }
            if (Schema::hasColumn('inventory_details', 'is_counted')) {
                $table->dropColumn('is_counted');
            }
        });
    }
};
