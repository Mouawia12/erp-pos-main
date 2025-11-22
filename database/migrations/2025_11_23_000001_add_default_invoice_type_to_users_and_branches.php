<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('default_invoice_type', 50)->nullable()->after('status');
        });

        Schema::table('branches', function (Blueprint $table) {
            $table->string('default_invoice_type', 50)->nullable()->after('manager_name');
        });
    }

    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn('default_invoice_type');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('default_invoice_type');
        });
    }
};
