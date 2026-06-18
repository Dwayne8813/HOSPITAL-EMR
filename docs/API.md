# API Documentation

## Base URL

```
Development: http://localhost:8000/api
Production: https://your-domain.com/api
```

## API Version

All endpoints are prefixed with version: `/api/v1/`

## Authentication

### JWT Token Authentication

The API uses JSON Web Tokens (JWT) for authentication. Include the token in the `Authorization` header:

```http
Authorization: Bearer <your-jwt-token>
```

### Getting a Token

```http
POST /api/v1/auth/login
Content-Type: application/json

{
    "email": "user@hospital.com",
    "password": "password"
}
```

**Response:**

```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "user": {
            "id": 1,
            "name": "Dr. John Smith",
            "email": "doctor@hospital.com",
            "role": "doctor"
        },
        "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
        "token_type": "Bearer",
        "expires_in": 3600
    }
}
```

### Refresh Token

```http
POST /api/v1/auth/refresh
Authorization: Bearer <current-token>
```

### Token Expiration

- **Access Token:** 1 hour (3600 seconds)
- **Refresh Token:** 7 days

## Request/Response Format

### Request Headers

```http
Content-Type: application/json
Accept: application/json
Authorization: Bearer <token>
X-Requested-With: XMLHttpRequest
```

### Response Format

**Success Response:**

```json
{
    "success": true,
    "message": "Resource retrieved successfully",
    "data": {
        // Response data
    }
}
```

**Success List Response:**

```json
{
    "success": true,
    "message": "Resources retrieved successfully",
    "data": [
        // Array of resources
    ],
    "meta": {
        "current_page": 1,
        "last_page": 5,
        "per_page": 15,
        "total": 75
    }
}
```

**Error Response:**

```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "email": ["The email field is required."],
        "password": ["The password must be at least 8 characters."]
    }
}
```

## Error Handling

### HTTP Status Codes

| Code | Description |
|------|-------------|
| 200 | Success |
| 201 | Created |
| 204 | No Content |
| 400 | Bad Request |
| 401 | Unauthorized |
| 403 | Forbidden |
| 404 | Not Found |
| 422 | Validation Error |
| 429 | Rate Limit Exceeded |
| 500 | Server Error |

### Error Response Structure

```json
{
    "success": false,
    "message": "Error description",
    "error": "error_code",
    "errors": {}
}
```

### Common Error Codes

| Error Code | Description |
|------------|-------------|
| `unauthenticated` | No valid token provided |
| `token_expired` | JWT token has expired |
| `invalid_credentials` | Email or password incorrect |
| `account_deactivated` | User account is inactive |
| `account_locked` | Account temporarily locked |
| `validation_error` | Request validation failed |
| `not_found` | Resource not found |
| `forbidden` | Insufficient permissions |

---

## Endpoints

### Authentication Module

#### Login

```http
POST /api/v1/auth/login
Content-Type: application/json

{
    "email": "user@hospital.com",
    "password": "password"
}
```

#### Register

```http
POST /api/v1/auth/register
Content-Type: application/json

{
    "name": "New User",
    "email": "newuser@hospital.com",
    "password": "password",
    "password_confirmation": "password",
    "phone": "+1234567890"
}
```

#### Forgot Password

```http
POST /api/v1/auth/forgot-password
Content-Type: application/json

{
    "email": "user@hospital.com"
}
```

#### Reset Password

```http
POST /api/v1/auth/reset-password
Content-Type: application/json

{
    "token": "reset-token-from-email",
    "email": "user@hospital.com",
    "password": "new-password",
    "password_confirmation": "new-password"
}
```

#### Logout

```http
POST /api/v1/auth/logout
Authorization: Bearer <token>
```

#### Get Current User

```http
GET /api/v1/auth/me
Authorization: Bearer <token>
```

**Response:**

```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "Dr. John Smith",
        "email": "doctor@hospital.com",
        "phone": "+1234567890",
        "role": {
            "id": 1,
            "name": "Doctor",
            "slug": "doctor"
        },
        "specialty": {
            "id": 1,
            "name": "Cardiology"
        },
        "institution": {
            "id": 1,
            "name": "City Hospital"
        }
    }
}
```

#### Change Password

```http
POST /api/v1/auth/change-password
Authorization: Bearer <token>
Content-Type: application/json

{
    "current_password": "old-password",
    "password": "new-password",
    "password_confirmation": "new-password"
}
```

#### Enable MFA

```http
POST /api/v1/auth/enable-mfa
Authorization: Bearer <token>
```

**Response:**

```json
{
    "success": true,
    "data": {
        "secret": "JBSWY3DPEHPK3PXP",
        "qr_code": "data:image/png;base64,...",
        "recovery_codes": ["ABC123", "DEF456", ...]
    }
}
```

#### Verify MFA

```http
POST /api/v1/auth/verify-mfa
Authorization: Bearer <token>
Content-Type: application/json

{
    "code": "123456"
}
```

---

### Patient Module

#### List Patients

```http
GET /api/v1/patients?page=1&per_page=15&search=smith&status=active
Authorization: Bearer <token>
```

**Response:**

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "dni": "12345678",
            "first_name": "John",
            "last_name": "Smith",
            "email": "john.smith@email.com",
            "phone": "+1234567890",
            "date_of_birth": "1985-06-15",
            "gender": "male",
            "blood_type": "O+",
            "status": "active",
            "created_at": "2024-01-15T10:30:00Z"
        }
    ],
    "meta": {
        "current_page": 1,
        "last_page": 5,
        "per_page": 15,
        "total": 75
    }
}
```

#### Create Patient

```http
POST /api/v1/patients
Authorization: Bearer <token>
Content-Type: application/json

{
    "dni": "12345678",
    "first_name": "John",
    "last_name": "Smith",
    "email": "john.smith@email.com",
    "phone": "+1234567890",
    "date_of_birth": "1985-06-15",
    "gender": "male",
    "blood_type": "O+",
    "address": "123 Main St",
    "city_id": 1,
    "country_id": 1,
    "province_id": 1
}
```

**Response (201):**

```json
{
    "success": true,
    "message": "Patient created successfully",
    "data": {
        "id": 1,
        "dni": "12345678",
        "first_name": "John",
        "last_name": "Smith",
        // ... other fields
    }
}
```

#### Get Patient

```http
GET /api/v1/patients/{id}
Authorization: Bearer <token>
```

**Response:**

```json
{
    "success": true,
    "data": {
        "id": 1,
        "dni": "12345678",
        "first_name": "John",
        "last_name": "Smith",
        "email": "john.smith@email.com",
        "phone": "+1234567890",
        "date_of_birth": "1985-06-15",
        "gender": "male",
        "blood_type": "O+",
        "contacts": [
            {
                "id": 1,
                "name": "Jane Smith",
                "relationship": "Spouse",
                "phone": "+0987654321"
            }
        ],
        "allergies": [
            {
                "id": 1,
                "allergen": "Penicillin",
                "severity": "severe",
                "reaction": "Anaphylaxis"
            }
        ],
        "insurances": [
            {
                "id": 1,
                "provider": "Blue Cross",
                "policy_number": "BC123456",
                "coverage_percentage": 80
            }
        ]
    }
}
```

#### Update Patient

```http
PUT /api/v1/patients/{id}
Authorization: Bearer <token>
Content-Type: application/json

{
    "phone": "+0987654321",
    "address": "456 Oak Ave"
}
```

#### Delete Patient

```http
DELETE /api/v1/patients/{id}
Authorization: Bearer <token>
```

#### Search Patients

```http
GET /api/v1/patients/search?term=smith&field=last_name
Authorization: Bearer <token>
```

#### Get Patient Medical History

```http
GET /api/v1/patients/{id}/medical-history
Authorization: Bearer <token>
```

**Response:**

```json
{
    "success": true,
    "data": {
        "patient": {
            "id": 1,
            "name": "John Smith"
        },
        "records": [
            {
                "id": 1,
                "record_date": "2024-01-15",
                "doctor": "Dr. Smith",
                "type": "consultation",
                "diagnosis": "Hypertension",
                "notes": "Follow up in 3 months"
            }
        ]
    }
}
```

#### Add Patient Document

```http
POST /api/v1/patients/{id}/documents
Authorization: Bearer <token>
Content-Type: multipart/form-data

document: [file]
document_type: "lab_result"
description: "Blood test results"
```

#### Add Patient Allergy

```http
POST /api/v1/patients/{id}/allergies
Authorization: Bearer <token>
Content-Type: application/json

{
    "allergen": "Penicillin",
    "severity": "severe",
    "reaction": "Anaphylaxis",
    "notes": "Administer epinephrine if exposed"
}
```

#### Add Patient Insurance

```http
POST /api/v1/patients/{id}/insurances
Authorization: Bearer <token>
Content-Type: application/json

{
    "provider": "Blue Cross",
    "policy_number": "BC123456",
    "group_number": "GRP001",
    "coverage_percentage": 80,
    "effective_date": "2024-01-01",
    "expiration_date": "2024-12-31"
}
```

#### Add Patient Contact

```http
POST /api/v1/patients/{id}/contacts
Authorization: Bearer <token>
Content-Type: application/json

{
    "name": "Jane Smith",
    "relationship": "Spouse",
    "phone": "+0987654321",
    "email": "jane@email.com",
    "is_primary": true
}
```

---

### Medical Records Module

#### List Medical Records

```http
GET /api/v1/medical-records?patient_id=1&doctor_id=2&page=1
Authorization: Bearer <token>
```

#### Create Medical Record

```http
POST /api/v1/medical-records
Authorization: Bearer <token>
Content-Type: application/json

{
    "patient_id": 1,
    "record_date": "2024-01-15",
    "chief_complaint": "Chest pain",
    "notes": "Patient presents with mild chest pain..."
}
```

**Response (201):**

```json
{
    "success": true,
    "message": "Medical record created successfully",
    "data": {
        "id": 1,
        "patient_id": 1,
        "doctor_id": 2,
        "record_date": "2024-01-15",
        "record_number": "MR-2024-001",
        "status": "active",
        "created_at": "2024-01-15T10:30:00Z"
    }
}
```

#### Get Medical Record

```http
GET /api/v1/medical-records/{id}
Authorization: Bearer <token>
```

#### Get Medical Record Antecedents

```http
GET /api/v1/medical-records/{id}/antecedents
Authorization: Bearer <token>
```

**Response:**

```json
{
    "success": true,
    "data": {
        "personal": {
            "diabetes": false,
            "hypertension": true,
            "heart_disease": false,
            "asthma": false,
            "cancer": false,
            "surgeries": ["Appendectomy 2010"],
            "medications": ["Lisinopril 10mg"]
        },
        "family": {
            "father": "Hypertension, Type 2 Diabetes",
            "mother": "Heart Disease",
            "siblings": "None"
        }
    }
}
```

#### Update Antecedents

```http
PUT /api/v1/medical-records/{id}/antecedents
Authorization: Bearer <token>
Content-Type: application/json

{
    "personal": {
        "hypertension": true,
        "medications": ["Lisinopril 10mg", "Aspirin 81mg"]
    },
    "family": {
        "father": "Hypertension, Type 2 Diabetes, Heart Disease"
    }
}
```

---

### Consultation Module

#### Create Consultation

```http
POST /api/v1/consultations
Authorization: Bearer <token>
Content-Type: application/json

{
    "medical_record_id": 1,
    "consultation_date": "2024-01-15",
    "consultation_type": "initial",
    "notes": "Patient presents with chief complaint of..."
}
```

#### Get Consultation

```http
GET /api/v1/consultations/{id}
Authorization: Bearer <token>
```

---

### SOAP Notes Module

#### Create SOAP Note

```http
POST /api/v1/soap-notes
Authorization: Bearer <token>
Content-Type: application/json

{
    "medical_record_id": 1,
    "subjective": "Patient reports chest pain, 5/10 severity",
    "objective": "BP: 130/85, HR: 78, Temp: 98.6F",
    "assessment": "Possible musculoskeletal chest pain",
    "plan": "1. Order ECG\n2. Prescribe NSAIDs\n3. Follow up in 1 week"
}
```

**Response (201):**

```json
{
    "success": true,
    "message": "SOAP note created successfully",
    "data": {
        "id": 1,
        "medical_record_id": 1,
        "subjective": "Patient reports chest pain, 5/10 severity",
        "objective": "BP: 130/85, HR: 78, Temp: 98.6F",
        "assessment": "Possible musculoskeletal chest pain",
        "plan": "1. Order ECG\n2. Prescribe NSAIDs\n3. Follow up in 1 week",
        "created_by": 2,
        "created_at": "2024-01-15T10:30:00Z"
    }
}
```

#### Get SOAP Note

```http
GET /api/v1/soap-notes/{medicalRecordId}
Authorization: Bearer <token>
```

#### Update SOAP Note

```http
PUT /api/v1/soap-notes/{id}
Authorization: Bearer <token>
Content-Type: application/json

{
    "assessment": "Musculoskeletal chest pain confirmed",
    "plan": "1. Prescribe Ibuprofen 600mg\n2. Rest\n3. Follow up in 2 weeks"
}
```

---

### Evolutions Module

#### List Evolutions

```http
GET /api/v1/evolutions/{medicalRecordId}
Authorization: Bearer <token>
```

**Response:**

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "evolution_date": "2024-01-15T10:30:00Z",
            "doctor": "Dr. Smith",
            "notes": "Patient improving, pain reduced to 3/10",
            "vital_signs": {
                "blood_pressure": "125/80",
                "heart_rate": 72,
                "temperature": 98.4
            }
        },
        {
            "id": 2,
            "evolution_date": "2024-01-16T09:00:00Z",
            "doctor": "Dr. Smith",
            "notes": "Pain resolved, ready for discharge"
        }
    ]
}
```

#### Create Evolution

```http
POST /api/v1/evolutions
Authorization: Bearer <token>
Content-Type: application/json

{
    "medical_record_id": 1,
    "evolution_date": "2024-01-16",
    "notes": "Patient responding well to treatment",
    "vital_signs": {
        "blood_pressure": "120/78",
        "heart_rate": 68,
        "respiratory_rate": 16,
        "temperature": 98.6,
        "oxygen_saturation": 98,
        "weight": 75,
        "height": 175
    }
}
```

---

### Vital Signs Module

#### Record Vital Signs

```http
POST /api/v1/vital-signs
Authorization: Bearer <token>
Content-Type: application/json

{
    "patient_id": 1,
    "medical_record_id": 1,
    "blood_pressure_systolic": 120,
    "blood_pressure_diastolic": 80,
    "heart_rate": 72,
    "respiratory_rate": 16,
    "temperature": 98.6,
    "oxygen_saturation": 98,
    "weight": 75,
    "height": 175,
    "pain_scale": 2,
    "notes": "Patient comfortable"
}
```

#### Get Patient Vital Signs History

```http
GET /api/v1/vital-signs/{patientId}?start_date=2024-01-01&end_date=2024-01-31
Authorization: Bearer <token>
```

**Response:**

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "recorded_at": "2024-01-15T10:30:00Z",
            "recorded_by": "Nurse Johnson",
            "blood_pressure": "120/80",
            "heart_rate": 72,
            "temperature": 98.6,
            "oxygen_saturation": 98
        },
        {
            "id": 2,
            "recorded_at": "2024-01-16T09:00:00Z",
            "recorded_by": "Nurse Johnson",
            "blood_pressure": "118/78",
            "heart_rate": 70,
            "temperature": 98.4,
            "oxygen_saturation": 99
        }
    ]
}
```

---

### Diagnosis Module

#### Store Diagnosis

```http
POST /api/v1/diagnoses
Authorization: Bearer <token>
Content-Type: application/json

{
    "medical_record_id": 1,
    "cie10_code_id": 125,
    "type": "presumptive",
    "notes": "Primary diagnosis",
    "is_principal": true
}
```

#### Get Medical Record Diagnoses

```http
GET /api/v1/diagnoses/{medicalRecordId}
Authorization: Bearer <token>
```

**Response:**

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "cie10_code": {
                "code": "I10",
                "description": "Essential (primary) hypertension"
            },
            "type": "confirmed",
            "is_principal": true,
            "diagnosed_by": "Dr. Smith",
            "diagnosed_at": "2024-01-15"
        }
    ]
}
```

#### Delete Diagnosis

```http
DELETE /api/v1/diagnoses/{id}
Authorization: Bearer <token>
```

---

### CIE-10 Module

#### List CIE-10 Codes

```http
GET /api/v1/cie10?page=1&per_page=20
Authorization: Bearer <token>
```

#### Search CIE-10 Codes

```http
GET /api/v1/cie10/search?term=hypertension
Authorization: Bearer <token>
```

**Response:**

```json
{
    "success": true,
    "data": [
        {
            "id": 125,
            "code": "I10",
            "description": "Essential (primary) hypertension",
            "category": {
                "code": "I00-I99",
                "name": "Diseases of the circulatory system"
            }
        },
        {
            "id": 126,
            "code": "I11",
            "description": "Hypertensive heart disease",
            "category": {
                "code": "I00-I99",
                "name": "Diseases of the circulatory system"
            }
        }
    ]
}
```

---

### Appointments Module

#### List Appointments

```http
GET /api/v1/appointments?doctor_id=1&date=2024-01-15&status=scheduled
Authorization: Bearer <token>
```

**Response:**

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "patient": {
                "id": 1,
                "name": "John Smith"
            },
            "doctor": {
                "id": 1,
                "name": "Dr. Smith"
            },
            "appointment_date": "2024-01-15",
            "appointment_time": "10:00",
            "duration_minutes": 30,
            "type": "consultation",
            "status": "scheduled",
            "reason": "Follow-up"
        }
    ]
}
```

#### Create Appointment

```http
POST /api/v1/appointments
Authorization: Bearer <token>
Content-Type: application/json

{
    "patient_id": 1,
    "doctor_id": 1,
    "appointment_date": "2024-01-20",
    "appointment_time": "10:00",
    "duration_minutes": 30,
    "type": "consultation",
    "reason": "Annual checkup"
}
```

#### Confirm Appointment

```http
POST /api/v1/appointments/{id}/confirm
Authorization: Bearer <token>
```

#### Cancel Appointment

```http
POST /api/v1/appointments/{id}/cancel
Authorization: Bearer <token>
Content-Type: application/json

{
    "reason": "Patient requested cancellation"
}
```

#### Complete Appointment

```http
POST /api/v1/appointments/{id}/complete
Authorization: Bearer <token>
```

#### Mark No Show

```http
POST /api/v1/appointments/{id}/no-show
Authorization: Bearer <token>
```

#### Get Available Slots

```http
GET /api/v1/appointments/available-slots?doctor_id=1&date=2024-01-20
Authorization: Bearer <token>
```

**Response:**

```json
{
    "success": true,
    "data": {
        "doctor": {
            "id": 1,
            "name": "Dr. Smith"
        },
        "date": "2024-01-20",
        "available_slots": [
            "09:00",
            "09:30",
            "10:00",
            "10:30",
            "14:00",
            "14:30",
            "15:00"
        ]
    }
}
```

---

### Doctor Schedules Module

#### List Doctor Schedules

```http
GET /api/v1/doctor-schedules?doctor_id=1
Authorization: Bearer <token>
```

#### Create Doctor Schedule

```http
POST /api/v1/doctor-schedules
Authorization: Bearer <token>
Content-Type: application/json

{
    "doctor_id": 1,
    "day_of_week": "monday",
    "start_time": "09:00",
    "end_time": "17:00",
    "slot_duration_minutes": 30,
    "is_active": true
}
```

---

### Prescriptions Module

#### List Prescriptions

```http
GET /api/v1/prescriptions?patient_id=1&doctor_id=2
Authorization: Bearer <token>
```

#### Create Prescription

```http
POST /api/v1/prescriptions
Authorization: Bearer <token>
Content-Type: application/json

{
    "medical_record_id": 1,
    "prescription_date": "2024-01-15",
    "notes": "Take with food",
    "items": [
        {
            "medication_id": 10,
            "dosage": "500mg",
            "frequency": "Twice daily",
            "duration": "7 days",
            "instructions": "Take with food"
        },
        {
            "medication_id": 15,
            "dosage": "10mg",
            "frequency": "Once daily",
            "duration": "30 days",
            "instructions": "Take in the morning"
        }
    ]
}
```

**Response (201):**

```json
{
    "success": true,
    "message": "Prescription created successfully",
    "data": {
        "id": 1,
        "prescription_number": "RX-2024-001",
        "prescription_date": "2024-01-15",
        "doctor": "Dr. Smith",
        "patient": "John Smith",
        "status": "active",
        "items": [
            {
                "id": 1,
                "medication": "Amoxicillin",
                "dosage": "500mg",
                "frequency": "Twice daily"
            },
            {
                "id": 2,
                "medication": "Lisinopril",
                "dosage": "10mg",
                "frequency": "Once daily"
            }
        ]
    }
}
```

#### Get Prescription PDF

```http
GET /api/v1/prescriptions/{id}/pdf
Authorization: Bearer <token>
```

Returns PDF file download.

---

### Lab Orders Module

#### List Lab Orders

```http
GET /api/v1/lab-orders?patient_id=1&status=pending
Authorization: Bearer <token>
```

#### Create Lab Order

```http
POST /api/v1/lab-orders
Authorization: Bearer <token>
Content-Type: application/json

{
    "patient_id": 1,
    "medical_record_id": 1,
    "priority": "routine",
    "clinical_info": "Routine checkup",
    "items": [
        {
            "test_name": "Complete Blood Count",
            "specimen_type": "blood"
        },
        {
            "test_name": "Lipid Panel",
            "specimen_type": "blood"
        },
        {
            "test_name": "Urinalysis",
            "specimen_type": "urine"
        }
    ]
}
```

**Response (201):**

```json
{
    "success": true,
    "message": "Lab order created successfully",
    "data": {
        "id": 1,
        "order_number": "LAB-2024-001",
        "order_date": "2024-01-15",
        "patient": "John Smith",
        "doctor": "Dr. Smith",
        "status": "pending",
        "items": [
            {
                "id": 1,
                "test_name": "Complete Blood Count",
                "specimen_type": "blood",
                "status": "pending"
            }
        ]
    }
}
```

#### Store Lab Result

```http
POST /api/v1/lab-results
Authorization: Bearer <token>
Content-Type: application/json

{
    "lab_order_item_id": 1,
    "result_value": "14.5 g/dL",
    "reference_range": "12.0-16.0 g/dL",
    "unit": "g/dL",
    "is_abnormal": false,
    "notes": "Within normal range"
}
```

#### Validate Lab Result

```http
POST /api/v1/lab-results/{labOrderItem}/validate
Authorization: Bearer <token>
Content-Type: application/json

{
    "validation_notes": "Results reviewed and approved"
}
```

#### Get Patient Lab Results

```http
GET /api/v1/lab-results/patient/{patientId}
Authorization: Bearer <token>
```

**Response:**

```json
{
    "success": true,
    "data": [
        {
            "order_date": "2024-01-15",
            "order_number": "LAB-2024-001",
            "test_name": "Complete Blood Count",
            "result_value": "14.5 g/dL",
            "reference_range": "12.0-16.0 g/dL",
            "status": "validated",
            "validated_by": "Dr. Smith"
        }
    ]
}
```

---

### Imaging Orders Module

#### List Imaging Orders

```http
GET /api/v1/imaging-orders?patient_id=1
Authorization: Bearer <token>
```

#### Create Imaging Order

```http
POST /api/v1/imaging-orders
Authorization: Bearer <token>
Content-Type: application/json

{
    "patient_id": 1,
    "medical_record_id": 1,
    "imaging_type": "x_ray",
    "body_part": "chest",
    "clinical_info": "Chest pain evaluation",
    "priority": "urgent"
}
```

#### Store Imaging Result

```http
POST /api/v1/imaging-results
Authorization: Bearer <token>
Content-Type: application/json

{
    "imaging_order_id": 1,
    "findings": "No acute cardiopulmonary process",
    "impression": "Normal chest X-ray",
    "recommendation": "Correlate clinically",
    "radiologist": "Dr. radiologist"
}
```

---

### Medications Module

#### List Medications

```http
GET /api/v1/medications?category_id=1&search=aspirin
Authorization: Bearer <token>
```

**Response:**

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "Aspirin",
            "generic_name": "Acetylsalicylic acid",
            "category": "Analgesics",
            "dosage_form": "Tablet",
            "strength": "500mg",
            "current_stock": 1000,
            "minimum_stock": 100,
            "price": 0.50
        }
    ]
}
```

#### Get Low Stock Medications

```http
GET /api/v1/medications/low-stock
Authorization: Bearer <token>
```

#### Get Expiring Medications

```http
GET /api/v1/medications/expiring?days=90
Authorization: Bearer <token>
```

---

### Suppliers Module

#### List Suppliers

```http
GET /api/v1/suppliers
Authorization: Bearer <token>
```

#### Create Supplier

```http
POST /api/v1/suppliers
Authorization: Bearer <token>
Content-Type: application/json

{
    "name": "PharmaCorp",
    "contact_person": "John Doe",
    "email": "contact@pharmacorp.com",
    "phone": "+1234567890",
    "address": "123 Industry Blvd"
}
```

---

### Purchase Orders Module

#### List Purchase Orders

```http
GET /api/v1/purchase-orders?status=pending
Authorization: Bearer <token>
```

#### Create Purchase Order

```http
POST /api/v1/purchase-orders
Authorization: Bearer <token>
Content-Type: application/json

{
    "supplier_id": 1,
    "expected_delivery_date": "2024-02-01",
    "items": [
        {
            "medication_id": 1,
            "quantity": 500,
            "unit_price": 0.45
        },
        {
            "medication_id": 2,
            "quantity": 200,
            "unit_price": 1.20
        }
    ]
}
```

#### Receive Purchase Order

```http
POST /api/v1/purchase-orders/{id}/receive
Authorization: Bearer <token>
Content-Type: application/json

{
    "received_items": [
        {
            "medication_id": 1,
            "quantity_received": 500,
            "batch_number": "BATCH001",
            "expiration_date": "2025-12-31"
        }
    ]
}
```

---

### Inventory Module

#### List Inventory Items

```http
GET /api/v1/inventory-items?category_id=1&search=syringe
Authorization: Bearer <token>
```

#### Create Inventory Item

```http
POST /api/v1/inventory-items
Authorization: Bearer <token>
Content-Type: application/json

{
    "name": "Disposable Syringe 10ml",
    "category_id": 1,
    "sku": "SYR-010-001",
    "current_stock": 500,
    "minimum_stock": 100,
    "unit_cost": 0.25,
    "unit_price": 0.50
}
```

#### Record Inventory Movement

```http
POST /api/v1/inventory-movements
Authorization: Bearer <token>
Content-Type: application/json

{
    "item_id": 1,
    "movement_type": "out",
    "quantity": 50,
    "reference_type": "department",
    "reference_id": 5,
    "notes": "Distribution to Emergency Department"
}
```

---

### Buildings Module

#### List Buildings

```http
GET /api/v1/buildings
Authorization: Bearer <token>
```

**Response:**

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "Main Building",
            "code": "MB",
            "floors": 5,
            "is_active": true
        },
        {
            "id": 2,
            "name": "Emergency Wing",
            "code": "EW",
            "floors": 2,
            "is_active": true
        }
    ]
}
```

#### Create Building

```http
POST /api/v1/buildings
Authorization: Bearer <token>
Content-Type: application/json

{
    "name": "New Wing",
    "code": "NW",
    "address": "123 Hospital Drive",
    "floors": 3
}
```

---

### Rooms Module

#### List Rooms

```http
GET /api/v1/rooms?building_id=1&floor=2
Authorization: Bearer <token>
```

#### Create Room

```http
POST /api/v1/rooms
Authorization: Bearer <token>
Content-Type: application/json

{
    "building_id": 1,
    "floor_id": 1,
    "number": "101",
    "type": "patient",
    "beds_count": 4
}
```

---

### Beds Module

#### List Beds

```http
GET /api/v1/beds?room_id=1&status=available
Authorization: Bearer <token>
```

#### Get Bed Map

```http
GET /api/v1/beds/map
Authorization: Bearer <token>
```

**Response:**

```json
{
    "success": true,
    "data": {
        "summary": {
            "total_beds": 100,
            "available": 35,
            "occupied": 60,
            "maintenance": 5
        },
        "by_building": [
            {
                "building": "Main Building",
                "floors": [
                    {
                        "floor": 1,
                        "rooms": [
                            {
                                "room": "101",
                                "beds": [
                                    {"bed": "A", "status": "available"},
                                    {"bed": "B", "status": "occupied", "patient": "John Smith"}
                                ]
                            }
                        ]
                    }
                ]
            }
        ]
    }
}
```

#### Create Bed

```http
POST /api/v1/beds
Authorization: Bearer <token>
Content-Type: application/json

{
    "room_id": 1,
    "number": "A",
    "type": "standard",
    "status": "available"
}
```

---

### Hospitalization Module

#### List Hospitalizations

```http
GET /api/v1/hospitalizations?status=active&patient_id=1
Authorization: Bearer <token>
```

**Response:**

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "patient": {
                "id": 1,
                "name": "John Smith"
            },
            "doctor": "Dr. Smith",
            "bed": "Main Building - Floor 2 - Room 201 - Bed A",
            "admission_date": "2024-01-15T10:30:00Z",
            "reason": "Chest pain evaluation",
            "status": "active"
        }
    ]
}
```

#### Create Hospitalization (Admit Patient)

```http
POST /api/v1/hospitalizations
Authorization: Bearer <token>
Content-Type: application/json

{
    "patient_id": 1,
    "doctor_id": 1,
    "bed_id": 1,
    "admission_date": "2024-01-15",
    "admission_type": "emergency",
    "reason": "Acute myocardial infarction",
    "diagnosis": "STEMI"
}
```

**Response (201):**

```json
{
    "success": true,
    "message": "Patient admitted successfully",
    "data": {
        "id": 1,
        "patient": "John Smith",
        "bed": "Main Building - Room 201 - Bed A",
        "admission_date": "2024-01-15T10:30:00Z",
        "status": "active"
    }
}
```

#### Discharge Patient

```http
POST /api/v1/hospitalizations/{id}/discharge
Authorization: Bearer <token>
Content-Type: application/json

{
    "discharge_date": "2024-01-18",
    "discharge_type": "medical",
    "discharge_notes": "Patient stable, follow up in clinic in 1 week",
    "discharge_diagnosis": "Acute MI, resolved"
}
```

#### Transfer Patient

```http
POST /api/v1/hospitalizations/{id}/transfer
Authorization: Bearer <token>
Content-Type: application/json

{
    "new_bed_id": 5,
    "transfer_date": "2024-01-16",
    "reason": "Transfer to step-down unit"
}
```

#### Get Hospitalization Evolutions

```http
GET /api/v1/hospitalizations/{id}/evolutions
Authorization: Bearer <token>
```

#### Get Hospitalization Transfers

```http
GET /api/v1/hospitalizations/{id}/transfers
Authorization: Bearer <token>
```

---

### Surgery Module

#### List Surgery Schedules

```http
GET /api/v1/surgery-schedules?date=2024-01-15&status=scheduled
Authorization: Bearer <token>
```

#### Create Surgery Schedule

```http
POST /api/v1/surgery-schedules
Authorization: Bearer <token>
Content-Type: application/json

{
    "patient_id": 1,
    "doctor_id": 1,
    "operating_room_id": 1,
    "surgery_type": "Appendectomy",
    "scheduled_date": "2024-01-20",
    "scheduled_time": "08:00",
    "estimated_duration_minutes": 120,
    "priority": "elective",
    "notes": "Patient NPO after midnight"
}
```

---

### Emergency Module

#### List Emergency Triage

```http
GET /api/v1/emergency-triage?status=waiting
Authorization: Bearer <token>
```

**Response:**

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "patient": "John Smith",
            "triage_level": 2,
            "chief_complaint": "Chest pain",
            "arrival_time": "2024-01-15T14:30:00Z",
            "status": "waiting",
            "assigned_doctor": null
        }
    ]
}
```

#### Create Emergency Triage

```http
POST /api/v1/emergency-triage
Authorization: Bearer <token>
Content-Type: application/json

{
    "patient_id": 1,
    "triage_level": 2,
    "chief_complaint": "Severe chest pain, shortness of breath",
    "symptoms": ["chest pain", "dyspnea", "diaphoresis"],
    "vital_signs": {
        "blood_pressure": "150/95",
        "heart_rate": 105,
        "respiratory_rate": 24,
        "temperature": 98.8,
        "oxygen_saturation": 94
    }
}
```

#### Get Waiting Patients

```http
GET /api/v1/emergency/waiting
Authorization: Bearer <token>
```

#### Get Triage Statistics

```http
GET /api/v1/emergency/stats
Authorization: Bearer <token>
```

**Response:**

```json
{
    "success": true,
    "data": {
        "waiting_count": 8,
        "in_treatment_count": 12,
        "by_level": {
            "1_critical": 1,
            "2_urgent": 3,
            "3_less_urgent": 4,
            "4_non_urgent": 0
        },
        "average_wait_time_minutes": 45
    }
}
```

---

### Billing Module

#### List Invoices

```http
GET /api/v1/invoices?patient_id=1&status=pending
Authorization: Bearer <token>
```

**Response:**

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "invoice_number": "INV-2024-001",
            "patient": "John Smith",
            "invoice_date": "2024-01-15",
            "total_amount": 1500.00,
            "paid_amount": 500.00,
            "balance": 1000.00,
            "status": "partial"
        }
    ]
}
```

#### Create Invoice

```http
POST /api/v1/invoices
Authorization: Bearer <token>
Content-Type: application/json

{
    "patient_id": 1,
    "items": [
        {
            "service_code": "CONSULT",
            "description": "General Consultation",
            "quantity": 1,
            "unit_price": 100.00
        },
        {
            "service_code": "LAB_CBC",
            "description": "Complete Blood Count",
            "quantity": 1,
            "unit_price": 50.00
        },
        {
            "service_code": "XRAY_CHEST",
            "description": "Chest X-Ray",
            "quantity": 1,
            "unit_price": 75.00
        }
    ],
    "discount_percentage": 10,
    "notes": "Insurance covers 80%"
}
```

**Response (201):**

```json
{
    "success": true,
    "message": "Invoice created successfully",
    "data": {
        "id": 1,
        "invoice_number": "INV-2024-001",
        "patient": "John Smith",
        "subtotal": 225.00,
        "discount": 22.50,
        "tax": 18.00,
        "total_amount": 220.50,
        "status": "pending"
    }
}
```

#### List Payments

```http
GET /api/v1/payments?invoice_id=1
Authorization: Bearer <token>
```

#### Record Payment

```http
POST /api/v1/payments
Authorization: Bearer <token>
Content-Type: application/json

{
    "invoice_id": 1,
    "payment_method_id": 1,
    "amount": 220.50,
    "payment_date": "2024-01-15",
    "reference": "CASH-001",
    "notes": "Full payment"
}
```

**Response (201):**

```json
{
    "success": true,
    "message": "Payment recorded successfully",
    "data": {
        "id": 1,
        "payment_number": "PAY-2024-001",
        "invoice_number": "INV-2024-001",
        "amount": 220.50,
        "payment_method": "Cash",
        "payment_date": "2024-01-15",
        "received_by": "Cashier Jane"
    }
}
```

#### Get Service Catalog

```http
GET /api/v1/service-catalog
Authorization: Bearer <token>
```

#### Create Service

```http
POST /api/v1/service-catalog
Authorization: Bearer <token>
Content-Type: application/json

{
    "code": "ECG",
    "name": "Electrocardiogram",
    "description": "12-lead ECG",
    "category": "Diagnostics",
    "price": 50.00,
    "is_active": true
}
```

---

### Cash Register Module

#### Open Cash Register

```http
POST /api/v1/cash-register/open
Authorization: Bearer <token>
Content-Type: application/json

{
    "opening_balance": 200.00
}
```

#### Close Cash Register

```http
POST /api/v1/cash-register/close
Authorization: Bearer <token>
Content-Type: application/json

{
    "closing_balance": 1500.00
}
```

#### Get Cash Movements

```http
GET /api/v1/cash-register/movements?date=2024-01-15
Authorization: Bearer <token>
```

#### Get Cash Arrears

```http
GET /api/v1/cash-register/arrears
Authorization: Bearer <token>
```

---

### Certificates Module

#### List Certificates

```http
GET /api/v1/certificates?patient_id=1
Authorization: Bearer <token>
```

#### Create Certificate

```http
POST /api/v1/certificates
Authorization: Bearer <token>
Content-Type: application/json

{
    "patient_id": 1,
    "certificate_type": "medical_leave",
    "issue_date": "2024-01-15",
    "valid_from": "2024-01-15",
    "valid_until": "2024-01-22",
    "diagnosis": "Acute bronchitis",
    "notes": "Patient unfit for work for 7 days"
}
```

**Response (201):**

```json
{
    "success": true,
    "message": "Certificate created successfully",
    "data": {
        "id": 1,
        "certificate_number": "CERT-2024-001",
        "patient": "John Smith",
        "type": "Medical Leave",
        "valid_from": "2024-01-15",
        "valid_until": "2024-01-22",
        "issued_by": "Dr. Smith"
    }
}
```

#### Get Certificate PDF

```http
GET /api/v1/certificates/{id}/pdf
Authorization: Bearer <token>
```

Returns PDF file download.

---

### Telemedicine Module

#### List Telemedicine Sessions

```http
GET /api/v1/telemedicine-sessions?doctor_id=1&status=active
Authorization: Bearer <token>
```

#### Create Telemedicine Session

```http
POST /api/v1/telemedicine-sessions
Authorization: Bearer <token>
Content-Type: application/json

{
    "patient_id": 1,
    "scheduled_date": "2024-01-20",
    "scheduled_time": "14:00",
    "reason": "Follow-up consultation",
    "notes": "Review lab results"
}
```

**Response (201):**

```json
{
    "success": true,
    "message": "Telemedicine session created",
    "data": {
        "id": 1,
        "session_code": "TM-2024-001",
        "meeting_url": "https://meet.hospital.com/session/abc123",
        "scheduled_date": "2024-01-20",
        "scheduled_time": "14:00",
        "status": "scheduled"
    }
}
```

#### Get Session Messages

```http
GET /api/v1/telemedicine-sessions/{id}/messages
Authorization: Bearer <token>
```

#### Send Message

```http
POST /api/v1/telemedicine-sessions/{id}/messages
Authorization: Bearer <token>
Content-Type: application/json

{
    "message": "How are you feeling today?"
}
```

---

### Reports Module

#### Get Dashboard Summary

```http
GET /api/v1/reports/dashboard
Authorization: Bearer <token>
```

**Response:**

```json
{
    "success": true,
    "data": {
        "patients": {
            "total": 1500,
            "new_today": 12,
            "new_this_month": 150
        },
        "appointments": {
            "today": 45,
            "completed": 30,
            "cancelled": 3,
            "no_show": 2
        },
        "revenue": {
            "today": 15000.00,
            "this_month": 350000.00,
            "outstanding": 85000.00
        },
        "hospitalizations": {
            "active": 45,
            "admissions_today": 5,
            "discharges_today": 3,
            "bed_occupancy_rate": 75.5
        }
    }
}
```

#### Get Patient Report

```http
GET /api/v1/reports/patients?start_date=2024-01-01&end_date=2024-01-31&group_by=gender
Authorization: Bearer <token>
```

#### Get Appointment Report

```http
GET /api/v1/reports/appointments?start_date=2024-01-01&end_date=2024-01-31&doctor_id=1
Authorization: Bearer <token>
```

#### Get Revenue Report

```http
GET /api/v1/reports/revenue?start_date=2024-01-01&end_date=2024-01-31
Authorization: Bearer <token>
```

**Response:**

```json
{
    "success": true,
    "data": {
        "period": {
            "start": "2024-01-01",
            "end": "2024-01-31"
        },
        "summary": {
            "total_invoiced": 500000.00,
            "total_collected": 420000.00,
            "outstanding": 80000.00,
            "collection_rate": 84.0
        },
        "by_service": [
            {"service": "Consultations", "amount": 150000.00},
            {"service": "Laboratory", "amount": 120000.00},
            {"service": "Radiology", "amount": 80000.00},
            {"service": "Surgery", "amount": 150000.00}
        ]
    }
}
```

#### Get Lab Report

```http
GET /api/v1/reports/lab?start_date=2024-01-01&end_date=2024-01-31
Authorization: Bearer <token>
```

#### Get Pharmacy Report

```http
GET /api/v1/reports/pharmacy?start_date=2024-01-01&end_date=2024-01-31
Authorization: Bearer <token>
```

#### Get Hospitalization Report

```http
GET /api/v1/reports/hospitalization?start_date=2024-01-01&end_date=2024-01-31
Authorization: Bearer <token>
```

#### Get Diagnoses Report

```http
GET /api/v1/reports/diagnoses?start_date=2024-01-01&end_date=2024-01-31
Authorization: Bearer <token>
```

#### Export Report

```http
GET /api/v1/reports/export/{type}?format=excel&start_date=2024-01-01&end_date=2024-01-31
Authorization: Bearer <token>
```

Types: `patients`, `appointments`, `revenue`, `lab`, `pharmacy`, `hospitalization`

Formats: `excel`, `pdf`, `csv`

---

### Dashboard Module

#### Get Dashboard Data

```http
GET /api/v1/dashboard
Authorization: Bearer <token>
```

**Response:**

```json
{
    "success": true,
    "data": {
        "widgets": [
            {
                "type": "stats",
                "title": "Today's Summary",
                "data": {
                    "patients": 45,
                    "appointments": 12,
                    "surgeries": 3
                }
            },
            {
                "type": "chart",
                "title": "Weekly Appointments",
                "data": {...}
            }
        ]
    }
}
```

---

### Settings Module

#### Get Settings

```http
GET /api/v1/settings
Authorization: Bearer <token>
```

**Response:**

```json
{
    "success": true,
    "data": {
        "institution": {
            "name": "City Hospital",
            "address": "123 Medical Center Drive",
            "phone": "+1234567890",
            "email": "info@cityhospital.com"
        },
        "appointments": {
            "slot_duration_minutes": 30,
            "allow_booking_days_ahead": 30,
            "cancellation_hours_before": 24
        },
        "billing": {
            "tax_rate": 8.0,
            "currency": "USD"
        }
    }
}
```

#### Update Settings

```http
PUT /api/v1/settings
Authorization: Bearer <token>
Content-Type: application/json

{
    "institution": {
        "name": "City Hospital",
        "phone": "+1234567891"
    }
}
```

---

### Users Module

#### List Users

```http
GET /api/v1/users?role=doctor&is_active=true
Authorization: Bearer <token>
```

**Response:**

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "Dr. Smith",
            "email": "smith@hospital.com",
            "role": "doctor",
            "specialty": "Cardiology",
            "is_active": true
        }
    ]
}
```

#### Create User

```http
POST /api/v1/users
Authorization: Bearer <token>
Content-Type: application/json

{
    "name": "Dr. New Doctor",
    "email": "newdoctor@hospital.com",
    "password": "password",
    "role_id": 2,
    "specialty_id": 1,
    "phone": "+1234567890"
}
```

#### Update User

```http
PUT /api/v1/users/{id}
Authorization: Bearer <token>
Content-Type: application/json

{
    "is_active": true,
    "specialty_id": 2
}
```

---

### Notifications Module

#### Get Notifications

```http
GET /api/v1/notifications?unread_only=true
Authorization: Bearer <token>
```

**Response:**

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "type": "appointment_reminder",
            "title": "Appointment Reminder",
            "message": "You have an appointment with John Smith at 10:00 AM",
            "is_read": false,
            "created_at": "2024-01-15T09:00:00Z"
        }
    ]
}
```

#### Mark Notification as Read

```http
POST /api/v1/notifications/{id}/read
Authorization: Bearer <token>
```

#### Mark All as Read

```http
POST /api/v1/notifications/read-all
Authorization: Bearer <token>
```

#### Get Unread Count

```http
GET /api/v1/notifications/unread-count
Authorization: Bearer <token>
```

**Response:**

```json
{
    "success": true,
    "data": {
        "count": 5
    }
}
```

---

### Backups Module

#### Create Backup

```http
POST /api/v1/backups/create
Authorization: Bearer <token>
```

**Response (201):**

```json
{
    "success": true,
    "message": "Backup created successfully",
    "data": {
        "id": 1,
        "filename": "backup_2024_01_15_103000.sql",
        "size": "15.2 MB",
        "created_at": "2024-01-15T10:30:00Z"
    }
}
```

#### List Backups

```http
GET /api/v1/backups
Authorization: Bearer <token>
```

**Response:**

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "filename": "backup_2024_01_15_103000.sql",
            "size": "15.2 MB",
            "type": "full",
            "created_at": "2024-01-15T10:30:00Z"
        }
    ]
}
```

#### Download Backup

```http
GET /api/v1/backups/{id}/download
Authorization: Bearer <token>
```

Returns SQL file download.

#### Restore Backup

```http
POST /api/v1/backups/{id}/restore
Authorization: Bearer <token>
Content-Type: application/json

{
    "confirm": true
}
```

**Warning:** This will overwrite current database data.

---

## Rate Limiting

| Endpoint | Limit |
|----------|-------|
| Login | 5 attempts/minute |
| Register | 3 attempts/minute |
| Password Reset | 3 attempts/hour |
| API (General) | 60 requests/minute |
| Report Generation | 10 requests/minute |

## Pagination

All list endpoints support pagination:

```http
GET /api/v1/patients?page=2&per_page=20
```

**Pagination Response:**

```json
{
    "meta": {
        "current_page": 2,
        "last_page": 5,
        "per_page": 20,
        "total": 100
    }
}
```

## Filtering

Most list endpoints support filtering:

```http
GET /api/v1/appointments?status=scheduled&date=2024-01-15&doctor_id=1
```

## Sorting

```http
GET /api/v1/patients?sort_by=last_name&sort_order=asc
```

## Searching

```http
GET /api/v1/patients/search?term=john&field=first_name
```
