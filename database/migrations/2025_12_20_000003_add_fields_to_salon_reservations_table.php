<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('salon_reservations', function (Blueprint $table) {
            if (!Schema::hasColumn('salon_reservations', 'reservation_no')) {
                $table->string('reservation_no')->nullable()->after('id');
            }
            if (!Schema::hasColumn('salon_reservations', 'warehouse_id')) {
                $table->unsignedBigInteger('warehouse_id')->nullable()->after('assigned_user_id');
                $table->index('warehouse_id');
            }
            if (!Schema::hasColumn('salon_reservations', 'sale_id')) {
                $table->unsignedBigInteger('sale_id')->nullable()->after('status');
                $table->index('sale_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('salon_reservations', function (Blueprint $table) {
            if (Schema::hasColumn('salon_reservations', 'reservation_no')) {
                $table->dropColumn('reservation_no');
            }
            if (Schema::hasColumn('salon_reservations', 'warehouse_id')) {
                $table->dropIndex(['warehouse_id']);
                $table->dropColumn('warehouse_id');
            }
            if (Schema::hasColumn('salon_reservations', 'sale_id')) {
                $table->dropIndex(['sale_id']);
                $table->dropColumn('sale_id');
            }
        });
    }
};
