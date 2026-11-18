import { useState } from 'react'
import { useNavigate, useParams } from 'react-router-dom'
import { useHospitalization } from '@/hooks/useHospitalization'
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/Card'
import { StatusBadge, Badge } from '@/components/ui/Badge'
import Button from '@/components/ui/Button'
import { PageLoadingSpinner } from '@/components/ui/LoadingSpinner'
import { EmptyState } from '@/components/ui/EmptyState'
import { formatDate } from '@/lib/utils'
import { ArrowLeft, Bed, User, Calendar, FileText, Activity } from 'lucide-react'

export default function HospitalizationDetailPage() {
  const navigate = useNavigate()
  const { id } = useParams<{ id: string }>()
  const { data: hospitalization, isLoading } = useHospitalization(id!)

  if (isLoading) return <PageLoadingSpinner />
  if (!hospitalization) return <EmptyState title="Hospitalization not found" />

  return (
    <div className="space-y-6">
      <div className="flex items-center gap-4">
        <Button variant="ghost" size="sm" onClick={() => navigate(-1)}>
          <ArrowLeft className="h-4 w-4" />
        </Button>
        <div className="flex-1">
          <h1 className="text-2xl font-bold text-gray-900">
            Hospitalization - {hospitalization.patient?.first_name} {hospitalization.patient?.last_name}
          </h1>
          <p className="text-sm text-gray-500">
            Admitted on {formatDate(hospitalization.admission_date)}
          </p>
        </div>
        <StatusBadge status={hospitalization.status} />
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* Patient Info */}
        <Card variant="elevated">
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <User className="h-5 w-5 text-primary-600" />
              Patient
            </CardTitle>
          </CardHeader>
          <CardContent className="space-y-3">
            <div>
              <p className="text-sm text-gray-500">Name</p>
              <p className="font-medium">{hospitalization.patient?.first_name} {hospitalization.patient?.last_name}</p>
            </div>
            <div>
              <p className="text-sm text-gray-500">Document</p>
              <p className="font-medium">{hospitalization.patient?.document_number}</p>
            </div>
            <div>
              <p className="text-sm text-gray-500">Phone</p>
              <p className="font-medium">{hospitalization.patient?.phone || 'Not recorded'}</p>
            </div>
          </CardContent>
        </Card>

        {/* Admission Details */}
        <Card variant="elevated">
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <Bed className="h-5 w-5 text-primary-600" />
              Admission Details
            </CardTitle>
          </CardHeader>
          <CardContent className="space-y-3">
            <div>
              <p className="text-sm text-gray-500">Bed</p>
              <p className="font-medium">{hospitalization.bed?.number || 'Unassigned'}</p>
            </div>
            <div>
              <p className="text-sm text-gray-500">Department</p>
              <p className="font-medium">{hospitalization.department || 'Not specified'}</p>
            </div>
            <div>
              <p className="text-sm text-gray-500">Doctor</p>
              <p className="font-medium">Dr. {hospitalization.doctor?.first_name} {hospitalization.doctor?.last_name}</p>
            </div>
            <div>
              <p className="text-sm text-gray-500">Admission Date</p>
              <p className="font-medium">{formatDate(hospitalization.admission_date)}</p>
            </div>
            {hospitalization.discharge_date && (
              <div>
                <p className="text-sm text-gray-500">Discharge Date</p>
                <p className="font-medium">{formatDate(hospitalization.discharge_date)}</p>
              </div>
            )}
          </CardContent>
        </Card>

        {/* Clinical Info */}
        <Card variant="elevated">
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <FileText className="h-5 w-5 text-primary-600" />
              Clinical Information
            </CardTitle>
          </CardHeader>
          <CardContent className="space-y-3">
            <div>
              <p className="text-sm text-gray-500">Admission Reason</p>
              <p className="font-medium">{hospitalization.reason}</p>
            </div>
            <div>
              <p className="text-sm text-gray-500">Diagnosis</p>
              <p className="font-medium">{hospitalization.diagnosis || 'Not recorded'}</p>
            </div>
            <div>
              <p className="text-sm text-gray-500">Notes</p>
              <p className="font-medium">{hospitalization.notes || 'None'}</p>
            </div>
          </CardContent>
        </Card>
      </div>

      {/* Actions */}
      <Card variant="elevated">
        <CardHeader>
          <CardTitle>Actions</CardTitle>
        </CardHeader>
        <CardContent>
          <div className="flex flex-wrap gap-3">
            <Button variant="outline">View Progress Notes</Button>
            <Button variant="outline">New Progress Note</Button>
            <Button variant="outline">Add Order</Button>
            {hospitalization.status === 'active' && (
              <Button variant="success">Discharge Patient</Button>
            )}
          </div>
        </CardContent>
      </Card>
    </div>
  )
}
