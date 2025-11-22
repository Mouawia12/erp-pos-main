<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscribers', function (Blueprint $table) {
            if (!Schema::hasColumn('subscribers', 'login_email')) {
                $table->string('login_email')->nullable()->after('contact_email');
            }
            if (!Schema::hasColumn('subscribers', 'login_password')) {
                $table->string('login_password')->nullable()->after('login_email');
            }
            if (!Schema::hasColumn('subscribers', 'login_password_plain')) {
                $table->string('login_password_plain')->nullable()->after('login_password');
            }
            if (!Schema::hasColumn('subscribers', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('login_password_plain');
            }
        });
    }

    public function down(): void
    {
        Schema::table('subscribers', function (Blueprint $table) {
            if (Schema::hasColumn('subscribers', 'login_password_plain')) {
                $table->dropColumn('login_password_plain');
            }
            if (Schema::hasColumn('subscribers', 'login_password')) {
                $table->dropColumn('login_password');
            }
            if (Schema::hasColumn('subscribers', 'login_email')) {
                $table->dropColumn('login_email');
            }
            if (Schema::hasColumn('subscribers', 'user_id')) {
                $table->dropColumn('user_id');
            }
        });
    }
};
