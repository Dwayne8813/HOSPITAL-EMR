<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $institutionId = DB::table('institutions')->first()->id;

        $roles = DB::table('roles')->pluck('id', 'slug')->toArray();
        $specialties = DB::table('specialties')->pluck('id', 'slug')->toArray();

        $users = [
            [
                'institution_id' => $institutionId,
                'name' => 'Carlos Mendoza',
                'email' => 'admin@hospital.bo',
                'phone' => '+591 70123456',
                'password' => Hash::make('password'),
                'avatar' => null,
                'role_id' => $roles['super_admin'],
                'specialty_id' => null,
                'is_active' => true,
                'mfa_enabled' => false,
                'mfa_secret' => null,
                'email_verified_at' => now(),
                'last_login' => null,
                'failed_attempts' => 0,
                'locked_until' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'institution_id' => $institutionId,
                'name' => 'María González',
                'email' => 'director@hospital.bo',
                'phone' => '+591 70123457',
                'password' => Hash::make('password'),
                'avatar' => null,
                'role_id' => $roles['institution_admin'],
                'specialty_id' => null,
                'is_active' => true,
                'mfa_enabled' => false,
                'mfa_secret' => null,
                'email_verified_at' => now(),
                'last_login' => null,
                'failed_attempts' => 0,
                'locked_until' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'institution_id' => $institutionId,
                'name' => 'Dr. Juan Pérez',
                'email' => 'doctor@hospital.bo',
                'phone' => '+591 70123458',
                'password' => Hash::make('password'),
                'avatar' => null,
                'role_id' => $roles['doctor'],
                'specialty_id' => $specialties['medicina-interna'],
                'is_active' => true,
                'mfa_enabled' => false,
                'mfa_secret' => null,
                'email_verified_at' => now(),
                'last_login' => null,
                'failed_attempts' => 0,
                'locked_until' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'institution_id' => $institutionId,
                'name' => 'Ana Rodríguez',
                'email' => 'nurse@hospital.bo',
                'phone' => '+591 70123459',
                'password' => Hash::make('password'),
                'avatar' => null,
                'role_id' => $roles['nurse'],
                'specialty_id' => null,
                'is_active' => true,
                'mfa_enabled' => false,
                'mfa_secret' => null,
                'email_verified_at' => now(),
                'last_login' => null,
                'failed_attempts' => 0,
                'locked_until' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'institution_id' => $institutionId,
                'name' => 'Laura Flores',
                'email' => 'recepcion@hospital.bo',
                'phone' => '+591 70123460',
                'password' => Hash::make('password'),
                'avatar' => null,
                'role_id' => $roles['receptionist'],
                'specialty_id' => null,
                'is_active' => true,
                'mfa_enabled' => false,
                'mfa_secret' => null,
                'email_verified_at' => now(),
                'last_login' => null,
                'failed_attempts' => 0,
                'locked_until' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'institution_id' => $institutionId,
                'name' => 'Roberto Vargas',
                'email' => 'farmacia@hospital.bo',
                'phone' => '+591 70123461',
                'password' => Hash::make('password'),
                'avatar' => null,
                'role_id' => $roles['pharmacist'],
                'specialty_id' => null,
                'is_active' => true,
                'mfa_enabled' => false,
                'mfa_secret' => null,
                'email_verified_at' => now(),
                'last_login' => null,
                'failed_attempts' => 0,
                'locked_until' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('users')->insert($users);
    }
}
