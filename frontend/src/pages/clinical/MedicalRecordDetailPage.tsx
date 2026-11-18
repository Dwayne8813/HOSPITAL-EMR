import { useNavigate, useParams } from 'react-router-dom'
import { useMedicalRecord } from '@/hooks/useMedicalRecords'
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/Card'
import { StatusBadge } from '@/components/ui/Badge'
import Button from '@/components/ui/Button'
import { PageLoadingSpinner } from '@/components/ui/LoadingSpinner'
import { EmptyState } from '@/components/ui/EmptyState'
import { formatDate } from '@/lib/utils'
import { ArrowLeft, Plus, User } from 'lucide-react'

export default function MedicalRecordDetailPage() {
  const navigate = useNavigate()
  const { id } = useParams<{ id: string }>()
  const { data: record, isLoading } = useMedicalRecord(id!)

  if (isLoading) return <PageLoadingSpinner />
  if (!record) return <EmptyState title="Record not found" />

  const consultation = record.consultation

  return (
    <div className="space-y-6">
      <div className="flex items-center gap-4">
        <Button variant="ghost" size="sm" onClick={() => navigate(-1)}>
          <ArrowLeft className="h-4 w-4" />
        </Button>
        <div className="flex-1">
          <h1 className="text-2xl font-bold text-gray-900">
            Record {record.record_number}
          </h1>
          <p className="text-sm text-gray-500">
            Patient: {record.patient?.first_name} {record.patient?.last_name}
          </p>
        </div>
        <StatusBadge status={record.status} />
      </div>

      {/* Patient Info */}
      <Card variant="elevated">
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <User className="h-5 w-5 text-primary-600" />
            Patient Information
          </CardTitle>
        </CardHeader>
        <CardContent>
          <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <p className="text-sm text-gray-500">Name</p>
              <p className="font-medium">{record.patient?.first_name} {record.patient?.last_name}</p>
            </div>
            <div>
              <p className="text-sm text-gray-500">Document</p>
              <p className="font-medium">{record.patient?.document_type}: {record.patient?.document_number}</p>
            </div>
            <div>
              <p className="text-sm text-gray-500">Phone</p>
              <p className="font-medium">{record.patient?.phone || 'Not recorded'}</p>
            </div>
          </div>
        </CardContent>
      </Card>

      {/* Consultation */}
      <Card variant="elevated">
        <CardHeader>
          <div className="flex items-center justify-between">
            <CardTitle>Consultation</CardTitle>
            {!consultation && (
              <Button size="sm" leftIcon={<Plus className="h-4 w-4" />} onClick={() => navigate(`/clinical/records/${id}/consultation`)}>
                New Consultation
              </Button>
            )}
          </div>
        </CardHeader>
        <CardContent>
          {consultation ? (
            <div
              className="p-4 bg-gray-50 rounded-lg hover:bg-gray-100 cursor-pointer transition-colors"
              onClick={() => navigate(`/clinical/consultation/${consultation.id}`)}
            >
              <div className="flex items-center justify-between">
                <div>
                  <p className="font-medium text-gray-900">{consultation.chief_complaint || 'Medical consultation'}</p>
                  <p className="text-sm text-gray-500">No. {consultation.consultation_number}</p>
                </div>
                <div className="text-right">
                  <p className="text-sm text-gray-900">{formatDate(consultation.date || consultation.created_at)}</p>
                  <StatusBadge status={consultation.status} />
                </div>
              </div>
            </div>
          ) : (
            <EmptyState
              title="No consultations"
              description="Start a new consultation for this patient"
              action={
                <Button leftIcon={<Plus className="h-4 w-4" />} onClick={() => navigate(`/clinical/records/${id}/consultation`)}>
                  New Consultation
                </Button>
              }
            />
          )}
        </CardContent>
      </Card>

      {/* SOAP Notes */}
      {record.soapNote && (
        <Card variant="elevated">
          <CardHeader>
            <CardTitle>SOAP Note</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div className="p-3 bg-blue-50 rounded-lg">
                <p className="text-xs font-medium text-blue-700 mb-1">Subjective</p>
                <p className="text-sm text-gray-700">{record.soapNote.subjective}</p>
              </div>
              <div className="p-3 bg-green-50 rounded-lg">
                <p className="text-xs font-medium text-green-700 mb-1">Objective</p>
                <p className="text-sm text-gray-700">{record.soapNote.objective}</p>
              </div>
              <div className="p-3 bg-yellow-50 rounded-lg">
                <p className="text-xs font-medium text-yellow-700 mb-1">Assessment</p>
                <p className="text-sm text-gray-700">{record.soapNote.assessment}</p>
              </div>
              <div className="p-3 bg-purple-50 rounded-lg">
                <p className="text-xs font-medium text-purple-700 mb-1">Plan</p>
                <p className="text-sm text-gray-700">{record.soapNote.plan}</p>
              </div>
            </div>
          </CardContent>
        </Card>
      )}

      {/* Diagnoses */}
      {record.diagnoses && record.diagnoses.length > 0 && (
        <Card variant="elevated">
          <CardHeader>
            <CardTitle>Diagnoses</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="space-y-2">
              {record.diagnoses.map((d: any) => (
                <div key={d.id} className="p-3 bg-gray-50 rounded-lg">
                  <span className="font-mono text-primary-700 mr-2">{d.description}</span>
                  <StatusBadge status={d.diagnosis_type || 'primary'} />
                </div>
              ))}
            </div>
          </CardContent>
        </Card>
      )}
    </div>
  )
}
