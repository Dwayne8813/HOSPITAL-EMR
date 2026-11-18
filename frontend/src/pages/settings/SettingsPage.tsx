import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/Card'
import { Settings, Building2, Bell, Shield, Database, Globe } from 'lucide-react'
import { Link } from 'react-router-dom'

export default function SettingsPage() {
  const settingsSections = [
    {
      title: 'Institution',
      description: 'General institution settings',
      icon: Building2,
      path: '/settings/institution',
    },
    {
      title: 'Users',
      description: 'System user management',
      icon: Shield,
      path: '/settings/users',
    },
    {
      title: 'Roles and Permissions',
      description: 'Role and permission settings',
      icon: Shield,
      path: '/settings/roles',
    },
    {
      title: 'Notifications',
      description: 'Notification settings',
      icon: Bell,
      path: '/notifications',
    },
    {
      title: 'Database',
      description: 'Backups and maintenance',
      icon: Database,
      path: '#',
    },
    {
      title: 'Integrations',
      description: 'Connections to external services',
      icon: Globe,
      path: '#',
    },
  ]

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-bold text-gray-900">Settings</h1>
        <p className="text-sm text-gray-500">General system administration</p>
      </div>

      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        {settingsSections.map((section) => (
          <Link key={section.title} to={section.path}>
            <Card variant="elevated" className="hover:shadow-lg transition-shadow cursor-pointer h-full">
              <CardContent className="py-6">
                <div className="flex items-start gap-4">
                  <div className="p-3 bg-primary-100 rounded-xl">
                    <section.icon className="h-6 w-6 text-primary-600" />
                  </div>
                  <div>
                    <h3 className="font-medium text-gray-900">{section.title}</h3>
                    <p className="text-sm text-gray-500 mt-1">{section.description}</p>
                  </div>
                </div>
              </CardContent>
            </Card>
          </Link>
        ))}
      </div>
    </div>
  )
}
