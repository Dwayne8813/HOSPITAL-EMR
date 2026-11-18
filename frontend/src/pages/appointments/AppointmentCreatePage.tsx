import { useNavigate } from 'react-router-dom'
import { useForm } from 'react-hook-form'
import { zodResolver } from '@hookform/resolvers/zod'
import { z } from 'zod'
import { useCreateAppointment } from '@/hooks/useAppointments'
import { usePatients } from '@/hooks/usePatients'
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/Card'
import Input from '@/components/ui/Input'
import Select from '@/components/ui/Select'
import Button from '@/components/ui/Button'
import { ArrowLeft, Save, Calendar } from 'lucide-react'
import toast from 'react-hot-toast'
import { useState } from 'react'

const appointmentCreateSchema = z.object({
  patient_id: z.string().min(1, 'Patient is required'),
  doctor_id: z.string().min(1, 'Doctor is required'),
  appointment_date: z.string().min(1, 'Date is required'),
  start_time: z.string().min(1, 'Start time is required'),
  duration_minutes: z.string().optional(),
  type: z.string().optional(),
  reason: z.string().optional(),
  notes: z.string().optional(),
})

type AppointmentCreateFormData = z.infer<typeof appointmentCreateSchema>

export default function AppointmentCreatePage() {
  const navigate = useNavigate()
  const createAppointment = useCreateAppointment()
  const { data: patientsData } = usePatients({ per_page: 100 })
  const [doctors, setDoctors] = useState<{ id: string; name: string }[]>([])

  const { register, handleSubmit, watch, formState: { errors } } = useForm<AppointmentCreateFormData>({
    resolver: zodResolver(appointmentCreateSchema),
    defaultValues: {
      duration_minutes: '30',
      type: 'consultation',
    },
  })

  const selectedDate = watch('appointment_date')
  const selectedDoctor = watch('doctor_id')

  const onSubmit = async (data: AppointmentCreateFormData) => {
    try {
      const date = data.appointment_date
      const scheduledAt = `${date}T${data.start_time}:00`
      const duration = data.duration_minutes ? parseInt(data.duration_minutes) : 30

      const endDate = new Date(`${date}T${data.start_time}:00`)
      endDate.setMinutes(endDate.getMinutes() + duration)
      const endTime = endDate.toTimeString().slice(0, 5)

      await createAppointment.mutateAsync({
        patient_id: data.patient_id,
        doctor_id: data.doctor_id,
        scheduled_at: scheduledAt,
        duration_minutes: duration,
        type: data.type || undefined,
        reason: data.reason || undefined,
        notes: data.notes || undefined,
      })
      toast.success('Appointment created successfully')
      navigate('/appointments')
    } catch (error: any) {
      const message = error?.response?.data?.message || 'Failed to create appointment'
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
          <h1 className="text-2xl font-bold text-gray-900">New Appointment</h1>
          <p className="text-sm text-gray-500">Schedule a new medical appointment</p>
        </div>
      </div>

      <form onSubmit={handleSubmit(onSubmit)} className="space-y-6">
        {/* Patient & Doctor */}
        <Card variant="elevated">
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <Calendar className="h-5 w-5 text-primary-600" />
              Appointment Details
            </CardTitle>
          </CardHeader>
          <CardContent>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <Select
                label="Patient"
                required
                placeholder="Select a patient"
                options={
                  patientsData?.data?.map((p) => ({
                    value: String(p.id),
                    label: `${p.first_name} ${p.last_name} — ${p.document_number}`,
                  })) || []
                }
                {...register('patient_id')}
                error={errors.patient_id?.message}
              />
              <Input
                label="Doctor ID"
                required
                placeholder="Enter the doctor ID"
                {...register('doctor_id')}
                error={errors.doctor_id?.message}
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
                  min={new Date().toISOString().split('T')[0]}
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
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1.5">Reason</label>
                <textarea
                  {...register('reason')}
                  rows={2}
                  className="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20 resize-y"
                  placeholder="Reason for the visit..."
                />
              </div>
              <div className="md:col-span-2">
                <label className="block text-sm font-medium text-gray-700 mb-1.5">Notes</label>
                <textarea
                  {...register('notes')}
                  rows={3}
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
            isLoading={createAppointment.isPending}
          >
            Create Appointment
          </Button>
        </div>
      </form>
    </div>
  )
}
