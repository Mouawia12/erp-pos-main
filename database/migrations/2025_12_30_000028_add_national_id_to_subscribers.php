<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscribers', function (Blueprint $table) {
            if (! Schema::hasColumn('subscribers', 'national_id')) {
                $table->string('national_id')->nullable()->after('tax_number');
            }
        });
    }

    public function down(): void
    {
        Schema::table('subscribers', function (Blueprint $table) {
            if (Schema::hasColumn('subscribers', 'national_id')) {
                $table->dropColumn('national_id');
            }
        });
    }
};
