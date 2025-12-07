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
        $afterColumn = null;
        if (Schema::hasColumn('sales', 'session_type')) {
            $afterColumn = 'session_type';
        } elseif (Schema::hasColumn('sales', 'session_location')) {
            $afterColumn = 'session_location';
        } elseif (Schema::hasColumn('sales', 'service_mode')) {
            $afterColumn = 'service_mode';
        } elseif (Schema::hasColumn('sales', 'invoice_type')) {
            $afterColumn = 'invoice_type';
        }

        Schema::table('sales', function (Blueprint $table) use ($afterColumn) {
            if (! Schema::hasColumn('sales', 'reservation_time')) {
                $column = $table->dateTime('reservation_time')->nullable();
                if ($afterColumn) {
                    $column->after($afterColumn);
                }
            }

            if (! Schema::hasColumn('sales', 'reservation_guests')) {
                $column = $table->unsignedInteger('reservation_guests')->nullable();
                if (Schema::hasColumn('sales', 'reservation_time')) {
                    $column->after('reservation_time');
                } elseif ($afterColumn) {
                    $column->after($afterColumn);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (Schema::hasColumn('sales', 'reservation_guests')) {
                $table->dropColumn('reservation_guests');
            }

            if (Schema::hasColumn('sales', 'reservation_time')) {
                $table->dropColumn('reservation_time');
            }
        });
    }
};
