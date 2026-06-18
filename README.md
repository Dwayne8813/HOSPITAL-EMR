# Hospital EMR/EHR System

A comprehensive Electronic Medical Records (EMR) and Electronic Health Records (EHR) system designed for healthcare facilities. Built with Laravel backend and React frontend, featuring multi-tenant architecture, role-based access control, and complete clinical workflow management.

## Features

### Core Clinical Features
- **Patient Management** - Complete patient profiles with demographics, contacts, allergies, and insurance
- **Medical Records** - Digital health records with SOAP notes, evolutions, and clinical documentation
- **Appointments** - Scheduling system with doctor availability, reminders, and calendar integration
- **Prescriptions** - Electronic prescribing with medication catalog and drug interaction checks
- **Laboratory** - Lab orders, results management, and specimen tracking
- **Radiology** - Imaging orders, results, and PACS integration support
- **Diagnosis** - CIE-10/ICD-10 coding system integration

### Hospital Operations
- **Hospitalization** - Patient admission, bed management, transfers, and discharge
- **Surgery** - Operating room scheduling, surgery team management, and reports
- **Emergency** - Triage system with priority-based patient queue management
- **Pharmacy** - Medication inventory, dispensation, and batch tracking
- **Inventory** - Medical supplies and equipment inventory management

### Financial Management
- **Billing** - Invoice generation, payment processing, and service catalog
- **Cash Register** - Point-of-sale operations with cash movement tracking
- **Insurance** - Insurance claims processing and authorizations
- **Reports** - Financial reports, revenue analytics, and export capabilities

### Communication & Collaboration
- **Telemedicine** - Virtual consultation sessions with messaging
- **Notifications** - Real-time alerts and system notifications
- **Certificates** - Medical certificate generation and PDF export
- **Referral Letters** - Doctor-to-doctor referral management

### System Features
- **Multi-Tenant** - Support for multiple healthcare institutions
- **Role-Based Access** - Granular permissions for doctors, nurses, admin, billing, etc.
- **Audit Logging** - Complete activity tracking for compliance
- **Backup System** - Automated database backups with restore capability
- **Dashboard** - Customizable widgets with real-time statistics

## Tech Stack

### Backend
- **Framework:** Laravel 11
- **PHP Version:** 8.2+
- **Database:** SQLite (default) / MySQL 8.0+ / PostgreSQL 15+
- **Authentication:** JWT (tymon/jwt-auth)
- **Queue:** Database driver

### Frontend
- **Framework:** React 18 with TypeScript
- **Build Tool:** Vite
- **Styling:** Tailwind CSS
- **State Management:** React Query + Context
- **Forms:** React Hook Form + Zod validation
- **Routing:** React Router v6

## Directory Structure

```
hospital/
├── backend/                    # Laravel API
│   ├── app/
│   │   ├── DTOs/              # Data Transfer Objects
│   │   ├── Enums/             # PHP Enums
│   │   ├── Exceptions/        # Custom Exceptions
│   │   ├── Http/
│   │   │   ├── Controllers/   # API Controllers (V1)
│   │   │   ├── Middleware/     # Request Middleware
│   │   │   └── Requests/      # Form Requests
│   │   ├── Models/            # Eloquent Models (100+)
│   │   ├── Providers/         # Service Providers
│   │   ├── Repositories/      # Repository Pattern
│   │   └── Services/          # Business Logic
│   ├── config/                # Configuration Files
│   ├── database/
│   │   ├── factories/         # Model Factories
│   │   ├── migrations/        # Database Migrations (110+)
│   │   ├── procedures/        # Stored Procedures
│   │   ├── seeders/           # Database Seeders
│   │   └── views/             # Database Views
│   ├── routes/
│   │   ├── api.php            # API Routes
│   │   └── web.php            # Web Routes
│   └── storage/               # Application Storage
├── frontend/                  # React Application
│   ├── src/
│   │   ├── components/        # Reusable Components
│   │   ├── hooks/             # Custom React Hooks
│   │   ├── lib/               # Utility Libraries
│   │   ├── pages/             # Page Components
│   │   ├── router/            # Route Configuration
│   │   ├── types/             # TypeScript Types
│   │   └── validations/       # Zod Schemas
│   └── public/                # Static Assets
├── installer/                 # Web Installer
├── docs/                      # Documentation
└── .gitignore
```

## Quick Start

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js 18+ and npm
- SQLite, MySQL, or PostgreSQL

### Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd hospital
   ```

2. **Backend Setup**
   ```bash
   cd backend
   composer install
   cp .env.example .env
   php artisan key:generate
   php artisan jwt:secret
   php artisan migrate --seed
   php artisan serve
   ```

3. **Frontend Setup**
   ```bash
   cd frontend
   npm install
   cp .env.example .env
   npm run dev
   ```

4. **Access the Application**
   - Frontend: http://localhost:5173
   - API: http://localhost:8000/api/v1

### Web Installer (Alternative)
1. Upload files to your web server
2. Navigate to `http://your-domain/install`
3. Follow the installation wizard
4. Configure database and admin account

## Default Credentials

### Admin Account
- **Email:** admin@hospital.com
- **Password:** password
- **Role:** Administrator

### Doctor Account
- **Email:** doctor@hospital.com
- **Password:** password
- **Role:** Doctor

### Nurse Account
- **Email:** nurse@hospital.com
- **Password:** password
- **Role:** Nurse

### Billing Account
- **Email:** billing@hospital.com
- **Password:** password
- **Role:** Billing Staff

> **Note:** Change these credentials immediately after installation in production environments.

## Module Descriptions

### 1. Patient Module
Complete patient management with registration, demographics, medical history, allergies, contacts, and insurance information. Supports patient search and advanced filtering.

### 2. Clinical Module
Electronic health records management including SOAP notes, clinical evolutions, vital signs monitoring, diagnosis coding (CIE-10), and treatment plans. Full audit trail for all clinical documentation.

### 3. Appointment Module
Scheduling system with doctor availability management, appointment slots, confirmation workflows, reminders, and calendar integration. Supports recurring appointments and waitlist management.

### 4. Prescription Module
Electronic prescribing with access to medication catalog, dosage calculations, drug interaction warnings, and prescription PDF generation. Integration with pharmacy module for dispensation tracking.

### 5. Laboratory Module
Lab order management with test catalog, specimen collection tracking, result entry, validation workflows, and patient result history. Supports reference ranges and abnormal value flagging.

### 6. Radiology Module
Imaging order management, result recording with structured reporting, and integration points for PACS systems. Template-based reporting for common imaging studies.

### 7. Hospitalization Module
Patient admission workflow, bed assignment and tracking, inter-department transfers, hospitalization evolutions, and discharge planning. Real-time bed occupancy dashboard.

### 8. Surgery Module
Operating room scheduling, surgery team assignment, material/resource tracking, pre-operative and post-operative documentation, and surgery reports.

### 9. Emergency Module
Triage system with configurable protocols, priority-based patient queuing, emergency records, and real-time waiting room dashboard.

### 10. Pharmacy Module
Medication inventory management, batch tracking with expiration dates, dispensation records, low stock alerts, and supplier management.

### 11. Billing Module
Service catalog management, invoice generation, payment processing, cash register operations, insurance claims, and financial reporting.

### 12. Telemedicine Module
Virtual consultation sessions with real-time messaging, session recording, and integration with appointment system.

### 13. Reports Module
Comprehensive reporting with dashboard analytics, patient statistics, appointment analytics, revenue reports, lab and pharmacy reports, and export to Excel/PDF.

### 14. Administration Module
User management, role and permission configuration, system settings, backup management, audit logs, and institution configuration.

## API Documentation

See [docs/API.md](docs/API.md) for complete API documentation.

## Database Documentation

See [docs/DATABASE.md](docs/DATABASE.md) for database schema and relationships.

## Architecture

See [docs/ARCHITECTURE.md](docs/ARCHITECTURE.md) for system architecture details.

## Installation Guide

See [docs/INSTALLATION.md](docs/INSTALLATION.md) for detailed installation instructions.

## Deployment Guide

See [docs/DEPLOYMENT.md](docs/DEPLOYMENT.md) for production deployment instructions.

## Security Features

- JWT-based authentication with refresh tokens
- Multi-factor authentication (MFA) support
- Role-based access control (RBAC)
- Account lockout after failed attempts
- Password history tracking
- Complete audit logging
- API rate limiting
- Input validation and sanitization
- SQL injection prevention
- XSS protection

## License

This software is proprietary and confidential. Unauthorized copying, modification, distribution, or use of this software is strictly prohibited.

## Support

For technical support and inquiries, please contact your system administrator.
