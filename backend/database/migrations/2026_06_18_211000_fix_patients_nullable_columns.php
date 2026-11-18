<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite creates these columns as nullable in the base migration, so the
        // MySQL-only MODIFY COLUMN statements are neither needed nor supported.
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("ALTER TABLE `patients` MODIFY COLUMN `emergency_contact_name` VARCHAR(255) NULL");
        DB::statement("ALTER TABLE `patients` MODIFY COLUMN `emergency_contact_phone` VARCHAR(50) NULL");
        DB::statement("ALTER TABLE `patients` MODIFY COLUMN `blood_type` VARCHAR(10) NULL");
        DB::statement("ALTER TABLE `patients` MODIFY COLUMN `email` VARCHAR(255) NULL");
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("ALTER TABLE `patients` MODIFY COLUMN `emergency_contact_name` VARCHAR(255) NOT NULL");
        DB::statement("ALTER TABLE `patients` MODIFY COLUMN `emergency_contact_phone` VARCHAR(50) NOT NULL");
    }
};
