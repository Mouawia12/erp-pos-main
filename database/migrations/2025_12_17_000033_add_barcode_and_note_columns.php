<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'barcode')) {
                $table->string('barcode')->nullable()->after('code');
            }
        });

        Schema::table('sale_details', function (Blueprint $table) {
            if (! Schema::hasColumn('sale_details', 'note')) {
                $table->string('note')->nullable()->after('product_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'barcode')) {
                $table->dropColumn('barcode');
            }
        });

        Schema::table('sale_details', function (Blueprint $table) {
            if (Schema::hasColumn('sale_details', 'note')) {
                $table->dropColumn('note');
            }
        });
    }
};
