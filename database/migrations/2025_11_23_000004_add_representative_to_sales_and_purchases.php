<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (!Schema::hasColumn('sales', 'representative_id')) {
                $table->unsignedBigInteger('representative_id')->nullable()->after('customer_id');
            }
        });

        Schema::table('purchases', function (Blueprint $table) {
            if (!Schema::hasColumn('purchases', 'representative_id')) {
                $table->unsignedBigInteger('representative_id')->nullable()->after('customer_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            if (Schema::hasColumn('purchases', 'representative_id')) {
                $table->dropColumn('representative_id');
            }
        });

        Schema::table('sales', function (Blueprint $table) {
            if (Schema::hasColumn('sales', 'representative_id')) {
                $table->dropColumn('representative_id');
            }
        });
    }
};
