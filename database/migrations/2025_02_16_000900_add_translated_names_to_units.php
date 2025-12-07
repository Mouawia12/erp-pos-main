<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('units', function (Blueprint $table) {
            if (! Schema::hasColumn('units', 'name_ar')) {
                $table->string('name_ar')->nullable()->after('name');
            }
            if (! Schema::hasColumn('units', 'name_en')) {
                $table->string('name_en')->nullable()->after('name_ar');
            }
        });

        if (Schema::hasColumn('units', 'name') && Schema::hasColumn('units', 'name_ar')) {
            DB::statement('UPDATE units SET name_ar = name WHERE name_ar IS NULL OR name_ar = ""');
        }

        if (Schema::hasColumn('units', 'name') && Schema::hasColumn('units', 'name_en')) {
            DB::statement('UPDATE units SET name_en = name WHERE name_en IS NULL OR name_en = ""');
        }
    }

    public function down(): void
    {
        Schema::table('units', function (Blueprint $table) {
            if (Schema::hasColumn('units', 'name_en')) {
                $table->dropColumn('name_en');
            }
            if (Schema::hasColumn('units', 'name_ar')) {
                $table->dropColumn('name_ar');
            }
        });
    }
};
