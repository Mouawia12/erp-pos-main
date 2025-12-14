<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (!Schema::hasColumn('sales', 'vehicle_name')) {
                $table->string('vehicle_name')->nullable()->after('vehicle_plate');
            }
            if (!Schema::hasColumn('sales', 'vehicle_color')) {
                $table->string('vehicle_color')->nullable()->after('vehicle_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (Schema::hasColumn('sales', 'vehicle_color')) {
                $table->dropColumn('vehicle_color');
            }
            if (Schema::hasColumn('sales', 'vehicle_name')) {
                $table->dropColumn('vehicle_name');
            }
        });
    }
};
