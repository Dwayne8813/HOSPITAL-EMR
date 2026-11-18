import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { useBedMap, useBuildings, useRooms } from '@/hooks/useHospitalization'
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/Card'
import Button from '@/components/ui/Button'
import { Badge } from '@/components/ui/Badge'
import { PageLoadingSpinner } from '@/components/ui/LoadingSpinner'
import { Bed, Building2, Users, AlertTriangle, CheckCircle } from 'lucide-react'

export default function BedMapPage() {
  const navigate = useNavigate()
  const [selectedBuilding, setSelectedBuilding] = useState('')
  const [selectedFloor, setSelectedFloor] = useState('')
  const [selectedBed, setSelectedBed] = useState<any>(null)

  const { data: bedMap, isLoading } = useBedMap()
  const { data: buildings } = useBuildings()

  if (isLoading) return <PageLoadingSpinner />

  const bedStatusColors: Record<string, string> = {
    available: 'bg-green-100 border-green-400 hover:bg-green-200',
    occupied: 'bg-red-100 border-red-400',
    reserved: 'bg-yellow-100 border-yellow-400',
    maintenance: 'bg-gray-200 border-gray-400',
  }

  const bedStatusIcons: Record<string, string> = {
    available: '✓',
    occupied: '●',
    reserved: '◉',
    maintenance: '⚙',
  }

  const stats = {
    total: 50,
    available: 18,
    occupied: 25,
    reserved: 4,
    maintenance: 3,
  }

  return (
    <div className="space-y-6">
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Bed Map</h1>
          <p className="text-sm text-gray-500">View of hospital bed status</p>
        </div>
        <Button onClick={() => navigate('/hospitalization/list')}>
          View Hospitalized Patients
        </Button>
      </div>

      {/* Stats */}
      <div className="grid grid-cols-2 sm:grid-cols-5 gap-4">
        <Card variant="elevated" className="text-center">
          <CardContent className="py-4">
            <p className="text-2xl font-bold text-gray-900">{stats.total}</p>
            <p className="text-sm text-gray-500">Total</p>
          </CardContent>
        </Card>
        <Card variant="elevated" className="text-center">
          <CardContent className="py-4">
            <p className="text-2xl font-bold text-green-600">{stats.available}</p>
            <p className="text-sm text-gray-500">Available</p>
          </CardContent>
        </Card>
        <Card variant="elevated" className="text-center">
          <CardContent className="py-4">
            <p className="text-2xl font-bold text-red-600">{stats.occupied}</p>
            <p className="text-sm text-gray-500">Occupied</p>
          </CardContent>
        </Card>
        <Card variant="elevated" className="text-center">
          <CardContent className="py-4">
            <p className="text-2xl font-bold text-yellow-600">{stats.reserved}</p>
            <p className="text-sm text-gray-500">Reserved</p>
          </CardContent>
        </Card>
        <Card variant="elevated" className="text-center">
          <CardContent className="py-4">
            <p className="text-2xl font-bold text-gray-500">{stats.maintenance}</p>
            <p className="text-sm text-gray-500">Maintenance</p>
          </CardContent>
        </Card>
      </div>

      {/* Filters */}
      <Card variant="elevated">
        <CardHeader>
          <div className="flex flex-col sm:flex-row gap-3">
            <select
              value={selectedBuilding}
              onChange={(e) => setSelectedBuilding(e.target.value)}
              className="block w-full sm:w-48 rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20"
            >
              <option value="">All Buildings</option>
              <option value="main">Main Building</option>
              <option value="annex">Annex</option>
            </select>
            <select
              value={selectedFloor}
              onChange={(e) => setSelectedFloor(e.target.value)}
              className="block w-full sm:w-36 rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20"
            >
              <option value="">All Floors</option>
              <option value="1">Floor 1</option>
              <option value="2">Floor 2</option>
              <option value="3">Floor 3</option>
            </select>
          </div>
        </CardHeader>
      </Card>

      {/* Bed Grid */}
      <Card variant="elevated">
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <Bed className="h-5 w-5 text-primary-600" />
            Floor Plan - Floor 1
          </CardTitle>
        </CardHeader>
        <CardContent>
          <div className="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-8 lg:grid-cols-10 gap-3">
            {Array.from({ length: 30 }, (_, i) => {
              const statuses = ['available', 'occupied', 'reserved', 'maintenance']
              const status = statuses[i % 4]
              return (
                <div
                  key={i}
                  onClick={() => setSelectedBed({ number: `${i + 101}`, status })}
                  className={`aspect-square rounded-lg border-2 flex flex-col items-center justify-center cursor-pointer transition-all ${bedStatusColors[status]}`}
                >
                  <span className="text-lg font-bold">{i + 101}</span>
                  <span className="text-xs">{bedStatusIcons[status]}</span>
                </div>
              )
            })}
          </div>
          <div className="flex flex-wrap gap-4 mt-6 text-xs">
            <div className="flex items-center gap-2">
              <div className="w-4 h-4 rounded bg-green-100 border-2 border-green-400"></div>
              <span>Available</span>
            </div>
            <div className="flex items-center gap-2">
              <div className="w-4 h-4 rounded bg-red-100 border-2 border-red-400"></div>
              <span>Occupied</span>
            </div>
            <div className="flex items-center gap-2">
              <div className="w-4 h-4 rounded bg-yellow-100 border-2 border-yellow-400"></div>
              <span>Reserved</span>
            </div>
            <div className="flex items-center gap-2">
              <div className="w-4 h-4 rounded bg-gray-200 border-2 border-gray-400"></div>
              <span>Maintenance</span>
            </div>
          </div>
        </CardContent>
      </Card>

      {/* Bed Detail Modal */}
      {selectedBed && (
        <Card variant="elevated">
          <CardHeader>
            <div className="flex items-center justify-between">
              <CardTitle>Bed {selectedBed.number}</CardTitle>
              <Button variant="ghost" size="sm" onClick={() => setSelectedBed(null)}>×</Button>
            </div>
          </CardHeader>
          <CardContent>
            <div className="flex items-center gap-4">
              <Badge variant={selectedBed.status === 'available' ? 'success' : selectedBed.status === 'occupied' ? 'danger' : selectedBed.status === 'reserved' ? 'warning' : 'default'}>
                {selectedBed.status === 'available' ? 'Available' :
                 selectedBed.status === 'occupied' ? 'Occupied' :
                 selectedBed.status === 'reserved' ? 'Reserved' : 'Maintenance'}
              </Badge>
              {selectedBed.status === 'available' && (
                <Button size="sm" onClick={() => navigate('/hospitalization/list')}>
                  Hospitalize Patient
                </Button>
              )}
            </div>
          </CardContent>
        </Card>
      )}

      {/* Legend */}
      <div className="flex flex-wrap gap-4 text-xs text-gray-600">
        <div className="flex items-center gap-2">
          <div className="w-4 h-4 rounded bg-green-200 border-2 border-green-500"></div>
          <span>18 Available</span>
        </div>
        <div className="flex items-center gap-2">
          <div className="w-4 h-4 rounded bg-red-200 border-2 border-red-500"></div>
          <span>25 Occupied</span>
        </div>
        <div className="flex items-center gap-2">
          <div className="w-4 h-4 rounded bg-yellow-200 border-2 border-yellow-500"></div>
          <span>4 Reserved</span>
        </div>
        <div className="flex items-center gap-2">
          <div className="w-4 h-4 rounded bg-gray-300 border-2 border-gray-500"></div>
          <span>3 in Maintenance</span>
        </div>
      </div>
    </div>
  )
}
