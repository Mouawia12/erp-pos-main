<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pos_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('pos_settings', 'receipt_width')) {
                $table->unsignedSmallInteger('receipt_width')->nullable()->after('print_format');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pos_settings', function (Blueprint $table) {
            if (Schema::hasColumn('pos_settings', 'receipt_width')) {
                $table->dropColumn('receipt_width');
            }
        });
    }
};
