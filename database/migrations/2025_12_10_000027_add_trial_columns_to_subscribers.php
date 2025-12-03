<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscribers', function (Blueprint $table) {
            if (!Schema::hasColumn('subscribers', 'is_trial')) {
                $table->boolean('is_trial')->default(false)->after('status');
            }

            if (!Schema::hasColumn('subscribers', 'trial_starts_at')) {
                $table->date('trial_starts_at')->nullable()->after('is_trial');
            }

            if (!Schema::hasColumn('subscribers', 'trial_ends_at')) {
                $table->date('trial_ends_at')->nullable()->after('trial_starts_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('subscribers', function (Blueprint $table) {
            if (Schema::hasColumn('subscribers', 'trial_ends_at')) {
                $table->dropColumn('trial_ends_at');
            }

            if (Schema::hasColumn('subscribers', 'trial_starts_at')) {
                $table->dropColumn('trial_starts_at');
            }

            if (Schema::hasColumn('subscribers', 'is_trial')) {
                $table->dropColumn('is_trial');
            }
        });
    }
};
