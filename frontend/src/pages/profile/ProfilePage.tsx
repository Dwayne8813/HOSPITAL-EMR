import { useState } from 'react'
import { useQuery } from '@tanstack/react-query'
import { Card, CardHeader, CardTitle, CardContent, CardDescription } from '@/components/ui/Card'
import Button from '@/components/ui/Button'
import Input from '@/components/ui/Input'
import { getInitials } from '@/lib/utils'
import { useAuth } from '@/hooks/useAuth'
import { useInstitution } from '@/hooks/useInstitution'
import api from '@/lib/api'
import { User, Mail, Phone, Shield, Building2, CalendarDays, KeyRound } from 'lucide-react'
import toast from 'react-hot-toast'

export default function ProfilePage() {
  const { user } = useAuth()
  const { data: institution } = useInstitution()
  const [saving, setSaving] = useState(false)
  const [form, setForm] = useState({ old_password: '', new_password: '', new_password_confirmation: '' })

  const { data: profile } = useQuery({
    queryKey: ['myProfile', user?.id],
    queryFn: async () => {
      const response = await api.get('/auth/me')
      return response.data?.data ?? null
    },
    enabled: !!user,
  })

  const current = profile ?? user
  const fullName = current
    ? `${current.first_name || current.name || ''} ${current.last_name || ''}`.trim() || 'User'
    : 'User'

  const handleChangePassword = async (e: React.FormEvent) => {
    e.preventDefault()
    if (form.new_password !== form.new_password_confirmation) {
      toast.error('New passwords do not match')
      return
    }
    setSaving(true)
    try {
      const response = await api.post('/auth/change-password', form)
      toast.success(response.data?.message || 'Password updated successfully')
      setForm({ old_password: '', new_password: '', new_password_confirmation: '' })
    } catch (error: any) {
      toast.error(error?.response?.data?.message || 'Failed to update password')
    } finally {
      setSaving(false)
    }
  }

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-bold text-gray-900">My Profile</h1>
        <p className="text-sm text-gray-500">View your account details and change your password</p>
      </div>

      <Card variant="elevated">
        <CardContent>
          <div className="flex flex-col sm:flex-row items-center sm:items-start gap-5">
            <div className="w-20 h-20 rounded-full bg-primary-600 flex items-center justify-center text-2xl font-semibold text-white shrink-0">
              {current ? getInitials(current.first_name || current.name, current.last_name || '') : '?'}
            </div>
            <div className="text-center sm:text-left">
              <h2 className="text-xl font-semibold text-gray-900">{fullName}</h2>
              <p className="text-sm text-gray-500">{current?.role?.name || 'No role'}</p>
              <p className="text-sm text-gray-500 mt-1">{institution?.name || ''}</p>
            </div>
          </div>
        </CardContent>
      </Card>

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <Card variant="elevated">
          <CardHeader>
            <CardTitle>Account Details</CardTitle>
            <CardDescription>Your personal and account information</CardDescription>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="flex items-center gap-3">
              <User className="h-4 w-4 text-gray-400" />
              <div>
                <p className="text-xs text-gray-500">Full name</p>
                <p className="text-sm font-medium text-gray-900">{fullName}</p>
              </div>
            </div>
            <div className="flex items-center gap-3">
              <Mail className="h-4 w-4 text-gray-400" />
              <div>
                <p className="text-xs text-gray-500">Email</p>
                <p className="text-sm font-medium text-gray-900">{current?.email || '—'}</p>
              </div>
            </div>
            <div className="flex items-center gap-3">
              <Phone className="h-4 w-4 text-gray-400" />
              <div>
                <p className="text-xs text-gray-500">Phone</p>
                <p className="text-sm font-medium text-gray-900">{current?.phone || '—'}</p>
              </div>
            </div>
            <div className="flex items-center gap-3">
              <Shield className="h-4 w-4 text-gray-400" />
              <div>
                <p className="text-xs text-gray-500">Role</p>
                <p className="text-sm font-medium text-gray-900">{current?.role?.name || '—'}</p>
              </div>
            </div>
            <div className="flex items-center gap-3">
              <Building2 className="h-4 w-4 text-gray-400" />
              <div>
                <p className="text-xs text-gray-500">Institution</p>
                <p className="text-sm font-medium text-gray-900">{institution?.name || '—'}</p>
              </div>
            </div>
            {current?.created_at && (
              <div className="flex items-center gap-3">
                <CalendarDays className="h-4 w-4 text-gray-400" />
                <div>
                  <p className="text-xs text-gray-500">Member since</p>
                  <p className="text-sm font-medium text-gray-900">
                    {new Date(current.created_at).toLocaleDateString('en-US', {
                      year: 'numeric', month: 'long', day: 'numeric',
                    })}
                  </p>
                </div>
              </div>
            )}
          </CardContent>
        </Card>

        <Card variant="elevated">
          <CardHeader>
            <CardTitle>Change Password</CardTitle>
            <CardDescription>Use at least 8 characters for your new password</CardDescription>
          </CardHeader>
          <CardContent>
            <form onSubmit={handleChangePassword} className="space-y-4">
              <Input
                label="Current password"
                type="password"
                required
                leftIcon={<KeyRound className="h-4 w-4" />}
                value={form.old_password}
                onChange={(e) => setForm({ ...form, old_password: e.target.value })}
              />
              <Input
                label="New password"
                type="password"
                required
                minLength={8}
                leftIcon={<KeyRound className="h-4 w-4" />}
                value={form.new_password}
                onChange={(e) => setForm({ ...form, new_password: e.target.value })}
              />
              <Input
                label="Confirm new password"
                type="password"
                required
                minLength={8}
                leftIcon={<KeyRound className="h-4 w-4" />}
                value={form.new_password_confirmation}
                onChange={(e) => setForm({ ...form, new_password_confirmation: e.target.value })}
              />
              <Button type="submit" isLoading={saving} leftIcon={<KeyRound className="h-4 w-4" />}>
                Update Password
              </Button>
            </form>
          </CardContent>
        </Card>
      </div>
    </div>
  )
}
