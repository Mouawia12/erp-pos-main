<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('system_settings', 'default_product_type')) {
                $table->string('default_product_type')->default('1')->after('default_invoice_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            if (Schema::hasColumn('system_settings', 'default_product_type')) {
                $table->dropColumn('default_product_type');
            }
        });
    }
};
