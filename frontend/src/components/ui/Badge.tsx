import { cn } from '@/lib/utils'

interface BadgeProps extends React.HTMLAttributes<HTMLDivElement> {
  variant?: 'default' | 'primary' | 'secondary' | 'success' | 'warning' | 'danger' | 'info'
  size?: 'sm' | 'md'
}

export function Badge({ className, variant = 'default', size = 'sm', ...props }: BadgeProps) {
  const variants = {
    default: 'bg-gray-100 text-gray-800',
    primary: 'bg-primary-100 text-primary-800',
    secondary: 'bg-secondary-100 text-secondary-800',
    success: 'bg-green-100 text-green-800',
    warning: 'bg-yellow-100 text-yellow-800',
    danger: 'bg-red-100 text-red-800',
    info: 'bg-blue-100 text-blue-800',
  }

  const sizes = {
    sm: 'px-2 py-0.5 text-xs',
    md: 'px-2.5 py-1 text-sm',
  }

  return (
    <div
      className={cn(
        'inline-flex items-center rounded-full font-medium',
        variants[variant],
        sizes[size],
        className
      )}
      {...props}
    />
  )
}

export function StatusBadge({ status, className }: { status: string; className?: string }) {
  const statusConfig: Record<string, { variant: BadgeProps['variant']; label: string }> = {
    active: { variant: 'success', label: 'Active' },
    inactive: { variant: 'danger', label: 'Inactive' },
    pending: { variant: 'warning', label: 'Pending' },
    completed: { variant: 'success', label: 'Completed' },
    cancelled: { variant: 'danger', label: 'Cancelled' },
    in_progress: { variant: 'info', label: 'In Progress' },
    scheduled: { variant: 'primary', label: 'Scheduled' },
    confirmed: { variant: 'success', label: 'Confirmed' },
    paid: { variant: 'success', label: 'Paid' },
    partial: { variant: 'warning', label: 'Partial' },
    draft: { variant: 'default', label: 'Draft' },
    available: { variant: 'success', label: 'Available' },
    occupied: { variant: 'danger', label: 'Occupied' },
    maintenance: { variant: 'warning', label: 'Maintenance' },
    routine: { variant: 'default', label: 'Routine' },
    urgent: { variant: 'danger', label: 'Urgent' },
    stat: { variant: 'danger', label: 'STAT' },
  }

  const config = statusConfig[status] || { variant: 'default' as const, label: status }

  return (
    <Badge variant={config.variant} className={className}>
      {config.label}
    </Badge>
  )
}
