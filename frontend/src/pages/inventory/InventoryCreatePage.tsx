import { useState } from 'react'
import { useNavigate, useParams } from 'react-router-dom'
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/Card'
import Input from '@/components/ui/Input'
import Select from '@/components/ui/Select'
import Button from '@/components/ui/Button'
import { ArrowLeft, Save, Package } from 'lucide-react'

export default function InventoryCreatePage() {
  const navigate = useNavigate()
  const { id } = useParams<{ id: string }>()
  const [formData, setFormData] = useState({
    name: '',
    category: '',
    quantity: 0,
    min_quantity: 10,
    unit: '',
    location: '',
    supplier: '',
    cost: 0,
  })

  const updateField = (field: string, value: string | number) => {
    setFormData(prev => ({ ...prev, [field]: value }))
  }

  const handleSave = () => {
    console.log('Saving inventory item:', formData)
    navigate('/inventory')
  }

  return (
    <div className="space-y-6">
      <div className="flex items-center gap-4">
        <Button variant="ghost" size="sm" onClick={() => navigate(-1)}>
          <ArrowLeft className="h-4 w-4" />
        </Button>
        <div>
          <h1 className="text-2xl font-bold text-gray-900">
            {id ? 'Editar Ítem' : 'Nuevo Ítem de Inventario'}
          </h1>
          <p className="text-sm text-gray-500">
            {id ? 'Actualizar datos del ítem' : 'Registrar nuevo ítem en inventario'}
          </p>
        </div>
      </div>

      <Card variant="elevated">
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <Package className="h-5 w-5 text-primary-600" />
            Información del Ítem
          </CardTitle>
        </CardHeader>
        <CardContent className="space-y-4">
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <Input label="Nombre" required value={formData.name} onChange={(e) => updateField('name', e.target.value)} />
            <Select
              label="Categoría"
              required
              options={[
                { value: 'medical', label: 'Material Médico' },
                { value: 'surgical', label: 'Material Quirúrgico' },
                { value: 'office', label: 'Material de Oficina' },
                { value: 'cleaning', label: 'Limpieza' },
                { value: 'other', label: 'Otros' },
              ]}
              value={formData.category}
              onChange={(e) => updateField('category', e.target.value)}
            />
            <Input label="Unidad" required placeholder="ej: Unidades, Cajas" value={formData.unit} onChange={(e) => updateField('unit', e.target.value)} />
            <Input label="Cantidad" type="number" min={0} value={formData.quantity} onChange={(e) => updateField('quantity', parseInt(e.target.value) || 0)} />
            <Input label="Cantidad Mínima" type="number" min={0} value={formData.min_quantity} onChange={(e) => updateField('min_quantity', parseInt(e.target.value) || 0)} />
            <Input label="Ubicación" value={formData.location} onChange={(e) => updateField('location', e.target.value)} />
            <Input label="Costo Unitario" type="number" step="0.01" min={0} value={formData.cost} onChange={(e) => updateField('cost', parseFloat(e.target.value) || 0)} />
          </div>
        </CardContent>
      </Card>

      <div className="flex justify-end gap-3">
        <Button variant="outline" onClick={() => navigate(-1)}>Cancelar</Button>
        <Button leftIcon={<Save className="h-4 w-4" />} onClick={handleSave}>
          {id ? 'Guardar Cambios' : 'Crear Ítem'}
        </Button>
      </div>
    </div>
  )
}
