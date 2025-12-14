<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'track_batch')) {
                $table->boolean('track_batch')->default(false)->after('track_quantity');
            }
        });

        Schema::table('purchase_details', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_details', 'production_date')) {
                if (Schema::hasColumn('purchase_details', 'batch_no')) {
                    $table->date('production_date')->nullable()->after('batch_no');
                } else {
                    $table->date('production_date')->nullable();
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('purchase_details', function (Blueprint $table) {
            if (Schema::hasColumn('purchase_details', 'production_date')) {
                $table->dropColumn('production_date');
            }
        });

        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'track_batch')) {
                $table->dropColumn('track_batch');
            }
        });
    }
};
