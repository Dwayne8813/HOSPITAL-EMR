import { useState } from 'react'
import { useNavigate, useParams } from 'react-router-dom'
import { useHospitalization } from '@/hooks/useHospitalization'
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/Card'
import { StatusBadge, Badge } from '@/components/ui/Badge'
import Button from '@/components/ui/Button'
import { PageLoadingSpinner } from '@/components/ui/LoadingSpinner'
import { EmptyState } from '@/components/ui/EmptyState'
import { formatDate } from '@/lib/utils'
import { ArrowLeft, Bed, User, Calendar, FileText, Activity } from 'lucide-react'

export default function HospitalizationDetailPage() {
  const navigate = useNavigate()
  const { id } = useParams<{ id: string }>()
  const { data: hospitalization, isLoading } = useHospitalization(id!)

  if (isLoading) return <PageLoadingSpinner />
  if (!hospitalization) return <EmptyState title="Hospitalización no encontrada" />

  return (
    <div className="space-y-6">
      <div className="flex items-center gap-4">
        <Button variant="ghost" size="sm" onClick={() => navigate(-1)}>
          <ArrowLeft className="h-4 w-4" />
        </Button>
        <div className="flex-1">
          <h1 className="text-2xl font-bold text-gray-900">
            Hospitalización - {hospitalization.patient?.first_name} {hospitalization.patient?.last_name}
          </h1>
          <p className="text-sm text-gray-500">
            Ingresado el {formatDate(hospitalization.admission_date)}
          </p>
        </div>
        <StatusBadge status={hospitalization.status} />
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* Patient Info */}
        <Card variant="elevated">
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <User className="h-5 w-5 text-primary-600" />
              Paciente
            </CardTitle>
          </CardHeader>
          <CardContent className="space-y-3">
            <div>
              <p className="text-sm text-gray-500">Nombre</p>
              <p className="font-medium">{hospitalization.patient?.first_name} {hospitalization.patient?.last_name}</p>
            </div>
            <div>
              <p className="text-sm text-gray-500">Documento</p>
              <p className="font-medium">{hospitalization.patient?.document_number}</p>
            </div>
            <div>
              <p className="text-sm text-gray-500">Teléfono</p>
              <p className="font-medium">{hospitalization.patient?.phone || 'No registrado'}</p>
            </div>
          </CardContent>
        </Card>

        {/* Admission Details */}
        <Card variant="elevated">
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <Bed className="h-5 w-5 text-primary-600" />
              Detalles del Ingreso
            </CardTitle>
          </CardHeader>
          <CardContent className="space-y-3">
            <div>
              <p className="text-sm text-gray-500">Cama</p>
              <p className="font-medium">{hospitalization.bed?.number || 'Sin asignar'}</p>
            </div>
            <div>
              <p className="text-sm text-gray-500">Departamento</p>
              <p className="font-medium">{hospitalization.department || 'No especificado'}</p>
            </div>
            <div>
              <p className="text-sm text-gray-500">Médico</p>
              <p className="font-medium">Dr. {hospitalization.doctor?.first_name} {hospitalization.doctor?.last_name}</p>
            </div>
            <div>
              <p className="text-sm text-gray-500">Fecha de Ingreso</p>
              <p className="font-medium">{formatDate(hospitalization.admission_date)}</p>
            </div>
            {hospitalization.discharge_date && (
              <div>
                <p className="text-sm text-gray-500">Fecha de Alta</p>
                <p className="font-medium">{formatDate(hospitalization.discharge_date)}</p>
              </div>
            )}
          </CardContent>
        </Card>

        {/* Clinical Info */}
        <Card variant="elevated">
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <FileText className="h-5 w-5 text-primary-600" />
              Información Clínica
            </CardTitle>
          </CardHeader>
          <CardContent className="space-y-3">
            <div>
              <p className="text-sm text-gray-500">Motivo de Ingreso</p>
              <p className="font-medium">{hospitalization.reason}</p>
            </div>
            <div>
              <p className="text-sm text-gray-500">Diagnóstico</p>
              <p className="font-medium">{hospitalization.diagnosis || 'No registrado'}</p>
            </div>
            <div>
              <p className="text-sm text-gray-500">Observaciones</p>
              <p className="font-medium">{hospitalization.notes || 'Ninguna'}</p>
            </div>
          </CardContent>
        </Card>
      </div>

      {/* Actions */}
      <Card variant="elevated">
        <CardHeader>
          <CardTitle>Acciones</CardTitle>
        </CardHeader>
        <CardContent>
          <div className="flex flex-wrap gap-3">
            <Button variant="outline">Ver Evoluciones</Button>
            <Button variant="outline">Nueva Evolución</Button>
            <Button variant="outline">Agregar Orden</Button>
            {hospitalization.status === 'active' && (
              <Button variant="success">Dar de Alta</Button>
            )}
          </div>
        </CardContent>
      </Card>
    </div>
  )
}
