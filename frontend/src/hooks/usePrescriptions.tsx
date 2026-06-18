import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import api from '@/lib/api'
import type { Prescription, PaginatedResponse, ApiResponse } from '@/types'

interface PrescriptionFilters {
  patient_id?: string
  doctor_id?: string
  status?: string
  page?: number
  per_page?: number
}

export function usePrescriptions(filters: PrescriptionFilters = {}) {
  return useQuery({
    queryKey: ['prescriptions', filters],
    queryFn: async () => {
      const params = new URLSearchParams()
      Object.entries(filters).forEach(([key, value]) => {
        if (value !== undefined && value !== '') {
          params.append(key, String(value))
        }
      })
      const response = await api.get<PaginatedResponse<Prescription>>(`/prescriptions?${params.toString()}`)
      return response.data
    },
  })
}

export function usePrescription(id: string) {
  return useQuery({
    queryKey: ['prescriptions', id],
    queryFn: async () => {
      const response = await api.get<ApiResponse<Prescription>>(`/prescriptions/${id}`)
      return response.data.data
    },
    enabled: !!id,
  })
}

export function useCreatePrescription() {
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: async (data: Partial<Prescription>) => {
      const response = await api.post<ApiResponse<Prescription>>('/prescriptions', data)
      return response.data.data
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['prescriptions'] })
    },
  })
}

export function useUpdatePrescription() {
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: async ({ id, data }: { id: string; data: Partial<Prescription> }) => {
      const response = await api.put<ApiResponse<Prescription>>(`/prescriptions/${id}`, data)
      return response.data.data
    },
    onSuccess: (_, variables) => {
      queryClient.invalidateQueries({ queryKey: ['prescriptions'] })
      queryClient.invalidateQueries({ queryKey: ['prescriptions', variables.id] })
    },
  })
}

export function useDispensePrescription() {
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: async (id: string) => {
      const response = await api.post<ApiResponse<Prescription>>(`/prescriptions/${id}/dispense`)
      return response.data.data
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['prescriptions'] })
    },
  })
}
