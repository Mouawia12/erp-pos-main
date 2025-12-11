<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('zatca_documents')) {
            DB::statement('ALTER TABLE `zatca_documents` MODIFY `qr_value` LONGTEXT NULL');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('zatca_documents')) {
            DB::statement('ALTER TABLE `zatca_documents` MODIFY `qr_value` VARCHAR(255) NULL');
        }
    }
};
