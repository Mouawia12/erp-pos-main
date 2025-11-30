<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            if (! Schema::hasColumn('roles', 'subscriber_id')) {
                $table->unsignedBigInteger('subscriber_id')->nullable()->after('guard_name');
                $table->index('subscriber_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            if (Schema::hasColumn('roles', 'subscriber_id')) {
                $table->dropIndex(['subscriber_id']);
                $table->dropColumn('subscriber_id');
            }
        });
    }
};
