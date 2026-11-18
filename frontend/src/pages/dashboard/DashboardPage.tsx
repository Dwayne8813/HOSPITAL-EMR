import { useDashboardStats } from '@/hooks/useDashboard'
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/Card'
import { PageLoadingSpinner } from '@/components/ui/LoadingSpinner'
import { formatCurrency, formatDate } from '@/lib/utils'
import {
  Users,
  Calendar,
  DollarSign,
  BedDouble,
  Activity,
  AlertTriangle,
  ArrowUpRight,
  ArrowDownRight,
  Plus,
} from 'lucide-react'
import { Link } from 'react-router-dom'

export default function DashboardPage() {
  const { data: stats, isLoading } = useDashboardStats()

  if (isLoading) {
    return <PageLoadingSpinner />
  }

  const summaryCards = [
    {
      title: 'Total Patients',
      value: stats?.summary?.total_patients || 0,
      change: stats?.recent_patients?.length || 0,
      changeLabel: 'recent',
      icon: Users,
      color: 'bg-primary-600',
      trend: 'up',
    },
    {
      title: "Today's Appointments",
      value: stats?.summary?.today_appointments || 0,
      change: stats?.summary?.active_hospitalizations || 0,
      changeLabel: 'hospitalized',
      icon: Calendar,
      color: 'bg-secondary-600',
      trend: 'up',
    },
    {
      title: 'Monthly Revenue',
      value: formatCurrency(stats?.summary?.monthly_revenue || 0),
      change: formatCurrency(stats?.summary?.pending_payments || 0),
      changeLabel: 'pending',
      icon: DollarSign,
      color: 'bg-green-600',
      trend: 'up',
    },
    {
      title: 'Hospitalized',
      value: stats?.summary?.active_hospitalizations || 0,
      change: stats?.summary?.pending_lab_orders || 0,
      changeLabel: 'pending lab orders',
      icon: BedDouble,
      color: 'bg-yellow-600',
      trend: 'neutral',
    },
  ]

  return (
    <div className="space-y-6">
      {/* Welcome Header */}
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Dashboard</h1>
          <p className="text-sm text-gray-500">
            General hospital system overview
          </p>
        </div>
        <div className="flex gap-2">
          <Link
            to="/patients/create"
            className="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-lg text-sm font-medium hover:bg-primary-700 transition-colors"
          >
            <Plus className="h-4 w-4" />
            New Patient
          </Link>
          <Link
            to="/appointments"
            className="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors"
          >
            <Calendar className="h-4 w-4" />
            New Appointment
          </Link>
        </div>
      </div>

      {/* Summary Cards */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {summaryCards.map((card) => (
          <Card key={card.title} variant="elevated" className="relative overflow-hidden">
            <div className="flex items-start justify-between">
              <div>
                <p className="text-sm font-medium text-gray-500">{card.title}</p>
                <p className="text-2xl font-bold text-gray-900 mt-1">{card.value}</p>
                <div className="flex items-center gap-1 mt-2">
                  {card.trend === 'up' ? (
                    <ArrowUpRight className="h-4 w-4 text-green-500" />
                  ) : card.trend === 'down' ? (
                    <ArrowDownRight className="h-4 w-4 text-red-500" />
                  ) : null}
                  <span className="text-xs text-gray-500">
                    {card.change} {card.changeLabel}
                  </span>
                </div>
              </div>
              <div className={`p-3 rounded-xl ${card.color}`}>
                <card.icon className="h-6 w-6 text-white" />
              </div>
            </div>
          </Card>
        ))}
      </div>

      {/* Emergency & Pending */}
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <Card variant="elevated" className="lg:col-span-2">
          <CardHeader>
            <div className="flex items-center justify-between">
              <CardTitle>Recent Appointments</CardTitle>
              <Link
                to="/appointments"
                className="text-sm text-primary-600 hover:text-primary-700 font-medium"
              >
                View all
              </Link>
            </div>
          </CardHeader>
          <CardContent>
            {stats?.recent_appointments?.length ? (
              <div className="space-y-3">
                {stats.recent_appointments.slice(0, 5).map((appointment) => (
                  <div
                    key={appointment.id}
                    className="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
                  >
                    <div className="flex items-center gap-3">
                      <div className="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center">
                        <span className="text-sm font-medium text-primary-700">
                          {appointment.patient?.first_name?.charAt(0)}
                          {appointment.patient?.last_name?.charAt(0)}
                        </span>
                      </div>
                      <div>
                        <p className="text-sm font-medium text-gray-900">
                          {appointment.patient?.first_name} {appointment.patient?.last_name}
                        </p>
                        <p className="text-xs text-gray-500">
                          {appointment.doctor?.first_name} {appointment.doctor?.last_name}
                        </p>
                      </div>
                    </div>
                    <div className="text-right">
                      <p className="text-sm font-medium text-gray-900">{appointment.start_time}</p>
                      <p className="text-xs text-gray-500">{formatDate(appointment.appointment_date)}</p>
                    </div>
                  </div>
                ))}
              </div>
            ) : (
              <p className="text-sm text-gray-500 text-center py-4">No recent appointments</p>
            )}
          </CardContent>
        </Card>

        <div className="space-y-4">
          <Card variant="elevated">
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <AlertTriangle className="h-5 w-5 text-yellow-500" />
                Emergencies Today
              </CardTitle>
            </CardHeader>
            <CardContent>
              <div className="text-center py-4">
                <p className="text-4xl font-bold text-gray-900">
                  {stats?.summary?.emergency_waiting || 0}
                </p>
                <p className="text-sm text-gray-500 mt-1">waiting</p>
              </div>
            </CardContent>
          </Card>

          <Card variant="elevated">
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <Activity className="h-5 w-5 text-blue-500" />
                Laboratory
              </CardTitle>
            </CardHeader>
            <CardContent>
              <div className="text-center py-4">
                <p className="text-4xl font-bold text-gray-900">
                  {stats?.summary?.pending_lab_orders || 0}
                </p>
                <p className="text-sm text-gray-500 mt-1">pending orders</p>
              </div>
            </CardContent>
          </Card>
        </div>
      </div>

      {/* Quick Actions */}
      <Card variant="elevated">
        <CardHeader>
          <CardTitle>Quick Actions</CardTitle>
        </CardHeader>
        <CardContent>
          <div className="grid grid-cols-2 sm:grid-cols-4 gap-3">
            {[
              { label: 'New Patient', path: '/patients/create', color: 'bg-primary-50 text-primary-700 hover:bg-primary-100' },
              { label: 'Schedule Appointment', path: '/appointments', color: 'bg-secondary-50 text-secondary-700 hover:bg-secondary-100' },
              { label: 'View Laboratory', path: '/lab/orders', color: 'bg-yellow-50 text-yellow-700 hover:bg-yellow-100' },
              { label: 'Emergency', path: '/emergency', color: 'bg-red-50 text-red-700 hover:bg-red-100' },
            ].map((action) => (
              <Link
                key={action.path}
                to={action.path}
                className={`flex items-center justify-center p-4 rounded-lg font-medium text-sm transition-colors ${action.color}`}
              >
                {action.label}
              </Link>
            ))}
          </div>
        </CardContent>
      </Card>
    </div>
  )
}
