# System Architecture

## Overview

The Hospital EMR/EHR system follows a decoupled architecture with a Laravel REST API backend and a React single-page application (SPA) frontend. The system is designed for scalability, security, and maintainability in healthcare environments.

```
┌─────────────────────────────────────────────────────────────────┐
│                        CLIENT LAYER                             │
├─────────────────────────────────────────────────────────────────┤
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────────────┐  │
│  │ React SPA    │  │ Mobile App   │  │ Third-Party Systems  │  │
│  │ (Vite)       │  │ (Future)     │  │ (HL7/FHIR)          │  │
│  └──────────────┘  └──────────────┘  └──────────────────────┘  │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                       API GATEWAY                               │
├─────────────────────────────────────────────────────────────────┤
│  ┌──────────────────────────────────────────────────────────┐   │
│  │ Laravel Middleware Stack                                  │   │
│  │ • CORS  • JWT Auth  • Rate Limit  • Audit Log           │   │
│  └──────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                     APPLICATION LAYER                           │
├─────────────────────────────────────────────────────────────────┤
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────────────┐    │
│  │ Controllers │  │ Services    │  │ Repositories        │    │
│  │ (HTTP)      │──│ (Business)  │──│ (Data Access)       │    │
│  └─────────────┘  └─────────────┘  └─────────────────────┘    │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                       DATA LAYER                                │
├─────────────────────────────────────────────────────────────────┤
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────────────┐    │
│  │ Eloquent    │  │ Migrations  │  │ Seeders & Factories │    │
│  │ Models      │  │             │  │                     │    │
│  └─────────────┘  └─────────────┘  └─────────────────────┘    │
│                         │                                       │
│  ┌──────────────────────┴──────────────────────────────────┐   │
│  │ Database (SQLite / MySQL / PostgreSQL)                    │   │
│  └─────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────┘
```

## Clean Architecture

The backend follows Clean Architecture principles with clear separation of concerns:

### Layers

```
┌─────────────────────────────────────────────────────────────┐
│                    PRESENTATION LAYER                        │
│  Controllers, Requests, Resources, DTOs                     │
├─────────────────────────────────────────────────────────────┤
│                    BUSINESS LOGIC LAYER                      │
│  Services, Use Cases, Domain Rules                          │
├─────────────────────────────────────────────────────────────┤
│                    DATA ACCESS LAYER                         │
│  Repositories, Eloquent Models, Migrations                  │
├─────────────────────────────────────────────────────────────┤
│                    INFRASTRUCTURE LAYER                      │
│  External Services, Mail, File Storage, Queue               │
└─────────────────────────────────────────────────────────────┘
```

### Directory Structure

```
app/
├── DTOs/                    # Data Transfer Objects
│   ├── PatientDTO.php
│   ├── AppointmentDTO.php
│   └── ...
├── Enums/                   # PHP Enums
│   ├── UserRole.php
│   ├── AppointmentStatus.php
│   └── ...
├── Exceptions/              # Custom Exceptions
│   ├── UnauthorizedException.php
│   ├── ValidationException.php
│   └── ...
├── Http/
│   ├── Controllers/
│   │   ├── V1/              # API Version 1
│   │   │   ├── Auth/
│   │   │   ├── PatientController.php
│   │   │   └── ...
│   │   └── Installer/
│   ├── Middleware/
│   │   ├── AuditLog.php
│   │   ├── CheckPermission.php
│   │   ├── EnsureActive.php
│   │   └── ...
│   └── Requests/            # Form Request Validation
├── Models/                  # Eloquent Models (100+)
│   ├── Patient.php
│   ├── MedicalRecord.php
│   └── ...
├── Providers/               # Service Providers
├── Repositories/            # Repository Pattern
│   ├── PatientRepository.php
│   └── ...
└── Services/                # Business Logic
    ├── Appointment/
    ├── Auth/
    ├── Billing/
    ├── Clinical/
    ├── Emergency/
    ├── Hospitalization/
    ├── Laboratory/
    ├── Patient/
    ├── Pharmacy/
    ├── Radiology/
    ├── Report/
    └── Telemedicine/
```

### Key Principles

1. **Dependency Injection** - Services receive dependencies through constructors
2. **Interface Segregation** - Small, focused interfaces
3. **Single Responsibility** - Each class has one reason to change
4. **Open/Closed** - Open for extension, closed for modification
5. **Liskov Substitution** - Subtypes must be substitutable

## Multi-Tenant Strategy

The system supports multiple healthcare institutions through a shared database approach with tenant isolation.

### Implementation

```
┌─────────────────────────────────────────────────────────────┐
│                    INSTITUTION MODEL                         │
├─────────────────────────────────────────────────────────────┤
│  • id                                                      │
│  • name                                                    │
│  • slug (unique identifier)                                │
│  • settings (JSON)                                         │
│  • is_active                                                │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│              TENANT-AWARE MODELS                             │
├─────────────────────────────────────────────────────────────┤
│  All major tables include institution_id:                   │
│  • users                                                   │
│  • patients                                                │
│  • medical_records                                         │
│  • appointments                                            │
│  • etc.                                                    │
└─────────────────────────────────────────────────────────────┘
```

### Tenant Isolation

1. **Database Level** - All queries filtered by `institution_id`
2. **Model Scopes** - Global scopes automatically filter by tenant
3. **JWT Claims** - Institution ID embedded in JWT token
4. **Middleware** - `EnsureInstitution` middleware validates access

### Model Example

```php
class Patient extends Model
{
    protected static function boot(): void
    {
        parent::boot();
        
        static::addGlobalScope('institution', function ($query) {
            if (auth()->check()) {
                $query->where('institution_id', auth()->user()->institution_id);
            }
        });
    }
}
```

## Security Layers

### 1. Authentication Layer

```
┌─────────────────────────────────────────────────────────────┐
│                 JWT Authentication                           │
├─────────────────────────────────────────────────────────────┤
│  • Token Generation (Access + Refresh)                      │
│  • Token Validation                                         │
│  • Token Refresh                                            │
│  • Token Revocation                                         │
│  • MFA Support (TOTP)                                       │
└─────────────────────────────────────────────────────────────┘
```

### 2. Authorization Layer

```
┌─────────────────────────────────────────────────────────────┐
│              Role-Based Access Control                       │
├─────────────────────────────────────────────────────────────┤
│  Roles: admin, doctor, nurse, billing, pharmacist, etc.     │
│  Permissions: view_patients, edit_records, manage_billing   │
│  Middleware: CheckPermission validates access                │
└─────────────────────────────────────────────────────────────┘
```

### 3. Audit Layer

```
┌─────────────────────────────────────────────────────────────┐
│                    Audit Logging                             │
├─────────────────────────────────────────────────────────────┤
│  • User actions tracked                                     │
│  • IP address and user agent recorded                       │
│  • Before/after values for changes                          │
│  • Compliance with healthcare regulations                   │
└─────────────────────────────────────────────────────────────┘
```

### 4. Rate Limiting

```
┌─────────────────────────────────────────────────────────────┐
│                  Rate Limiting                               │
├─────────────────────────────────────────────────────────────┤
│  • Login attempts: 5 per minute                             │
│  • API requests: 60 per minute                              │
│  • Password reset: 3 per hour                               │
│  • Configurable per endpoint                                │
└─────────────────────────────────────────────────────────────┘
```

### 5. Data Protection

```
┌─────────────────────────────────────────────────────────────┐
│                Data Protection                               │
├─────────────────────────────────────────────────────────────┤
│  • Password hashing (bcrypt)                                │
│  • Sensitive data encryption                                │
│  • Input validation (Form Requests)                         │
│  • SQL injection prevention (Eloquent)                      │
│  • XSS protection                                           │
│  • CORS configuration                                       │
└─────────────────────────────────────────────────────────────┘
```

## API Versioning

### Version Strategy

The API uses URI-based versioning for backward compatibility:

```
/api/v1/patients
/api/v1/appointments
/api/v2/patients  (future)
```

### Version Header

```http
Accept: application/json
X-API-Version: 1
```

### Deprecation Policy

- Old versions supported for 12 months after new version release
- Deprecation warnings in response headers
- Migration guides provided for major version changes

## Technology Decisions

### Backend: Laravel

| Decision | Rationale |
|----------|-----------|
| Laravel 11 | Mature framework, excellent ecosystem, strong security |
| Eloquent ORM | Active Record pattern, relationship support, scopes |
| JWT Auth | Stateless authentication, mobile-ready |
| SQLite Default | Zero configuration, easy development |
| MySQL/PostgreSQL | Production-grade, ACID compliance |
| Service Layer | Business logic separation, testability |
| Repository Pattern | Data access abstraction, multiple data sources |

### Frontend: React + TypeScript

| Decision | Rationale |
|----------|-----------|
| React 18 | Component-based, large ecosystem, performance |
| TypeScript | Type safety, better IDE support, fewer bugs |
| Vite | Fast development, HMR, optimized builds |
| Tailwind CSS | Utility-first, rapid UI development |
| React Query | Server state management, caching, pagination |
| React Hook Form | Performant forms, easy validation |
| Zod | Runtime type validation, TypeScript integration |

### Database

| Decision | Rationale |
|----------|-----------|
| SQLite (Dev) | Zero config, portable, suitable for small-medium |
| MySQL (Prod) | Widely supported, good performance |
| JSON Columns | Flexible schema for settings, metadata |
| Soft Deletes | Data retention, audit trail |
| UUID Support | Distributed system readiness |

## Performance Considerations

### Backend Optimization

1. **Query Optimization**
   - Eager loading relationships
   - Database indexing on frequently queried columns
   - Query result caching

2. **Queue System**
   - Background jobs for heavy operations
   - Email notifications via queue
   - Report generation async

3. **Caching Strategy**
   - Configuration caching
   - Route caching
   - Query result caching
   - Frontend API response caching

### Frontend Optimization

1. **Code Splitting**
   - Route-based lazy loading
   - Component-level code splitting

2. **State Management**
   - React Query for server state
   - Local state for UI state
   - Context for global UI state

3. **Bundle Optimization**
   - Tree shaking
   - Image optimization
   - CSS purging

## Scalability

### Horizontal Scaling

```
┌─────────────────────────────────────────────────────────────┐
│                    Load Balancer                             │
├─────────────────────────────────────────────────────────────┤
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐        │
│  │ App Server 1│  │ App Server 2│  │ App Server 3│        │
│  └─────────────┘  └─────────────┘  └─────────────┘        │
│         │                │                │                  │
│         └────────────────┼────────────────┘                  │
│                          ▼                                   │
│  ┌─────────────────────────────────────────────────────┐    │
│  │              Shared Database                         │    │
│  └─────────────────────────────────────────────────────┘    │
└─────────────────────────────────────────────────────────────┘
```

### Vertical Scaling

- Database connection pooling
- Redis for caching and sessions
- Queue workers for background jobs
- CDN for static assets

## Integration Points

### HL7/FHIR (Future)

```json
{
  "resourceType": "Patient",
  "id": "12345",
  "name": [{"family": "Smith", "given": ["John"]}],
  "birthDate": "1990-01-15"
}
```

### External Systems

- Laboratory Information Systems (LIS)
- Picture Archiving (PACS)
- Pharmacy Systems
- Insurance Portals
- Government Health Databases

## Disaster Recovery

### Backup Strategy

1. **Daily automated backups**
2. **Weekly full backups**
3. **Off-site backup storage**
4. **Point-in-time recovery capability**

### Recovery Procedures

1. **RTO (Recovery Time Objective):** 4 hours
2. **RPO (Recovery Point Objective):** 1 hour
3. **Backup testing:** Monthly
4. **Disaster recovery drills:** Quarterly

## Compliance

### Healthcare Regulations

- HIPAA compliance considerations
- Data encryption at rest and in transit
- Access logging and audit trails
- Patient consent management
- Data retention policies

### Security Standards

- OWASP Top 10 protection
- HTTPS enforcement
- Secure session management
- Input validation
- Output encoding
