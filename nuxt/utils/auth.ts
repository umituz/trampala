// Auth utility functions for role-based operations

export interface User {
  id: string
  name: string
  email: string
  role: string
}

export interface SessionData {
  user: User
  accessToken: string
}

// Get current user session from localStorage
export const getCurrentUser = (): User | null => {
  if (!process.client) return null
  
  try {
    const session = localStorage.getItem('auth-session')
    if (session) {
      const sessionData: SessionData = JSON.parse(session)
      return sessionData.user || null
    }
  } catch (error) {
    console.error('Error parsing session data:', error)
    // Clear invalid session
    localStorage.removeItem('auth-session')
    localStorage.removeItem('auth-token')
  }
  
  return null
}

// Check if user is authenticated
export const isAuthenticated = (): boolean => {
  return getCurrentUser() !== null
}

// Check if current user is admin
export const isAdmin = (): boolean => {
  const user = getCurrentUser()
  return user?.role === 'admin'
}

// Check if current user is student/regular user
export const isStudent = (): boolean => {
  const user = getCurrentUser()
  return user?.role === 'student' || user?.role === 'user'
}

// Get user role
export const getUserRole = (): string | null => {
  const user = getCurrentUser()
  return user?.role || null
}

// Get dashboard route based on user role
export const getDashboardRoute = (): string => {
  if (isAdmin()) {
    return '/admin'
  }
  return '/dashboard'
}

// Get navigation items based on user role
export const getNavigationItems = () => {
  const user = getCurrentUser()
  if (!user) return []

  if (user.role === 'admin') {
    return [
      {
        title: 'Overview',
        href: '/admin',
        icon: 'lucide:layout-dashboard'
      },
      {
        title: 'Pending Listings',
        href: '/admin/pending',
        icon: 'lucide:clock'
      },
      {
        title: 'All Users',
        href: '/admin/users',
        icon: 'lucide:users'
      },
      {
        title: 'Categories',
        href: '/admin/categories',
        icon: 'lucide:tag'
      }
    ]
  }

  // Student/User navigation
  return [
    {
      title: 'Overview',
      href: '/dashboard',
      icon: 'lucide:layout-dashboard'
    },
    {
      title: 'My Listings',
      href: '/dashboard/listings',
      icon: 'lucide:package'
    },
    {
      title: 'Create Listing',
      href: '/dashboard/listings/create',
      icon: 'lucide:plus-circle'
    }
  ]
}

// Clear session and logout
export const clearSession = (): void => {
  if (process.client) {
    localStorage.removeItem('auth-session')
    localStorage.removeItem('auth-token')
  }
}