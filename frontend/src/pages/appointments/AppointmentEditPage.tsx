import { useEffect } from 'react'
import { useNavigate, useParams } from 'react-router-dom'
import { useForm } from 'react-hook-form'
import { zodResolver } from '@hookform/resolvers/zod'
import { z } from 'zod'
import { useAppointment, useUpdateAppointment } from '@/hooks/useAppointments'
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/Card'
import Input from '@/components/ui/Input'
import Select from '@/components/ui/Select'
import Button from '@/components/ui/Button'
import { PageLoadingSpinner } from '@/components/ui/LoadingSpinner'
import { EmptyState } from '@/components/ui/EmptyState'
import { ArrowLeft, Save, Calendar } from 'lucide-react'
import toast from 'react-hot-toast'

const appointmentEditSchema = z.object({
  doctor_id: z.string().min(1, 'Doctor is required'),
  appointment_date: z.string().min(1, 'Date is required'),
  start_time: z.string().min(1, 'Start time is required'),
  duration_minutes: z.string().optional(),
  type: z.string().optional(),
  reason: z.string().optional(),
  notes: z.string().optional(),
})

type AppointmentEditFormData = z.infer<typeof appointmentEditSchema>

export default function AppointmentEditPage() {
  const navigate = useNavigate()
  const { id } = useParams<{ id: string }>()
  const { data: appointment, isLoading } = useAppointment(id!)
  const updateAppointment = useUpdateAppointment()

  const { register, handleSubmit, reset, formState: { errors } } = useForm<AppointmentEditFormData>({
    resolver: zodResolver(appointmentEditSchema),
  })

  useEffect(() => {
    if (appointment) {
      reset({
        doctor_id: String(appointment.doctor_id),
        appointment_date: appointment.appointment_date,
        start_time: appointment.start_time,
        duration_minutes: '30',
        type: appointment.type || 'consultation',
        reason: appointment.reason || '',
        notes: appointment.notes || '',
      })
    }
  }, [appointment, reset])

  if (isLoading) return <PageLoadingSpinner />
  if (!appointment) return <EmptyState title="Appointment not found" />

  const onSubmit = async (data: AppointmentEditFormData) => {
    try {
      const date = data.appointment_date
      const scheduledAt = `${date}T${data.start_time}:00`

      await updateAppointment.mutateAsync({
        id: appointment.id,
        data: {
          doctor_id: data.doctor_id,
          appointment_date: date,
          start_time: data.start_time,
          type: (data.type as any) || undefined,
          reason: data.reason || undefined,
          notes: data.notes || undefined,
        },
      })
      toast.success('Appointment updated successfully')
      navigate(`/appointments/${id}`)
    } catch (error: any) {
      const message = error?.response?.data?.message || 'Failed to update appointment'
      toast.error(message)
    }
  }

  return (
    <div className="space-y-6">
      <div className="flex items-center gap-4">
        <Button variant="ghost" size="sm" onClick={() => navigate(-1)}>
          <ArrowLeft className="h-4 w-4" />
        </Button>
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Edit Appointment</h1>
          <p className="text-sm text-gray-500">
            {appointment.patient?.first_name} {appointment.patient?.last_name} — {appointment.appointment_date}
          </p>
        </div>
      </div>

      <form onSubmit={handleSubmit(onSubmit)} className="space-y-6">
        {/* Doctor */}
        <Card variant="elevated">
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <Calendar className="h-5 w-5 text-primary-600" />
              Appointment Details
            </CardTitle>
          </CardHeader>
          <CardContent>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <Input
                label="Doctor ID"
                required
                {...register('doctor_id')}
                error={errors.doctor_id?.message}
              />
              <Select
                label="Appointment Type"
                options={[
                  { value: 'consultation', label: 'Consultation' },
                  { value: 'follow_up', label: 'Follow-up' },
                  { value: 'emergency', label: 'Emergency' },
                  { value: 'telemedicine', label: 'Telemedicine' },
                ]}
                {...register('type')}
              />
            </div>
          </CardContent>
        </Card>

        {/* Schedule */}
        <Card variant="elevated">
          <CardHeader>
            <CardTitle>Schedule</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1.5">
                  Date <span className="text-red-500 ml-1">*</span>
                </label>
                <input
                  type="date"
                  {...register('appointment_date')}
                  className="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20"
                />
                {errors.appointment_date && (
                  <p className="mt-1.5 text-sm text-red-600">{errors.appointment_date.message}</p>
                )}
              </div>
              <Input
                label="Start Time"
                required
                type="time"
                {...register('start_time')}
                error={errors.start_time?.message}
              />
              <Select
                label="Duration"
                options={[
                  { value: '15', label: '15 minutes' },
                  { value: '30', label: '30 minutes' },
                  { value: '45', label: '45 minutes' },
                  { value: '60', label: '1 hour' },
                  { value: '90', label: '1.5 hours' },
                  { value: '120', label: '2 hours' },
                ]}
                {...register('duration_minutes')}
              />
            </div>
          </CardContent>
        </Card>

        {/* Details */}
        <Card variant="elevated">
          <CardHeader>
            <CardTitle>Details</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1.5">Reason</label>
                <textarea
                  {...register('reason')}
                  rows={2}
                  className="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20 resize-y"
                  placeholder="Reason for the visit..."
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1.5">Notes</label>
                <textarea
                  {...register('notes')}
                  rows={2}
                  className="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20 resize-y"
                  placeholder="Additional notes..."
                />
              </div>
            </div>
          </CardContent>
        </Card>

        {/* Actions */}
        <div className="flex justify-end gap-3">
          <Button type="button" variant="outline" onClick={() => navigate(-1)}>
            Cancel
          </Button>
          <Button
            type="submit"
            leftIcon={<Save className="h-4 w-4" />}
            isLoading={updateAppointment.isPending}
          >
            Save Changes
          </Button>
        </div>
      </form>
    </div>
  )
}
