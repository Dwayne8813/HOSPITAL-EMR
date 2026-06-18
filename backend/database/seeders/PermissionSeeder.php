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
            ['name' => 'Ver pacientes', 'slug' => 'patients.view', 'module' => 'patients', 'description' => 'Visualizar listado y detalles de pacientes', 'created_at' => now()],
            ['name' => 'Crear pacientes', 'slug' => 'patients.create', 'module' => 'patients', 'description' => 'Registrar nuevos pacientes', 'created_at' => now()],
            ['name' => 'Editar pacientes', 'slug' => 'patients.update', 'module' => 'patients', 'description' => 'Modificar información de pacientes', 'created_at' => now()],
            ['name' => 'Eliminar pacientes', 'slug' => 'patients.delete', 'module' => 'patients', 'description' => 'Eliminar pacientes del sistema', 'created_at' => now()],

            // Medical Records
            ['name' => 'Ver historiales médicos', 'slug' => 'medical_records.view', 'module' => 'medical_records', 'description' => 'Visualizar historiales clínicos', 'created_at' => now()],
            ['name' => 'Crear historiales médicos', 'slug' => 'medical_records.create', 'module' => 'medical_records', 'description' => 'Crear nuevos historiales clínicos', 'created_at' => now()],
            ['name' => 'Editar historiales médicos', 'slug' => 'medical_records.update', 'module' => 'medical_records', 'description' => 'Modificar historiales clínicos', 'created_at' => now()],
            ['name' => 'Eliminar historiales médicos', 'slug' => 'medical_records.delete', 'module' => 'medical_records', 'description' => 'Eliminar historiales clínicos', 'created_at' => now()],

            // Appointments
            ['name' => 'Ver citas', 'slug' => 'appointments.view', 'module' => 'appointments', 'description' => 'Visualizar agenda de citas', 'created_at' => now()],
            ['name' => 'Crear citas', 'slug' => 'appointments.create', 'module' => 'appointments', 'description' => 'Programar nuevas citas', 'created_at' => now()],
            ['name' => 'Editar citas', 'slug' => 'appointments.update', 'module' => 'appointments', 'description' => 'Modificar citas existentes', 'created_at' => now()],
            ['name' => 'Eliminar citas', 'slug' => 'appointments.delete', 'module' => 'appointments', 'description' => 'Cancelar eliminar citas', 'created_at' => now()],
            ['name' => 'Confirmar citas', 'slug' => 'appointments.confirm', 'module' => 'appointments', 'description' => 'Confirmar asistencia a citas', 'created_at' => now()],
            ['name' => 'Cancelar citas', 'slug' => 'appointments.cancel', 'module' => 'appointments', 'description' => 'Cancelar citas programadas', 'created_at' => now()],

            // Prescriptions
            ['name' => 'Ver recetas', 'slug' => 'prescriptions.view', 'module' => 'prescriptions', 'description' => 'Visualizar recetas médicas', 'created_at' => now()],
            ['name' => 'Crear recetas', 'slug' => 'prescriptions.create', 'module' => 'prescriptions', 'description' => 'Emitir recetas médicas', 'created_at' => now()],
            ['name' => 'Editar recetas', 'slug' => 'prescriptions.update', 'module' => 'prescriptions', 'description' => 'Modificar recetas médicas', 'created_at' => now()],
            ['name' => 'Dispensar recetas', 'slug' => 'prescriptions.dispense', 'module' => 'prescriptions', 'description' => 'Dispensar medicamentos de recetas', 'created_at' => now()],

            // Laboratory
            ['name' => 'Ver análisis', 'slug' => 'laboratory.view', 'module' => 'laboratory', 'description' => 'Visualizar órdenes y resultados de laboratorio', 'created_at' => now()],
            ['name' => 'Crear análisis', 'slug' => 'laboratory.create', 'module' => 'laboratory', 'description' => 'Crear órdenes de laboratorio', 'created_at' => now()],
            ['name' => 'Editar análisis', 'slug' => 'laboratory.update', 'module' => 'laboratory', 'description' => 'Actualizar resultados de laboratorio', 'created_at' => now()],
            ['name' => 'Validar análisis', 'slug' => 'laboratory.validate', 'module' => 'laboratory', 'description' => 'Validar resultados de laboratorio', 'created_at' => now()],

            // Pharmacy
            ['name' => 'Ver farmacia', 'slug' => 'pharmacy.view', 'module' => 'pharmacy', 'description' => 'Visualizar inventario de farmacia', 'created_at' => now()],
            ['name' => 'Crear medicamentos', 'slug' => 'pharmacy.create', 'module' => 'pharmacy', 'description' => 'Registrar nuevos medicamentos', 'created_at' => now()],
            ['name' => 'Editar medicamentos', 'slug' => 'pharmacy.update', 'module' => 'pharmacy', 'description' => 'Modificar información de medicamentos', 'created_at' => now()],
            ['name' => 'Dispensar medicamentos', 'slug' => 'pharmacy.dispense', 'module' => 'pharmacy', 'description' => 'Dispensar medicamentos', 'created_at' => now()],

            // Billing
            ['name' => 'Ver facturación', 'slug' => 'billing.view', 'module' => 'billing', 'description' => 'Visualizar facturas y pagos', 'created_at' => now()],
            ['name' => 'Crear facturas', 'slug' => 'billing.create', 'module' => 'billing', 'description' => 'Generar nuevas facturas', 'created_at' => now()],
            ['name' => 'Editar facturas', 'slug' => 'billing.update', 'module' => 'billing', 'description' => 'Modificar facturas', 'created_at' => now()],
            ['name' => 'Registrar pagos', 'slug' => 'billing.payment', 'module' => 'billing', 'description' => 'Registrar pagos de facturas', 'created_at' => now()],

            // Hospitalization
            ['name' => 'Ver hospitalizaciones', 'slug' => 'hospitalization.view', 'module' => 'hospitalization', 'description' => 'Visualizar hospitalizaciones', 'created_at' => now()],
            ['name' => 'Crear hospitalizaciones', 'slug' => 'hospitalization.create', 'module' => 'hospitalization', 'description' => 'Registrar nuevas hospitalizaciones', 'created_at' => now()],
            ['name' => 'Editar hospitalizaciones', 'slug' => 'hospitalization.update', 'module' => 'hospitalization', 'description' => 'Modificar hospitalizaciones', 'created_at' => now()],
            ['name' => 'Ingresar pacientes', 'slug' => 'hospitalization.admit', 'module' => 'hospitalization', 'description' => 'Ingresar pacientes al hospital', 'created_at' => now()],
            ['name' => 'Dar de alta pacientes', 'slug' => 'hospitalization.discharge', 'module' => 'hospitalization', 'description' => 'Dar de alta pacientes hospitalizados', 'created_at' => now()],

            // Emergency
            ['name' => 'Ver emergencias', 'slug' => 'emergency.view', 'module' => 'emergency', 'description' => 'Visualizar consultas de emergencia', 'created_at' => now()],
            ['name' => 'Crear emergencias', 'slug' => 'emergency.create', 'module' => 'emergency', 'description' => 'Registrar nuevas emergencias', 'created_at' => now()],
            ['name' => 'Editar emergencias', 'slug' => 'emergency.update', 'module' => 'emergency', 'description' => 'Actualizar información de emergencias', 'created_at' => now()],
            ['name' => 'Triage', 'slug' => 'emergency.triage', 'module' => 'emergency', 'description' => 'Realizar triage de pacientes', 'created_at' => now()],

            // Reports
            ['name' => 'Ver reportes', 'slug' => 'reports.view', 'module' => 'reports', 'description' => 'Visualizar reportes del sistema', 'created_at' => now()],
            ['name' => 'Exportar reportes', 'slug' => 'reports.export', 'module' => 'reports', 'description' => 'Exportar reportes en diferentes formatos', 'created_at' => now()],

            // Settings
            ['name' => 'Ver configuración', 'slug' => 'settings.view', 'module' => 'settings', 'description' => 'Visualizar configuración del sistema', 'created_at' => now()],
            ['name' => 'Actualizar configuración', 'slug' => 'settings.update', 'module' => 'settings', 'description' => 'Modificar configuración del sistema', 'created_at' => now()],

            // Users
            ['name' => 'Ver usuarios', 'slug' => 'users.view', 'module' => 'users', 'description' => 'Visualizar listado de usuarios', 'created_at' => now()],
            ['name' => 'Crear usuarios', 'slug' => 'users.create', 'module' => 'users', 'description' => 'Registrar nuevos usuarios', 'created_at' => now()],
            ['name' => 'Editar usuarios', 'slug' => 'users.update', 'module' => 'users', 'description' => 'Modificar información de usuarios', 'created_at' => now()],
            ['name' => 'Eliminar usuarios', 'slug' => 'users.delete', 'module' => 'users', 'description' => 'Eliminar usuarios del sistema', 'created_at' => now()],

            // Roles
            ['name' => 'Ver roles', 'slug' => 'roles.view', 'module' => 'roles', 'description' => 'Visualizar listado de roles', 'created_at' => now()],
            ['name' => 'Crear roles', 'slug' => 'roles.create', 'module' => 'roles', 'description' => 'Crear nuevos roles', 'created_at' => now()],
            ['name' => 'Editar roles', 'slug' => 'roles.update', 'module' => 'roles', 'description' => 'Modificar roles existentes', 'created_at' => now()],
            ['name' => 'Eliminar roles', 'slug' => 'roles.delete', 'module' => 'roles', 'description' => 'Eliminar roles del sistema', 'created_at' => now()],

            // Audit
            ['name' => 'Ver auditoría', 'slug' => 'audit.view', 'module' => 'audit', 'description' => 'Visualizar logs de auditoría', 'created_at' => now()],

            // Backups
            ['name' => 'Crear respaldos', 'slug' => 'backups.create', 'module' => 'backups', 'description' => 'Crear copias de seguridad', 'created_at' => now()],
            ['name' => 'Restaurar respaldos', 'slug' => 'backups.restore', 'module' => 'backups', 'description' => 'Restaurar copias de seguridad', 'created_at' => now()],
        ];

        DB::table('permissions')->insert($permissions);
    }
}
