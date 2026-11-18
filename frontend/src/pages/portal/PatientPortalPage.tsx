import { useState } from 'react'
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/Card'
import Button from '@/components/ui/Button'
import { Badge } from '@/components/ui/Badge'
import { EmptyState } from '@/components/ui/EmptyState'
import { formatDate } from '@/lib/utils'
import {
  User, FileText, Pill, TestTube, Download, Calendar,
  Clock, CheckCircle, AlertCircle, Plus
} from 'lucide-react'

type PortalTab = 'overview' | 'history' | 'prescriptions' | 'lab' | 'certificates' | 'appointments'

export default function PatientPortalPage() {
  const [activeTab, setActiveTab] = useState<PortalTab>('overview')

  const tabs: { key: PortalTab; label: string; icon: React.ReactNode }[] = [
    { key: 'overview', label: 'Overview', icon: <User className="h-4 w-4" /> },
    { key: 'history', label: 'History', icon: <FileText className="h-4 w-4" /> },
    { key: 'prescriptions', label: 'Prescriptions', icon: <Pill className="h-4 w-4" /> },
    { key: 'lab', label: 'Laboratory', icon: <TestTube className="h-4 w-4" /> },
    { key: 'certificates', label: 'Certificates', icon: <FileText className="h-4 w-4" /> },
    { key: 'appointments', label: 'Appointments', icon: <Calendar className="h-4 w-4" /> },
  ]

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-bold text-gray-900">Patient Portal</h1>
        <p className="text-sm text-gray-500">Access your medical information</p>
      </div>

      {/* Tabs */}
      <div className="border-b border-gray-200">
        <nav className="flex overflow-x-auto -mb-px gap-1">
          {tabs.map((tab) => (
            <button
              key={tab.key}
              onClick={() => setActiveTab(tab.key)}
              className={`flex items-center gap-2 px-4 py-3 text-sm font-medium border-b-2 transition-colors whitespace-nowrap ${
                activeTab === tab.key
                  ? 'border-primary-600 text-primary-600'
                  : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
              }`}
            >
              {tab.icon}
              {tab.label}
            </button>
          ))}
        </nav>
      </div>

      {/* Overview Tab */}
      {activeTab === 'overview' && (
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <Card variant="elevated">
            <CardContent className="py-6 text-center">
              <FileText className="h-8 w-8 text-primary-600 mx-auto mb-3" />
              <p className="text-2xl font-bold text-gray-900">3</p>
              <p className="text-sm text-gray-500">Consultations</p>
            </CardContent>
          </Card>
          <Card variant="elevated">
            <CardContent className="py-6 text-center">
              <Pill className="h-8 w-8 text-green-600 mx-auto mb-3" />
              <p className="text-2xl font-bold text-gray-900">5</p>
              <p className="text-sm text-gray-500">Active Prescriptions</p>
            </CardContent>
          </Card>
          <Card variant="elevated">
            <CardContent className="py-6 text-center">
              <TestTube className="h-8 w-8 text-blue-600 mx-auto mb-3" />
              <p className="text-2xl font-bold text-gray-900">2</p>
              <p className="text-sm text-gray-500">Pending Results</p>
            </CardContent>
          </Card>
          <Card variant="elevated">
            <CardContent className="py-6 text-center">
              <Calendar className="h-8 w-8 text-yellow-600 mx-auto mb-3" />
              <p className="text-2xl font-bold text-gray-900">1</p>
              <p className="text-sm text-gray-500">Next Appointment</p>
            </CardContent>
          </Card>
        </div>
      )}

      {/* History Tab */}
      {activeTab === 'history' && (
        <Card variant="elevated">
          <CardHeader>
            <CardTitle>Medical History</CardTitle>
          </CardHeader>
          <CardContent>
            <EmptyState
              title="Medical history"
              description="Your consultations and medical progress notes will appear here"
              icon={<FileText className="h-8 w-8 text-gray-400" />}
            />
          </CardContent>
        </Card>
      )}

      {/* Prescriptions Tab */}
      {activeTab === 'prescriptions' && (
        <Card variant="elevated">
          <CardHeader>
            <div className="flex items-center justify-between">
              <CardTitle>My Prescriptions</CardTitle>
              <Button variant="outline" size="sm" leftIcon={<Download className="h-4 w-4" />}>
                Download
              </Button>
            </div>
          </CardHeader>
          <CardContent>
            <EmptyState
              title="No prescriptions"
              description="Your prescriptions will appear here"
              icon={<Pill className="h-8 w-8 text-gray-400" />}
            />
          </CardContent>
        </Card>
      )}

      {/* Lab Tab */}
      {activeTab === 'lab' && (
        <Card variant="elevated">
          <CardHeader>
            <div className="flex items-center justify-between">
              <CardTitle>Lab Results</CardTitle>
              <Button variant="outline" size="sm" leftIcon={<Download className="h-4 w-4" />}>
                Download
              </Button>
            </div>
          </CardHeader>
          <CardContent>
            <EmptyState
              title="No results"
              description="Your lab results will appear here"
              icon={<TestTube className="h-8 w-8 text-gray-400" />}
            />
          </CardContent>
        </Card>
      )}

      {/* Certificates Tab */}
      {activeTab === 'certificates' && (
        <Card variant="elevated">
          <CardHeader>
            <CardTitle>Medical Certificates</CardTitle>
          </CardHeader>
          <CardContent>
            <EmptyState
              title="No certificates"
              description="Your medical certificates will appear here"
              icon={<FileText className="h-8 w-8 text-gray-400" />}
            />
          </CardContent>
        </Card>
      )}

      {/* Appointments Tab */}
      {activeTab === 'appointments' && (
        <div className="space-y-4">
          <div className="flex justify-end">
            <Button leftIcon={<Plus className="h-4 w-4" />}>
              Schedule Appointment
            </Button>
          </div>
          <Card variant="elevated">
            <CardHeader>
              <CardTitle>My Appointments</CardTitle>
            </CardHeader>
            <CardContent>
              <EmptyState
                title="No scheduled appointments"
                description="Schedule a medical consultation"
                icon={<Calendar className="h-8 w-8 text-gray-400" />}
                action={
                  <Button leftIcon={<Plus className="h-4 w-4" />}>
                    Schedule Appointment
                  </Button>
                }
              />
            </CardContent>
          </Card>
        </div>
      )}
    </div>
  )
}
