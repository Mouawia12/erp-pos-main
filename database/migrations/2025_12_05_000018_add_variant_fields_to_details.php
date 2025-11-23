<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sale_details', function (Blueprint $table) {
            if (!Schema::hasColumn('sale_details', 'variant_id')) {
                $table->unsignedBigInteger('variant_id')->nullable()->after('product_id');
            }
            if (!Schema::hasColumn('sale_details', 'variant_color')) {
                $table->string('variant_color')->nullable()->after('variant_id');
            }
            if (!Schema::hasColumn('sale_details', 'variant_size')) {
                $table->string('variant_size')->nullable()->after('variant_color');
            }
            if (!Schema::hasColumn('sale_details', 'variant_barcode')) {
                $table->string('variant_barcode')->nullable()->after('variant_size');
            }
        });

        Schema::table('purchase_details', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_details', 'variant_id')) {
                $table->unsignedBigInteger('variant_id')->nullable()->after('product_id');
            }
            if (!Schema::hasColumn('purchase_details', 'variant_color')) {
                $table->string('variant_color')->nullable()->after('variant_id');
            }
            if (!Schema::hasColumn('purchase_details', 'variant_size')) {
                $table->string('variant_size')->nullable()->after('variant_color');
            }
            if (!Schema::hasColumn('purchase_details', 'variant_barcode')) {
                $table->string('variant_barcode')->nullable()->after('variant_size');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sale_details', function (Blueprint $table) {
            if (Schema::hasColumn('sale_details', 'variant_barcode')) {
                $table->dropColumn('variant_barcode');
            }
            if (Schema::hasColumn('sale_details', 'variant_size')) {
                $table->dropColumn('variant_size');
            }
            if (Schema::hasColumn('sale_details', 'variant_color')) {
                $table->dropColumn('variant_color');
            }
            if (Schema::hasColumn('sale_details', 'variant_id')) {
                $table->dropColumn('variant_id');
            }
        });

        Schema::table('purchase_details', function (Blueprint $table) {
            if (Schema::hasColumn('purchase_details', 'variant_barcode')) {
                $table->dropColumn('variant_barcode');
            }
            if (Schema::hasColumn('purchase_details', 'variant_size')) {
                $table->dropColumn('variant_size');
            }
            if (Schema::hasColumn('purchase_details', 'variant_color')) {
                $table->dropColumn('variant_color');
            }
            if (Schema::hasColumn('purchase_details', 'variant_id')) {
                $table->dropColumn('variant_id');
            }
        });
    }
};
