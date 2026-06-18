import { createBrowserRouter, Navigate } from 'react-router-dom'
import { useAuth } from '@/hooks/useAuth'
import type { ReactNode } from 'react'

// Layouts
import AppLayout from '@/components/layout/AppLayout'

// Auth Pages
import LoginPage from '@/pages/auth/LoginPage'
import ForgotPasswordPage from '@/pages/auth/ForgotPasswordPage'
import ResetPasswordPage from '@/pages/auth/ResetPasswordPage'

// Dashboard
import DashboardPage from '@/pages/dashboard/DashboardPage'

// Patients
import PatientListPage from '@/pages/patients/PatientListPage'
import PatientCreatePage from '@/pages/patients/PatientCreatePage'
import PatientDetailPage from '@/pages/patients/PatientDetailPage'
import PatientEditPage from '@/pages/patients/PatientEditPage'

// Clinical
import MedicalRecordListPage from '@/pages/clinical/MedicalRecordListPage'
import MedicalRecordDetailPage from '@/pages/clinical/MedicalRecordDetailPage'
import ConsultationPage from '@/pages/clinical/ConsultationPage'
import EvolutionPage from '@/pages/clinical/EvolutionPage'

// Appointments
import AppointmentCalendarPage from '@/pages/appointments/AppointmentCalendarPage'
import AppointmentListPage from '@/pages/appointments/AppointmentListPage'
import AppointmentDetailPage from '@/pages/appointments/AppointmentDetailPage'
import AppointmentCreatePage from '@/pages/appointments/AppointmentCreatePage'
import AppointmentEditPage from '@/pages/appointments/AppointmentEditPage'

// Radiology
import ImagingOrderListPage from '@/pages/radiology/ImagingOrderListPage'
import ImagingOrderCreatePage from '@/pages/radiology/ImagingOrderCreatePage'

// Pharmacy
import MedicationListPage from '@/pages/pharmacy/MedicationListPage'
import MedicationCreatePage from '@/pages/pharmacy/MedicationCreatePage'
import SupplierListPage from '@/pages/pharmacy/SupplierListPage'
import SupplierCreatePage from '@/pages/pharmacy/SupplierCreatePage'
import PurchaseOrderListPage from '@/pages/pharmacy/PurchaseOrderListPage'
import PurchaseOrderCreatePage from '@/pages/pharmacy/PurchaseOrderCreatePage'
import DispensationPage from '@/pages/pharmacy/DispensationPage'

// Inventory
import InventoryListPage from '@/pages/inventory/InventoryListPage'
import InventoryCreatePage from '@/pages/inventory/InventoryCreatePage'
import InventoryMovementPage from '@/pages/inventory/InventoryMovementPage'

// Hospitalization
import BedMapPage from '@/pages/hospitalization/BedMapPage'
import HospitalizationListPage from '@/pages/hospitalization/HospitalizationListPage'
import HospitalizationDetailPage from '@/pages/hospitalization/HospitalizationDetailPage'

// Emergency
import TriagePage from '@/pages/emergency/TriagePage'

// Surgery
import SurgerySchedulePage from '@/pages/surgery/SurgerySchedulePage'
import SurgeryCreatePage from '@/pages/surgery/SurgeryCreatePage'

// Billing
import InvoiceListPage from '@/pages/billing/InvoiceListPage'
import InvoiceCreatePage from '@/pages/billing/InvoiceCreatePage'
import InvoiceDetailPage from '@/pages/billing/InvoiceDetailPage'
import InvoicePrintPage from '@/pages/billing/InvoicePrintPage'
import PaymentListPage from '@/pages/billing/PaymentListPage'
import CashRegisterPage from '@/pages/billing/CashRegisterPage'

// Prescriptions
import PrescriptionListPage from '@/pages/prescriptions/PrescriptionListPage'
import PrescriptionCreatePage from '@/pages/prescriptions/PrescriptionCreatePage'
import PrescriptionPrintPage from '@/pages/prescriptions/PrescriptionPrintPage'

// Lab
import LabOrderListPage from '@/pages/laboratory/LabOrderListPage'
import LabOrderCreatePage from '@/pages/laboratory/LabOrderCreatePage'
import LabResultEntryPage from '@/pages/laboratory/LabResultEntryPage'
import LabResultListPage from '@/pages/laboratory/LabResultListPage'
import LabResultPrintPage from '@/pages/laboratory/LabResultPrintPage'

// Reports
import ReportsPage from '@/pages/reports/ReportsPage'

// Settings
import SettingsPage from '@/pages/settings/SettingsPage'
import InstitutionSettingsPage from '@/pages/settings/InstitutionSettingsPage'
import UserListPage from '@/pages/settings/UserListPage'
import UserCreatePage from '@/pages/settings/UserCreatePage'
import RoleListPage from '@/pages/settings/RoleListPage'

// Notifications
import NotificationListPage from '@/pages/notifications/NotificationListPage'

// Audit
import AuditLogPage from '@/pages/audit/AuditLogPage'

// Portal
import PatientPortalPage from '@/pages/portal/PatientPortalPage'

interface ProtectedRouteProps {
  children: ReactNode
}

function ProtectedRoute({ children }: ProtectedRouteProps) {
  const { isAuthenticated, isLoading } = useAuth()

  if (isLoading) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-gray-50">
        <div className="text-center">
          <div className="w-12 h-12 border-4 border-primary-600 border-t-transparent rounded-full animate-spin mx-auto" />
          <p className="mt-4 text-sm text-gray-500">Cargando...</p>
        </div>
      </div>
    )
  }

  if (!isAuthenticated) {
    return <Navigate to="/login" replace />
  }

  return <>{children}</>
}

function PublicRoute({ children }: ProtectedRouteProps) {
  const { isAuthenticated, isLoading } = useAuth()

  if (isLoading) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-gray-50">
        <div className="text-center">
          <div className="w-12 h-12 border-4 border-primary-600 border-t-transparent rounded-full animate-spin mx-auto" />
          <p className="mt-4 text-sm text-gray-500">Cargando...</p>
        </div>
      </div>
    )
  }

  if (isAuthenticated) {
    return <Navigate to="/dashboard" replace />
  }

  return <>{children}</>
}

export const router = createBrowserRouter([
  // Public Routes
  {
    path: '/login',
    element: (
      <PublicRoute>
        <LoginPage />
      </PublicRoute>
    ),
  },
  {
    path: '/forgot-password',
    element: (
      <PublicRoute>
        <ForgotPasswordPage />
      </PublicRoute>
    ),
  },
  {
    path: '/reset-password',
    element: (
      <PublicRoute>
        <ResetPasswordPage />
      </PublicRoute>
    ),
  },

  // Protected Routes
  {
    path: '/',
    element: (
      <ProtectedRoute>
        <AppLayout />
      </ProtectedRoute>
    ),
    children: [
      { index: true, element: <Navigate to="/dashboard" replace /> },
      
      // Dashboard
      { path: 'dashboard', element: <DashboardPage /> },

      // Patients
      { path: 'patients', element: <PatientListPage /> },
      { path: 'patients/create', element: <PatientCreatePage /> },
      { path: 'patients/:id', element: <PatientDetailPage /> },
      { path: 'patients/:id/edit', element: <PatientEditPage /> },

      // Clinical
      { path: 'clinical/records', element: <MedicalRecordListPage /> },
      { path: 'clinical/records/create', element: <MedicalRecordDetailPage /> },
      { path: 'clinical/records/:id', element: <MedicalRecordDetailPage /> },
      { path: 'clinical/consultation/:id', element: <ConsultationPage /> },
      { path: 'clinical/evolution/:id', element: <EvolutionPage /> },

      // Appointments
      { path: 'appointments', element: <AppointmentListPage /> },
      { path: 'appointments/calendar', element: <AppointmentCalendarPage /> },
      { path: 'appointments/create', element: <AppointmentCreatePage /> },
      { path: 'appointments/:id', element: <AppointmentDetailPage /> },
      { path: 'appointments/:id/edit', element: <AppointmentEditPage /> },

      // Radiology
      { path: 'radiology', element: <ImagingOrderListPage /> },
      { path: 'radiology/orders/create', element: <ImagingOrderCreatePage /> },

      // Pharmacy
      { path: 'pharmacy/medications', element: <MedicationListPage /> },
      { path: 'pharmacy/medications/create', element: <MedicationCreatePage /> },
      { path: 'pharmacy/medications/:id', element: <MedicationCreatePage /> },
      { path: 'pharmacy/suppliers', element: <SupplierListPage /> },
      { path: 'pharmacy/suppliers/create', element: <SupplierCreatePage /> },
      { path: 'pharmacy/purchases', element: <PurchaseOrderListPage /> },
      { path: 'pharmacy/purchases/create', element: <PurchaseOrderCreatePage /> },
      { path: 'pharmacy/dispensation', element: <DispensationPage /> },

      // Inventory
      { path: 'inventory', element: <InventoryListPage /> },
      { path: 'inventory/create', element: <InventoryCreatePage /> },
      { path: 'inventory/:id', element: <InventoryCreatePage /> },
      { path: 'inventory/movements', element: <InventoryMovementPage /> },

      // Hospitalization
      { path: 'hospitalization', element: <HospitalizationListPage /> },
      { path: 'hospitalization/bed-map', element: <BedMapPage /> },
      { path: 'hospitalization/list', element: <HospitalizationListPage /> },
      { path: 'hospitalization/create', element: <HospitalizationListPage /> },
      { path: 'hospitalization/:id', element: <HospitalizationDetailPage /> },

      // Emergency
      { path: 'emergency', element: <TriagePage /> },

      // Surgery
      { path: 'surgery', element: <SurgerySchedulePage /> },
      { path: 'surgery/create', element: <SurgeryCreatePage /> },

      // Billing
      { path: 'billing/invoices', element: <InvoiceListPage /> },
      { path: 'billing/invoices/create', element: <InvoiceCreatePage /> },
      { path: 'billing/invoices/:id', element: <InvoiceDetailPage /> },
      { path: 'billing/invoices/:id/print', element: <InvoicePrintPage /> },
      { path: 'billing/payments', element: <PaymentListPage /> },
      { path: 'billing/cash-register', element: <CashRegisterPage /> },

      // Prescriptions
      { path: 'prescriptions', element: <PrescriptionListPage /> },
      { path: 'prescriptions/create', element: <PrescriptionCreatePage /> },
      { path: 'prescriptions/:id/print', element: <PrescriptionPrintPage /> },

      // Lab
      { path: 'lab/orders', element: <LabOrderListPage /> },
      { path: 'lab/orders/create', element: <LabOrderCreatePage /> },
      { path: 'lab/orders/:id', element: <LabResultEntryPage /> },
      { path: 'lab/results', element: <LabResultListPage /> },
      { path: 'lab/results/:id/print', element: <LabResultPrintPage /> },

      // Reports
      { path: 'reports', element: <ReportsPage /> },

      // Settings
      { path: 'settings', element: <SettingsPage /> },
      { path: 'settings/institution', element: <InstitutionSettingsPage /> },
      { path: 'settings/users', element: <UserListPage /> },
      { path: 'settings/users/create', element: <UserCreatePage /> },
      { path: 'settings/roles', element: <RoleListPage /> },

      // Audit
      { path: 'audit', element: <AuditLogPage /> },

      // Notifications
      { path: 'notifications', element: <NotificationListPage /> },

      // Patient Portal
      { path: 'patient-portal', element: <PatientPortalPage /> },
    ],
  },

  // Catch all
  { path: '*', element: <Navigate to="/dashboard" replace /> },
])
