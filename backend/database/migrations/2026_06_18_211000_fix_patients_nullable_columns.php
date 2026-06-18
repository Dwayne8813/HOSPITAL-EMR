<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE `patients` MODIFY COLUMN `emergency_contact_name` VARCHAR(255) NULL");
        DB::statement("ALTER TABLE `patients` MODIFY COLUMN `emergency_contact_phone` VARCHAR(50) NULL");
        DB::statement("ALTER TABLE `patients` MODIFY COLUMN `blood_type` VARCHAR(10) NULL");
        DB::statement("ALTER TABLE `patients` MODIFY COLUMN `email` VARCHAR(255) NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `patients` MODIFY COLUMN `emergency_contact_name` VARCHAR(255) NOT NULL");
        DB::statement("ALTER TABLE `patients` MODIFY COLUMN `emergency_contact_phone` VARCHAR(50) NOT NULL");
    }
};
