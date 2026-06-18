import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import api from '@/lib/api'
import type { LabOrder, LabResult, PaginatedResponse, ApiResponse } from '@/types'

interface LabOrderFilters {
  patient_id?: string
  status?: string
  priority?: string
  page?: number
  per_page?: number
}

export function useLabOrders(filters: LabOrderFilters = {}) {
  return useQuery({
    queryKey: ['lab-orders', filters],
    queryFn: async () => {
      const params = new URLSearchParams()
      Object.entries(filters).forEach(([key, value]) => {
        if (value !== undefined && value !== '') {
          params.append(key, String(value))
        }
      })
      const response = await api.get(`/lab-orders?${params.toString()}`)
      return response.data as PaginatedResponse<LabOrder>
    },
  })
}

export function useLabOrder(id: string) {
  return useQuery({
    queryKey: ['lab-orders', id],
    queryFn: async () => {
      const response = await api.get(`/lab-orders/${id}`)
      return (response.data as ApiResponse<LabOrder>).data
    },
    enabled: !!id,
  })
}

export function useCreateLabOrder() {
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: async (data: {
      medical_record_id: string
      patient_id: string
      doctor_id: string
      priority?: string
      clinical_reason?: string
      notes?: string
      items: Array<{ test_name: string; test_code?: string; notes?: string }>
    }) => {
      const response = await api.post('/lab-orders', data)
      return (response.data as ApiResponse<LabOrder>).data
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['lab-orders'] })
    },
  })
}

export function useUpdateLabOrderStatus() {
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: async ({ id, status }: { id: string; status: string }) => {
      const response = await api.put(`/lab-orders/${id}`, { status })
      return (response.data as ApiResponse<LabOrder>).data
    },
    onSuccess: (_, variables) => {
      queryClient.invalidateQueries({ queryKey: ['lab-orders'] })
      queryClient.invalidateQueries({ queryKey: ['lab-orders', variables.id] })
    },
  })
}

export function useAddLabResult() {
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: async (data: {
      lab_order_item_id: string
      result_value: string
      reference_range?: string
      unit?: string
      is_abnormal?: boolean
      notes?: string
      file?: File
    }) => {
      const formData = new FormData()
      formData.append('lab_order_item_id', data.lab_order_item_id)
      formData.append('result_value', data.result_value)
      if (data.reference_range) formData.append('reference_range', data.reference_range)
      if (data.unit) formData.append('unit', data.unit)
      if (data.is_abnormal !== undefined) formData.append('is_abnormal', String(data.is_abnormal))
      if (data.notes) formData.append('notes', data.notes)
      if (data.file) formData.append('file', data.file)

      const response = await api.post('/lab-results', formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      })
      return (response.data as ApiResponse<LabResult>).data
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['lab-orders'] })
    },
  })
}

export function usePendingLabOrders() {
  return useQuery({
    queryKey: ['lab-orders', 'pending'],
    queryFn: async () => {
      const response = await api.get<PaginatedResponse<LabOrder>>('/lab-orders?status=pending')
      return response.data
    },
  })
}

export function useValidateLabResult() {
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: async (labOrderItemId: string) => {
      const response = await api.post(`/lab-results/${labOrderItemId}/validate`)
      return (response.data as ApiResponse<any>).data
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['lab-orders'] })
    },
  })
}
