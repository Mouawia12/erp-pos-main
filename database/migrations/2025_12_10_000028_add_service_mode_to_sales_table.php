<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (!Schema::hasColumn('sales', 'service_mode')) {
                $table->string('service_mode')->default('dine_in')->after('invoice_type');
            }

            if (!Schema::hasColumn('sales', 'session_location')) {
                $table->string('session_location')->nullable()->after('service_mode');
            }

            if (!Schema::hasColumn('sales', 'session_type')) {
                $table->string('session_type')->nullable()->after('session_location');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            foreach (['session_type', 'session_location', 'service_mode'] as $column) {
                if (Schema::hasColumn('sales', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
