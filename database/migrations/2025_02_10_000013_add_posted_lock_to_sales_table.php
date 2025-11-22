<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (!Schema::hasColumn('sales', 'locked_at')) {
                $table->timestamp('locked_at')->nullable()->after('payment_status');
            }
        });

        // Backfill existing invoices to be locked based on their creation time
        DB::table('sales')
            ->whereNull('locked_at')
            ->update(['locked_at' => DB::raw('created_at')]);
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (Schema::hasColumn('sales', 'locked_at')) {
                $table->dropColumn('locked_at');
            }
        });
    }
};
