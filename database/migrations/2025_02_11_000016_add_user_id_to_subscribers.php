<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscribers', function (Blueprint $table) {
            if (!Schema::hasColumn('subscribers', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('login_password_plain');
            }
        });
    }

    public function down(): void
    {
        Schema::table('subscribers', function (Blueprint $table) {
            if (Schema::hasColumn('subscribers', 'user_id')) {
                $table->dropColumn('user_id');
            }
        });
    }
};
