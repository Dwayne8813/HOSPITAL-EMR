import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import api from '@/lib/api'
import type { Hospitalization, Bed, Room, Building, PaginatedResponse, ApiResponse } from '@/types'

interface HospitalizationFilters {
  patient_id?: string
  status?: string
  department?: string
  page?: number
  per_page?: number
}

export function useHospitalizations(filters: HospitalizationFilters = {}) {
  return useQuery({
    queryKey: ['hospitalizations', filters],
    queryFn: async () => {
      const params = new URLSearchParams()
      Object.entries(filters).forEach(([key, value]) => {
        if (value !== undefined && value !== '') {
          params.append(key, String(value))
        }
      })
      const response = await api.get<PaginatedResponse<Hospitalization>>(`/hospitalizations?${params.toString()}`)
      return response.data
    },
  })
}

export function useHospitalization(id: string) {
  return useQuery({
    queryKey: ['hospitalizations', id],
    queryFn: async () => {
      const response = await api.get<ApiResponse<Hospitalization>>(`/hospitalizations/${id}`)
      return response.data.data
    },
    enabled: !!id,
  })
}

export function useCreateHospitalization() {
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: async (data: Partial<Hospitalization>) => {
      const response = await api.post<ApiResponse<Hospitalization>>('/hospitalizations', data)
      return response.data.data
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['hospitalizations'] })
      queryClient.invalidateQueries({ queryKey: ['beds'] })
    },
  })
}

export function useDischargeHospitalization() {
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: async ({ id, data }: { id: string; data: { discharge_date: string; diagnosis?: string; notes?: string } }) => {
      const response = await api.post<ApiResponse<Hospitalization>>(`/hospitalizations/${id}/discharge`, data)
      return response.data.data
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['hospitalizations'] })
      queryClient.invalidateQueries({ queryKey: ['beds'] })
    },
  })
}

export function useBuildings() {
  return useQuery({
    queryKey: ['buildings'],
    queryFn: async () => {
      const response = await api.get<PaginatedResponse<Building>>('/buildings')
      return response.data
    },
  })
}

export function useRooms(buildingId?: string) {
  return useQuery({
    queryKey: ['rooms', buildingId],
    queryFn: async () => {
      const params = buildingId ? `?building_id=${buildingId}` : ''
      const response = await api.get<PaginatedResponse<Room>>(`/rooms${params}`)
      return response.data
    },
  })
}

export function useBeds(filters: { room_id?: string; status?: string; type?: string } = {}) {
  return useQuery({
    queryKey: ['beds', filters],
    queryFn: async () => {
      const params = new URLSearchParams()
      Object.entries(filters).forEach(([key, value]) => {
        if (value !== undefined && value !== '') {
          params.append(key, String(value))
        }
      })
      const response = await api.get<PaginatedResponse<Bed>>(`/beds?${params.toString()}`)
      return response.data
    },
  })
}

export function useBedMap() {
  return useQuery({
    queryKey: ['beds', 'map'],
    queryFn: async () => {
      const response = await api.get('/beds/map')
      return response.data.data
    },
  })
}

export function useUpdateBedStatus() {
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: async ({ id, status }: { id: string; status: string }) => {
      const response = await api.patch<ApiResponse<Bed>>(`/beds/${id}/status`, { status })
      return response.data.data
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['beds'] })
    },
  })
}
