<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventorys', function (Blueprint $table) {
            if (!Schema::hasColumn('inventorys', 'is_matched')) {
                $table->unsignedTinyInteger('is_matched')->default(0)->after('state');
            }
        });
    }

    public function down(): void
    {
        Schema::table('inventorys', function (Blueprint $table) {
            if (Schema::hasColumn('inventorys', 'is_matched')) {
                $table->dropColumn('is_matched');
            }
        });
    }
};
