<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            if (! Schema::hasColumn('branches', 'national_address_short')) {
                $table->string('national_address_short')->nullable()->after('branch_address');
            }
            if (! Schema::hasColumn('branches', 'national_address_building_no')) {
                $table->string('national_address_building_no')->nullable()->after('national_address_short');
            }
            if (! Schema::hasColumn('branches', 'national_address_street')) {
                $table->string('national_address_street')->nullable()->after('national_address_building_no');
            }
            if (! Schema::hasColumn('branches', 'national_address_district')) {
                $table->string('national_address_district')->nullable()->after('national_address_street');
            }
            if (! Schema::hasColumn('branches', 'national_address_city')) {
                $table->string('national_address_city')->nullable()->after('national_address_district');
            }
            if (! Schema::hasColumn('branches', 'national_address_region')) {
                $table->string('national_address_region')->nullable()->after('national_address_city');
            }
            if (! Schema::hasColumn('branches', 'national_address_postal_code')) {
                $table->string('national_address_postal_code')->nullable()->after('national_address_region');
            }
            if (! Schema::hasColumn('branches', 'national_address_additional_no')) {
                $table->string('national_address_additional_no')->nullable()->after('national_address_postal_code');
            }
            if (! Schema::hasColumn('branches', 'national_address_unit_no')) {
                $table->string('national_address_unit_no')->nullable()->after('national_address_additional_no');
            }
            if (! Schema::hasColumn('branches', 'national_address_proof_no')) {
                $table->string('national_address_proof_no')->nullable()->after('national_address_unit_no');
            }
            if (! Schema::hasColumn('branches', 'national_address_proof_issue_date')) {
                $table->date('national_address_proof_issue_date')->nullable()->after('national_address_proof_no');
            }
            if (! Schema::hasColumn('branches', 'national_address_proof_expiry_date')) {
                $table->date('national_address_proof_expiry_date')->nullable()->after('national_address_proof_issue_date');
            }
            if (! Schema::hasColumn('branches', 'national_address_country')) {
                $table->string('national_address_country')->nullable()->after('national_address_proof_expiry_date');
            }
        });
    }

    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $columns = [
                'national_address_country',
                'national_address_proof_expiry_date',
                'national_address_proof_issue_date',
                'national_address_proof_no',
                'national_address_unit_no',
                'national_address_additional_no',
                'national_address_postal_code',
                'national_address_region',
                'national_address_city',
                'national_address_district',
                'national_address_street',
                'national_address_building_no',
                'national_address_short',
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('branches', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
