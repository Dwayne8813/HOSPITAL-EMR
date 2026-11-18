import { useState } from 'react'
import { useNavigate, useParams } from 'react-router-dom'
import { useInvoice, useRecordPayment } from '@/hooks/useBilling'
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/Card'
import { StatusBadge } from '@/components/ui/Badge'
import Button from '@/components/ui/Button'
import Input from '@/components/ui/Input'
import Select from '@/components/ui/Select'
import { PageLoadingSpinner } from '@/components/ui/LoadingSpinner'
import { EmptyState } from '@/components/ui/EmptyState'
import { formatDate, formatCurrency } from '@/lib/utils'
import { ArrowLeft, CreditCard, Save, FileText } from 'lucide-react'

export default function InvoiceDetailPage() {
  const navigate = useNavigate()
  const { id } = useParams<{ id: string }>()
  const { data: invoice, isLoading } = useInvoice(id!)
  const recordPayment = useRecordPayment()

  const [showPaymentForm, setShowPaymentForm] = useState(false)
  const [paymentAmount, setPaymentAmount] = useState(0)
  const [paymentMethod, setPaymentMethod] = useState('cash')
  const [paymentReference, setPaymentReference] = useState('')

  if (isLoading) return <PageLoadingSpinner />
  if (!invoice) return <EmptyState title="Invoice not found" />

  const totalPaid = invoice.payments?.reduce((sum, p) => sum + p.amount, 0) || 0
  const balance = invoice.total_amount - totalPaid

  const handleRecordPayment = async () => {
    try {
      await recordPayment.mutateAsync({
        invoice_id: invoice.id,
        amount: paymentAmount,
        payment_method: paymentMethod as any,
        reference_number: paymentReference || undefined,
      })
      setShowPaymentForm(false)
      setPaymentAmount(0)
      setPaymentReference('')
    } catch (error) {
      console.error('Error recording payment:', error)
    }
  }

  return (
    <div className="space-y-6">
      <div className="flex items-center gap-4">
        <Button variant="ghost" size="sm" onClick={() => navigate(-1)}>
          <ArrowLeft className="h-4 w-4" />
        </Button>
        <div className="flex-1">
          <h1 className="text-2xl font-bold text-gray-900">
            Invoice {invoice.invoice_number}
          </h1>
          <p className="text-sm text-gray-500">
            {invoice.patient?.first_name} {invoice.patient?.last_name} • {formatDate(invoice.invoice_date)}
          </p>
        </div>
        <StatusBadge status={invoice.payment_status} />
        <Button
          variant="outline"
          size="sm"
          onClick={() => window.open(`/billing/invoices/${invoice.id}/print`, '_blank')}
        >
          <FileText className="h-4 w-4 mr-1" />
          Print
        </Button>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* Invoice Items */}
        <div className="lg:col-span-2 space-y-6">
          <Card variant="elevated">
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <FileText className="h-5 w-5 text-primary-600" />
                Service Details
              </CardTitle>
            </CardHeader>
            <CardContent>
              <div className="overflow-x-auto">
                <table className="min-w-full">
                  <thead>
                    <tr className="border-b">
                      <th className="text-left py-3 text-sm font-medium text-gray-500">Description</th>
                      <th className="text-right py-3 text-sm font-medium text-gray-500">Qty</th>
                      <th className="text-right py-3 text-sm font-medium text-gray-500">Price</th>
                      <th className="text-right py-3 text-sm font-medium text-gray-500">Disc.</th>
                      <th className="text-right py-3 text-sm font-medium text-gray-500">Subtotal</th>
                    </tr>
                  </thead>
                  <tbody>
                    {invoice.items.map((item) => (
                      <tr key={item.id} className="border-b">
                        <td className="py-3 text-sm text-gray-900">{item.description}</td>
                        <td className="py-3 text-sm text-gray-900 text-right">{item.quantity}</td>
                        <td className="py-3 text-sm text-gray-900 text-right">{formatCurrency(item.unit_price)}</td>
                        <td className="py-3 text-sm text-gray-900 text-right">{formatCurrency(item.discount)}</td>
                        <td className="py-3 text-sm font-medium text-gray-900 text-right">{formatCurrency(item.subtotal)}</td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </CardContent>
          </Card>

          {/* Payments */}
          <Card variant="elevated">
            <CardHeader>
              <CardTitle>Recorded Payments</CardTitle>
            </CardHeader>
            <CardContent>
              {invoice.payments && invoice.payments.length > 0 ? (
                <div className="space-y-3">
                  {invoice.payments.map((payment) => (
                    <div key={payment.id} className="p-3 bg-green-50 rounded-lg">
                      <div className="flex items-center justify-between">
                        <div>
                          <p className="font-medium text-green-900">{formatCurrency(payment.amount)}</p>
                          <p className="text-sm text-green-700">{payment.payment_method} {payment.reference_number && `- ${payment.reference_number}`}</p>
                        </div>
                        <p className="text-sm text-green-600">{formatDate(payment.received_at)}</p>
                      </div>
                    </div>
                  ))}
                </div>
              ) : (
                <p className="text-sm text-gray-500 text-center py-4">No payments recorded</p>
              )}
            </CardContent>
          </Card>
        </div>

        {/* Summary & Actions */}
        <div className="space-y-6">
          <Card variant="elevated">
            <CardHeader>
              <CardTitle>Summary</CardTitle>
            </CardHeader>
            <CardContent className="space-y-3">
              <div className="flex justify-between">
                <span className="text-gray-500">Subtotal</span>
                <span>{formatCurrency(invoice.subtotal)}</span>
              </div>
              <div className="flex justify-between">
                <span className="text-gray-500">Tax</span>
                <span>{formatCurrency(invoice.tax_amount)}</span>
              </div>
              <div className="flex justify-between">
                <span className="text-gray-500">Discount</span>
                <span>-{formatCurrency(invoice.discount)}</span>
              </div>
              <div className="border-t pt-3 flex justify-between">
                <span className="font-bold">Total</span>
                <span className="font-bold text-lg">{formatCurrency(invoice.total_amount)}</span>
              </div>
              <div className="border-t pt-3 flex justify-between">
                <span className="text-gray-500">Paid</span>
                <span className="text-green-600">{formatCurrency(totalPaid)}</span>
              </div>
              <div className="flex justify-between">
                <span className="font-bold">Balance</span>
                <span className={`font-bold ${balance > 0 ? 'text-red-600' : 'text-green-600'}`}>
                  {formatCurrency(balance)}
                </span>
              </div>
            </CardContent>
          </Card>

          {balance > 0 && (
            <Card variant="elevated">
              <CardContent>
                <Button className="w-full" leftIcon={<CreditCard className="h-4 w-4" />} onClick={() => setShowPaymentForm(true)}>
                  Record Payment
                </Button>
              </CardContent>
            </Card>
          )}
        </div>
      </div>

      {/* Payment Modal */}
      {showPaymentForm && (
        <Card variant="elevated">
          <CardHeader>
            <CardTitle>Record Payment</CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            <Input
              label="Amount"
              type="number"
              step="0.01"
              min={0.01}
              max={balance}
              value={paymentAmount}
              onChange={(e) => setPaymentAmount(parseFloat(e.target.value) || 0)}
            />
            <Select
              label="Payment Method"
              options={[
                { value: 'cash', label: 'Cash' },
                { value: 'card', label: 'Card' },
                { value: 'transfer', label: 'Transfer' },
                { value: 'insurance', label: 'Insurance' },
                { value: 'mixed', label: 'Mixed' },
              ]}
              value={paymentMethod}
              onChange={(e) => setPaymentMethod(e.target.value)}
            />
            <Input
              label="Reference"
              value={paymentReference}
              onChange={(e) => setPaymentReference(e.target.value)}
              placeholder="Reference or receipt number"
            />
            <div className="flex justify-end gap-3">
              <Button variant="outline" onClick={() => setShowPaymentForm(false)}>Cancel</Button>
              <Button leftIcon={<Save className="h-4 w-4" />} onClick={handleRecordPayment} isLoading={recordPayment.isPending} disabled={paymentAmount <= 0}>
                Save Payment
              </Button>
            </div>
          </CardContent>
        </Card>
      )}
    </div>
  )
}
