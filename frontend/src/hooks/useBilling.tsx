import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import api from '@/lib/api'
import type { Invoice, Payment, ServiceCatalogItem, PaginatedResponse, ApiResponse } from '@/types'

interface InvoiceFilters {
  patient_id?: string
  payment_status?: string
  date_from?: string
  date_to?: string
  page?: number
  per_page?: number
}

export function useInvoices(filters: InvoiceFilters = {}) {
  return useQuery({
    queryKey: ['invoices', filters],
    queryFn: async () => {
      const params = new URLSearchParams()
      Object.entries(filters).forEach(([key, value]) => {
        if (value !== undefined && value !== '') {
          params.append(key, String(value))
        }
      })
      const response = await api.get<PaginatedResponse<Invoice>>(`/invoices?${params.toString()}`)
      return response.data
    },
  })
}

export function useInvoice(id: string) {
  return useQuery({
    queryKey: ['invoices', id],
    queryFn: async () => {
      const response = await api.get<ApiResponse<Invoice>>(`/invoices/${id}`)
      return response.data.data
    },
    enabled: !!id,
  })
}

export function useCreateInvoice() {
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: async (data: {
      patient_id: string
      notes?: string
      due_date?: string
      items: {
        service_catalog_id?: number
        description: string
        quantity: number
        unit_price: number
        discount?: number
      }[]
    }) => {
      const response = await api.post<ApiResponse<Invoice>>('/invoices', data)
      return response.data.data
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['invoices'] })
    },
  })
}

export function useUpdateInvoice() {
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: async ({ id, data }: { id: string; data: Partial<Invoice> }) => {
      const response = await api.put<ApiResponse<Invoice>>(`/invoices/${id}`, data)
      return response.data.data
    },
    onSuccess: (_, variables) => {
      queryClient.invalidateQueries({ queryKey: ['invoices'] })
      queryClient.invalidateQueries({ queryKey: ['invoices', variables.id] })
    },
  })
}

export function useRecordPayment() {
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: async (data: {
      invoice_id: string
      amount: number
      payment_method: 'cash' | 'card' | 'transfer' | 'insurance' | 'mixed'
      reference_number?: string
      notes?: string
      received_at?: string
    }) => {
      const response = await api.post<ApiResponse<Payment>>('/payments', data)
      return response.data.data
    },
    onSuccess: (_, variables) => {
      queryClient.invalidateQueries({ queryKey: ['invoices'] })
      if (variables.invoice_id) {
        queryClient.invalidateQueries({ queryKey: ['invoices', variables.invoice_id] })
      }
    },
  })
}

export function usePayments(invoiceId: string) {
  return useQuery({
    queryKey: ['invoices', invoiceId, 'payments'],
    queryFn: async () => {
      const response = await api.get<PaginatedResponse<Payment>>(`/payments?invoice_id=${invoiceId}`)
      return response.data
    },
    enabled: !!invoiceId,
  })
}

export function usePaymentsList(filters: { invoice_id?: string; method?: string; date_from?: string; date_to?: string; page?: number; per_page?: number } = {}) {
  return useQuery({
    queryKey: ['payments', filters],
    queryFn: async () => {
      const params = new URLSearchParams()
      Object.entries(filters).forEach(([key, value]) => {
        if (value !== undefined && value !== '') {
          params.append(key, String(value))
        }
      })
      const response = await api.get<PaginatedResponse<Payment>>(`/payments?${params.toString()}`)
      return response.data
    },
  })
}

export function useServiceCatalog(search?: string) {
  return useQuery({
    queryKey: ['service-catalog', search],
    queryFn: async () => {
      const params = new URLSearchParams()
      if (search) params.append('search', search)
      const response = await api.get<PaginatedResponse<ServiceCatalogItem>>(`/service-catalog?${params.toString()}`)
      return response.data
    },
  })
}

export function useRevenueStats(filters: { date_from?: string; date_to?: string } = {}) {
  return useQuery({
    queryKey: ['revenue', 'stats', filters],
    queryFn: async () => {
      const params = new URLSearchParams()
      Object.entries(filters).forEach(([key, value]) => {
        if (value !== undefined && value !== '') {
          params.append(key, String(value))
        }
      })
      const response = await api.get(`/invoices/stats?${params.toString()}`)
      return response.data.data
    },
  })
}
