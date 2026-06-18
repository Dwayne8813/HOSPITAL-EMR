import { useQuery } from '@tanstack/react-query'
import api from '@/lib/api'

export interface Institution {
  id: number
  name: string
  nit: string
  address: string
  city: string
  phone: string
  email: string
  logo: string | null
  mission: string | null
  vision: string | null
  primary_color: string | null
  secondary_color: string | null
}

export function useInstitution() {
  return useQuery({
    queryKey: ['institution'],
    queryFn: async () => {
      const response = await api.get('/institution')
      return response.data.data as Institution
    },
    staleTime: 5 * 60 * 1000,
  })
}
