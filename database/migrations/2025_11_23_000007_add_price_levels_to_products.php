<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            for ($i = 1; $i <= 6; $i++) {
                $column = 'price_level_'.$i;
                if (!Schema::hasColumn('products', $column)) {
                    $table->double($column)->nullable()->after('price');
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            for ($i = 1; $i <= 6; $i++) {
                $column = 'price_level_'.$i;
                if (Schema::hasColumn('products', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
