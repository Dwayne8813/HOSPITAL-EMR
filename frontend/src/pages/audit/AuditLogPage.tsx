import { useState } from 'react'
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/Card'
import { Badge } from '@/components/ui/Badge'
import { EmptyState } from '@/components/ui/EmptyState'
import { formatDateTime } from '@/lib/utils'
import { History, User, FileText, ArrowUpDown } from 'lucide-react'

interface AuditEntry {
  id: string
  user_name: string
  action: string
  resource: string
  resource_id: string
  details: string
  ip_address: string
  created_at: string
}

export default function AuditLogPage() {
  const [actionFilter, setActionFilter] = useState('')
  const [resourceFilter, setResourceFilter] = useState('')

  const logs: AuditEntry[] = []

  const actionColors: Record<string, string> = {
    create: 'bg-green-100 text-green-800',
    update: 'bg-blue-100 text-blue-800',
    delete: 'bg-red-100 text-red-800',
    login: 'bg-purple-100 text-purple-800',
    logout: 'bg-gray-100 text-gray-800',
  }

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-bold text-gray-900">Audit Log</h1>
        <p className="text-sm text-gray-500">System action history</p>
      </div>

      <Card variant="elevated">
        <CardHeader>
          <div className="flex flex-col sm:flex-row gap-3">
            <select
              value={actionFilter}
              onChange={(e) => setActionFilter(e.target.value)}
              className="block w-full sm:w-40 rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20"
            >
              <option value="">All actions</option>
              <option value="create">Create</option>
              <option value="update">Update</option>
              <option value="delete">Delete</option>
              <option value="login">Login</option>
            </select>
            <select
              value={resourceFilter}
              onChange={(e) => setResourceFilter(e.target.value)}
              className="block w-full sm:w-48 rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20"
            >
              <option value="">All resources</option>
              <option value="patient">Patients</option>
              <option value="consultation">Consultations</option>
              <option value="invoice">Invoices</option>
              <option value="user">Users</option>
            </select>
          </div>
        </CardHeader>
        <CardContent>
          {logs.length === 0 ? (
            <EmptyState
              title="No records"
              description="The audit history will appear here"
              icon={<History className="h-8 w-8 text-gray-400" />}
            />
          ) : (
            <div className="space-y-3">
              {logs.map((log) => (
                <div key={log.id} className="p-4 bg-gray-50 rounded-lg">
                  <div className="flex items-start justify-between">
                    <div className="flex items-start gap-3">
                      <div className="p-2 bg-white rounded-lg border">
                        <User className="h-4 w-4 text-gray-500" />
                      </div>
                      <div>
                        <div className="flex items-center gap-2">
                          <p className="font-medium text-gray-900">{log.user_name}</p>
                          <Badge className={actionColors[log.action] || 'bg-gray-100 text-gray-800'}>
                            {log.action}
                          </Badge>
                          <span className="text-sm text-gray-500">{log.resource}</span>
                        </div>
                        <p className="text-sm text-gray-500 mt-1">{log.details}</p>
                        <div className="flex items-center gap-4 mt-2 text-xs text-gray-400">
                          <span>{formatDateTime(log.created_at)}</span>
                          <span>IP: {log.ip_address}</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          )}
        </CardContent>
      </Card>
    </div>
  )
}
