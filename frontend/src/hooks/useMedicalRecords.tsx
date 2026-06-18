import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import api from '@/lib/api'
import type { MedicalRecord, Consultation, SoapNote, VitalSign, Diagnosis, PaginatedResponse, ApiResponse } from '@/types'

export function useMedicalRecords(filters: { patient_id?: string; page?: number; per_page?: number } = {}) {
  return useQuery({
    queryKey: ['medical-records', filters],
    queryFn: async () => {
      const params = new URLSearchParams()
      Object.entries(filters).forEach(([key, value]) => {
        if (value !== undefined && value !== '') {
          params.append(key, String(value))
        }
      })
      const response = await api.get(`/medical-records?${params.toString()}`)
      return response.data as PaginatedResponse<MedicalRecord>
    },
  })
}

export function useMedicalRecord(id: string) {
  return useQuery({
    queryKey: ['medical-records', id],
    queryFn: async () => {
      const response = await api.get(`/medical-records/${id}`)
      return (response.data as ApiResponse<MedicalRecord>).data
    },
    enabled: !!id,
  })
}

export function useCreateMedicalRecord() {
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: async (data: { patient_id: string; doctor_id: string }) => {
      const response = await api.post('/medical-records', data)
      return (response.data as ApiResponse<MedicalRecord>).data
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['medical-records'] })
    },
  })
}

export function useConsultations(medicalRecordId: string) {
  return useQuery({
    queryKey: ['medical-records', medicalRecordId, 'consultations'],
    queryFn: async () => {
      const response = await api.get(`/medical-records/${medicalRecordId}`)
      const record = (response.data as ApiResponse<MedicalRecord>).data
      const consultation = record?.consultation
      return { data: consultation ? [consultation] : [] } as PaginatedResponse<Consultation>
    },
    enabled: !!medicalRecordId,
  })
}

export function useConsultation(id: string) {
  return useQuery({
    queryKey: ['consultations', id],
    queryFn: async () => {
      const response = await api.get(`/consultations/${id}`)
      return (response.data as ApiResponse<Consultation>).data
    },
    enabled: !!id,
  })
}

export function useCreateConsultation() {
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: async (data: {
      medical_record_id: string
      patient_id: string
      doctor_id: string
      anamnesis?: string
      physical_examination?: string
      assessment?: string
      plan?: string
      additional_notes?: string
      consultation_date?: string
    }) => {
      const response = await api.post('/consultations', data)
      return (response.data as ApiResponse<Consultation>).data
    },
    onSuccess: (_, variables) => {
      queryClient.invalidateQueries({ queryKey: ['medical-records'] })
      if (variables.medical_record_id) {
        queryClient.invalidateQueries({ queryKey: ['medical-records', variables.medical_record_id, 'consultations'] })
      }
    },
  })
}

export function useUpdateConsultation() {
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: async ({ id, data }: { id: string; data: Partial<Consultation> }) => {
      const response = await api.put(`/consultations/${id}`, data)
      return (response.data as ApiResponse<Consultation>).data
    },
    onSuccess: (_, variables) => {
      queryClient.invalidateQueries({ queryKey: ['consultations'] })
      queryClient.invalidateQueries({ queryKey: ['consultations', variables.id] })
    },
  })
}

export function useSoapNotes(medicalRecordId: string) {
  return useQuery({
    queryKey: ['soap-notes', medicalRecordId],
    queryFn: async () => {
      const response = await api.get(`/soap-notes/${medicalRecordId}`)
      return (response.data as ApiResponse<SoapNote | null>).data
    },
    enabled: !!medicalRecordId,
  })
}

export function useCreateSoapNote() {
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: async (data: {
      medical_record_id: string
      subjective?: string
      objective?: string
      assessment?: string
      plan?: string
      doctor_id?: string
      note_date?: string
    }) => {
      const response = await api.post('/soap-notes', data)
      return (response.data as ApiResponse<SoapNote>).data
    },
    onSuccess: (_, variables) => {
      queryClient.invalidateQueries({ queryKey: ['soap-notes', variables.medical_record_id] })
    },
  })
}

export function useCreateVitalSigns() {
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: async (data: {
      medical_record_id: string
      patient_id: string
      temperature?: number
      blood_pressure_systolic?: string
      blood_pressure_diastolic?: string
      heart_rate?: number
      respiratory_rate?: number
      oxygen_saturation?: number
      weight?: number
      height?: number
      notes?: string
      recorded_at?: string
    }) => {
      const response = await api.post('/vital-signs', data)
      return (response.data as ApiResponse<VitalSign>).data
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['vital-signs'] })
    },
  })
}

export function useVitalSigns(patientId: string) {
  return useQuery({
    queryKey: ['vital-signs', patientId],
    queryFn: async () => {
      const response = await api.get(`/vital-signs/${patientId}`)
      return (response.data as ApiResponse<VitalSign[]>).data
    },
    enabled: !!patientId,
  })
}

export function useCreateDiagnosis() {
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: async (data: {
      medical_record_id: string
      patient_id: string
      cie10_code_id?: number
      diagnosis_type?: string
      description: string
      notes?: string
      diagnosed_by?: string
      diagnosis_date?: string
    }) => {
      const response = await api.post('/diagnoses', data)
      return (response.data as ApiResponse<Diagnosis>).data
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['diagnoses'] })
    },
  })
}

export function useDiagnoses(medicalRecordId: string) {
  return useQuery({
    queryKey: ['diagnoses', medicalRecordId],
    queryFn: async () => {
      const response = await api.get(`/diagnoses/${medicalRecordId}`)
      return (response.data as ApiResponse<Diagnosis[]>).data
    },
    enabled: !!medicalRecordId,
  })
}

export function useDeleteDiagnosis() {
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: async (id: string) => {
      await api.delete(`/diagnoses/${id}`)
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['diagnoses'] })
    },
  })
}

export function useSearchCie10(term: string) {
  return useQuery({
    queryKey: ['cie10', 'search', term],
    queryFn: async () => {
      const response = await api.get(`/cie10/search?term=${encodeURIComponent(term)}`)
      return (response.data as ApiResponse<any[]>).data
    },
    enabled: term.length >= 2,
  })
}

export function useCie10Codes(perPage = 50) {
  return useQuery({
    queryKey: ['cie10', perPage],
    queryFn: async () => {
      const response = await api.get(`/cie10?per_page=${perPage}`)
      return response.data as PaginatedResponse<any>
    },
  })
}
