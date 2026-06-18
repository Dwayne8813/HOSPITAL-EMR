import { useQuery } from '@tanstack/react-query'
import api from '@/lib/api'
import type { DashboardStats, ApiResponse } from '@/types'

export function useDashboardStats() {
  return useQuery({
    queryKey: ['dashboard', 'stats'],
    queryFn: async () => {
      const response = await api.get<ApiResponse<DashboardStats>>('/dashboard')
      return response.data.data
    },
    refetchInterval: 5 * 60 * 1000,
  })
}

export function useDashboardCharts() {
  return useQuery({
    queryKey: ['dashboard', 'charts'],
    queryFn: async () => {
      const response = await api.get('/dashboard/charts')
      return response.data.data
    },
    refetchInterval: 5 * 60 * 1000,
  })
}

export function useRecentActivity() {
  return useQuery({
    queryKey: ['dashboard', 'activity'],
    queryFn: async () => {
      const response = await api.get('/dashboard/activity')
      return response.data.data
    },
    refetchInterval: 2 * 60 * 1000,
  })
}
