import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { useForm } from 'react-hook-form'
import { zodResolver } from '@hookform/resolvers/zod'
import { patientSchema, type PatientFormData } from '@/validations'
import { useCreatePatient } from '@/hooks/usePatients'
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/Card'
import Input from '@/components/ui/Input'
import Select from '@/components/ui/Select'
import Button from '@/components/ui/Button'
import { DatePicker } from '@/components/ui/DatePicker'
import { ArrowLeft, Save, UserPlus } from 'lucide-react'
import { formatDate } from '@/lib/utils'

export default function PatientCreatePage() {
  const navigate = useNavigate()
  const createPatient = useCreatePatient()

  const { register, handleSubmit, setValue, watch, formState: { errors } } = useForm<PatientFormData>({
    resolver: zodResolver(patientSchema),
    defaultValues: {
      gender: 'M',
      document_type: 'CI',
    },
  })

  const onSubmit = async (data: PatientFormData) => {
    try {
      const patient = await createPatient.mutateAsync(data as any)
      navigate(`/patients/${patient.id}`)
    } catch (error) {
      console.error('Error creating patient:', error)
    }
  }

  return (
    <div className="space-y-6">
      <div className="flex items-center gap-4">
        <Button variant="ghost" size="sm" onClick={() => navigate(-1)}>
          <ArrowLeft className="h-4 w-4" />
        </Button>
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Register Patient</h1>
          <p className="text-sm text-gray-500">Enter the details for the new patient</p>
        </div>
      </div>

      <form onSubmit={handleSubmit(onSubmit)} className="space-y-6">
        {/* Personal Data */}
        <Card variant="elevated">
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <UserPlus className="h-5 w-5 text-primary-600" />
              Personal Details
            </CardTitle>
          </CardHeader>
          <CardContent>
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
              <Input
                label="First Name"
                required
                {...register('first_name')}
                error={errors.first_name?.message}
              />
              <Input
                label="Last Name"
                required
                {...register('last_name')}
                error={errors.last_name?.message}
              />
              <Input
                label="Second Last Name"
                {...register('second_last_name')}
              />
              <Select
                label="Document Type"
                required
                options={[
                  { value: 'CI', label: 'Identity Card' },
                  { value: 'PASSPORT', label: 'Passport' },
                  { value: 'OTHER', label: 'Other' },
                ]}
                {...register('document_type')}
                error={errors.document_type?.message}
              />
              <Input
                label="Document Number"
                required
                {...register('document_number')}
                error={errors.document_number?.message}
              />
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1.5">
                  Date of Birth <span className="text-red-500 ml-1">*</span>
                </label>
                <input
                  type="date"
                  {...register('birth_date')}
                  className="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20"
                />
                {errors.birth_date && (
                  <p className="mt-1.5 text-sm text-red-600">{errors.birth_date.message}</p>
                )}
              </div>
              <Select
                label="Sex"
                required
                options={[
                  { value: 'M', label: 'Male' },
                  { value: 'F', label: 'Female' },
                  { value: 'O', label: 'Other' },
                ]}
                {...register('gender')}
                error={errors.gender?.message}
              />
              <Select
                label="Blood Type"
                options={[
                  { value: 'A+', label: 'A+' },
                  { value: 'A-', label: 'A-' },
                  { value: 'B+', label: 'B+' },
                  { value: 'B-', label: 'B-' },
                  { value: 'AB+', label: 'AB+' },
                  { value: 'AB-', label: 'AB-' },
                  { value: 'O+', label: 'O+' },
                  { value: 'O-', label: 'O-' },
                ]}
                {...register('blood_type')}
              />
            </div>
          </CardContent>
        </Card>

        {/* Contact Information */}
        <Card variant="elevated">
          <CardHeader>
            <CardTitle>Contact Information</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
              <Input
                label="Phone"
                {...register('phone')}
              />
              <Input
                label="Email"
                type="email"
                {...register('email')}
                error={errors.email?.message}
              />
              <Input
                label="Address"
                {...register('address')}
              />
            </div>
          </CardContent>
        </Card>

        {/* Emergency Contact */}
        <Card variant="elevated">
          <CardHeader>
            <CardTitle>Emergency Contact</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <Input
                label="Contact Name"
                {...register('emergency_contact_name')}
              />
              <Input
                label="Contact Phone"
                {...register('emergency_contact_phone')}
              />
            </div>
          </CardContent>
        </Card>

        {/* Insurance */}
        <Card variant="elevated">
          <CardHeader>
            <CardTitle>Health Insurance</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <Input
                label="Insurance Provider"
                {...register('insurance_provider')}
              />
              <Input
                label="Policy Number"
                {...register('insurance_number')}
              />
            </div>
          </CardContent>
        </Card>

        {/* Medical History */}
        <Card variant="elevated">
          <CardHeader>
            <CardTitle>Medical History</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="space-y-4">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1.5">Allergies</label>
                <textarea
                  {...register('allergies')}
                  rows={3}
                  className="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20 resize-y"
                  placeholder="Describe known allergies..."
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1.5">Medical History</label>
                <textarea
                  {...register('medical_history')}
                  rows={4}
                  className="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20 resize-y"
                  placeholder="Previous illnesses, surgeries, medications..."
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
            isLoading={createPatient.isPending}
          >
            Register Patient
          </Button>
        </div>
      </form>
    </div>
  )
}
