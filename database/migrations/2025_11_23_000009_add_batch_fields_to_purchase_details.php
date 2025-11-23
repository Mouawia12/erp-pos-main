<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_details', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_details', 'batch_no')) {
                $table->string('batch_no')->nullable()->after('unit_id');
            }
            if (!Schema::hasColumn('purchase_details', 'expiry_date')) {
                $table->date('expiry_date')->nullable()->after('batch_no');
            }
        });
    }

    public function down(): void
    {
        Schema::table('purchase_details', function (Blueprint $table) {
            if (Schema::hasColumn('purchase_details', 'expiry_date')) {
                $table->dropColumn('expiry_date');
            }
            if (Schema::hasColumn('purchase_details', 'batch_no')) {
                $table->dropColumn('batch_no');
            }
        });
    }
};
