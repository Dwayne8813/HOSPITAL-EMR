import { useParams } from 'react-router-dom'
import { useQuery } from '@tanstack/react-query'
import api from '@/lib/api'
import { useInstitution } from '@/hooks/useInstitution'
import { formatDate } from '@/lib/utils'
import { Printer, ArrowLeft } from 'lucide-react'
import { useNavigate } from 'react-router-dom'
import Button from '@/components/ui/Button'
import '@/styles/print.css'

export default function PrescriptionPrintPage() {
  const { id } = useParams<{ id: string }>()
  const navigate = useNavigate()
  const { data: institution } = useInstitution()

  const { data: prescription, isLoading } = useQuery({
    queryKey: ['prescription', id],
    queryFn: async () => {
      const response = await api.get(`/prescriptions/${id}`)
      return response.data.data
    },
    enabled: !!id,
  })

  if (isLoading) return <div className="flex justify-center py-12"><div className="w-8 h-8 border-4 border-primary-600 border-t-transparent rounded-full animate-spin" /></div>
  if (!prescription) return <div className="text-center py-12 text-gray-500">Prescription not found</div>

  const handlePrint = () => window.print()

  return (
    <>
      <div className="no-print max-w-4xl mx-auto py-4 px-4">
        <div className="flex items-center gap-4 mb-4">
          <Button variant="ghost" size="sm" onClick={() => navigate(-1)}>
            <ArrowLeft className="h-4 w-4" />
          </Button>
          <h1 className="text-xl font-bold">Print Preview - Prescription</h1>
          <Button leftIcon={<Printer className="h-4 w-4" />} onClick={handlePrint}>
            Print
          </Button>
        </div>
      </div>

      <div className="print-area" style={{ fontFamily: 'Arial, sans-serif', color: '#000', fontSize: '12px' }}>
        {/* Header */}
        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', borderBottom: '2px solid #1380a0', paddingBottom: '15px', marginBottom: '15px' }}>
          <div style={{ display: 'flex', alignItems: 'center', gap: '15px' }}>
            {institution?.logo ? (
              <img src={institution.logo} alt="Logo" style={{ width: '70px', height: '70px', objectFit: 'contain' }} />
            ) : (
              <div style={{ width: '70px', height: '70px', background: '#1380a0', borderRadius: '10px', display: 'flex', alignItems: 'center', justifyContent: 'center', color: 'white', fontSize: '24px', fontWeight: 'bold' }}>
                {institution?.name?.charAt(0) || 'H'}
              </div>
            )}
            <div>
              <h2 style={{ fontSize: '18px', fontWeight: 'bold', margin: 0, color: '#1380a0' }}>{institution?.name || 'Hospital'}</h2>
              <p style={{ margin: '2px 0', fontSize: '11px', color: '#666' }}>Tax ID: {institution?.nit || '---'}</p>
              <p style={{ margin: '2px 0', fontSize: '11px', color: '#666' }}>{institution?.address || ''}, {institution?.city || ''}</p>
              <p style={{ margin: '2px 0', fontSize: '11px', color: '#666' }}>Tel: {institution?.phone || ''}</p>
            </div>
          </div>
          <div style={{ textAlign: 'right' }}>
            <h3 style={{ fontSize: '20px', fontWeight: 'bold', color: '#1380a0', margin: 0 }}>PRESCRIPTION</h3>
            <p style={{ fontSize: '11px', color: '#666', margin: '5px 0' }}>Date: {formatDate(prescription.prescription_date || prescription.created_at)}</p>
          </div>
        </div>

        {/* Patient & Doctor */}
        <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '15px', marginBottom: '15px' }}>
          <div style={{ padding: '10px', background: '#f8f9fa', borderRadius: '5px' }}>
            <p style={{ fontSize: '10px', color: '#666', margin: '0 0 3px 0', textTransform: 'uppercase' }}>Patient</p>
            <p style={{ fontSize: '13px', fontWeight: 'bold', margin: '0 0 2px 0' }}>{prescription.patient?.first_name} {prescription.patient?.last_name}</p>
            <p style={{ fontSize: '11px', color: '#666', margin: 0 }}>ID: {prescription.patient?.document_number || '---'}</p>
            <p style={{ fontSize: '11px', color: '#666', margin: '2px 0 0 0' }}>Age: {prescription.patient?.birth_date ? Math.floor((Date.now() - new Date(prescription.patient.birth_date).getTime()) / 31557600000) : '---'} years</p>
          </div>
          <div style={{ padding: '10px', background: '#f8f9fa', borderRadius: '5px' }}>
            <p style={{ fontSize: '10px', color: '#666', margin: '0 0 3px 0', textTransform: 'uppercase' }}>Doctor</p>
            <p style={{ fontSize: '13px', fontWeight: 'bold', margin: '0 0 2px 0' }}>{prescription.doctor?.name || '---'}</p>
            <p style={{ fontSize: '11px', color: '#666', margin: 0 }}>License: {prescription.doctor?.cmp || '---'}</p>
            <p style={{ fontSize: '11px', color: '#666', margin: '2px 0 0 0' }}>Specialty: {prescription.doctor?.specialty?.name || '---'}</p>
          </div>
        </div>

        {/* Diagnosis */}
        <div style={{ padding: '10px', border: '1px solid #e5e7eb', borderRadius: '5px', marginBottom: '15px' }}>
          <p style={{ fontSize: '10px', color: '#666', margin: '0 0 5px 0', textTransform: 'uppercase' }}>Diagnosis</p>
          <p style={{ fontSize: '12px', margin: 0 }}>{prescription.diagnosis || 'No diagnosis recorded'}</p>
        </div>

        {/* Medications */}
        <div style={{ marginBottom: '15px' }}>
          <h4 style={{ fontSize: '13px', fontWeight: 'bold', color: '#1380a0', margin: '0 0 10px 0', borderBottom: '1px solid #e5e7eb', paddingBottom: '5px' }}>Medications</h4>
          <table style={{ width: '100%', borderCollapse: 'collapse' }}>
            <thead>
              <tr style={{ background: '#1380a0', color: 'white' }}>
                <th style={{ padding: '8px', textAlign: 'left', fontSize: '11px' }}>Medication</th>
                <th style={{ padding: '8px', textAlign: 'left', fontSize: '11px' }}>Dosage</th>
                <th style={{ padding: '8px', textAlign: 'left', fontSize: '11px' }}>Frequency</th>
                <th style={{ padding: '8px', textAlign: 'left', fontSize: '11px' }}>Duration</th>
                <th style={{ padding: '8px', textAlign: 'left', fontSize: '11px' }}>Route</th>
              </tr>
            </thead>
            <tbody>
              {prescription.items?.map((item: any, idx: number) => (
                <tr key={idx} style={{ borderBottom: '1px solid #e5e7eb', background: idx % 2 === 0 ? '#fff' : '#f9fafb' }}>
                  <td style={{ padding: '8px', fontSize: '11px', fontWeight: 'bold' }}>{item.medication?.name || item.medication_name || '---'}</td>
                  <td style={{ padding: '8px', fontSize: '11px' }}>{item.dosage || '---'}</td>
                  <td style={{ padding: '8px', fontSize: '11px' }}>{item.frequency || '---'}</td>
                  <td style={{ padding: '8px', fontSize: '11px' }}>{item.duration || '---'}</td>
                  <td style={{ padding: '8px', fontSize: '11px' }}>{item.route || 'Oral'}</td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>

        {/* Notes */}
        {prescription.notes && (
          <div style={{ padding: '10px', background: '#fffbeb', border: '1px solid #fde68a', borderRadius: '5px', marginBottom: '15px', fontSize: '11px' }}>
            <strong>Instructions:</strong> {prescription.notes}
          </div>
        )}

        {/* Signature */}
        <div style={{ display: 'flex', justifyContent: 'flex-end', marginTop: '40px', paddingTop: '15px' }}>
          <div style={{ textAlign: 'center', width: '200px' }}>
            <div style={{ borderTop: '1px solid #000', marginBottom: '5px' }}></div>
            <p style={{ fontSize: '11px', fontWeight: 'bold', margin: '2px 0' }}>{prescription.doctor?.name || 'Doctor'}</p>
            <p style={{ fontSize: '10px', color: '#666', margin: '2px 0' }}>License: {prescription.doctor?.cmp || '---'}</p>
            <p style={{ fontSize: '10px', color: '#666', margin: '2px 0' }}>{prescription.doctor?.specialty?.name || ''}</p>
          </div>
        </div>

        {/* Footer */}
        <div style={{ borderTop: '1px solid #ccc', paddingTop: '10px', textAlign: 'center', fontSize: '9px', color: '#666', marginTop: '20px' }}>
          <p style={{ margin: '2px 0' }}>{institution?.name || ''} | Tax ID: {institution?.nit || ''}</p>
          <p style={{ margin: '2px 0' }}>This prescription is valid for 30 days from the date of issue</p>
        </div>
      </div>
    </>
  )
}
