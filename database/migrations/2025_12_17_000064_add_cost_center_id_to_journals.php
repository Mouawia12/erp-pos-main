<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('journals', function (Blueprint $table) {
            if (!Schema::hasColumn('journals', 'cost_center_id')) {
                $table->unsignedBigInteger('cost_center_id')->nullable()->after('branch_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('journals', function (Blueprint $table) {
            if (Schema::hasColumn('journals', 'cost_center_id')) {
                $table->dropColumn('cost_center_id');
            }
        });
    }
};
