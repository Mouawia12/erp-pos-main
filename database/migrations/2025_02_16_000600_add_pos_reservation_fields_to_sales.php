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
        Schema::table('sales', function (Blueprint $table) {
            if (! Schema::hasColumn('sales', 'reservation_time')) {
                $table->dateTime('reservation_time')->nullable()->after('session_type');
            }

            if (! Schema::hasColumn('sales', 'reservation_guests')) {
                $table->unsignedInteger('reservation_guests')->nullable()->after('reservation_time');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (Schema::hasColumn('sales', 'reservation_guests')) {
                $table->dropColumn('reservation_guests');
            }

            if (Schema::hasColumn('sales', 'reservation_time')) {
                $table->dropColumn('reservation_time');
            }
        });
    }
};
