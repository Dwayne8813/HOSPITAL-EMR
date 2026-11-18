import { useParams } from 'react-router-dom'
import { useQuery } from '@tanstack/react-query'
import api from '@/lib/api'
import { useInstitution } from '@/hooks/useInstitution'
import { formatDate } from '@/lib/utils'
import { Printer, ArrowLeft } from 'lucide-react'
import { useNavigate } from 'react-router-dom'
import Button from '@/components/ui/Button'
import '@/styles/print.css'

export default function LabResultPrintPage() {
  const { id } = useParams<{ id: string }>()
  const navigate = useNavigate()
  const { data: institution } = useInstitution()

  const { data: result, isLoading } = useQuery({
    queryKey: ['lab-result', id],
    queryFn: async () => {
      const response = await api.get(`/lab-results/${id}`)
      return response.data.data
    },
    enabled: !!id,
  })

  if (isLoading) return <div className="flex justify-center py-12"><div className="w-8 h-8 border-4 border-primary-600 border-t-transparent rounded-full animate-spin" /></div>
  if (!result) return <div className="text-center py-12 text-gray-500">Result not found</div>

  const handlePrint = () => window.print()

  return (
    <>
      <div className="no-print max-w-4xl mx-auto py-4 px-4">
        <div className="flex items-center gap-4 mb-4">
          <Button variant="ghost" size="sm" onClick={() => navigate(-1)}>
            <ArrowLeft className="h-4 w-4" />
          </Button>
          <h1 className="text-xl font-bold">Print Preview - Lab Result</h1>
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
            <h3 style={{ fontSize: '18px', fontWeight: 'bold', color: '#1380a0', margin: 0 }}>LABORATORY RESULT</h3>
            <p style={{ fontSize: '11px', color: '#666', margin: '5px 0' }}>No. {result.result_number || result.id}</p>
            <p style={{ fontSize: '11px', color: '#666' }}>Date: {formatDate(result.result_date || result.created_at)}</p>
          </div>
        </div>

        {/* Patient & Doctor */}
        <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '15px', marginBottom: '15px' }}>
          <div style={{ padding: '10px', background: '#f8f9fa', borderRadius: '5px' }}>
            <p style={{ fontSize: '10px', color: '#666', margin: '0 0 3px 0', textTransform: 'uppercase' }}>Patient</p>
            <p style={{ fontSize: '13px', fontWeight: 'bold', margin: '0 0 2px 0' }}>{result.lab_order?.patient?.first_name} {result.lab_order?.patient?.last_name}</p>
            <p style={{ fontSize: '11px', color: '#666', margin: 0 }}>ID: {result.lab_order?.patient?.ci || result.lab_order?.patient?.document_number || '---'}</p>
            <p style={{ fontSize: '11px', color: '#666', margin: '2px 0 0 0' }}>Record: {result.lab_order?.patient?.medical_record_number || '---'}</p>
          </div>
          <div style={{ padding: '10px', background: '#f8f9fa', borderRadius: '5px' }}>
            <p style={{ fontSize: '10px', color: '#666', margin: '0 0 3px 0', textTransform: 'uppercase' }}>Requesting Doctor</p>
            <p style={{ fontSize: '13px', fontWeight: 'bold', margin: '0 0 2px 0' }}>{result.lab_order?.doctor?.name || '---'}</p>
            <p style={{ fontSize: '11px', color: '#666', margin: 0 }}>Specialty: {result.lab_order?.doctor?.specialty?.name || '---'}</p>
          </div>
        </div>

        {/* Exam Info */}
        <div style={{ padding: '10px', border: '1px solid #e5e7eb', borderRadius: '5px', marginBottom: '15px' }}>
          <p style={{ fontSize: '10px', color: '#666', margin: '0 0 5px 0', textTransform: 'uppercase' }}>Exam / Test</p>
          <p style={{ fontSize: '13px', fontWeight: 'bold', margin: 0, color: '#1380a0' }}>{result.exam_name || result.test_name || '---'}</p>
          {result.clinical_context && (
            <p style={{ fontSize: '11px', color: '#666', margin: '5px 0 0 0' }}>Clinical Context: {result.clinical_context}</p>
          )}
        </div>

        {/* Results Table */}
        {(result.results || result.items) && (result.results || result.items).length > 0 && (
          <div style={{ marginBottom: '15px' }}>
            <h4 style={{ fontSize: '13px', fontWeight: 'bold', color: '#1380a0', margin: '0 0 10px 0', borderBottom: '1px solid #e5e7eb', paddingBottom: '5px' }}>Results</h4>
            <table style={{ width: '100%', borderCollapse: 'collapse' }}>
              <thead>
                <tr style={{ background: '#1380a0', color: 'white' }}>
                  <th style={{ padding: '8px', textAlign: 'left', fontSize: '11px' }}>Parameter</th>
                  <th style={{ padding: '8px', textAlign: 'center', fontSize: '11px' }}>Result</th>
                  <th style={{ padding: '8px', textAlign: 'center', fontSize: '11px' }}>Unit</th>
                  <th style={{ padding: '8px', textAlign: 'center', fontSize: '11px' }}>Reference Values</th>
                  <th style={{ padding: '8px', textAlign: 'center', fontSize: '11px' }}>Status</th>
                </tr>
              </thead>
              <tbody>
                {(result.results || result.items).map((item: any, idx: number) => {
                  const isAbnormal = item.is_abnormal || (item.status === 'abnormal' || item.status === 'high' || item.status === 'low')
                  return (
                    <tr key={idx} style={{ borderBottom: '1px solid #e5e7eb', background: isAbnormal ? '#fef2f2' : idx % 2 === 0 ? '#fff' : '#f9fafb' }}>
                      <td style={{ padding: '8px', fontSize: '11px', fontWeight: 'bold' }}>{item.parameter || item.test_name || '---'}</td>
                      <td style={{ padding: '8px', fontSize: '11px', textAlign: 'center', fontWeight: 'bold', color: isAbnormal ? '#dc2626' : '#000' }}>
                        {item.result_value || item.value || '---'}
                      </td>
                      <td style={{ padding: '8px', fontSize: '11px', textAlign: 'center' }}>{item.unit || '---'}</td>
                      <td style={{ padding: '8px', fontSize: '11px', textAlign: 'center', color: '#666' }}>{item.reference_range || '---'}</td>
                      <td style={{ padding: '8px', fontSize: '10px', textAlign: 'center', fontWeight: 'bold', color: isAbnormal ? '#dc2626' : '#16a34a' }}>
                        {item.status === 'high' ? '▲ HIGH' : item.status === 'low' ? '▼ LOW' : item.status === 'abnormal' ? '⚠ ABNORMAL' : '✓ Normal'}
                      </td>
                    </tr>
                  )
                })}
              </tbody>
            </table>
          </div>
        )}

        {/* Observations */}
        {result.observations && (
          <div style={{ padding: '10px', background: '#fffbeb', border: '1px solid #fde68a', borderRadius: '5px', marginBottom: '15px', fontSize: '11px' }}>
            <strong>Notes:</strong> {result.observations}
          </div>
        )}

        {/* Signature */}
        <div style={{ display: 'flex', justifyContent: 'space-between', marginTop: '30px', paddingTop: '15px' }}>
          <div style={{ textAlign: 'center', width: '200px' }}>
            <div style={{ borderTop: '1px solid #000', marginBottom: '5px' }}></div>
            <p style={{ fontSize: '11px', fontWeight: 'bold', margin: '2px 0' }}>Laboratory Technician</p>
            <p style={{ fontSize: '10px', color: '#666', margin: '2px 0' }}>{result.technician_name || '---'}</p>
          </div>
          <div style={{ textAlign: 'center', width: '200px' }}>
            <div style={{ borderTop: '1px solid #000', marginBottom: '5px' }}></div>
            <p style={{ fontSize: '11px', fontWeight: 'bold', margin: '2px 0' }}>Responsible Doctor</p>
            <p style={{ fontSize: '10px', color: '#666', margin: '2px 0' }}>{result.lab_order?.doctor?.name || '---'}</p>
          </div>
        </div>

        {/* Footer */}
        <div style={{ borderTop: '1px solid #ccc', paddingTop: '10px', textAlign: 'center', fontSize: '9px', color: '#666', marginTop: '20px' }}>
          <p style={{ margin: '2px 0' }}>{institution?.name || ''} | Tax ID: {institution?.nit || ''}</p>
          <p style={{ margin: '2px 0' }}>Results must be interpreted by the treating doctor</p>
        </div>
      </div>
    </>
  )
}
