# Database Documentation

## Overview

The Hospital EMR/EHR system uses a relational database with 110+ tables organized into logical modules. The database supports SQLite (development), MySQL 8.0+, and PostgreSQL 15+.

## Complete Table List

### Core System Tables

| Table | Description |
|-------|-------------|
| `institutions` | Healthcare facilities/organizations |
| `users` | System users (doctors, nurses, admin, etc.) |
| `roles` | User roles (admin, doctor, nurse, billing) |
| `permissions` | Granular system permissions |
| `role_user` | User-role pivot table |
| `permission_role` | Role-permission pivot table |
| `settings` | Application settings |
| `system_config` | System-wide configuration |
| `audit_logs` | User activity audit trail |
| `login_history` | User login attempts tracking |
| `sessions` | Active user sessions |
| `password_history` | Previous passwords for policy |
| `api_tokens` | API access tokens |
| `backups` | Database backup records |
| `notifications` | System notifications |
| `dashboard_widgets` | User dashboard configuration |

### Geographic Tables

| Table | Description |
|-------|-------------|
| `countries` | Country reference data |
| `provinces` | States/provinces |
| `cities` | Cities |
| `departments` | Hospital departments |

### Clinical Reference Tables

| Table | Description |
|-------|-------------|
| `specialties` | Medical specialties |
| `cie10_categories` | ICD-10/CIE-10 categories |
| `cie10_codes` | ICD-10/CIE-10 diagnosis codes |
| `medication_catalog` | Medication reference data |
| `medication_categories` | Medication categories |
| `service_catalog` | Billable services |
| `triage_protocols` | Emergency triage protocols |
| `medical_protocols` | Clinical protocols |
| `radiology_templates` | Radiology report templates |
| `consultation_templates` | Consultation note templates |
| `report_templates` | Report generation templates |

### Patient Module Tables

| Table | Description |
|-------|-------------|
| `patients` | Patient demographics and core data |
| `patient_contacts` | Emergency contacts and family |
| `patient_documents` | Patient file attachments |
| `patient_allergies` | Patient allergy records |
| `patient_insurances` | Insurance coverage information |
| `patient_symptoms` | Reported symptoms |
| `patient_vitals_history` | Historical vital signs |

### Medical Records Module Tables

| Table | Description |
|-------|-------------|
| `medical_records` | Patient encounter records |
| `antecedents` | Medical/family history |
| `consultations` | Consultation details |
| `soap_notes` | SOAP format clinical notes |
| `evolutions` | Clinical progress notes |
| `vital_signs` | Vital sign measurements |
| `diagnoses` | Patient diagnoses |
| `clinical_notes` | Free-form clinical notes |
| `clinical_alerts` | Clinical warnings/alerts |
| `medical_record_attachments` | Record file attachments |
| `referral_letters` | Doctor referral documents |

### Appointment Module Tables

| Table | Description |
|-------|-------------|
| `appointments` | Scheduled appointments |
| `appointment_slots` | Available time slots |
| `doctor_schedules` | Doctor availability schedules |
| `appointment_reminders` | Appointment reminders |

### Prescription Module Tables

| Table | Description |
|-------|-------------|
| `prescriptions` | Prescription headers |
| `prescription_items` | Prescription line items |

### Laboratory Module Tables

| Table | Description |
|-------|-------------|
| `lab_orders` | Laboratory test orders |
| `lab_order_items` | Individual test requests |
| `lab_results` | Test results |
| `lab_specimens` | Specimen collection tracking |

### Radiology Module Tables

| Table | Description |
|-------|-------------|
| `imaging_orders` | Imaging study orders |
| `imaging_results` | Imaging study results |

### Pharmacy Module Tables

| Table | Description |
|-------|-------------|
| `medications` | Medication inventory |
| `medication_batches` | Medication lot tracking |
| `medication_dispensations` | Dispensation records |
| `drug_interactions` | Drug interaction database |
| `suppliers` | Medication suppliers |
| `purchase_orders` | Supplier orders |
| `purchase_order_items` | Order line items |

### Hospital Operations Tables

| Table | Description |
|-------|-------------|
| `buildings` | Hospital buildings |
| `floors` | Building floors |
| `rooms` | Patient rooms |
| `beds` | Patient beds |
| `hospitalizations` | Patient admissions |
| `hospitalization_evolutions` | Admission progress notes |
| `bed_transfers` | Inter-bed transfers |

### Surgery Module Tables

| Table | Description |
|-------|-------------|
| `operating_rooms` | OR room definitions |
| `surgery_schedules` | Scheduled surgeries |
| `surgery_team` | Surgery team assignments |
| `surgery_materials` | Surgery supplies |
| `surgery_reports` | Post-surgery reports |

### Emergency Module Tables

| Table | Description |
|-------|-------------|
| `emergency_triage` | Triage assessment records |
| `emergency_records` | Emergency encounter records |

### Billing Module Tables

| Table | Description |
|-------|-------------|
| `invoices` | Patient invoices |
| `invoice_items` | Invoice line items |
| `payments` | Payment records |
| `payment_methods` | Payment method definitions |
| `cash_registers` | Cash register sessions |
| `cash_movements` | Cash transactions |
| `cash_arrears` | Outstanding balances |
| `insurance_claims` | Insurance claim records |
| `discount_authorizations` | Discount approvals |
| `cost_centers` | Department cost centers |
| `budgets` | Budget allocations |

### Telemedicine Module Tables

| Table | Description |
|-------|-------------|
| `telemedicine_sessions` | Virtual consultation sessions |
| `telemedicine_messages` | Session chat messages |

### Nursing Module Tables

| Table | Description |

| `nursing_notes` | Nursing documentation |

### Treatment Planning Tables

| Table | Description |
|-------|-------------|
| `treatment_plans` | Patient treatment plans |
| `clinical_pathways` | Standardized care pathways |

### Communication Tables

| Table | Description |
|-------|-------------|
| `communications` | Internal messages |

### Feedback Tables

| Table | Description |
|-------|-------------|
| `feedback` | User feedback |

### Reporting Tables

| Table | Description |
|-------|-------------|
| `saved_reports` | User saved reports |
| `scheduled_reports` | Automated report schedules |

### Inventory Module Tables

| Table | Description |
|-------|-------------|
| `inventory_categories` | Inventory categories |
| `inventory_items` | Inventory items |
| `inventory_movements` | Stock movements |

## Key Relationships

### Core Relationships

```
┌─────────────────────────────────────────────────────────────────┐
│                    INSTITUTION                                   │
│                         │                                        │
│         ┌───────────────┼───────────────┐                       │
│         ▼               ▼               ▼                       │
│      Users          Patients       Departments                   │
│         │               │                                        │
│         ├───────────────┤                                        │
│         ▼               ▼                                        │
│   Medical Records ◄── Patient                                    │
│         │                                                        │
│    ┌────┴────┬────────┬────────┐                                │
│    ▼         ▼        ▼        ▼                                │
│ Consultations Evolutions Diagnoses SOAP Notes                    │
└─────────────────────────────────────────────────────────────────┘
```

### Patient Module Relationships

```
patients ──┬── patient_contacts (1:N)
           ├── patient_documents (1:N)
           ├── patient_allergies (1:N)
           ├── patient_insurances (1:N)
           ├── patient_symptoms (1:N)
           ├── patient_vitals_history (1:N)
           ├── medical_records (1:N)
           ├── appointments (1:N)
           ├── prescriptions (1:N)
           ├── lab_orders (1:N)
           ├── imaging_orders (1:N)
           ├── hospitalizations (1:N)
           └── emergency_triage (1:N)
```

### Medical Record Relationships

```
medical_records ──┬── antecedents (1:1)
                  ├── consultations (1:N)
                  ├── soap_notes (1:N)
                  ├── evolutions (1:N)
                  ├── vital_signs (1:N)
                  ├── diagnoses (1:N)
                  ├── clinical_notes (1:N)
                  ├── clinical_alerts (1:N)
                  └── medical_record_attachments (1:N)
```

### Appointment Relationships

```
appointments ──┬── patients (N:1)
               ├── users/doctors (N:1)
               ├── appointment_slots (N:1)
               └── appointment_reminders (1:N)
```

### Hospitalization Relationships

```
hospitalizations ──┬── patients (N:1)
                   ├── users/doctors (N:1)
                   ├── beds (N:1)
                   ├── hospitalization_evolutions (1:N)
                   └── bed_transfers (1:N)
```

### Billing Relationships

```
invoices ──┬── patients (N:1)
           ├── invoice_items (1:N)
           └── payments (1:N)

payments ──── payment_methods (N:1)
```

## Indexes

### Performance Indexes

```sql
-- Patients
CREATE INDEX idx_patients_institution ON patients(institution_id);
CREATE INDEX idx_patients_dni ON patients(dni);
CREATE INDEX idx_patients_email ON patients(email);
CREATE INDEX idx_patients_last_name ON patients(last_name);

-- Medical Records
CREATE INDEX idx_medical_records_patient ON medical_records(patient_id);
CREATE INDEX idx_medical_records_doctor ON medical_records(doctor_id);
CREATE INDEX idx_medical_records_date ON medical_records(record_date);

-- Appointments
CREATE INDEX idx_appointments_patient ON appointments(patient_id);
CREATE INDEX idx_appointments_doctor ON appointments(doctor_id);
CREATE INDEX idx_appointments_date ON appointments(appointment_date);
CREATE INDEX idx_appointments_status ON appointments(status);

-- Lab Orders
CREATE INDEX idx_lab_orders_patient ON lab_orders(patient_id);
CREATE INDEX idx_lab_orders_doctor ON lab_orders(doctor_id);
CREATE INDEX idx_lab_orders_status ON lab_orders(status);

-- Prescriptions
CREATE INDEX idx_prescriptions_patient ON prescriptions(patient_id);
CREATE INDEX idx_prescriptions_doctor ON prescriptions(doctor_id);

-- Hospitalizations
CREATE INDEX idx_hospitalizations_patient ON hospitalizations(patient_id);
CREATE INDEX idx_hospitalizations_bed ON hospitalizations(bed_id);
CREATE INDEX idx_hospitalizations_status ON hospitalizations(status);

-- Invoices
CREATE INDEX idx_invoices_patient ON invoices(patient_id);
CREATE INDEX idx_invoices_status ON invoices(status);
CREATE INDEX idx_invoices_date ON invoices(invoice_date);

-- Audit Logs
CREATE INDEX idx_audit_logs_user ON audit_logs(user_id);
CREATE INDEX idx_audit_logs_action ON audit_logs(action);
CREATE INDEX idx_audit_logs_created ON audit_logs(created_at);

-- Inventory
CREATE INDEX idx_inventory_movements_item ON inventory_movements(item_id);
CREATE INDEX idx_inventory_movements_type ON inventory_movements(movement_type);
```

## Views

### Patient Summary View

```sql
CREATE VIEW v_patient_summary AS
SELECT 
    p.id,
    p.institution_id,
    p.dni,
    p.first_name,
    p.last_name,
    p.email,
    p.phone,
    p.date_of_birth,
    p.gender,
    COUNT(DISTINCT mr.id) as total_records,
    COUNT(DISTINCT a.id) as total_appointments,
    MAX(mr.record_date) as last_visit
FROM patients p
LEFT JOIN medical_records mr ON p.id = mr.patient_id
LEFT JOIN appointments a ON p.id = a.patient_id
GROUP BY p.id;
```

### Appointment Calendar View

```sql
CREATE VIEW v_appointment_calendar AS
SELECT 
    a.id,
    a.institution_id,
    a.appointment_date,
    a.appointment_time,
    a.duration_minutes,
    a.status,
    CONCAT(p.first_name, ' ', p.last_name) as patient_name,
    CONCAT(u.first_name, ' ', u.last_name) as doctor_name,
    s.name as specialty_name
FROM appointments a
JOIN patients p ON a.patient_id = p.id
JOIN users u ON a.doctor_id = u.id
LEFT JOIN specialties s ON u.specialty_id = s.id;
```

### Bed Occupancy View

```sql
CREATE VIEW v_bed_occupancy AS
SELECT 
    b.id,
    b.institution_id,
    r.number as room_number,
    f.number as floor_number,
    bl.name as building_name,
    b.number as bed_number,
    b.status,
    b.type,
    CASE 
        WHEN h.id IS NOT NULL THEN CONCAT(p.first_name, ' ', p.last_name)
        ELSE NULL
    END as patient_name,
    h.admission_date
FROM beds b
JOIN rooms r ON b.room_id = r.id
JOIN floors f ON r.floor_id = f.id
JOIN buildings bl ON f.building_id = bl.id
LEFT JOIN hospitalizations h ON b.id = h.bed_id AND h.status = 'active'
LEFT JOIN patients p ON h.patient_id = p.id;
```

### Revenue Summary View

```sql
CREATE VIEW v_revenue_summary AS
SELECT 
    i.institution_id,
    DATE(i.invoice_date) as revenue_date,
    COUNT(DISTINCT i.id) as invoice_count,
    SUM(i.total_amount) as total_invoiced,
    COALESCE(SUM(p.amount), 0) as total_collected,
    SUM(i.total_amount) - COALESCE(SUM(p.amount), 0) as outstanding
FROM invoices i
LEFT JOIN payments p ON i.id = p.invoice_id
GROUP BY i.institution_id, DATE(i.invoice_date);
```

## Stored Procedures

### sp_admit_patient

```sql
CREATE PROCEDURE sp_admit_patient(
    IN p_patient_id INT,
    IN p_bed_id INT,
    IN p_doctor_id INT,
    IN p_admission_date DATETIME,
    IN p_reason TEXT
)
BEGIN
    START TRANSACTION;
    
    -- Update bed status
    UPDATE beds SET status = 'occupied' WHERE id = p_bed_id;
    
    -- Create hospitalization
    INSERT INTO hospitalizations (
        institution_id, patient_id, bed_id, doctor_id,
        admission_date, reason, status
    )
    SELECT 
        p.institution_id, p_patient_id, p_bed_id, p_doctor_id,
        p_admission_date, p_reason, 'active'
    FROM patients p WHERE p.id = p_patient_id;
    
    COMMIT;
END;
```

### sp_discharge_patient

```sql
CREATE PROCEDURE sp_discharge_patient(
    IN p_hospitalization_id INT,
    IN p_discharge_date DATETIME,
    IN p_discharge_notes TEXT
)
BEGIN
    DECLARE v_bed_id INT;
    
    START TRANSACTION;
    
    -- Get bed ID
    SELECT bed_id INTO v_bed_id 
    FROM hospitalizations WHERE id = p_hospitalization_id;
    
    -- Update hospitalization
    UPDATE hospitalizations 
    SET status = 'discharged',
        discharge_date = p_discharge_date,
        discharge_notes = p_discharge_notes
    WHERE id = p_hospitalization_id;
    
    -- Free bed
    UPDATE beds SET status = 'available' WHERE id = v_bed_id;
    
    COMMIT;
END;
```

### sp_generate_invoice

```sql
CREATE PROCEDURE sp_generate_invoice(
    IN p_patient_id INT,
    IN p_institution_id INT,
    IN p_created_by INT
)
BEGIN
    DECLARE v_invoice_id INT;
    
    START TRANSACTION;
    
    -- Create invoice
    INSERT INTO invoices (institution_id, patient_id, created_by, invoice_date, status)
    VALUES (p_institution_id, p_patient_id, p_created_by, NOW(), 'pending');
    
    SET v_invoice_id = LAST_INSERT_ID();
    
    -- Add billable items (consultations, lab orders, etc.)
    -- ... (complex billing logic)
    
    COMMIT;
    
    SELECT v_invoice_id as invoice_id;
END;
```

## Performance Considerations

### Query Optimization

1. **Eager Loading** - Use `with()` to prevent N+1 queries
2. **Indexing** - Index all foreign keys and frequently queried columns
3. **Pagination** - Always paginate large result sets
4. **Soft Deletes** - Use `withTrashed()` only when necessary
5. **Query Caching** - Cache frequently accessed, rarely changing data

### Connection Pooling

```php
// config/database.php
'mysql' => [
    'options' => [
        PDO::ATTR_PERSISTENT => true,
        PDO::ATTR_EMULATE_PREPARES => false,
    ],
],
```

### Slow Query Log

Enable slow query logging to identify optimization opportunities:

```sql
-- MySQL
SET GLOBAL slow_query_log = 1;
SET GLOBAL long_query_time = 2;
```

## Backup Strategy

### Automated Backups

```bash
# Daily backup
php artisan backup:run --only-db

# Weekly full backup
php artisan backup:run

# Backup verification
php artisan backup:verify
```

### Backup Schedule

| Frequency | Type | Retention |
|-----------|------|-----------|
| Daily | Database only | 30 days |
| Weekly | Full (db + files) | 12 weeks |
| Monthly | Full archive | 12 months |

## Migration Strategy

### Creating New Migrations

```bash
php artisan make:migration add_column_to_table_name
```

### Running Migrations

```bash
# Development
php artisan migrate

# Production
php artisan migrate --force

# Rollback
php artisan migrate:rollback --step=5
```

### Seeding Data

```bash
# Run all seeders
php artisan db:seed

# Run specific seeder
php artisan db:seed --class=RoleSeeder
```

## Data Integrity

### Foreign Key Constraints

All foreign keys are enforced at the database level:

```sql
ALTER TABLE appointments
ADD CONSTRAINT fk_appointments_patient
FOREIGN KEY (patient_id) REFERENCES patients(id)
ON DELETE RESTRICT;

ALTER TABLE appointments
ADD CONSTRAINT fk_appointments_doctor
FOREIGN KEY (doctor_id) REFERENCES users(id)
ON DELETE RESTRICT;
```

### Check Constraints

```sql
ALTER TABLE patients
ADD CONSTRAINT chk_gender 
CHECK (gender IN ('male', 'female', 'other', 'unknown'));

ALTER TABLE beds
ADD CONSTRAINT chk_bed_status 
CHECK (status IN ('available', 'occupied', 'maintenance', 'reserved'));
```
