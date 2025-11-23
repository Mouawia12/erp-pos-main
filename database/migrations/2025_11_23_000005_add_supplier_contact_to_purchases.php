<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            if (!Schema::hasColumn('purchases', 'supplier_name')) {
                $table->string('supplier_name')->nullable()->after('customer_id');
            }
            if (!Schema::hasColumn('purchases', 'supplier_phone')) {
                $table->string('supplier_phone')->nullable()->after('supplier_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            if (Schema::hasColumn('purchases', 'supplier_phone')) {
                $table->dropColumn('supplier_phone');
            }
            if (Schema::hasColumn('purchases', 'supplier_name')) {
                $table->dropColumn('supplier_name');
            }
        });
    }
};
