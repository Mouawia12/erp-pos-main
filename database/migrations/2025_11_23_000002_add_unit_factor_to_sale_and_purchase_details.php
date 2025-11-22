<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sale_details', function (Blueprint $table) {
            $table->double('unit_factor')->default(1)->after('unit_id');
        });

        Schema::table('purchase_details', function (Blueprint $table) {
            $table->double('unit_factor')->default(1)->after('unit_id');
        });
    }

    public function down(): void
    {
        Schema::table('purchase_details', function (Blueprint $table) {
            $table->dropColumn('unit_factor');
        });

        Schema::table('sale_details', function (Blueprint $table) {
            $table->dropColumn('unit_factor');
        });
    }
};
