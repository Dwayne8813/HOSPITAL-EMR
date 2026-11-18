<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Patients
            ['name' => 'View patients', 'slug' => 'patients.view', 'module' => 'patients', 'description' => 'View patient list and details', 'created_at' => now()],
            ['name' => 'Create patients', 'slug' => 'patients.create', 'module' => 'patients', 'description' => 'Register new patients', 'created_at' => now()],
            ['name' => 'Edit patients', 'slug' => 'patients.update', 'module' => 'patients', 'description' => 'Modify patient information', 'created_at' => now()],
            ['name' => 'Delete patients', 'slug' => 'patients.delete', 'module' => 'patients', 'description' => 'Delete patients from the system', 'created_at' => now()],

            // Medical Records
            ['name' => 'View medical records', 'slug' => 'medical_records.view', 'module' => 'medical_records', 'description' => 'View clinical records', 'created_at' => now()],
            ['name' => 'Create medical records', 'slug' => 'medical_records.create', 'module' => 'medical_records', 'description' => 'Create new clinical records', 'created_at' => now()],
            ['name' => 'Edit medical records', 'slug' => 'medical_records.update', 'module' => 'medical_records', 'description' => 'Modify clinical records', 'created_at' => now()],
            ['name' => 'Delete medical records', 'slug' => 'medical_records.delete', 'module' => 'medical_records', 'description' => 'Delete clinical records', 'created_at' => now()],

            // Appointments
            ['name' => 'View appointments', 'slug' => 'appointments.view', 'module' => 'appointments', 'description' => 'View appointment schedule', 'created_at' => now()],
            ['name' => 'Create appointments', 'slug' => 'appointments.create', 'module' => 'appointments', 'description' => 'Schedule new appointments', 'created_at' => now()],
            ['name' => 'Edit appointments', 'slug' => 'appointments.update', 'module' => 'appointments', 'description' => 'Modify existing appointments', 'created_at' => now()],
            ['name' => 'Delete appointments', 'slug' => 'appointments.delete', 'module' => 'appointments', 'description' => 'Cancel and delete appointments', 'created_at' => now()],
            ['name' => 'Confirm appointments', 'slug' => 'appointments.confirm', 'module' => 'appointments', 'description' => 'Confirm appointment attendance', 'created_at' => now()],
            ['name' => 'Cancel appointments', 'slug' => 'appointments.cancel', 'module' => 'appointments', 'description' => 'Cancel scheduled appointments', 'created_at' => now()],

            // Prescriptions
            ['name' => 'View prescriptions', 'slug' => 'prescriptions.view', 'module' => 'prescriptions', 'description' => 'View medical prescriptions', 'created_at' => now()],
            ['name' => 'Create prescriptions', 'slug' => 'prescriptions.create', 'module' => 'prescriptions', 'description' => 'Issue medical prescriptions', 'created_at' => now()],
            ['name' => 'Edit prescriptions', 'slug' => 'prescriptions.update', 'module' => 'prescriptions', 'description' => 'Modify medical prescriptions', 'created_at' => now()],
            ['name' => 'Dispense prescriptions', 'slug' => 'prescriptions.dispense', 'module' => 'prescriptions', 'description' => 'Dispense medications from prescriptions', 'created_at' => now()],

            // Laboratory
            ['name' => 'View lab tests', 'slug' => 'laboratory.view', 'module' => 'laboratory', 'description' => 'View lab orders and results', 'created_at' => now()],
            ['name' => 'Create lab tests', 'slug' => 'laboratory.create', 'module' => 'laboratory', 'description' => 'Create lab orders', 'created_at' => now()],
            ['name' => 'Edit lab tests', 'slug' => 'laboratory.update', 'module' => 'laboratory', 'description' => 'Update lab results', 'created_at' => now()],
            ['name' => 'Validate lab tests', 'slug' => 'laboratory.validate', 'module' => 'laboratory', 'description' => 'Validate lab results', 'created_at' => now()],

            // Pharmacy
            ['name' => 'View pharmacy', 'slug' => 'pharmacy.view', 'module' => 'pharmacy', 'description' => 'View pharmacy inventory', 'created_at' => now()],
            ['name' => 'Create medications', 'slug' => 'pharmacy.create', 'module' => 'pharmacy', 'description' => 'Register new medications', 'created_at' => now()],
            ['name' => 'Edit medications', 'slug' => 'pharmacy.update', 'module' => 'pharmacy', 'description' => 'Modify medication information', 'created_at' => now()],
            ['name' => 'Dispense medications', 'slug' => 'pharmacy.dispense', 'module' => 'pharmacy', 'description' => 'Dispense medications', 'created_at' => now()],

            // Billing
            ['name' => 'View billing', 'slug' => 'billing.view', 'module' => 'billing', 'description' => 'View invoices and payments', 'created_at' => now()],
            ['name' => 'Create invoices', 'slug' => 'billing.create', 'module' => 'billing', 'description' => 'Generate new invoices', 'created_at' => now()],
            ['name' => 'Edit invoices', 'slug' => 'billing.update', 'module' => 'billing', 'description' => 'Modify invoices', 'created_at' => now()],
            ['name' => 'Record payments', 'slug' => 'billing.payment', 'module' => 'billing', 'description' => 'Record invoice payments', 'created_at' => now()],

            // Hospitalization
            ['name' => 'View hospitalizations', 'slug' => 'hospitalization.view', 'module' => 'hospitalization', 'description' => 'View hospitalizations', 'created_at' => now()],
            ['name' => 'Create hospitalizations', 'slug' => 'hospitalization.create', 'module' => 'hospitalization', 'description' => 'Register new hospitalizations', 'created_at' => now()],
            ['name' => 'Edit hospitalizations', 'slug' => 'hospitalization.update', 'module' => 'hospitalization', 'description' => 'Modify hospitalizations', 'created_at' => now()],
            ['name' => 'Admit patients', 'slug' => 'hospitalization.admit', 'module' => 'hospitalization', 'description' => 'Admit patients to the hospital', 'created_at' => now()],
            ['name' => 'Discharge patients', 'slug' => 'hospitalization.discharge', 'module' => 'hospitalization', 'description' => 'Discharge hospitalized patients', 'created_at' => now()],

            // Emergency
            ['name' => 'View emergencies', 'slug' => 'emergency.view', 'module' => 'emergency', 'description' => 'View emergency consultations', 'created_at' => now()],
            ['name' => 'Create emergencies', 'slug' => 'emergency.create', 'module' => 'emergency', 'description' => 'Register new emergencies', 'created_at' => now()],
            ['name' => 'Edit emergencies', 'slug' => 'emergency.update', 'module' => 'emergency', 'description' => 'Update emergency information', 'created_at' => now()],
            ['name' => 'Triage', 'slug' => 'emergency.triage', 'module' => 'emergency', 'description' => 'Perform patient triage', 'created_at' => now()],

            // Reports
            ['name' => 'View reports', 'slug' => 'reports.view', 'module' => 'reports', 'description' => 'View system reports', 'created_at' => now()],
            ['name' => 'Export reports', 'slug' => 'reports.export', 'module' => 'reports', 'description' => 'Export reports in different formats', 'created_at' => now()],

            // Settings
            ['name' => 'View settings', 'slug' => 'settings.view', 'module' => 'settings', 'description' => 'View system settings', 'created_at' => now()],
            ['name' => 'Update settings', 'slug' => 'settings.update', 'module' => 'settings', 'description' => 'Modify system settings', 'created_at' => now()],

            // Users
            ['name' => 'View users', 'slug' => 'users.view', 'module' => 'users', 'description' => 'View user list', 'created_at' => now()],
            ['name' => 'Create users', 'slug' => 'users.create', 'module' => 'users', 'description' => 'Register new users', 'created_at' => now()],
            ['name' => 'Edit users', 'slug' => 'users.update', 'module' => 'users', 'description' => 'Modify user information', 'created_at' => now()],
            ['name' => 'Delete users', 'slug' => 'users.delete', 'module' => 'users', 'description' => 'Delete users from the system', 'created_at' => now()],

            // Roles
            ['name' => 'View roles', 'slug' => 'roles.view', 'module' => 'roles', 'description' => 'View role list', 'created_at' => now()],
            ['name' => 'Create roles', 'slug' => 'roles.create', 'module' => 'roles', 'description' => 'Create new roles', 'created_at' => now()],
            ['name' => 'Edit roles', 'slug' => 'roles.update', 'module' => 'roles', 'description' => 'Modify existing roles', 'created_at' => now()],
            ['name' => 'Delete roles', 'slug' => 'roles.delete', 'module' => 'roles', 'description' => 'Delete roles from the system', 'created_at' => now()],

            // Audit
            ['name' => 'View audit logs', 'slug' => 'audit.view', 'module' => 'audit', 'description' => 'View audit logs', 'created_at' => now()],

            // Backups
            ['name' => 'Create backups', 'slug' => 'backups.create', 'module' => 'backups', 'description' => 'Create backups', 'created_at' => now()],
            ['name' => 'Restore backups', 'slug' => 'backups.restore', 'module' => 'backups', 'description' => 'Restore backups', 'created_at' => now()],
        ];

        DB::table('permissions')->insert($permissions);
    }
}
