import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import api from '@/lib/api'
import type { Medication, PaginatedResponse, ApiResponse } from '@/types'

interface MedicationFilters {
  search?: string
  category?: string
  is_active?: boolean
  low_stock?: boolean
  page?: number
  per_page?: number
}

export function useMedications(filters: MedicationFilters = {}) {
  return useQuery({
    queryKey: ['medications', filters],
    queryFn: async () => {
      const params = new URLSearchParams()
      Object.entries(filters).forEach(([key, value]) => {
        if (value !== undefined && value !== '') {
          params.append(key, String(value))
        }
      })
      const response = await api.get(`/medications?${params.toString()}`)
      return response.data as PaginatedResponse<Medication>
    },
  })
}

export function useMedication(id: string) {
  return useQuery({
    queryKey: ['medications', id],
    queryFn: async () => {
      const response = await api.get(`/medications/${id}`)
      return (response.data as ApiResponse<Medication>).data
    },
    enabled: !!id,
  })
}

export function useCreateMedication() {
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: async (data: Partial<Medication>) => {
      const response = await api.post('/medications', data)
      return (response.data as ApiResponse<Medication>).data
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['medications'] })
    },
  })
}

export function useUpdateMedication() {
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: async ({ id, data }: { id: string; data: Partial<Medication> }) => {
      const response = await api.put(`/medications/${id}`, data)
      return (response.data as ApiResponse<Medication>).data
    },
    onSuccess: (_, variables) => {
      queryClient.invalidateQueries({ queryKey: ['medications'] })
      queryClient.invalidateQueries({ queryKey: ['medications', variables.id] })
    },
  })
}

export function useDeleteMedication() {
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: async (id: string) => {
      await api.delete(`/medications/${id}`)
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['medications'] })
    },
  })
}

export function useLowStockMedications() {
  return useQuery({
    queryKey: ['medications', 'low-stock'],
    queryFn: async () => {
      const response = await api.get('/medications/low-stock')
      return response.data as ApiResponse<Medication[]>
    },
  })
}

export function useSuppliers() {
  return useQuery({
    queryKey: ['suppliers'],
    queryFn: async () => {
      const response = await api.get('/suppliers')
      return (response.data as ApiResponse<any[]>).data
    },
  })
}

export function useCreateSupplier() {
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: async (data: {
      name: string
      contact_person?: string
      email?: string
      phone?: string
      address?: string
      nit?: string
    }) => {
      const response = await api.post('/suppliers', data)
      return (response.data as ApiResponse<any>).data
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['suppliers'] })
    },
  })
}

export function usePurchaseOrders(filters: { status?: string; supplier_id?: string; page?: number; per_page?: number } = {}) {
  return useQuery({
    queryKey: ['purchase-orders', filters],
    queryFn: async () => {
      const params = new URLSearchParams()
      Object.entries(filters).forEach(([key, value]) => {
        if (value !== undefined && value !== '') {
          params.append(key, String(value))
        }
      })
      const response = await api.get(`/purchase-orders?${params.toString()}`)
      return response.data as PaginatedResponse<any>
    },
  })
}

export function useCreatePurchaseOrder() {
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: async (data: {
      supplier_id: string
      expected_delivery?: string
      notes?: string
      items: Array<{ medication_id: string; quantity: number; unit_cost: number }>
    }) => {
      const response = await api.post('/purchase-orders', data)
      return (response.data as ApiResponse<any>).data
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['purchase-orders'] })
    },
  })
}
