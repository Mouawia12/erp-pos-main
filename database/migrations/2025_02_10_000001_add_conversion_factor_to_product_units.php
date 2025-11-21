<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('product_units', function (Blueprint $table) {
            if (!Schema::hasColumn('product_units', 'conversion_factor')) {
                $table->double('conversion_factor')->default(1)->after('price');
            }
            if (!Schema::hasColumn('product_units', 'barcode')) {
                $table->string('barcode')->nullable()->after('conversion_factor');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_units', function (Blueprint $table) {
            if (Schema::hasColumn('product_units', 'barcode')) {
                $table->dropColumn('barcode');
            }
            if (Schema::hasColumn('product_units', 'conversion_factor')) {
                $table->dropColumn('conversion_factor');
            }
        });
    }
};
