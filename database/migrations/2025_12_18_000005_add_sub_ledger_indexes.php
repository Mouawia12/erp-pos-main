<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sub_ledgers', function (Blueprint $table) {
            $table->index(['control_account_id', 'branch_id']);
        });

        Schema::table('sub_ledger_entries', function (Blueprint $table) {
            $table->index(['sub_ledger_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::table('sub_ledgers', function (Blueprint $table) {
            $table->dropIndex(['control_account_id', 'branch_id']);
        });

        Schema::table('sub_ledger_entries', function (Blueprint $table) {
            $table->dropIndex(['sub_ledger_id', 'date']);
        });
    }
};
