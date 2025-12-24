<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (!Schema::hasColumn('companies', 'national_address_short')) {
                $table->string('national_address_short')->nullable();
            }
            if (!Schema::hasColumn('companies', 'national_address_building_no')) {
                $table->string('national_address_building_no')->nullable();
            }
            if (!Schema::hasColumn('companies', 'national_address_street')) {
                $table->string('national_address_street')->nullable();
            }
            if (!Schema::hasColumn('companies', 'national_address_district')) {
                $table->string('national_address_district')->nullable();
            }
            if (!Schema::hasColumn('companies', 'national_address_city')) {
                $table->string('national_address_city')->nullable();
            }
            if (!Schema::hasColumn('companies', 'national_address_region')) {
                $table->string('national_address_region')->nullable();
            }
            if (!Schema::hasColumn('companies', 'national_address_postal_code')) {
                $table->string('national_address_postal_code')->nullable();
            }
            if (!Schema::hasColumn('companies', 'national_address_additional_no')) {
                $table->string('national_address_additional_no')->nullable();
            }
            if (!Schema::hasColumn('companies', 'national_address_unit_no')) {
                $table->string('national_address_unit_no')->nullable();
            }
            if (!Schema::hasColumn('companies', 'national_address_proof_no')) {
                $table->string('national_address_proof_no')->nullable();
            }
            if (!Schema::hasColumn('companies', 'national_address_proof_issue_date')) {
                $table->date('national_address_proof_issue_date')->nullable();
            }
            if (!Schema::hasColumn('companies', 'national_address_proof_expiry_date')) {
                $table->date('national_address_proof_expiry_date')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn([
                'national_address_short',
                'national_address_building_no',
                'national_address_street',
                'national_address_district',
                'national_address_city',
                'national_address_region',
                'national_address_postal_code',
                'national_address_additional_no',
                'national_address_unit_no',
                'national_address_proof_no',
                'national_address_proof_issue_date',
                'national_address_proof_expiry_date',
            ]);
        });
    }
};
