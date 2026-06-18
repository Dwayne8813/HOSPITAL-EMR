import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import api from '@/lib/api'
import type { Patient, PaginatedResponse, ApiResponse } from '@/types'

interface PatientFilters {
  search?: string
  gender?: string
  blood_type?: string
  is_active?: boolean
  page?: number
  per_page?: number
}

export function usePatients(filters: PatientFilters = {}) {
  return useQuery({
    queryKey: ['patients', filters],
    queryFn: async () => {
      const params = new URLSearchParams()
      Object.entries(filters).forEach(([key, value]) => {
        if (value !== undefined && value !== '') {
          params.append(key, String(value))
        }
      })
      const response = await api.get<PaginatedResponse<Patient>>(`/patients?${params.toString()}`)
      return response.data
    },
  })
}

export function usePatient(id: string) {
  return useQuery({
    queryKey: ['patients', id],
    queryFn: async () => {
      const response = await api.get<ApiResponse<Patient>>(`/patients/${id}`)
      return response.data.data
    },
    enabled: !!id,
  })
}

export function useCreatePatient() {
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: async (data: Partial<Patient>) => {
      const response = await api.post<ApiResponse<Patient>>('/patients', data)
      return response.data.data
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['patients'] })
    },
  })
}

export function useUpdatePatient() {
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: async ({ id, data }: { id: string; data: Partial<Patient> }) => {
      const response = await api.put<ApiResponse<Patient>>(`/patients/${id}`, data)
      return response.data.data
    },
    onSuccess: (_, variables) => {
      queryClient.invalidateQueries({ queryKey: ['patients'] })
      queryClient.invalidateQueries({ queryKey: ['patients', variables.id] })
    },
  })
}

export function useDeletePatient() {
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: async (id: string) => {
      await api.delete(`/patients/${id}`)
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['patients'] })
    },
  })
}

export function usePatientMedicalHistory(patientId: string) {
  return useQuery({
    queryKey: ['patients', patientId, 'medical-history'],
    queryFn: async () => {
      const response = await api.get(`/patients/${patientId}/medical-history`)
      return response.data.data
    },
    enabled: !!patientId,
  })
}

export function usePatientSearch(query: string) {
  return useQuery({
    queryKey: ['patients', 'search', query],
    queryFn: async () => {
      const response = await api.get<PaginatedResponse<Patient>>(`/patients?search=${encodeURIComponent(query)}&per_page=10`)
      return response.data.data
    },
    enabled: query.length >= 2,
  })
}
