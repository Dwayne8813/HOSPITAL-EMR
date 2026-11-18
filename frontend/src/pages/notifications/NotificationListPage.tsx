import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { Card, CardContent } from '@/components/ui/Card'
import Button from '@/components/ui/Button'
import { EmptyState } from '@/components/ui/EmptyState'
import { formatRelativeTime } from '@/lib/utils'
import api from '@/lib/api'
import { Bell, Check, CheckCheck, Info, AlertTriangle, CheckCircle, XCircle } from 'lucide-react'
import toast from 'react-hot-toast'

interface Notification {
  id: string
  title: string
  message: string
  type: 'info' | 'success' | 'warning' | 'error'
  read: boolean
  link?: string
  created_at: string
}

export default function NotificationListPage() {
  const queryClient = useQueryClient()

  const { data: notifications = [], isLoading } = useQuery({
    queryKey: ['notifications'],
    queryFn: async () => {
      const response = await api.get('/notifications', { params: { per_page: 100 } })
      return (response.data?.data ?? []) as Notification[]
    },
  })

  const refresh = () => {
    queryClient.invalidateQueries({ queryKey: ['notifications'] })
    queryClient.invalidateQueries({ queryKey: ['notificationUnreadCount'] })
  }

  const markAsReadMutation = useMutation({
    mutationFn: (id: string) => api.post(`/notifications/${id}/read`),
    onSuccess: refresh,
    onError: () => toast.error('Failed to mark notification as read'),
  })

  const markAllAsReadMutation = useMutation({
    mutationFn: () => api.post('/notifications/read-all'),
    onSuccess: refresh,
    onError: () => toast.error('Failed to mark all notifications as read'),
  })

  const typeIcons = {
    info: <Info className="h-5 w-5 text-blue-500" />,
    success: <CheckCircle className="h-5 w-5 text-green-500" />,
    warning: <AlertTriangle className="h-5 w-5 text-yellow-500" />,
    error: <XCircle className="h-5 w-5 text-red-500" />,
  }

  const unreadCount = notifications.filter(n => !n.read).length

  return (
    <div className="space-y-6">
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Notifications</h1>
          <p className="text-sm text-gray-500">
            {unreadCount > 0 ? `${unreadCount} unread notification(s)` : 'All notifications read'}
          </p>
        </div>
        {unreadCount > 0 && (
          <Button
            variant="outline"
            leftIcon={<CheckCheck className="h-4 w-4" />}
            onClick={() => markAllAsReadMutation.mutate()}
            disabled={markAllAsReadMutation.isPending}
          >
            Mark all as read
          </Button>
        )}
      </div>

      <Card variant="elevated">
        <CardContent>
          {isLoading ? (
            <p className="text-sm text-gray-500">Loading notifications...</p>
          ) : notifications.length === 0 ? (
            <EmptyState
              title="No notifications"
              description="System notifications will appear here"
              icon={<Bell className="h-8 w-8 text-gray-400" />}
            />
          ) : (
            <div className="space-y-2">
              {notifications.map((notification) => (
                <div
                  key={notification.id}
                  className={`p-4 rounded-lg border-l-4 ${
                    notification.read ? 'bg-gray-50 border-gray-200' : 'bg-blue-50 border-blue-400'
                  }`}
                >
                  <div className="flex items-start justify-between">
                    <div className="flex items-start gap-3">
                      {typeIcons[notification.type]}
                      <div>
                        <p className={`font-medium ${notification.read ? 'text-gray-700' : 'text-gray-900'}`}>
                          {notification.title}
                        </p>
                        <p className="text-sm text-gray-500 mt-1">{notification.message}</p>
                        <p className="text-xs text-gray-400 mt-2">
                          {formatRelativeTime(notification.created_at)}
                        </p>
                      </div>
                    </div>
                    {!notification.read && (
                      <Button
                        variant="ghost"
                        size="sm"
                        onClick={() => markAsReadMutation.mutate(notification.id)}
                        disabled={markAsReadMutation.isPending}
                      >
                        <Check className="h-4 w-4" />
                      </Button>
                    )}
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
