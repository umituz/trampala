export default defineNuxtRouteMiddleware((to) => {
  // Check localStorage session first
  if (process.client) {
    const session = localStorage.getItem('auth-session')
    if (session) {
      try {
        const sessionData = JSON.parse(session)
        if (sessionData?.user && sessionData?.accessToken) {
          // Admin routes check
          if (to.path.startsWith('/admin') && sessionData.user.role !== 'admin') {
            throw createError({
              statusCode: 403,
              statusMessage: 'Access denied. Admin privileges required.'
            })
          }
          
          return // Allow access
        }
      } catch (e) {
        localStorage.removeItem('auth-session')
      }
    }
  }
  
  // No valid session, redirect to login
  return navigateTo('/auth/login')
})