<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            if (!Schema::hasColumn('branches', 'cr_number')) {
                $table->string('cr_number')->nullable()->after('branch_name');
            }
            if (!Schema::hasColumn('branches', 'tax_number')) {
                $table->string('tax_number')->nullable()->after('cr_number');
            }
            if (!Schema::hasColumn('branches', 'manager_name')) {
                $table->string('manager_name')->nullable()->after('branch_address');
            }
            if (!Schema::hasColumn('branches', 'contact_email')) {
                $table->string('contact_email')->nullable()->after('manager_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            if (Schema::hasColumn('branches', 'contact_email')) {
                $table->dropColumn('contact_email');
            }
            if (Schema::hasColumn('branches', 'manager_name')) {
                $table->dropColumn('manager_name');
            }
            if (Schema::hasColumn('branches', 'tax_number')) {
                $table->dropColumn('tax_number');
            }
            if (Schema::hasColumn('branches', 'cr_number')) {
                $table->dropColumn('cr_number');
            }
        });
    }
};
