import { useQuery } from '@tanstack/react-query'
import api from '@/lib/api'
import type { ApiResponse } from '@/types'

export interface ReportFilters {
  date_from?: string
  date_to?: string
  doctor_id?: number
}

export interface DashboardReport {
  patients: {
    total: number
    new_this_month: number
    new_today: number
  }
  appointments: {
    today_total: number
    today_completed: number
    today_pending: number
    today_cancelled: number
  }
  revenue: {
    today: number
    this_month: number
    pending_payments: number
  }
  hospitalizations: {
    active: number
    discharged_today: number
  }
  emergencies: {
    waiting: number
    today_total: number
  }
  lab: {
    pending: number
    completed_today: number
  }
}

export interface PatientReport {
  total_patients: number
  new_patients_period: number
  by_gender: { gender: string; count: number }[]
  by_age_group: { age_group: string; count: number }[]
  by_blood_type: { blood_type: string; count: number }[]
  top_diagnoses: { code: string; name: string; count: number }[]
}

export interface AppointmentReport {
  total_appointments: number
  completed: number
  cancelled: number
  no_show: number
  scheduled: number
  by_status: { status: string; count: number }[]
  by_day_of_week: { day: string; count: number }[]
  by_specialty: { specialty: string; count: number }[]
}

export interface RevenueReport {
  total_revenue: number
  total_collected: number
  total_pending: number
  by_month: { month: string; amount: number }[]
  by_payment_method: { method: string; amount: number }[]
  top_services: { service: string; revenue: number; count: number }[]
}

export interface LabReport {
  total_orders: number
  completed: number
  pending: number
  in_progress: number
  by_status: { status: string; count: number }[]
  by_priority: { priority: string; count: number }[]
}

export interface PharmacyReport {
  total_medications: number
  low_stock: number
  expiring_soon: number
  total_dispensations: number
  top_dispensed: { name: string; count: number }[]
}

export interface HospitalizationReport {
  total: number
  active: number
  discharged: number
  average_stay_days: number
  by_status: { status: string; count: number }[]
  by_department: { department: string; count: number }[]
}

export interface DiagnosisReport {
  total_diagnoses: number
  top_diagnoses: { code: string; name: string; count: number; percentage: number }[]
  by_category: { category: string; count: number }[]
}

export function useDashboardReport() {
  return useQuery({
    queryKey: ['reports', 'dashboard'],
    queryFn: async () => {
      const response = await api.get<ApiResponse<DashboardReport>>('/reports/dashboard')
      return response.data.data
    },
  })
}

export function usePatientReport(filters: ReportFilters = {}) {
  return useQuery({
    queryKey: ['reports', 'patients', filters],
    queryFn: async () => {
      const params = new URLSearchParams()
      if (filters.date_from) params.append('date_from', filters.date_from)
      if (filters.date_to) params.append('date_to', filters.date_to)
      const response = await api.get<ApiResponse<PatientReport>>(`/reports/patients?${params.toString()}`)
      return response.data.data
    },
  })
}

export function useAppointmentReport(filters: ReportFilters = {}) {
  return useQuery({
    queryKey: ['reports', 'appointments', filters],
    queryFn: async () => {
      const params = new URLSearchParams()
      if (filters.date_from) params.append('date_from', filters.date_from)
      if (filters.date_to) params.append('date_to', filters.date_to)
      if (filters.doctor_id) params.append('doctor_id', String(filters.doctor_id))
      const response = await api.get<ApiResponse<AppointmentReport>>(`/reports/appointments?${params.toString()}`)
      return response.data.data
    },
  })
}

export function useRevenueReport(filters: ReportFilters = {}) {
  return useQuery({
    queryKey: ['reports', 'revenue', filters],
    queryFn: async () => {
      const params = new URLSearchParams()
      if (filters.date_from) params.append('date_from', filters.date_from)
      if (filters.date_to) params.append('date_to', filters.date_to)
      const response = await api.get<ApiResponse<RevenueReport>>(`/reports/revenue?${params.toString()}`)
      return response.data.data
    },
  })
}

export function useLabReport(filters: ReportFilters = {}) {
  return useQuery({
    queryKey: ['reports', 'lab', filters],
    queryFn: async () => {
      const params = new URLSearchParams()
      if (filters.date_from) params.append('date_from', filters.date_from)
      if (filters.date_to) params.append('date_to', filters.date_to)
      const response = await api.get<ApiResponse<LabReport>>(`/reports/lab?${params.toString()}`)
      return response.data.data
    },
  })
}

export function usePharmacyReport(filters: ReportFilters = {}) {
  return useQuery({
    queryKey: ['reports', 'pharmacy', filters],
    queryFn: async () => {
      const params = new URLSearchParams()
      if (filters.date_from) params.append('date_from', filters.date_from)
      if (filters.date_to) params.append('date_to', filters.date_to)
      const response = await api.get<ApiResponse<PharmacyReport>>(`/reports/pharmacy?${params.toString()}`)
      return response.data.data
    },
  })
}

export function useHospitalizationReport(filters: ReportFilters = {}) {
  return useQuery({
    queryKey: ['reports', 'hospitalization', filters],
    queryFn: async () => {
      const params = new URLSearchParams()
      if (filters.date_from) params.append('date_from', filters.date_from)
      if (filters.date_to) params.append('date_to', filters.date_to)
      const response = await api.get<ApiResponse<HospitalizationReport>>(`/reports/hospitalization?${params.toString()}`)
      return response.data.data
    },
  })
}

export function useDiagnosisReport(filters: ReportFilters = {}) {
  return useQuery({
    queryKey: ['reports', 'diagnoses', filters],
    queryFn: async () => {
      const params = new URLSearchParams()
      if (filters.date_from) params.append('date_from', filters.date_from)
      if (filters.date_to) params.append('date_to', filters.date_to)
      const response = await api.get<ApiResponse<DiagnosisReport>>(`/reports/diagnoses?${params.toString()}`)
      return response.data.data
    },
  })
}
