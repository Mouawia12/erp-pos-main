<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('journals', function (Blueprint $table) {
            if (! Schema::hasColumn('journals', 'status')) {
                $table->string('status')->default('posted')->after('notes');
            }
            if (! Schema::hasColumn('journals', 'reversed_journal_id')) {
                $table->unsignedBigInteger('reversed_journal_id')->nullable()->after('status');
            }
            if (! Schema::hasColumn('journals', 'reverses_journal_id')) {
                $table->unsignedBigInteger('reverses_journal_id')->nullable()->after('reversed_journal_id');
            }
            if (! Schema::hasColumn('journals', 'reversed_at')) {
                $table->timestamp('reversed_at')->nullable()->after('reverses_journal_id');
            }
        });

        DB::table('journals')->whereNull('status')->update(['status' => 'posted']);

        $driver = Schema::getConnection()->getDriverName();
        if (in_array($driver, ['mysql', 'pgsql', 'sqlite'], true)) {
            try {
                DB::statement('ALTER TABLE journals ADD CONSTRAINT chk_journals_balanced CHECK (ABS(total_debit - total_credit) < 0.0001)');
            } catch (\Throwable $e) {
                // Ignore if the constraint already exists or the driver rejects it.
            }
        }
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if (in_array($driver, ['mysql', 'pgsql', 'sqlite'], true)) {
            try {
                DB::statement('ALTER TABLE journals DROP CONSTRAINT chk_journals_balanced');
            } catch (\Throwable $e) {
                // Ignore if the constraint does not exist or the driver rejects it.
            }
        }

        Schema::table('journals', function (Blueprint $table) {
            if (Schema::hasColumn('journals', 'reversed_at')) {
                $table->dropColumn('reversed_at');
            }
            if (Schema::hasColumn('journals', 'reverses_journal_id')) {
                $table->dropColumn('reverses_journal_id');
            }
            if (Schema::hasColumn('journals', 'reversed_journal_id')) {
                $table->dropColumn('reversed_journal_id');
            }
            if (Schema::hasColumn('journals', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
