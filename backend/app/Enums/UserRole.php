<?php

declare(strict_types=1);

namespace App\Enums;

enum UserRole: string
{
    case SUPER_ADMIN = 'super_admin';
    case INSTITUTION_ADMIN = 'institution_admin';
    case MEDICAL_DIRECTOR = 'medical_director';
    case DOCTOR = 'doctor';
    case NURSE = 'nurse';
    case RECEPTIONIST = 'receptionist';
    case PHARMACIST = 'pharmacist';
    case LABORATORY = 'laboratory';
    case RADIOLOGIST = 'radiologist';
    case ACCOUNTANT = 'accountant';
    case PATIENT = 'patient';

    public function label(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'Super Administrator',
            self::INSTITUTION_ADMIN => 'Institution Administrator',
            self::MEDICAL_DIRECTOR => 'Medical Director',
            self::DOCTOR => 'Doctor',
            self::NURSE => 'Nurse',
            self::RECEPTIONIST => 'Receptionist',
            self::PHARMACIST => 'Pharmacist',
            self::LABORATORY => 'Laboratory Technician',
            self::RADIOLOGIST => 'Radiologist',
            self::ACCOUNTANT => 'Accountant',
            self::PATIENT => 'Patient',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'Full system access across all institutions',
            self::INSTITUTION_ADMIN => 'Administrative access within a specific institution',
            self::MEDICAL_DIRECTOR => 'Oversight of medical staff and clinical operations',
            self::DOCTOR => 'Clinical consultations, prescriptions, and medical records',
            self::NURSE => 'Patient care, vitals, and nursing documentation',
            self::RECEPTIONIST => 'Patient registration, scheduling, and front desk operations',
            self::PHARMACIST => 'Medication dispensing and pharmacy inventory management',
            self::LABORATORY => 'Lab orders, results, and diagnostic processing',
            self::RADIOLOGIST => 'Imaging orders, reports, and radiology studies',
            self::ACCOUNTANT => 'Financial operations, billing, and payments',
            self::PATIENT => 'Self-service access to own medical records and appointments',
        };
    }
}
