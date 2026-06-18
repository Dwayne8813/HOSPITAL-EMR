# Hospital Information System - Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Complete the EMR/EHR Hospital Information System for Bolivia with full frontend-backend integration, test data, and deployment readiness.

**Architecture:** Laravel 12 backend + React 19 frontend + MySQL (XAMPP) + JWT Auth. Multi-institution support with shared database schema. Bolivia-specific: CIE-10, BOB currency, SIN billing architecture.

**Tech Stack:** PHP 8.3+, Laravel 12, React 19, Vite, MySQL 8, Tailwind CSS, Chart.js, JWT Auth, SQLite (local dev alternative)

## Global Constraints
- No Docker, Kubernetes, Redis, or root access — Hostinger shared hosting only
- Multi-institution support (shared database, shared schema with `institution_id`)
- Clean Architecture, SOLID, DDD, Repository Pattern, Service Layer, DTO Pattern
- ~108 database tables, ~150+ API endpoints, ~47 frontend pages
- Bolivia-specific: CIE-10, BOB currency, SIN billing architecture (decoupled)
- Module-by-module delivery with validation between phases
- PHP 8.3+ (running 8.2.12 locally), Laravel 12, React 19, Vite, MySQL 8

---

## Current State Summary

### Backend (Laravel 12 + MySQL/XAMPP)
- ✅ 108 migrations complete
- ✅ 21 seeders complete (countries, departments, provinces, cities, CIE-10, specialties, institutions, roles, permissions, users, medications, services, triage protocols, buildings, floors, rooms, beds, payment methods)
- ✅ 16+ API endpoints working (auth, users, patients, appointments, buildings, rooms, beds, cie10, doctor-schedules, notifications, inventory-items, suppliers, service-catalog, payments, invoices)
- ✅ JWT authentication working
- ✅ Repository pattern implemented (36 interfaces + implementations)

### Frontend (React 19 + Vite)
- ✅ 47 pages implemented
- ✅ 15 UI components
- ✅ 10 custom hooks
- ✅ TypeScript types defined
- ✅ Builds successfully with `npm run build`
- ✅ Vite dev server running on port 3000

### Known Issues
- ⚠️ Some controllers have service dependencies that may not resolve
- ⚠️ Frontend not yet connected to backend API
- ⚠️ No test data (patients, appointments, clinical records)
- ⚠️ Missing `$table` property on some models
- ⚠️ Building->rooms() relationship fixed but other eager loads may fail

---

## Phase 1: Database & Backend Fixes (Immediate)

### Task 1.1: Fix Model Table Properties

**Files:**
- Modify: `app/Models/*.php` (all models without explicit `$table`)

**Interfaces:**
- Consumes: Existing migration table names
- Produces: Models with correct `$table` properties

- [ ] **Step 1: Identify models missing $table property**

```bash
cd C:\Users\ASUS TUF F15\Desktop\hospital\backend
php artisan tinker --execute='foreach (glob("app/Models/*.php") as $f) { $class = basename($f, ".php"); $m = "App\\Models\\$class"; if (!$m) continue; $r = new ReflectionClass($m); if (!$r->getProperty("table")) { echo "$class\n"; } }' 2>&1
```

- [ ] **Step 2: Add $table to models that need it**

For each model where Laravel auto-pluralizes incorrectly (e.g., `service_catalog` → `service_catalogs`), add:

```php
protected $table = 'correct_table_name';
```

- [ ] **Step 3: Verify with tinker**

```bash
php artisan tinker --execute='echo \App\Models\ServiceCatalog::count();' 2>&1
```

Expected: Returns count without error

---

### Task 1.2: Fix Remaining Controller Dependencies

**Files:**
- Modify: `app/Http/Controllers/V1/BedController.php` (already fixed)
- Modify: `app/Http/Controllers/V1/SupplierController.php`
- Modify: `app/Http/Controllers/V1/ServiceCatalogController.php`
- Modify: Other controllers with service/repository injection issues

**Interfaces:**
- Consumes: Repository interfaces, Service classes
- Produces: Controllers that work without complex DI

- [ ] **Step 1: Test each controller endpoint**

```bash
$body = '{"email":"admin@hospital.bo","password":"password"}'
$r = Invoke-RestMethod -Uri "http://localhost:8000/api/v1/auth/login" -Method Post -Body $body -ContentType "application/json"
$token = $r.data.access_token
$h = @{"Authorization"="Bearer $token"}

# Test each endpoint
$endpoints = @("users", "patients", "appointments", "medications", "buildings", "rooms", "beds", "cie10", "suppliers", "service-catalog", "invoices", "payments")
foreach ($ep in $endpoints) {
    try {
        $resp = Invoke-RestMethod -Uri "http://localhost:8000/api/v1/$ep" -Headers $h
        Write-Host "/$ep : OK"
    } catch {
        Write-Host "/$ep : FAIL - $($_.Exception.Response.StatusCode)"
    }
}
```

- [ ] **Step 2: Fix failing controllers**

For controllers with unresolved service dependencies, either:
1. Remove the service dependency and query directly, OR
2. Ensure the service class exists and has no broken dependencies

- [ ] **Step 3: Verify all endpoints return 200**

---

### Task 1.3: Create Test Data Seeders

**Files:**
- Create: `database/seeders/TestPatientSeeder.php`
- Create: `database/seeders/TestAppointmentSeeder.php`
- Create: `database/seeders/TestMedicalRecordSeeder.php`
- Modify: `database/seeders/DatabaseSeeder.php`

**Interfaces:**
- Consumes: Existing migration tables, models
- Produces: Realistic test data for demo

- [ ] **Step 1: Create TestPatientSeeder**

```php
<?php

namespace Database\Seeders;

use App\Models\Patient;
use Illuminate\Database\Seeder;

class TestPatientSeeder extends Seeder
{
    public function run(): void
    {
        $patients = [
            [
                'institution_id' => 1,
                'internal_code' => 'PAC-001',
                'medical_record_number' => 'HC-2024-001',
                'first_name' => 'María',
                'last_name' => 'González',
                'second_last_name' => 'López',
                'document_type' => 'ci',
                'document_number' => '1234567',
                'date_of_birth' => '1985-03-15',
                'gender' => 'female',
                'blood_type' => 'O+',
                'phone' => '70123456',
                'email' => 'maria.gonzalez@email.com',
                'address' => 'Av. Principal 123',
                'department_id' => 1,
                'province_id' => 1,
                'city_id' => 1,
                'marital_status' => 'married',
                'occupation' => 'Enfermera',
                'status' => 'active',
            ],
            [
                'institution_id' => 1,
                'internal_code' => 'PAC-002',
                'medical_record_number' => 'HC-2024-002',
                'first_name' => 'Carlos',
                'last_name' => 'Mamani',
                'second_last_name' => 'Quispe',
                'document_type' => 'ci',
                'document_number' => '7654321',
                'date_of_birth' => '1970-08-22',
                'gender' => 'male',
                'blood_type' => 'A+',
                'phone' => '70987654',
                'email' => 'carlos.mamani@email.com',
                'address' => 'Calle Bolívar 456',
                'department_id' => 1,
                'province_id' => 1,
                'city_id' => 1,
                'marital_status' => 'single',
                'occupation' => 'Profesor',
                'status' => 'active',
            ],
            [
                'institution_id' => 1,
                'internal_code' => 'PAC-003',
                'medical_record_number' => 'HC-2024-003',
                'first_name' => 'Ana',
                'last_name' => 'Huanca',
                'second_last_name' => 'Mamani',
                'document_type' => 'ci',
                'document_number' => '11223344',
                'date_of_birth' => '1992-11-05',
                'gender' => 'female',
                'blood_type' => 'B-',
                'phone' => '70556677',
                'email' => 'ana.huanca@email.com',
                'address' => 'Av. Arce 789',
                'department_id' => 1,
                'province_id' => 1,
                'city_id' => 1,
                'marital_status' => 'divorced',
                'occupation' => 'Abogada',
                'status' => 'active',
            ],
        ];

        foreach ($patients as $patient) {
            Patient::create($patient);
        }
    }
}
```

- [ ] **Step 2: Create TestAppointmentSeeder**

```php
<?php

namespace Database\Seeders;

use App\Models\Appointment;
use Illuminate\Database\Seeder;

class TestAppointmentSeeder extends Seeder
{
    public function run(): void
    {
        $appointments = [
            [
                'institution_id' => 1,
                'patient_id' => 1,
                'doctor_id' => 2,
                'specialty_id' => 1,
                'appointment_date' => now()->addDays(1),
                'appointment_time' => '09:00',
                'type' => 'consultation',
                'status' => 'scheduled',
                'reason' => 'Consulta general',
                'notes' => 'Primera consulta',
            ],
            [
                'institution_id' => 1,
                'patient_id' => 2,
                'doctor_id' => 3,
                'specialty_id' => 2,
                'appointment_date' => now()->addDays(2),
                'appointment_time' => '10:30',
                'type' => 'consultation',
                'status' => 'scheduled',
                'reason' => 'Control cardiológico',
                'notes' => 'Seguimiento',
            ],
        ];

        foreach ($appointments as $appointment) {
            Appointment::create($appointment);
        }
    }
}
```

- [ ] **Step 3: Update DatabaseSeeder to run test seeders**

```php
public function run(): void
{
    // ... existing seeders ...
    
    $this->call([
        TestPatientSeeder::class,
        TestAppointmentSeeder::class,
    ]);
}
```

- [ ] **Step 4: Run migration with test data**

```bash
cd C:\Users\ASUS TUF F15\Desktop\hospital\backend
php artisan migrate:fresh --seed --force
```

---

## Phase 2: Frontend-Backend Integration

### Task 2.1: Configure Frontend API Connection

**Files:**
- Modify: `frontend/.env` (create if not exists)
- Modify: `frontend/src/lib/api.ts`
- Verify: `frontend/src/contexts/AuthContext.tsx`

**Interfaces:**
- Consumes: Backend API at `http://localhost:8000/api/v1`
- Produces: Frontend configured to communicate with backend

- [ ] **Step 1: Create frontend .env file**

```env
VITE_API_URL=http://localhost:8000/api/v1
VITE_APP_NAME=Hospital System
```

- [ ] **Step 2: Verify api.ts configuration**

The `api.ts` file should already use `import.meta.env.VITE_API_URL` as base URL.

- [ ] **Step 3: Test frontend can reach backend**

Open browser console on `http://localhost:3000` and verify no CORS errors.

---

### Task 2.2: Fix Login Flow

**Files:**
- Modify: `frontend/src/pages/auth/LoginPage.tsx`
- Verify: `frontend/src/contexts/AuthContext.tsx`
- Verify: `frontend/src/hooks/useAuth.ts`

**Interfaces:**
- Consumes: `POST /api/v1/auth/login`
- Produces: Working login that stores JWT token

- [ ] **Step 1: Check AuthContext login method**

```typescript
// Should call API and store token
const login = async (email: string, password: string) => {
  const response = await api.post('/auth/login', { email, password });
  const { access_token, user } = response.data.data;
  localStorage.setItem('access_token', access_token);
  setUser(user);
};
```

- [ ] **Step 2: Test login from frontend**

1. Open `http://localhost:3000/login`
2. Enter: `admin@hospital.bo` / `password`
3. Click Login
4. Should redirect to dashboard

- [ ] **Step 3: Fix any API response format mismatches**

Backend returns: `{ success: true, data: { access_token, user } }`
Frontend expects: Check if frontend matches this format.

---

### Task 2.3: Connect Dashboard to Real Data

**Files:**
- Modify: `frontend/src/pages/DashboardPage.tsx`
- Create: `frontend/src/hooks/useDashboard.ts`

**Interfaces:**
- Consumes: `GET /api/v1/dashboard/stats`
- Produces: Dashboard showing real statistics

- [ ] **Step 1: Create useDashboard hook**

```typescript
import { useQuery } from '@tanstack/react-query';
import api from '@/lib/api';

export function useDashboard() {
  return useQuery({
    queryKey: ['dashboard'],
    queryFn: async () => {
      const { data } = await api.get('/dashboard/stats');
      return data.data;
    },
  });
}
```

- [ ] **Step 2: Update DashboardPage to use hook**

Replace mock data with real API call.

- [ ] **Step 3: Verify dashboard shows real counts**

---

### Task 2.4: Connect Patient Module

**Files:**
- Modify: `frontend/src/pages/patients/PatientListPage.tsx`
- Modify: `frontend/src/pages/patients/PatientDetailPage.tsx`
- Create: `frontend/src/hooks/usePatients.ts`

**Interfaces:**
- Consumes: `GET /api/v1/patients`, `GET /api/v1/patients/{id}`
- Produces: Working patient list and detail views

- [ ] **Step 1: Create usePatients hook**

```typescript
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import api from '@/lib/api';

export function usePatients(params?: Record<string, any>) {
  return useQuery({
    queryKey: ['patients', params],
    queryFn: async () => {
      const { data } = await api.get('/patients', { params });
      return data;
    },
  });
}

export function usePatient(id: number) {
  return useQuery({
    queryKey: ['patient', id],
    queryFn: async () => {
      const { data } = await api.get(`/patients/${id}`);
      return data.data;
    },
    enabled: !!id,
  });
}
```

- [ ] **Step 2: Update PatientListPage to use hook**

Replace mock data with real API call.

- [ ] **Step 3: Update PatientDetailPage to use hook**

Replace mock data with real API call.

- [ ] **Step 4: Test patient CRUD operations**

1. View patient list (should show seeded patients)
2. Click patient to view detail
3. Create new patient
4. Edit patient
5. Delete patient

---

## Phase 3: Complete Core Modules

### Task 3.1: Complete Appointment Module

**Files:**
- Modify: `frontend/src/pages/appointments/AppointmentListPage.tsx`
- Modify: `frontend/src/pages/appointments/AppointmentDetailPage.tsx`
- Create: `frontend/src/hooks/useAppointments.ts`

**Interfaces:**
- Consumes: `GET /api/v1/appointments`, `POST /api/v1/appointments`
- Produces: Working appointment management

- [ ] **Step 1: Create useAppointments hook**

```typescript
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import api from '@/lib/api';

export function useAppointments(params?: Record<string, any>) {
  return useQuery({
    queryKey: ['appointments', params],
    queryFn: async () => {
      const { data } = await api.get('/appointments', { params });
      return data;
    },
  });
}

export function useCreateAppointment() {
  const queryClient = useQueryClient();
  return useMutation({
    mutationFn: async (data: any) => {
      const response = await api.post('/appointments', data);
      return response.data;
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['appointments'] });
    },
  });
}
```

- [ ] **Step 2: Update appointment pages to use hooks**

- [ ] **Step 3: Test appointment CRUD**

---

### Task 3.2: Complete Medical Records Module

**Files:**
- Modify: `frontend/src/pages/clinical/ConsultationPage.tsx`
- Create: `frontend/src/hooks/useMedicalRecords.ts`
- Create: `frontend/src/hooks/useConsultations.ts`

**Interfaces:**
- Consumes: `GET /api/v1/medical-records`, `POST /api/v1/consultations`
- Produces: Working clinical documentation

- [ ] **Step 1: Create hooks for medical records**

- [ ] **Step 2: Update consultation page**

- [ ] **Step 3: Test clinical documentation workflow**

---

### Task 3.3: Complete Laboratory Module

**Files:**
- Modify: `frontend/src/pages/laboratory/LabOrdersPage.tsx`
- Modify: `frontend/src/pages/laboratory/LabResultsPage.tsx`
- Create: `frontend/src/hooks/useLabOrders.ts`

**Interfaces:**
- Consumes: `GET /api/v1/lab-orders`, `POST /api/v1/lab-orders`
- Produces: Working lab order management

- [ ] **Step 1: Create lab order hooks**

- [ ] **Step 2: Update lab pages**

- [ ] **Step 3: Test lab workflow**

---

### Task 3.4: Complete Pharmacy Module

**Files:**
- Modify: `frontend/src/pages/pharmacy/MedicationsPage.tsx`
- Create: `frontend/src/hooks/useMedications.ts`

**Interfaces:**
- Consumes: `GET /api/v1/medications`
- Produces: Working medication catalog

- [ ] **Step 1: Create medication hooks**

- [ ] **Step 2: Update pharmacy pages**

- [ ] **Step 3: Test medication management**

---

## Phase 4: Billing & Reporting

### Task 4.1: Complete Billing Module

**Files:**
- Modify: `frontend/src/pages/billing/InvoicesPage.tsx`
- Create: `frontend/src/hooks/useInvoices.ts`

**Interfaces:**
- Consumes: `GET /api/v1/invoices`, `POST /api/v1/invoices`
- Produces: Working invoice management

- [ ] **Step 1: Create invoice hooks**

- [ ] **Step 2: Update billing pages**

- [ ] **Step 3: Test billing workflow**

---

### Task 4.2: Complete Reporting Module

**Files:**
- Modify: `frontend/src/pages/reports/ReportsPage.tsx`
- Create: `frontend/src/hooks/useReports.ts`

**Interfaces:**
- Consumes: `GET /api/v1/reports`
- Produces: Working report generation

- [ ] **Step 1: Create report hooks**

- [ ] **Step 2: Update reporting pages**

- [ ] **Step 3: Test report generation**

---

## Phase 5: Testing & Quality

### Task 5.1: Add Backend Tests

**Files:**
- Create: `tests/Feature/AuthTest.php`
- Create: `tests/Feature/PatientTest.php`
- Create: `tests/Feature/AppointmentTest.php`

**Interfaces:**
- Consumes: Existing controllers, models
- Produces: Test suite for critical paths

- [ ] **Step 1: Create auth test**

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_with_valid_credentials(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'admin@hospital.bo',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => ['access_token', 'user'],
            ]);
    }

    public function test_login_with_invalid_credentials(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'admin@hospital.bo',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(401);
    }

    public function test_me_endpoint_requires_auth(): void
    {
        $response = $this->getJson('/api/v1/auth/me');

        $response->assertStatus(401);
    }
}
```

- [ ] **Step 2: Create patient test**

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PatientTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_patients_requires_auth(): void
    {
        $response = $this->getJson('/api/v1/patients');

        $response->assertStatus(401);
    }

    public function test_list_patients_returns_paginated_results(): void
    {
        // Seed test data
        $this->seed();

        $token = $this->getAuthToken();
        $response = $this->getJson('/api/v1/patients', [
            'Authorization' => "Bearer $token",
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data',
            ]);
    }
}
```

- [ ] **Step 3: Run tests**

```bash
cd C:\Users\ASUS TUF F15\Desktop\hospital\backend
php artisan test
```

---

### Task 5.2: Add Frontend Tests

**Files:**
- Create: `frontend/src/__tests__/LoginPage.test.tsx`
- Create: `frontend/src/__tests__/PatientListPage.test.tsx`

**Interfaces:**
- Consumes: React components
- Produces: Component tests

- [ ] **Step 1: Create login page test**

```typescript
import { render, screen, fireEvent, waitFor } from '@testing-library/react';
import { BrowserRouter } from 'react-router-dom';
import { QueryClient, QueryClientProvider } from '@tanstack/react-query';
import LoginPage from '@/pages/auth/LoginPage';

const queryClient = new QueryClient();

describe('LoginPage', () => {
  it('renders login form', () => {
    render(
      <QueryClientProvider client={queryClient}>
        <BrowserRouter>
          <LoginPage />
        </BrowserRouter>
      </QueryClientProvider>
    );

    expect(screen.getByLabelText(/email/i)).toBeInTheDocument();
    expect(screen.getByLabelText(/password/i)).toBeInTheDocument();
    expect(screen.getByRole('button', { name: /login/i })).toBeInTheDocument();
  });
});
```

- [ ] **Step 2: Run frontend tests**

```bash
cd C:\Users\ASUS TUF F15\Desktop\hospital\frontend
npm test
```

---

## Phase 6: Deployment Preparation

### Task 6.1: Configure for Hostinger

**Files:**
- Modify: `backend/.env.production`
- Modify: `frontend/.env.production`
- Create: `deployment checklist.md`

**Interfaces:**
- Consumes: Hostinger MySQL credentials
- Produces: Production-ready configuration

- [ ] **Step 1: Create production .env**

```env
APP_NAME="Hospital System"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

JWT_SECRET=your-production-secret
JWT_BLACKLIST_ENABLED=false

CACHE_STORE=file
SESSION_DRIVER=file
```

- [ ] **Step 2: Build frontend for production**

```bash
cd C:\Users\ASUS TUF F15\Desktop\hospital\frontend
npm run build
```

- [ ] **Step 3: Create deployment checklist**

Document all steps for Hostinger deployment.

---

### Task 6.2: Create Deployment Script

**Files:**
- Create: `deploy.sh` (Linux/Mac)
- Create: `deploy.bat` (Windows)

**Interfaces:**
- Consumes: Built frontend, backend code
- Produces: Deployment package

- [ ] **Step 1: Create deployment script**

```bash
#!/bin/bash
echo "Starting deployment..."

# Build frontend
cd frontend
npm run run build

# Copy backend files
# ... deployment commands ...

echo "Deployment complete!"
```

- [ ] **Step 2: Test deployment locally**

---

## Verification Checklist

After completing all phases:

- [ ] All API endpoints return 200 for authenticated requests
- [ ] Frontend login works with test credentials
- [ ] Dashboard shows real statistics
- [ ] Patient CRUD operations work end-to-end
- [ ] Appointment scheduling works
- [ ] Medical records can be created and viewed
- [ ] Lab orders can be placed and results entered
- [ ] Medications can be cataloged
- [ ] Invoices can be generated
- [ ] All tests pass
- [ ] Frontend builds without errors
- [ ] Backend runs without errors
- [ ] Deployment package ready

---

## Notes

### Skills Applied to This Project

1. **writing-plans** - Created this implementation plan
2. **frontend-design** - Will guide UI/UX improvements
3. **api-designer** - Guided API endpoint design
4. **code-reviewer** - Will review code quality
5. **systematic-debugging** - Used for fixing issues
6. **test-driven-development** - Guides test creation
7. **architecture-designer** - Guided system architecture
8. **debugging-wizard** - Used for debugging sessions
9. **requesting-code-review** - Will guide code review process

### Execution Options

**Option 1: Subagent-Driven (recommended)**
- Dispatch fresh subagent per task
- Review between tasks
- Fast iteration

**Option 2: Inline Execution**
- Execute tasks in this session
- Batch execution with checkpoints
- More control

**Which approach?**
