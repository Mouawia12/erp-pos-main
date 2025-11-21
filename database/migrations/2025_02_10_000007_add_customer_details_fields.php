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
        Schema::table('companies', function (Blueprint $table) {
            if (!Schema::hasColumn('companies', 'cr_number')) {
                $table->string('cr_number')->nullable()->after('vat_no');
            }
            if (!Schema::hasColumn('companies', 'tax_number')) {
                $table->string('tax_number')->nullable()->after('cr_number');
            }
            if (!Schema::hasColumn('companies', 'parent_company_id')) {
                $table->unsignedBigInteger('parent_company_id')->nullable()->after('tax_number');
            }
            if (!Schema::hasColumn('companies', 'price_level_id')) {
                $table->unsignedBigInteger('price_level_id')->nullable()->after('parent_company_id');
            }
            if (!Schema::hasColumn('companies', 'default_discount')) {
                $table->double('default_discount')->default(0)->after('price_level_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (Schema::hasColumn('companies', 'default_discount')) {
                $table->dropColumn('default_discount');
            }
            if (Schema::hasColumn('companies', 'price_level_id')) {
                $table->dropColumn('price_level_id');
            }
            if (Schema::hasColumn('companies', 'parent_company_id')) {
                $table->dropColumn('parent_company_id');
            }
            if (Schema::hasColumn('companies', 'tax_number')) {
                $table->dropColumn('tax_number');
            }
            if (Schema::hasColumn('companies', 'cr_number')) {
                $table->dropColumn('cr_number');
            }
        });
    }
};
