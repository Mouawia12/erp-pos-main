<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $afterColumn = Schema::hasColumn('system_settings', 'single_device_login')
            ? 'single_device_login'
            : 'payment_method';

        Schema::table('system_settings', function (Blueprint $table) use ($afterColumn) {
            if (!Schema::hasColumn('system_settings', 'per_user_sequence')) {
                $table->boolean('per_user_sequence')->default(false)->after($afterColumn);
            }
        });
    }

    public function down(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            if (Schema::hasColumn('system_settings', 'per_user_sequence')) {
                $table->dropColumn('per_user_sequence');
            }
        });
    }
};
