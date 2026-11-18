import { useState } from 'react'
import { useParams } from 'react-router-dom'
import { useConsultation, useSoapNotes, useCreateSoapNote } from '@/hooks/useMedicalRecords'
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/Card'
import Button from '@/components/ui/Button'
import { PageLoadingSpinner } from '@/components/ui/LoadingSpinner'
import { EmptyState } from '@/components/ui/EmptyState'
import { formatDate } from '@/lib/utils'
import { Plus, Save, Clock, User } from 'lucide-react'

export default function EvolutionPage() {
  const { id: consultationId } = useParams<{ id: string }>()
  const { data: consultation, isLoading: consultationLoading } = useConsultation(consultationId!)
  const { data: soapNote, isLoading: notesLoading } = useSoapNotes(consultation?.medical_record_id || '')
  const createSoapNote = useCreateSoapNote()

  const [showForm, setShowForm] = useState(false)
  const [soap, setSoap] = useState({ subjective: '', objective: '', assessment: '', plan: '' })

  const handleSave = async () => {
    if (!consultation) return
    try {
      await createSoapNote.mutateAsync({
        medical_record_id: consultation.medical_record_id,
        subjective: soap.subjective,
        objective: soap.objective,
        assessment: soap.assessment,
        plan: soap.plan,
      })
      setSoap({ subjective: '', objective: '', assessment: '', plan: '' })
      setShowForm(false)
    } catch (error) {
      console.error('Error saving evolution:', error)
    }
  }

  if (consultationLoading) return <PageLoadingSpinner />

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Clinical Progress Note</h1>
          <p className="text-sm text-gray-500">
            Consultation: {consultation?.consultation_number}
          </p>
        </div>
        <Button leftIcon={<Plus className="h-4 w-4" />} onClick={() => setShowForm(!showForm)}>
          New Progress Note
        </Button>
      </div>

      {showForm && (
        <Card variant="elevated">
          <CardHeader>
            <CardTitle>New Progress Note</CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1.5">S - Subjective</label>
              <textarea
                value={soap.subjective}
                onChange={(e) => setSoap({ ...soap, subjective: e.target.value })}
                className="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20 resize-y min-h-[80px]"
                placeholder="Symptoms reported by the patient..."
              />
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1.5">O - Objective</label>
              <textarea
                value={soap.objective}
                onChange={(e) => setSoap({ ...soap, objective: e.target.value })}
                className="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20 resize-y min-h-[80px]"
                placeholder="Physical exam findings..."
              />
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1.5">A - Assessment</label>
              <textarea
                value={soap.assessment}
                onChange={(e) => setSoap({ ...soap, assessment: e.target.value })}
                className="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20 resize-y min-h-[80px]"
                placeholder="Progress and diagnosis..."
              />
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1.5">P - Plan</label>
              <textarea
                value={soap.plan}
                onChange={(e) => setSoap({ ...soap, plan: e.target.value })}
                className="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20 resize-y min-h-[80px]"
                placeholder="Treatment plan..."
              />
            </div>
            <div className="flex justify-end gap-3">
              <Button variant="outline" onClick={() => setShowForm(false)}>Cancel</Button>
              <Button leftIcon={<Save className="h-4 w-4" />} onClick={handleSave} isLoading={createSoapNote.isPending}>
                Save Progress Note
              </Button>
            </div>
          </CardContent>
        </Card>
      )}

      {/* Evolution Timeline */}
      {notesLoading ? (
        <PageLoadingSpinner />
      ) : soapNote ? (
        <Card key={soapNote.id} variant="bordered">
          <CardContent className="py-4">
            <div className="flex items-center gap-2 mb-3">
              <Clock className="h-4 w-4 text-gray-400" />
              <span className="text-sm text-gray-500">{formatDate(soapNote.created_at)}</span>
            </div>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div className="p-3 bg-blue-50 rounded-lg">
                <p className="text-xs font-medium text-blue-700 mb-1">Subjective</p>
                <p className="text-sm text-gray-700">{soapNote.subjective}</p>
              </div>
              <div className="p-3 bg-green-50 rounded-lg">
                <p className="text-xs font-medium text-green-700 mb-1">Objective</p>
                <p className="text-sm text-gray-700">{soapNote.objective}</p>
              </div>
              <div className="p-3 bg-yellow-50 rounded-lg">
                <p className="text-xs font-medium text-yellow-700 mb-1">Assessment</p>
                <p className="text-sm text-gray-700">{soapNote.assessment}</p>
              </div>
              <div className="p-3 bg-purple-50 rounded-lg">
                <p className="text-xs font-medium text-purple-700 mb-1">Plan</p>
                <p className="text-sm text-gray-700">{soapNote.plan}</p>
              </div>
            </div>
          </CardContent>
        </Card>
      ) : (
        <EmptyState
          title="No progress notes"
          description="Record the first progress note for this consultation"
        />
      )}
    </div>
  )
}
