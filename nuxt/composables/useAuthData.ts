// Unified auth data composable
export interface AuthUser {
  id?: string
  name: string
  email: string
  role?: {
    name: string
  }
}

export interface AuthData {
  user: AuthUser
  accessToken: string
}

// Get current user session from localStorage (unified approach)
export const useAuthData = () => {
  const authUser = ref<AuthUser | null>(null)

  // Check auth state from localStorage
  const checkAuth = () => {
    if (!process.client) return null

    try {
      const session = localStorage.getItem('auth-session')
      if (session) {
        const sessionData: AuthData = JSON.parse(session)
        if (sessionData?.user && sessionData?.accessToken) {
          authUser.value = sessionData.user
          return sessionData.user
        }
      }
    } catch (error) {
      console.error('Error parsing session data:', error)
      // Clear invalid session
      localStorage.removeItem('auth-session')
      localStorage.removeItem('auth-token')
    }

    authUser.value = null
    return null
  }

  // Get user initials from name
  const getUserInitials = (name?: string): string => {
    if (!name) return 'U'
    return name
      .split(' ')
      .map(n => n[0])
      .join('')
      .toUpperCase()
      .slice(0, 2)
  }

  // Check if user is authenticated
  const isAuthenticated = computed(() => authUser.value !== null)

  // Check if user is admin
  const isAdmin = computed(() => authUser.value?.role?.name === 'admin')

  // Clear session and logout
  const clearSession = () => {
    if (process.client) {
      localStorage.removeItem('auth-session')
      localStorage.removeItem('auth-token')
    }
    authUser.value = null
  }

  // Initialize auth state
  const initAuth = () => {
    checkAuth()
  }

  return {
    authUser: readonly(authUser),
    getUserInitials,
    isAuthenticated,
    isAdmin,
    clearSession,
    initAuth,
    checkAuth
  }
}