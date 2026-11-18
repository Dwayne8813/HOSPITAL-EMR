import { useState, useMemo } from 'react'
import {
  useDashboardReport,
  usePatientReport,
  useAppointmentReport,
  useRevenueReport,
  useLabReport,
  usePharmacyReport,
  useHospitalizationReport,
  useDiagnosisReport,
  type ReportFilters,
} from '@/hooks/useReports'
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/Card'
import Button from '@/components/ui/Button'
import { PageLoadingSpinner } from '@/components/ui/LoadingSpinner'
import { formatCurrency } from '@/lib/utils'
import {
  Download,
  BarChart3,
  PieChart,
  TrendingUp,
  Users,
  DollarSign,
  Calendar,
  Activity,
  Stethoscope,
  Pill,
  Building,
} from 'lucide-react'

export default function ReportsPage() {
  const [dateFrom, setDateFrom] = useState('')
  const [dateTo, setDateTo] = useState('')
  const [reportType, setReportType] = useState<string>('general')

  const filters: ReportFilters = useMemo(
    () => ({
      date_from: dateFrom || undefined,
      date_to: dateTo || undefined,
    }),
    [dateFrom, dateTo]
  )

  const { data: dashboardData, isLoading: dashboardLoading } = useDashboardReport()
  const { data: patientData, isLoading: patientsLoading } = usePatientReport(filters)
  const { data: appointmentData, isLoading: appointmentsLoading } = useAppointmentReport(filters)
  const { data: revenueData, isLoading: revenueLoading } = useRevenueReport(filters)
  const { data: labData, isLoading: labLoading } = useLabReport(filters)
  const { data: pharmacyData, isLoading: pharmacyLoading } = usePharmacyReport(filters)
  const { data: hospitalizationData, isLoading: hospitalizationLoading } = useHospitalizationReport(filters)
  const { data: diagnosisData, isLoading: diagnosesLoading } = useDiagnosisReport(filters)

  const isLoadingMap: Record<string, boolean> = {
    general: dashboardLoading,
    patients: patientsLoading,
    appointments: appointmentsLoading,
    revenue: revenueLoading,
    lab: labLoading,
    pharmacy: pharmacyLoading,
    hospitalization: hospitalizationLoading,
    diagnoses: diagnosesLoading,
  }

  if (isLoadingMap[reportType]) return <PageLoadingSpinner />

  const reportTypes = [
    { id: 'general', label: 'General Report', icon: BarChart3, description: 'General institution summary' },
    { id: 'revenue', label: 'Income', icon: DollarSign, description: 'Income details by period' },
    { id: 'appointments', label: 'Appointments', icon: Calendar, description: 'Medical appointment statistics' },
    { id: 'patients', label: 'Patients', icon: Users, description: 'Patient records' },
    { id: 'diagnoses', label: 'Diagnoses', icon: PieChart, description: 'Most frequent diagnoses' },
    { id: 'lab', label: 'Laboratory', icon: Activity, description: 'Lab orders and results' },
    { id: 'pharmacy', label: 'Pharmacy', icon: Pill, description: 'Inventory and dispensing' },
    { id: 'hospitalization', label: 'Hospitalization', icon: Building, description: 'Admissions and discharges' },
  ]

  const handleExport = (type: 'excel' | 'pdf') => {
    const reportData = getCurrentReportData()
    if (!reportData) return
    // Export logic would be triggered here
    console.log(`Exporting ${reportType} as ${type}`, reportData)
  }

  function getCurrentReportData() {
    switch (reportType) {
      case 'general': return dashboardData
      case 'patients': return patientData
      case 'appointments': return appointmentData
      case 'revenue': return revenueData
      case 'lab': return labData
      case 'pharmacy': return pharmacyData
      case 'hospitalization': return hospitalizationData
      case 'diagnoses': return diagnosisData
      default: return null
    }
  }

  return (
    <div className="space-y-6">
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Reports</h1>
          <p className="text-sm text-gray-500">Report and statistics generation</p>
        </div>
      </div>

      {/* Report Type Selection */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {reportTypes.map((report) => (
          <Card
            key={report.id}
            variant={reportType === report.id ? 'elevated' : 'bordered'}
            className={`cursor-pointer transition-all ${
              reportType === report.id ? 'ring-2 ring-primary-500 bg-primary-50' : 'hover:bg-gray-50'
            }`}
            onClick={() => setReportType(report.id)}
          >
            <CardContent className="py-4 text-center">
              <report.icon className={`h-8 w-8 mx-auto mb-2 ${
                reportType === report.id ? 'text-primary-600' : 'text-gray-400'
              }`} />
              <p className={`font-medium ${reportType === report.id ? 'text-primary-700' : 'text-gray-900'}`}>
                {report.label}
              </p>
              <p className="text-xs text-gray-500 mt-1">{report.description}</p>
            </CardContent>
          </Card>
        ))}
      </div>

      {/* Date Range & Export */}
      <Card variant="elevated">
        <CardHeader>
          <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <CardTitle>Report Filters</CardTitle>
            <div className="flex gap-2">
              <Button
                variant="outline"
                size="sm"
                leftIcon={<Download className="h-4 w-4" />}
                onClick={() => handleExport('excel')}
              >
                Export Excel
              </Button>
              <Button
                variant="outline"
                size="sm"
                leftIcon={<Download className="h-4 w-4" />}
                onClick={() => handleExport('pdf')}
              >
                Export PDF
              </Button>
            </div>
          </div>
        </CardHeader>
        <CardContent>
          <div className="flex flex-col sm:flex-row gap-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1.5">Start Date</label>
              <input
                type="date"
                value={dateFrom}
                onChange={(e) => setDateFrom(e.target.value)}
                className="block rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20"
              />
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1.5">End Date</label>
              <input
                type="date"
                value={dateTo}
                onChange={(e) => setDateTo(e.target.value)}
                className="block rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20"
              />
            </div>
          </div>
        </CardContent>
      </Card>

      {/* Report Content */}
      {reportType === 'general' && dashboardData && (
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          <Card variant="elevated">
            <CardContent className="py-4">
              <div className="flex items-center gap-3">
                <Users className="h-10 w-10 text-blue-500" />
                <div>
                  <p className="text-sm text-gray-500">Total Patients</p>
                  <p className="text-2xl font-bold">{dashboardData.patients.total}</p>
                  <p className="text-xs text-green-600">+{dashboardData.patients.new_this_month} this month</p>
                </div>
              </div>
            </CardContent>
          </Card>
          <Card variant="elevated">
            <CardContent className="py-4">
              <div className="flex items-center gap-3">
                <Calendar className="h-10 w-10 text-primary-500" />
                <div>
                  <p className="text-sm text-gray-500">Appointments Today</p>
                  <p className="text-2xl font-bold">{dashboardData.appointments.today_total}</p>
                  <p className="text-xs text-green-600">{dashboardData.appointments.today_completed} completed</p>
                </div>
              </div>
            </CardContent>
          </Card>
          <Card variant="elevated">
            <CardContent className="py-4">
              <div className="flex items-center gap-3">
                <DollarSign className="h-10 w-10 text-green-500" />
                <div>
                  <p className="text-sm text-gray-500">Monthly Income</p>
                  <p className="text-2xl font-bold">{formatCurrency(dashboardData.revenue.this_month)}</p>
                </div>
              </div>
            </CardContent>
          </Card>
          <Card variant="elevated">
            <CardContent className="py-4">
              <div className="flex items-center gap-3">
                <Activity className="h-10 w-10 text-red-500" />
                <div>
                  <p className="text-sm text-gray-500">Emergency Wait</p>
                  <p className="text-2xl font-bold">{dashboardData.emergencies.waiting}</p>
                </div>
              </div>
            </CardContent>
          </Card>
        </div>
      )}

      {reportType === 'revenue' && revenueData && (
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <Card variant="elevated">
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <TrendingUp className="h-5 w-5 text-primary-600" />
                Income Summary
              </CardTitle>
            </CardHeader>
            <CardContent>
              <div className="space-y-3">
                <div className="flex justify-between">
                  <span className="text-gray-600">Total Collected</span>
                  <span className="font-bold text-green-600">{formatCurrency(revenueData.total_collected)}</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-gray-600">Pending Collection</span>
                  <span className="font-bold text-yellow-600">{formatCurrency(revenueData.total_pending)}</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-gray-600">Total Income</span>
                  <span className="font-bold">{formatCurrency(revenueData.total_revenue)}</span>
                </div>
              </div>
            </CardContent>
          </Card>
          <Card variant="elevated">
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <DollarSign className="h-5 w-5 text-primary-600" />
                By Payment Method
              </CardTitle>
            </CardHeader>
            <CardContent>
              <div className="space-y-3">
                {revenueData.by_payment_method?.map((item) => (
                  <div key={item.method} className="flex justify-between">
                    <span className="text-gray-600 capitalize">{item.method}</span>
                    <span className="font-medium">{formatCurrency(item.amount)}</span>
                  </div>
                ))}
              </div>
            </CardContent>
          </Card>
        </div>
      )}

      {reportType === 'appointments' && appointmentData && (
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <Card variant="elevated">
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <Calendar className="h-5 w-5 text-primary-600" />
                Appointment Summary
              </CardTitle>
            </CardHeader>
            <CardContent>
              <div className="space-y-3">
                <div className="flex justify-between">
                  <span className="text-gray-600">Total</span>
                  <span className="font-bold">{appointmentData.total_appointments}</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-gray-600">Completed</span>
                  <span className="font-bold text-green-600">{appointmentData.completed}</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-gray-600">Cancelled</span>
                  <span className="font-bold text-red-600">{appointmentData.cancelled}</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-gray-600">No Show</span>
                  <span className="font-bold text-yellow-600">{appointmentData.no_show}</span>
                </div>
              </div>
            </CardContent>
          </Card>
          <Card variant="elevated">
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <Stethoscope className="h-5 w-5 text-primary-600" />
                By Specialty
              </CardTitle>
            </CardHeader>
            <CardContent>
              <div className="space-y-3">
                {appointmentData.by_specialty?.map((item) => (
                  <div key={item.specialty} className="flex justify-between">
                    <span className="text-gray-600">{item.specialty}</span>
                    <span className="font-medium">{item.count}</span>
                  </div>
                ))}
                {(!appointmentData.by_specialty || appointmentData.by_specialty.length === 0) && (
                  <p className="text-gray-400 text-sm">No data available</p>
                )}
              </div>
            </CardContent>
          </Card>
        </div>
      )}

      {reportType === 'patients' && patientData && (
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <Card variant="elevated">
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <Users className="h-5 w-5 text-primary-600" />
                Patient Summary
              </CardTitle>
            </CardHeader>
            <CardContent>
              <div className="space-y-3">
                <div className="flex justify-between">
                  <span className="text-gray-600">Total Patients</span>
                  <span className="font-bold">{patientData.total_patients}</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-gray-600">New in Period</span>
                  <span className="font-bold text-green-600">{patientData.new_patients_period}</span>
                </div>
              </div>
            </CardContent>
          </Card>
          <Card variant="elevated">
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <PieChart className="h-5 w-5 text-primary-600" />
                By Gender
              </CardTitle>
            </CardHeader>
            <CardContent>
              <div className="space-y-3">
                {patientData.by_gender?.map((item) => (
                  <div key={item.gender} className="flex justify-between">
                    <span className="text-gray-600 capitalize">{item.gender === 'M' ? 'Male' : item.gender === 'F' ? 'Female' : 'Other'}</span>
                    <span className="font-medium">{item.count}</span>
                  </div>
                ))}
              </div>
            </CardContent>
          </Card>
        </div>
      )}

      {reportType === 'diagnoses' && diagnosisData && (
        <Card variant="elevated">
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <PieChart className="h-5 w-5 text-primary-600" />
              Most Frequent Diagnoses
            </CardTitle>
          </CardHeader>
          <CardContent>
            <div className="space-y-3">
              {diagnosisData.top_diagnoses?.map((item) => (
                <div key={item.code} className="flex items-center justify-between">
                  <div>
                    <span className="font-mono text-sm text-gray-500 mr-2">{item.code}</span>
                    <span className="text-gray-900">{item.name}</span>
                  </div>
                  <div className="flex items-center gap-2">
                    <span className="text-sm font-medium">{item.count}</span>
                    <span className="text-xs text-gray-400">({item.percentage}%)</span>
                  </div>
                </div>
              ))}
              {(!diagnosisData.top_diagnoses || diagnosisData.top_diagnoses.length === 0) && (
                <p className="text-gray-400 text-sm">No data available</p>
              )}
            </div>
          </CardContent>
        </Card>
      )}

      {reportType === 'lab' && labData && (
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          <Card variant="elevated">
            <CardContent className="py-4 text-center">
              <Activity className="h-8 w-8 mx-auto mb-2 text-blue-500" />
              <p className="text-2xl font-bold">{labData.total_orders}</p>
              <p className="text-sm text-gray-500">Total Orders</p>
            </CardContent>
          </Card>
          <Card variant="elevated">
            <CardContent className="py-4 text-center">
              <p className="text-2xl font-bold text-green-600">{labData.completed}</p>
              <p className="text-sm text-gray-500">Completed</p>
            </CardContent>
          </Card>
          <Card variant="elevated">
            <CardContent className="py-4 text-center">
              <p className="text-2xl font-bold text-yellow-600">{labData.pending}</p>
              <p className="text-sm text-gray-500">Pending</p>
            </CardContent>
          </Card>
          <Card variant="elevated">
            <CardContent className="py-4 text-center">
              <p className="text-2xl font-bold text-blue-600">{labData.in_progress}</p>
              <p className="text-sm text-gray-500">In Progress</p>
            </CardContent>
          </Card>
        </div>
      )}

      {reportType === 'pharmacy' && pharmacyData && (
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          <Card variant="elevated">
            <CardContent className="py-4 text-center">
              <Pill className="h-8 w-8 mx-auto mb-2 text-primary-500" />
              <p className="text-2xl font-bold">{pharmacyData.total_medications}</p>
              <p className="text-sm text-gray-500">Medications</p>
            </CardContent>
          </Card>
          <Card variant="elevated">
            <CardContent className="py-4 text-center">
              <p className="text-2xl font-bold text-yellow-600">{pharmacyData.low_stock}</p>
              <p className="text-sm text-gray-500">Low Stock</p>
            </CardContent>
          </Card>
          <Card variant="elevated">
            <CardContent className="py-4 text-center">
              <p className="text-2xl font-bold text-red-600">{pharmacyData.expiring_soon}</p>
              <p className="text-sm text-gray-500">Expiring Soon</p>
            </CardContent>
          </Card>
          <Card variant="elevated">
            <CardContent className="py-4 text-center">
              <p className="text-2xl font-bold">{pharmacyData.total_dispensations}</p>
              <p className="text-sm text-gray-500">Dispensations</p>
            </CardContent>
          </Card>
        </div>
      )}

      {reportType === 'hospitalization' && hospitalizationData && (
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          <Card variant="elevated">
            <CardContent className="py-4 text-center">
              <Building className="h-8 w-8 mx-auto mb-2 text-primary-500" />
              <p className="text-2xl font-bold">{hospitalizationData.total}</p>
              <p className="text-sm text-gray-500">Total</p>
            </CardContent>
          </Card>
          <Card variant="elevated">
            <CardContent className="py-4 text-center">
              <p className="text-2xl font-bold text-blue-600">{hospitalizationData.active}</p>
              <p className="text-sm text-gray-500">Active</p>
            </CardContent>
          </Card>
          <Card variant="elevated">
            <CardContent className="py-4 text-center">
              <p className="text-2xl font-bold text-green-600">{hospitalizationData.discharged}</p>
              <p className="text-sm text-gray-500">Discharged</p>
            </CardContent>
          </Card>
          <Card variant="elevated">
            <CardContent className="py-4 text-center">
              <p className="text-2xl font-bold">{hospitalizationData.average_stay_days}</p>
              <p className="text-sm text-gray-500">Average Days</p>
            </CardContent>
          </Card>
        </div>
      )}
    </div>
  )
}
