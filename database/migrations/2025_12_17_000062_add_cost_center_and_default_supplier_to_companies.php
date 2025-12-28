<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (!Schema::hasColumn('companies', 'cost_center_id')) {
                $table->unsignedBigInteger('cost_center_id')->nullable()->after('representative_id_');
            }
            if (!Schema::hasColumn('companies', 'is_default_supplier')) {
                $table->boolean('is_default_supplier')->default(false)->after('is_walk_in');
            }
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (Schema::hasColumn('companies', 'cost_center_id')) {
                $table->dropColumn('cost_center_id');
            }
            if (Schema::hasColumn('companies', 'is_default_supplier')) {
                $table->dropColumn('is_default_supplier');
            }
        });
    }
};
