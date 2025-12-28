<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (!Schema::hasColumn('sales', 'cost_center_id')) {
                $table->unsignedBigInteger('cost_center_id')->nullable()->after('cost_center');
            }
        });

        Schema::table('purchases', function (Blueprint $table) {
            if (!Schema::hasColumn('purchases', 'cost_center_id')) {
                $table->unsignedBigInteger('cost_center_id')->nullable()->after('cost_center');
            }
        });

        Schema::table('quotations', function (Blueprint $table) {
            if (!Schema::hasColumn('quotations', 'cost_center_id')) {
                if (Schema::hasColumn('quotations', 'cost_center')) {
                    $table->unsignedBigInteger('cost_center_id')->nullable()->after('cost_center');
                } else {
                    $table->unsignedBigInteger('cost_center_id')->nullable();
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (Schema::hasColumn('sales', 'cost_center_id')) {
                $table->dropColumn('cost_center_id');
            }
        });

        Schema::table('purchases', function (Blueprint $table) {
            if (Schema::hasColumn('purchases', 'cost_center_id')) {
                $table->dropColumn('cost_center_id');
            }
        });

        Schema::table('quotations', function (Blueprint $table) {
            if (Schema::hasColumn('quotations', 'cost_center_id')) {
                $table->dropColumn('cost_center_id');
            }
        });
    }
};
