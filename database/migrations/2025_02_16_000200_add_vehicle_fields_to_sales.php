<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (! Schema::hasColumn('sales', 'vehicle_plate')) {
                $table->string('vehicle_plate')->nullable()->after('service_mode');
            }
            if (! Schema::hasColumn('sales', 'vehicle_odometer')) {
                $table->string('vehicle_odometer')->nullable()->after('vehicle_plate');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (Schema::hasColumn('sales', 'vehicle_odometer')) {
                $table->dropColumn('vehicle_odometer');
            }
            if (Schema::hasColumn('sales', 'vehicle_plate')) {
                $table->dropColumn('vehicle_plate');
            }
        });
    }
};
