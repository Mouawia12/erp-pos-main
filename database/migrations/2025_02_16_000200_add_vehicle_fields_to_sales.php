<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $afterColumn = Schema::hasColumn('sales', 'service_mode')
            ? 'service_mode'
            : (Schema::hasColumn('sales', 'invoice_type') ? 'invoice_type' : null);

        Schema::table('sales', function (Blueprint $table) use ($afterColumn) {
            if (! Schema::hasColumn('sales', 'vehicle_plate')) {
                if ($afterColumn) {
                    $table->string('vehicle_plate')->nullable()->after($afterColumn);
                } else {
                    $table->string('vehicle_plate')->nullable();
                }
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
