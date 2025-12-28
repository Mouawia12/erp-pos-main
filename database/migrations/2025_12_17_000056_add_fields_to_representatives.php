<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('representatives', function (Blueprint $table) {
            if (!Schema::hasColumn('representatives', 'warehouse_id')) {
                $table->unsignedBigInteger('warehouse_id')->nullable()->after('document_expiry_date');
            }
            if (!Schema::hasColumn('representatives', 'price_level_id')) {
                $table->unsignedTinyInteger('price_level_id')->nullable()->after('warehouse_id');
            }
            if (!Schema::hasColumn('representatives', 'profit_margin')) {
                $table->decimal('profit_margin', 8, 2)->nullable()->after('price_level_id');
            }
            if (!Schema::hasColumn('representatives', 'discount_percent')) {
                $table->decimal('discount_percent', 8, 2)->nullable()->after('profit_margin');
            }
        });
    }

    public function down(): void
    {
        Schema::table('representatives', function (Blueprint $table) {
            if (Schema::hasColumn('representatives', 'discount_percent')) {
                $table->dropColumn('discount_percent');
            }
            if (Schema::hasColumn('representatives', 'profit_margin')) {
                $table->dropColumn('profit_margin');
            }
            if (Schema::hasColumn('representatives', 'price_level_id')) {
                $table->dropColumn('price_level_id');
            }
            if (Schema::hasColumn('representatives', 'warehouse_id')) {
                $table->dropColumn('warehouse_id');
            }
        });
    }
};
