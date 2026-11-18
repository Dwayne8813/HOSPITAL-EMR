<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InstitutionSeeder extends Seeder
{
    public function run(): void
    {
        $institution = [
            'name' => 'Hospital General de Bolivia',
            'slug' => 'hospital-general-bolivia',
            'logo' => null,
            'address' => 'Av. Principal No. 1234, Zona Centro',
            'city' => 'La Paz',
            'phone' => '+591 2 123456',
            'email' => 'info@hgbhospital.bo',
            'nit' => '1234567890',
            'mission' => 'To provide high-quality, comprehensive, and compassionate medical care to the Bolivian population, with standards of excellence and social commitment.',
            'vision' => 'To be a national leader in medical care, recognized for excellence, technological innovation, and commitment to the health of Bolivians.',
            'config_json' => json_encode([
                'currency' => 'BOB',
                'tax_rate' => 13,
                'timezone' => 'America/La_Paz',
                'date_format' => 'd/m/Y',
                'time_format' => 'H:i',
                'phone_code' => '+591',
                'emergency_number' => '118',
                'appointment_duration' => 30,
                'max_appointments_per_day' => 20,
                'low_stock_threshold' => 10,
                'enable_telemedicine' => true,
                'enable_sms_notifications' => true,
                'enable_email_notifications' => true,
                'backup_frequency' => 'daily',
                'max_upload_size' => 10240,
                'session_timeout' => 120,
                'max_login_attempts' => 5,
                'password_min_length' => 8,
                'invoice_prefix' => 'FAC',
                'medical_record_prefix' => 'HC',
                'patient_code_prefix' => 'PAC',
            ]),
            'status' => 'active',
            'primary_color' => '#1E40AF',
            'secondary_color' => '#3B82F6',
            'created_at' => now(),
            'updated_at' => now(),
        ];

        DB::table('institutions')->insert($institution);
    }
}
