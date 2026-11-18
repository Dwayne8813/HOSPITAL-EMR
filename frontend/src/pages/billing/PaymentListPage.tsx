import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { usePaymentsList } from '@/hooks/useBilling'
import { DataTable } from '@/components/ui/DataTable'
import { Card, CardHeader } from '@/components/ui/Card'
import Button from '@/components/ui/Button'
import { formatDate, formatCurrency } from '@/lib/utils'
import { ArrowLeft, CreditCard } from 'lucide-react'
import type { Payment } from '@/types'

export default function PaymentListPage() {
  const navigate = useNavigate()
  const [page, setPage] = useState(1)
  const [methodFilter, setMethodFilter] = useState('')

  const { data, isLoading } = usePaymentsList({
    method: methodFilter || undefined,
    page,
    per_page: 15,
  })

  const columns = [
    {
      key: 'amount',
      header: 'Amount',
      render: (item: Payment) => (
        <span className="font-medium text-green-700">{formatCurrency(item.amount)}</span>
      ),
    },
    {
      key: 'invoice',
      header: 'Invoice',
      render: (item: Payment) => (
        <span className="font-mono text-primary-700">{item.invoice?.invoice_number || '-'}</span>
      ),
    },
    {
      key: 'patient',
      header: 'Patient',
      render: (item: Payment) => (
        <p className="text-sm text-gray-900">
          {item.invoice?.patient?.first_name} {item.invoice?.patient?.last_name}
        </p>
      ),
    },
    {
      key: 'payment_method',
      header: 'Method',
      render: (item: Payment) => (
        <span className="capitalize">{item.payment_method}</span>
      ),
    },
    {
      key: 'reference_number',
      header: 'Reference',
      render: (item: Payment) => (
        <span className="text-sm text-gray-500">{item.reference_number || '-'}</span>
      ),
    },
    {
      key: 'received_at',
      header: 'Date',
      render: (item: Payment) => formatDate(item.received_at),
    },
  ]

  return (
    <div className="space-y-6">
      <div className="flex items-center gap-4">
        <Button variant="ghost" size="sm" onClick={() => navigate(-1)}>
          <ArrowLeft className="h-4 w-4" />
        </Button>
        <div className="flex-1">
          <h1 className="text-2xl font-bold text-gray-900">Payment History</h1>
          <p className="text-sm text-gray-500">Record of all payments received</p>
        </div>
      </div>

      <Card variant="elevated">
        <CardHeader>
          <select
            value={methodFilter}
            onChange={(e) => { setMethodFilter(e.target.value); setPage(1) }}
            className="block w-full sm:w-40 rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20"
          >
            <option value="">All methods</option>
            <option value="cash">Cash</option>
            <option value="card">Card</option>
            <option value="transfer">Transfer</option>
            <option value="insurance">Insurance</option>
            <option value="mixed">Mixed</option>
          </select>
        </CardHeader>
        <DataTable
          columns={columns}
          data={data?.data || []}
          isLoading={isLoading}
          pagination={data?.meta ? {
            current_page: data.meta.current_page,
            last_page: data.meta.last_page,
            per_page: data.meta.per_page,
            total: data.meta.total,
            onPageChange: setPage,
          } : undefined}
          emptyTitle="No payments recorded"
          emptyDescription="Payments made will appear here"
        />
      </Card>
    </div>
  )
}
