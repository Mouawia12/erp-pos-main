<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('profit_type', 20)->nullable()->after('profit_margin');
            $table->decimal('profit_amount', 15, 4)->nullable()->after('profit_type');
            $table->string('shipping_service_type', 20)->default('free')->after('profit_amount');
            $table->decimal('shipping_service_amount', 15, 4)->default(0)->after('shipping_service_type');
            $table->string('delivery_service_type', 20)->default('free')->after('shipping_service_amount');
            $table->decimal('delivery_service_amount', 15, 4)->default(0)->after('delivery_service_type');
            $table->string('installation_service_type', 20)->default('free')->after('delivery_service_amount');
            $table->decimal('installation_service_amount', 15, 4)->default(0)->after('installation_service_type');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'profit_type',
                'profit_amount',
                'shipping_service_type',
                'shipping_service_amount',
                'delivery_service_type',
                'delivery_service_amount',
                'installation_service_type',
                'installation_service_amount',
            ]);
        });
    }
};
