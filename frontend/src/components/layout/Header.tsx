import { useState } from 'react'
import { Link, useLocation, useNavigate } from 'react-router-dom'
import { useQuery } from '@tanstack/react-query'
import { cn } from '@/lib/utils'
import { useAuth } from '@/hooks/useAuth'
import { getInitials } from '@/lib/utils'
import api from '@/lib/api'
import {
  Bell,
  Search,
  Settings,
  LogOut,
  User,
  ChevronRight,
  Menu,
} from 'lucide-react'

interface HeaderProps {
  onMenuClick?: () => void
}

export default function Header({ onMenuClick }: HeaderProps) {
  const { user, logout } = useAuth()
  const location = useLocation()
  const navigate = useNavigate()
  const [isUserMenuOpen, setIsUserMenuOpen] = useState(false)
  const [isSearchOpen, setIsSearchOpen] = useState(false)
  const [searchTerm, setSearchTerm] = useState('')

  const { data: searchResults = [], isFetching: isSearching } = useQuery({
    queryKey: ['patientSearch', searchTerm.trim()],
    queryFn: async () => {
      const response = await api.get('/patients/search', { params: { term: searchTerm.trim() } })
      return (response.data?.data ?? []) as Array<{
        id: string
        first_name: string
        last_name: string
        medical_record_number: string
        document_number: string
      }>
    },
    enabled: searchTerm.trim().length >= 2,
  })

  const { data: unreadCount = 0 } = useQuery({
    queryKey: ['notificationUnreadCount'],
    queryFn: async () => {
      const response = await api.get('/notifications/unread-count')
      return Number(response.data?.data?.count ?? 0)
    },
    refetchOnWindowFocus: true,
  })

  const getBreadcrumbs = () => {
    const pathSegments = location.pathname.split('/').filter(Boolean)
    const breadcrumbs = [{ label: 'Home', path: '/dashboard' }]

    let currentPath = ''
    pathSegments.forEach((segment) => {
      currentPath += `/${segment}`
      const label = segment.charAt(0).toUpperCase() + segment.slice(1).replace(/-/g, ' ')
      breadcrumbs.push({ label, path: currentPath })
    })

    return breadcrumbs
  }

  const breadcrumbs = getBreadcrumbs()

  return (
    <header className="sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm">
      <div className="flex items-center justify-between h-16 px-4 lg:px-6">
        {/* Left: Menu + Breadcrumbs */}
        <div className="flex items-center gap-4">
          <button
            onClick={onMenuClick}
            className="lg:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100"
          >
            <Menu className="h-5 w-5" />
          </button>

          {/* Breadcrumbs */}
          <nav className="hidden md:flex items-center gap-1 text-sm">
            {breadcrumbs.map((breadcrumb, index) => (
              <div key={`${breadcrumb.path}-${index}`} className="flex items-center">
                {index > 0 && <ChevronRight className="h-4 w-4 text-gray-400 mx-1" />}
                {index === breadcrumbs.length - 1 ? (
                  <span className="text-gray-900 font-medium">{breadcrumb.label}</span>
                ) : (
                  <Link
                    to={breadcrumb.path}
                    className="text-gray-500 hover:text-gray-700 transition-colors"
                  >
                    {breadcrumb.label}
                  </Link>
                )}
              </div>
            ))}
          </nav>
        </div>

        {/* Right: Search + Notifications + User */}
        <div className="flex items-center gap-2">
          {/* Search */}
          <div className="relative">
            <button
              onClick={() => {
                setIsSearchOpen(!isSearchOpen)
                setSearchTerm('')
              }}
              className="p-2 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-colors"
              aria-label="Search"
            >
              <Search className="h-5 w-5" />
            </button>
            {isSearchOpen && (
              <>
                <div className="fixed inset-0 z-40" onClick={() => setIsSearchOpen(false)} />
                <div className="absolute right-0 top-full mt-2 w-80 bg-white rounded-xl shadow-lg border border-gray-200 p-2 z-50">
                  <input
                    type="text"
                    placeholder="Search patients by name or record number..."
                    className="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500"
                    value={searchTerm}
                    onChange={(e) => setSearchTerm(e.target.value)}
                    autoFocus
                  />
                  {searchTerm.trim().length >= 2 && (
                    <div className="mt-2 max-h-80 overflow-y-auto">
                      {isSearching && <p className="px-3 py-2 text-sm text-gray-500">Searching...</p>}
                      {!isSearching && searchResults.length === 0 && (
                        <p className="px-3 py-2 text-sm text-gray-500">No patients found</p>
                      )}
                      {searchResults.map((patient) => (
                        <button
                          key={patient.id}
                          onClick={() => {
                            setIsSearchOpen(false)
                            setSearchTerm('')
                            navigate(`/patients/${patient.id}`)
                          }}
                          className="w-full text-left px-3 py-2 rounded-lg hover:bg-gray-50"
                        >
                          <p className="text-sm font-medium text-gray-900">
                            {`${patient.first_name || ''} ${patient.last_name || ''}`.trim() || 'Unnamed patient'}
                          </p>
                          <p className="text-xs text-gray-500">
                            {patient.medical_record_number || patient.document_number || ''}
                          </p>
                        </button>
                      ))}
                    </div>
                  )}
                </div>
              </>
            )}
          </div>

          {/* Notifications */}
          <Link
            to="/notifications"
            className="relative p-2 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-colors"
          >
            <Bell className="h-5 w-5" />
            {unreadCount > 0 && (
              <span className="absolute top-1 right-1 flex items-center justify-center w-4 h-4 text-[10px] font-bold text-white bg-red-500 rounded-full">
                {unreadCount}
              </span>
            )}
          </Link>

          {/* Settings */}
          <Link
            to="/settings"
            className="p-2 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-colors hidden sm:flex"
          >
            <Settings className="h-5 w-5" />
          </Link>

          {/* User Menu */}
          <div className="relative">
            <button
              onClick={() => setIsUserMenuOpen(!isUserMenuOpen)}
              className="flex items-center gap-2 p-1.5 rounded-lg hover:bg-gray-100 transition-colors"
            >
              <div className="w-8 h-8 bg-primary-600 rounded-full flex items-center justify-center">
                <span className="text-sm font-medium text-white">
                  {user ? getInitials(user.first_name || user.name, user.last_name || '') : '?'}
                </span>
              </div>
              <div className="hidden lg:block text-left">
                <p className="text-sm font-medium text-gray-900">
                  {user ? `${user.first_name || user.name || ''} ${user.last_name || ''}`.trim() || 'User' : 'User'}
                </p>
                <p className="text-xs text-gray-500">{user?.role?.name || 'No role'}</p>
              </div>
            </button>

            {/* Dropdown */}
            {isUserMenuOpen && (
              <>
                <div
                  className="fixed inset-0 z-40"
                  onClick={() => setIsUserMenuOpen(false)}
                />
                <div className="absolute right-0 top-full mt-2 w-56 bg-white rounded-xl shadow-lg border border-gray-200 py-1 z-50">
                  <div className="px-4 py-3 border-b border-gray-100">
                    <p className="text-sm font-medium text-gray-900">
                      {user ? `${user.first_name || user.name || ''} ${user.last_name || ''}`.trim() || 'User' : 'User'}
                    </p>
                    <p className="text-xs text-gray-500">{user?.email}</p>
                  </div>
                  <Link
                    to="/profile"
                    className="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50"
                    onClick={() => setIsUserMenuOpen(false)}
                  >
                    <User className="h-4 w-4" />
                    My Profile
                  </Link>
                  <Link
                    to="/settings"
                    className="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50"
                    onClick={() => setIsUserMenuOpen(false)}
                  >
                    <Settings className="h-4 w-4" />
                    Settings
                  </Link>
                  <div className="border-t border-gray-100 my-1" />
                  <button
                    onClick={() => {
                      setIsUserMenuOpen(false)
                      logout()
                    }}
                    className="flex items-center gap-2 w-full px-4 py-2.5 text-sm text-red-600 hover:bg-red-50"
                  >
                    <LogOut className="h-4 w-4" />
                    Sign Out
                  </button>
                </div>
              </>
            )}
          </div>
        </div>
      </div>
    </header>
  )
}
