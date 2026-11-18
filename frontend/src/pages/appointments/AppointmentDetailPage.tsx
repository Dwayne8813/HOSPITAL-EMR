import { useState } from 'react'
import { useNavigate, useParams } from 'react-router-dom'
import { useAppointment, useConfirmAppointment, useCancelAppointment, useCompleteAppointment } from '@/hooks/useAppointments'
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/Card'
import { StatusBadge, Badge } from '@/components/ui/Badge'
import Button from '@/components/ui/Button'
import { Modal, ModalContent, ModalHeader, ModalTitle, ModalDescription, ModalFooter } from '@/components/ui/Modal'
import { PageLoadingSpinner } from '@/components/ui/LoadingSpinner'
import { EmptyState } from '@/components/ui/EmptyState'
import { formatDate } from '@/lib/utils'
import toast from 'react-hot-toast'
import {
  ArrowLeft, Edit, Calendar, Clock, User, FileText,
  CheckCircle, Ban
} from 'lucide-react'
import type { Appointment } from '@/types'

export default function AppointmentDetailPage() {
  const navigate = useNavigate()
  const { id } = useParams<{ id: string }>()
  const [cancelDialogOpen, setCancelDialogOpen] = useState(false)
  const [cancelReason, setCancelReason] = useState('')

  const { data: appointment, isLoading } = useAppointment(id!)
  const confirmAppointment = useConfirmAppointment()
  const cancelAppointment = useCancelAppointment()
  const completeAppointment = useCompleteAppointment()

  if (isLoading) return <PageLoadingSpinner />
  if (!appointment) return <EmptyState title="Appointment not found" />

  const typeLabels: Record<string, string> = {
    consultation: 'Consultation',
    follow_up: 'Follow-up',
    emergency: 'Emergency',
    telemedicine: 'Telemedicine',
  }

  const statusLabels: Record<string, string> = {
    scheduled: 'Scheduled',
    confirmed: 'Confirmed',
    in_progress: 'In Progress',
    completed: 'Completed',
    cancelled: 'Cancelled',
    no_show: 'No Show',
  }

  const canConfirm = appointment.status === 'scheduled'
  const canComplete = appointment.status === 'confirmed' || appointment.status === 'in_progress'
  const canCancel = appointment.status === 'scheduled' || appointment.status === 'confirmed'

  const handleConfirm = async () => {
    try {
      await confirmAppointment.mutateAsync(appointment.id)
      toast.success('Appointment confirmed successfully')
    } catch {
      toast.error('Failed to confirm appointment')
    }
  }

  const handleComplete = async () => {
    try {
      await completeAppointment.mutateAsync(appointment.id)
      toast.success('Appointment marked as completed')
    } catch {
      toast.error('Failed to complete appointment')
    }
  }

  const handleCancel = async () => {
    if (!cancelReason.trim()) {
      toast.error('Please enter a cancellation reason')
      return
    }
    try {
      await cancelAppointment.mutateAsync({ id: appointment.id, reason: cancelReason })
      toast.success('Appointment cancelled successfully')
      setCancelDialogOpen(false)
      setCancelReason('')
    } catch {
      toast.error('Failed to cancel appointment')
    }
  }

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div className="flex items-center gap-4">
          <Button variant="ghost" size="sm" onClick={() => navigate(-1)}>
            <ArrowLeft className="h-4 w-4" />
          </Button>
          <div>
            <h1 className="text-2xl font-bold text-gray-900">Appointment Details</h1>
            <p className="text-sm text-gray-500">
              {appointment.patient?.first_name} {appointment.patient?.last_name} — {formatDate(appointment.appointment_date)}
            </p>
          </div>
        </div>
        <div className="flex gap-2">
          {canConfirm && (
            <Button
              variant="outline"
              leftIcon={<CheckCircle className="h-4 w-4" />}
              onClick={handleConfirm}
              isLoading={confirmAppointment.isPending}
            >
              Confirm
            </Button>
          )}
          {canComplete && (
            <Button
              variant="outline"
              leftIcon={<CheckCircle className="h-4 w-4" />}
              onClick={handleComplete}
              isLoading={completeAppointment.isPending}
            >
              Complete
            </Button>
          )}
          {canCancel && (
            <Button
              variant="outline"
              leftIcon={<Ban className="h-4 w-4" />}
              onClick={() => setCancelDialogOpen(true)}
            >
              Cancel Appointment
            </Button>
          )}
          <Button leftIcon={<Edit className="h-4 w-4" />} onClick={() => navigate(`/appointments/${id}/edit`)}>
            Edit
          </Button>
        </div>
      </div>

      {/* Status & Type */}
      <div className="flex gap-3">
        <StatusBadge status={appointment.status} />
        {appointment.type && (
          <Badge variant="primary">{typeLabels[appointment.type] || appointment.type}</Badge>
        )}
      </div>

      {/* Appointment Info */}
      <Card variant="elevated">
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <Calendar className="h-5 w-5 text-primary-600" />
            Appointment Information
          </CardTitle>
        </CardHeader>
        <CardContent>
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <InfoItem
              label="Date"
              value={formatDate(appointment.appointment_date)}
              icon={<Calendar className="h-4 w-4 text-gray-400" />}
            />
            <InfoItem
              label="Time"
              value={`${appointment.start_time} - ${appointment.end_time}`}
              icon={<Clock className="h-4 w-4 text-gray-400" />}
            />
            <InfoItem
              label="Status"
              value={statusLabels[appointment.status] || appointment.status}
            />
            {appointment.confirmed_at && (
              <InfoItem
                label="Confirmed On"
                value={formatDate(appointment.confirmed_at)}
              />
            )}
            {appointment.cancelled_at && (
              <InfoItem
                label="Cancelled On"
                value={formatDate(appointment.cancelled_at)}
              />
            )}
            {appointment.cancellation_reason && (
              <InfoItem
                label="Cancellation reason"
                value={appointment.cancellation_reason}
              />
            )}
          </div>
        </CardContent>
      </Card>

      {/* Patient & Doctor */}
      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        <Card variant="elevated">
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <User className="h-5 w-5 text-primary-600" />
              Patient
            </CardTitle>
          </CardHeader>
          <CardContent>
            {appointment.patient ? (
              <div
                className="cursor-pointer hover:bg-gray-50 p-3 rounded-lg transition-colors"
                onClick={() => navigate(`/patients/${appointment.patient_id}`)}
              >
                <p className="font-medium text-gray-900">
                  {appointment.patient.first_name} {appointment.patient.last_name}
                </p>
                <p className="text-sm text-gray-500">
                  {appointment.patient.document_type}: {appointment.patient.document_number}
                </p>
                {appointment.patient.phone && (
                  <p className="text-sm text-gray-500">Tel: {appointment.patient.phone}</p>
                )}
              </div>
            ) : (
              <p className="text-sm text-gray-500">Patient ID: {appointment.patient_id}</p>
            )}
          </CardContent>
        </Card>

        <Card variant="elevated">
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <User className="h-5 w-5 text-primary-600" />
              Doctor
            </CardTitle>
          </CardHeader>
          <CardContent>
            {appointment.doctor ? (
              <div>
                <p className="font-medium text-gray-900">
                  Dr. {appointment.doctor.first_name} {appointment.doctor.last_name}
                </p>
                {appointment.doctor.email && (
                  <p className="text-sm text-gray-500">{appointment.doctor.email}</p>
                )}
              </div>
            ) : (
              <p className="text-sm text-gray-500">Doctor ID: {appointment.doctor_id}</p>
            )}
          </CardContent>
        </Card>
      </div>

      {/* Reason & Notes */}
      <Card variant="elevated">
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <FileText className="h-5 w-5 text-primary-600" />
            Clinical Details
          </CardTitle>
        </CardHeader>
        <CardContent>
          <div className="space-y-4">
            <div>
              <p className="text-sm text-gray-500 mb-1">Reason for visit</p>
              <p className="text-gray-900">{appointment.reason || 'Not specified'}</p>
            </div>
            {appointment.notes && (
              <div>
                <p className="text-sm text-gray-500 mb-1">Notes</p>
                <p className="text-gray-900 whitespace-pre-wrap">{appointment.notes}</p>
              </div>
            )}
          </div>
        </CardContent>
      </Card>

      {/* Cancel Dialog */}
      <Modal open={cancelDialogOpen} onOpenChange={setCancelDialogOpen}>
        <ModalContent size="sm">
          <ModalHeader>
            <ModalTitle>Cancel Appointment</ModalTitle>
            <ModalDescription>Enter the reason for cancellation</ModalDescription>
          </ModalHeader>
          <div className="px-6">
            <label className="block text-sm font-medium text-gray-700 mb-1.5">
              Reason <span className="text-red-500">*</span>
            </label>
            <textarea
              value={cancelReason}
              onChange={(e) => setCancelReason(e.target.value)}
              rows={3}
              className="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20 resize-y"
              placeholder="Describe the reason for cancellation..."
            />
          </div>
          <ModalFooter>
            <Button variant="ghost" onClick={() => setCancelDialogOpen(false)} disabled={cancelAppointment.isPending}>
              Close
            </Button>
            <Button variant="danger" onClick={handleCancel} isLoading={cancelAppointment.isPending}>
              Cancel Appointment
            </Button>
          </ModalFooter>
        </ModalContent>
      </Modal>
    </div>
  )
}

function InfoItem({ label, value, icon }: { label: string; value: string; icon?: React.ReactNode }) {
  return (
    <div>
      <p className="text-sm text-gray-500 mb-1">{label}</p>
      <div className="flex items-center gap-2">
        {icon}
        <p className="text-gray-900 font-medium">{value}</p>
      </div>
    </div>
  )
}
