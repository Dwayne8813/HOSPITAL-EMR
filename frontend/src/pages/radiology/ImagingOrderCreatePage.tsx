import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { usePatientSearch } from '@/hooks/usePatients'
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/Card'
import Input from '@/components/ui/Input'
import TextArea from '@/components/ui/TextArea'
import Button from '@/components/ui/Button'
import Select from '@/components/ui/Select'
import { ArrowLeft, Save, ScanLine } from 'lucide-react'

export default function ImagingOrderCreatePage() {
  const navigate = useNavigate()
  const [patientSearch, setPatientSearch] = useState('')
  const [selectedPatient, setSelectedPatient] = useState<any>(null)
  const [examType, setExamType] = useState('')
  const [bodyArea, setBodyArea] = useState('')
  const [clinicalInfo, setClinicalInfo] = useState('')
  const [priority, setPriority] = useState('routine')

  const { data: searchResults } = usePatientSearch(patientSearch)

  const examTypes = [
    'X-ray', 'CT Scan', 'MRI',
    'Ultrasound', 'Mammography', 'Densitometry', 'Fluoroscopy',
  ]

  const bodyAreas = [
    'Head', 'Neck', 'Chest', 'Abdomen', 'Pelvis',
    'Spine', 'Upper Limb', 'Lower Limb',
    'Joints', 'Paranasal Sinuses',
  ]

  const handleSave = () => {
    console.log('Saving imaging order:', { selectedPatient, examType, bodyArea, clinicalInfo, priority })
    navigate('/radiology')
  }

  return (
    <div className="space-y-6">
      <div className="flex items-center gap-4">
        <Button variant="ghost" size="sm" onClick={() => navigate(-1)}>
          <ArrowLeft className="h-4 w-4" />
        </Button>
        <div>
          <h1 className="text-2xl font-bold text-gray-900">New Imaging Order</h1>
          <p className="text-sm text-gray-500">Request an imaging study</p>
        </div>
      </div>

      <Card variant="elevated">
        <CardHeader>
          <CardTitle>Patient</CardTitle>
        </CardHeader>
        <CardContent>
          <div className="relative">
            <Input
              placeholder="Search patient..."
              value={patientSearch}
              onChange={(e) => setPatientSearch(e.target.value)}
            />
            {searchResults && searchResults.length > 0 && !selectedPatient && (
              <div className="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-auto">
                {searchResults.map((patient) => (
                  <button
                    key={patient.id}
                    onClick={() => { setSelectedPatient(patient); setPatientSearch('') }}
                    className="w-full px-4 py-3 text-left hover:bg-gray-50 border-b last:border-0"
                  >
                    <p className="font-medium text-gray-900">{patient.first_name} {patient.last_name}</p>
                    <p className="text-sm text-gray-500">ID: {patient.document_number}</p>
                  </button>
                ))}
              </div>
            )}
          </div>
          {selectedPatient && (
            <div className="mt-3 p-3 bg-primary-50 rounded-lg flex items-center justify-between">
              <div>
                <p className="font-medium text-primary-900">{selectedPatient.first_name} {selectedPatient.last_name}</p>
                <p className="text-sm text-primary-700">ID: {selectedPatient.document_number}</p>
              </div>
              <Button variant="ghost" size="sm" onClick={() => setSelectedPatient(null)}>Change</Button>
            </div>
          )}
        </CardContent>
      </Card>

      <Card variant="elevated">
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <ScanLine className="h-5 w-5 text-primary-600" />
            Imaging Study
          </CardTitle>
        </CardHeader>
        <CardContent className="space-y-4">
          <Select
            label="Study Type"
            required
            options={examTypes.map(e => ({ value: e, label: e }))}
            value={examType}
            onChange={(e) => setExamType(e.target.value)}
          />
          <Select
            label="Body Area"
            required
            options={bodyAreas.map(a => ({ value: a, label: a }))}
            value={bodyArea}
            onChange={(e) => setBodyArea(e.target.value)}
          />
          <Select
            label="Priority"
            options={[
              { value: 'routine', label: 'Routine' },
              { value: 'urgent', label: 'Urgent' },
            ]}
            value={priority}
            onChange={(e) => setPriority(e.target.value)}
          />
          <TextArea
            label="Clinical Information"
            value={clinicalInfo}
            onChange={(e) => setClinicalInfo(e.target.value)}
            placeholder="Clinical indication and relevant findings..."
            rows={3}
          />
        </CardContent>
      </Card>

      <div className="flex justify-end gap-3">
        <Button variant="outline" onClick={() => navigate(-1)}>Cancel</Button>
        <Button leftIcon={<Save className="h-4 w-4" />} onClick={handleSave} disabled={!selectedPatient || !examType}>
          Create Order
        </Button>
      </div>
    </div>
  )
}
