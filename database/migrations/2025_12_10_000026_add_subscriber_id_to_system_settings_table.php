<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('system_settings', 'subscriber_id')) {
                $afterColumn = Schema::hasColumn('system_settings', 'branch_id') ? 'branch_id' : 'id';
                $table->unsignedBigInteger('subscriber_id')->nullable()->after($afterColumn);
            }
        });
    }

    public function down(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            if (Schema::hasColumn('system_settings', 'subscriber_id')) {
                $table->dropColumn('subscriber_id');
            }
        });
    }
};
