<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (! Schema::hasColumn('sales', 'pos_section_id')) {
                $table->unsignedBigInteger('pos_section_id')->nullable()->after('session_type');
            }
            if (! Schema::hasColumn('sales', 'pos_shift_id')) {
                $table->unsignedBigInteger('pos_shift_id')->nullable()->after('pos_section_id');
            }
            if (! Schema::hasColumn('sales', 'pos_reservation_id')) {
                $table->unsignedBigInteger('pos_reservation_id')->nullable()->after('pos_shift_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (Schema::hasColumn('sales', 'pos_reservation_id')) {
                $table->dropColumn('pos_reservation_id');
            }
            if (Schema::hasColumn('sales', 'pos_shift_id')) {
                $table->dropColumn('pos_shift_id');
            }
            if (Schema::hasColumn('sales', 'pos_section_id')) {
                $table->dropColumn('pos_section_id');
            }
        });
    }
};
