import { useState } from 'react'
import { useNavigate, useParams } from 'react-router-dom'
import { useConsultation, useCreateSoapNote, useCreateVitalSigns, useCreateDiagnosis, useSearchCie10 } from '@/hooks/useMedicalRecords'
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/Card'
import Input from '@/components/ui/Input'
import TextArea from '@/components/ui/TextArea'
import Button from '@/components/ui/Button'
import { Badge } from '@/components/ui/Badge'
import { PageLoadingSpinner } from '@/components/ui/LoadingSpinner'
import { calculateIMC, getIMCCategory, getIMCColor } from '@/lib/utils'
import {
  ArrowLeft, Save, Activity, Stethoscope, FileText,
  ClipboardList, Heart, Thermometer, Wind
} from 'lucide-react'

interface VitalSigns {
  weight: string
  height: string
  temperature: string
  blood_pressure_systolic: string
  blood_pressure_diastolic: string
  heart_rate: string
  respiratory_rate: string
  oxygen_saturation: string
  glycemia: string
}

interface SoapData {
  subjective: string
  objective: string
  assessment: string
  plan: string
}

export default function ConsultationPage() {
  const navigate = useNavigate()
  const { id } = useParams<{ id: string }>()
  const { data: consultation, isLoading } = useConsultation(id!)
  const createSoapNote = useCreateSoapNote()
  const createVitalSigns = useCreateVitalSigns()
  const createDiagnosis = useCreateDiagnosis()

  const [vitalSigns, setVitalSigns] = useState<VitalSigns>({
    weight: '', height: '', temperature: '',
    blood_pressure_systolic: '', blood_pressure_diastolic: '',
    heart_rate: '', respiratory_rate: '', oxygen_saturation: '', glycemia: '',
  })

  const [soap, setSoap] = useState<SoapData>({
    subjective: '', objective: '', assessment: '', plan: '',
  })

  const [chiefComplaint, setChiefComplaint] = useState('')
  const [presentIllness, setPresentIllness] = useState('')
  const [diagnoses, setDiagnoses] = useState<{ code: string; name: string; type: string }[]>([])
  const [searchDiagnosis, setSearchDiagnosis] = useState('')

  const { data: cie10Results } = useSearchCie10(searchDiagnosis)

  const imc = vitalSigns.weight && vitalSigns.height
    ? calculateIMC(parseFloat(vitalSigns.weight), parseFloat(vitalSigns.height))
    : null

  const handleSaveAll = async () => {
    if (!id || !consultation) return

    const medicalRecordId = consultation.medical_record_id
    const patientId = consultation.medical_record?.patient_id || ''
    const doctorId = consultation.doctor_id || ''

    try {
      // Save SOAP note
      if (soap.subjective || soap.objective || soap.assessment || soap.plan) {
        await createSoapNote.mutateAsync({
          medical_record_id: medicalRecordId,
          subjective: soap.subjective,
          objective: soap.objective,
          assessment: soap.assessment,
          plan: soap.plan,
        })
      }

      // Save vital signs
      const hasVitalSigns = vitalSigns.weight || vitalSigns.height || vitalSigns.temperature ||
        vitalSigns.blood_pressure_systolic || vitalSigns.heart_rate
      if (hasVitalSigns && patientId) {
        await createVitalSigns.mutateAsync({
          medical_record_id: medicalRecordId,
          patient_id: patientId,
          temperature: vitalSigns.temperature ? parseFloat(vitalSigns.temperature) : undefined,
          blood_pressure_systolic: vitalSigns.blood_pressure_systolic || undefined,
          blood_pressure_diastolic: vitalSigns.blood_pressure_diastolic || undefined,
          heart_rate: vitalSigns.heart_rate ? parseInt(vitalSigns.heart_rate) : undefined,
          respiratory_rate: vitalSigns.respiratory_rate ? parseInt(vitalSigns.respiratory_rate) : undefined,
          oxygen_saturation: vitalSigns.oxygen_saturation ? parseFloat(vitalSigns.oxygen_saturation) : undefined,
          weight: vitalSigns.weight ? parseFloat(vitalSigns.weight) : undefined,
          height: vitalSigns.height ? parseFloat(vitalSigns.height) : undefined,
        })
      }

      // Save diagnoses
      for (const d of diagnoses) {
        await createDiagnosis.mutateAsync({
          medical_record_id: medicalRecordId,
          patient_id: patientId,
          description: `${d.code} - ${d.name}`,
          diagnosis_type: d.type,
        })
      }

      navigate(-1)
    } catch (error) {
      console.error('Error saving consultation:', error)
    }
  }

  const addDiagnosis = (code: string, name: string) => {
    if (!diagnoses.find(d => d.code === code)) {
      setDiagnoses([...diagnoses, { code, name, type: 'primary' }])
      setSearchDiagnosis('')
    }
  }

  if (isLoading) return <PageLoadingSpinner />

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex items-center gap-4">
        <Button variant="ghost" size="sm" onClick={() => navigate(-1)}>
          <ArrowLeft className="h-4 w-4" />
        </Button>
        <div className="flex-1">
          <h1 className="text-2xl font-bold text-gray-900">Consulta Médica</h1>
          <p className="text-sm text-gray-500">
            Consulta N° {consultation?.consultation_number}
          </p>
        </div>
        <Button
          leftIcon={<Save className="h-4 w-4" />}
          onClick={handleSaveAll}
          isLoading={createSoapNote.isPending || createVitalSigns.isPending || createDiagnosis.isPending}
        >
          Guardar Consulta
        </Button>
      </div>

      {/* Patient Info Header */}
      {consultation?.medical_record?.patient && (
        <Card variant="bordered">
          <CardContent className="py-3">
            <div className="flex items-center justify-between">
              <div className="flex items-center gap-3">
                <div className="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center">
                  <span className="text-sm font-medium text-primary-700">
                    {consultation.medical_record.patient.first_name.charAt(0)}
                    {consultation.medical_record.patient.last_name.charAt(0)}
                  </span>
                </div>
                <div>
                  <p className="font-medium text-gray-900">
                    {consultation.medical_record.patient.first_name} {consultation.medical_record.patient.last_name}
                  </p>
                  <p className="text-xs text-gray-500">
                    CI: {consultation.medical_record.patient.document_number}
                  </p>
                </div>
              </div>
              <Badge variant="info">{consultation.status}</Badge>
            </div>
          </CardContent>
        </Card>
      )}

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* Main Content */}
        <div className="lg:col-span-2 space-y-6">
          {/* Chief Complaint */}
          <Card variant="elevated">
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <Stethoscope className="h-5 w-5 text-primary-600" />
                Motivo de Consulta
              </CardTitle>
            </CardHeader>
            <CardContent>
              <TextArea
                value={chiefComplaint}
                onChange={(e) => setChiefComplaint(e.target.value)}
                placeholder="Describa el motivo principal de la consulta..."
                rows={2}
              />
            </CardContent>
          </Card>

          {/* History of Present Illness */}
          <Card variant="elevated">
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <ClipboardList className="h-5 w-5 text-primary-600" />
                Historia de la Enfermedad Actual
              </CardTitle>
            </CardHeader>
            <CardContent>
              <TextArea
                value={presentIllness}
                onChange={(e) => setPresentIllness(e.target.value)}
                placeholder="Describa la historia de la enfermedad actual..."
                rows={4}
              />
            </CardContent>
          </Card>

          {/* SOAP Note */}
          <Card variant="elevated">
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <FileText className="h-5 w-5 text-primary-600" />
                Nota SOAP
              </CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1.5">
                  S - Subjetivo
                </label>
                <TextArea
                  value={soap.subjective}
                  onChange={(e) => setSoap({ ...soap, subjective: e.target.value })}
                  placeholder="Síntomas referidos por el paciente..."
                  rows={3}
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1.5">
                  O - Objetivo
                </label>
                <TextArea
                  value={soap.objective}
                  onChange={(e) => setSoap({ ...soap, objective: e.target.value })}
                  placeholder="Hallazgos del examen físico..."
                  rows={3}
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1.5">
                  A - Evaluación
                </label>
                <TextArea
                  value={soap.assessment}
                  onChange={(e) => setSoap({ ...soap, assessment: e.target.value })}
                  placeholder="Diagnóstico o impresión clínica..."
                  rows={3}
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1.5">
                  P - Plan
                </label>
                <TextArea
                  value={soap.plan}
                  onChange={(e) => setSoap({ ...soap, plan: e.target.value })}
                  placeholder="Plan de tratamiento..."
                  rows={3}
                />
              </div>
            </CardContent>
          </Card>

          {/* Diagnoses */}
          <Card variant="elevated">
            <CardHeader>
              <CardTitle>Diagnósticos (CIE-10)</CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="relative">
                <Input
                  placeholder="Buscar diagnóstico CIE-10..."
                  value={searchDiagnosis}
                  onChange={(e) => setSearchDiagnosis(e.target.value)}
                />
                {cie10Results && cie10Results.length > 0 && searchDiagnosis && (
                  <div className="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-auto">
                    {cie10Results.map((item: any) => (
                      <button
                        key={item.id}
                        onClick={() => addDiagnosis(item.code, item.description || item.name)}
                        className="w-full px-4 py-3 text-left hover:bg-gray-50 border-b last:border-0"
                      >
                        <p className="font-mono text-primary-700 text-sm">{item.code}</p>
                        <p className="text-sm text-gray-900">{item.description || item.name}</p>
                      </button>
                    ))}
                  </div>
                )}
              </div>
              {diagnoses.length > 0 && (
                <div className="space-y-2">
                  {diagnoses.map((d) => (
                    <div key={d.code} className="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                      <div>
                        <span className="font-mono text-primary-700 mr-2">{d.code}</span>
                        <span className="text-gray-900">{d.name}</span>
                      </div>
                      <button
                        onClick={() => setDiagnoses(diagnoses.filter(x => x.code !== d.code))}
                        className="text-red-500 hover:text-red-700 text-sm"
                      >
                        Eliminar
                      </button>
                    </div>
                  ))}
                </div>
              )}
            </CardContent>
          </Card>
        </div>

        {/* Sidebar - Vital Signs */}
        <div className="space-y-6">
          <Card variant="elevated">
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <Activity className="h-5 w-5 text-primary-600" />
                Signos Vitales
              </CardTitle>
            </CardHeader>
            <CardContent className="space-y-3">
              <div className="grid grid-cols-2 gap-3">
                <Input
                  label="Peso (kg)"
                  type="number"
                  step="0.1"
                  value={vitalSigns.weight}
                  onChange={(e) => setVitalSigns({ ...vitalSigns, weight: e.target.value })}
                />
                <Input
                  label="Altura (cm)"
                  type="number"
                  value={vitalSigns.height}
                  onChange={(e) => setVitalSigns({ ...vitalSigns, height: e.target.value })}
                />
              </div>
              {imc && (
                <div className="p-3 bg-gray-50 rounded-lg">
                  <p className="text-sm text-gray-500">IMC</p>
                  <p className={`text-lg font-bold ${getIMCColor(imc)}`}>
                    {imc} - {getIMCCategory(imc)}
                  </p>
                </div>
              )}
              <Input
                label="Temperatura (°C)"
                type="number"
                step="0.1"
                value={vitalSigns.temperature}
                onChange={(e) => setVitalSigns({ ...vitalSigns, temperature: e.target.value })}
              />
              <div className="grid grid-cols-2 gap-3">
                <Input
                  label="PA Sistólica"
                  type="number"
                  value={vitalSigns.blood_pressure_systolic}
                  onChange={(e) => setVitalSigns({ ...vitalSigns, blood_pressure_systolic: e.target.value })}
                />
                <Input
                  label="PA Diastólica"
                  type="number"
                  value={vitalSigns.blood_pressure_diastolic}
                  onChange={(e) => setVitalSigns({ ...vitalSigns, blood_pressure_diastolic: e.target.value })}
                />
              </div>
              <Input
                label="Frecuencia Cardíaca"
                type="number"
                value={vitalSigns.heart_rate}
                onChange={(e) => setVitalSigns({ ...vitalSigns, heart_rate: e.target.value })}
              />
              <Input
                label="Frecuencia Respiratoria"
                type="number"
                value={vitalSigns.respiratory_rate}
                onChange={(e) => setVitalSigns({ ...vitalSigns, respiratory_rate: e.target.value })}
              />
              <Input
                label="SpO2 (%)"
                type="number"
                value={vitalSigns.oxygen_saturation}
                onChange={(e) => setVitalSigns({ ...vitalSigns, oxygen_saturation: e.target.value })}
              />
            </CardContent>
          </Card>
        </div>
      </div>
    </div>
  )
}
