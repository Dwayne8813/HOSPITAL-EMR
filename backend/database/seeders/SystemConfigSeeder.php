<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SystemConfigSeeder extends Seeder
{
    public function run(): void
    {
        $configs = [
            ['key' => 'app_name', 'value' => 'Hospital General de Bolivia', 'config_group' => 'general', 'type' => 'string', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'app_version', 'value' => '1.0.0', 'config_group' => 'general', 'type' => 'string', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'currency', 'value' => 'BOB', 'config_group' => 'general', 'type' => 'string', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'currency_symbol', 'value' => 'Bs', 'config_group' => 'general', 'type' => 'string', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'timezone', 'value' => 'America/La_Paz', 'config_group' => 'general', 'type' => 'string', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'date_format', 'value' => 'd/m/Y', 'config_group' => 'general', 'type' => 'string', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'time_format', 'value' => 'H:i', 'config_group' => 'general', 'type' => 'string', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'tax_rate', 'value' => '13', 'config_group' => 'billing', 'type' => 'number', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'tax_name', 'value' => 'IVA', 'config_group' => 'billing', 'type' => 'string', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'max_upload_size', 'value' => '10240', 'config_group' => 'system', 'type' => 'number', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'backup_frequency', 'value' => 'daily', 'config_group' => 'system', 'type' => 'string', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'session_timeout', 'value' => '120', 'config_group' => 'security', 'type' => 'number', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'max_login_attempts', 'value' => '5', 'config_group' => 'security', 'type' => 'number', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'password_min_length', 'value' => '8', 'config_group' => 'security', 'type' => 'number', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'appointment_duration', 'value' => '30', 'config_group' => 'appointments', 'type' => 'number', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'max_appointments_per_day', 'value' => '20', 'config_group' => 'appointments', 'type' => 'number', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'low_stock_threshold', 'value' => '10', 'config_group' => 'pharmacy', 'type' => 'number', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'emergency_contact', 'value' => '118', 'config_group' => 'emergency', 'type' => 'string', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'hospital_code', 'value' => 'HGB001', 'config_group' => 'general', 'type' => 'string', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'fiscal_year_start', 'value' => '01', 'config_group' => 'billing', 'type' => 'number', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'fiscal_year_month', 'value' => '01', 'config_group' => 'billing', 'type' => 'number', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'invoice_prefix', 'value' => 'FAC', 'config_group' => 'billing', 'type' => 'string', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'medical_record_prefix', 'value' => 'HC', 'config_group' => 'general', 'type' => 'string', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'patient_code_prefix', 'value' => 'PAC', 'config_group' => 'general', 'type' => 'string', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'enable_telemedicine', 'value' => 'true', 'config_group' => 'features', 'type' => 'boolean', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'enable_sms_notifications', 'value' => 'true', 'config_group' => 'features', 'type' => 'boolean', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'enable_email_notifications', 'value' => 'true', 'config_group' => 'features', 'type' => 'boolean', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'auto_backup_enabled', 'value' => 'true', 'config_group' => 'system', 'type' => 'boolean', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'maintenance_mode', 'value' => 'false', 'config_group' => 'system', 'type' => 'boolean', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'debug_mode', 'value' => 'false', 'config_group' => 'system', 'type' => 'boolean', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('system_config')->insert($configs);
    }
}
