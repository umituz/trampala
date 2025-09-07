export const useErrorHandler = () => {
  const handleApiError = (error: any) => {
    console.error('API Error:', error)
    
    // Handle different types of errors
    if (error.response) {
      const status = error.response.status
      const data = error.response.data
      
      switch (status) {
        case 400:
          return {
            title: 'Bad Request',
            message: data.message || 'Invalid request parameters',
            type: 'warning'
          }
        
        case 401:
          // Clear auth state if unauthorized
          const { signOut } = useAuth()
          signOut()
          
          return {
            title: 'Authentication Required',
            message: 'Please log in to continue',
            type: 'error',
            redirect: '/auth/login'
          }
        
        case 403:
          return {
            title: 'Access Forbidden',
            message: 'You do not have permission to perform this action',
            type: 'error'
          }
        
        case 404:
          return {
            title: 'Not Found',
            message: 'The requested resource was not found',
            type: 'warning'
          }
        
        case 422:
          // Validation errors
          const validationErrors = data.errors || {}
          const firstError = Object.values(validationErrors)[0]
          const errorMessage = Array.isArray(firstError) ? firstError[0] : firstError
          
          return {
            title: 'Validation Error',
            message: errorMessage || data.message || 'Please check your input and try again',
            type: 'warning',
            validationErrors
          }
        
        case 429:
          return {
            title: 'Too Many Requests',
            message: 'Please wait a moment before trying again',
            type: 'warning'
          }
        
        case 500:
          return {
            title: 'Server Error',
            message: 'An internal server error occurred. Please try again later',
            type: 'error'
          }
        
        case 503:
          return {
            title: 'Service Unavailable',
            message: 'The service is temporarily unavailable. Please try again later',
            type: 'error'
          }
        
        default:
          return {
            title: 'Request Failed',
            message: data.message || 'An unexpected error occurred',
            type: 'error'
          }
      }
    }
    
    // Handle network errors
    if (error.code === 'NETWORK_ERROR' || !error.response) {
      return {
        title: 'Connection Error',
        message: 'Unable to connect to the server. Please check your internet connection',
        type: 'error'
      }
    }
    
    // Handle timeout errors
    if (error.code === 'ECONNABORTED') {
      return {
        title: 'Request Timeout',
        message: 'The request took too long to complete. Please try again',
        type: 'warning'
      }
    }
    
    // Default error
    return {
      title: 'Error',
      message: error.message || 'An unexpected error occurred',
      type: 'error'
    }
  }
  
  const handleAsyncError = async (asyncFunction: Function, errorContext?: string) => {
    try {
      return await asyncFunction()
    } catch (error) {
      const errorInfo = handleApiError(error)
      
      if (errorContext) {
        errorInfo.message = `${errorContext}: ${errorInfo.message}`
      }
      
      // You can add toast notification here if you implement one
      console.error('Async Error:', errorInfo)
      
      throw new Error(errorInfo.message)
    }
  }
  
  const isValidationError = (error: any): boolean => {
    return error.response?.status === 422
  }
  
  const getValidationErrors = (error: any): Record<string, string[]> => {
    if (!isValidationError(error)) return {}
    return error.response?.data?.errors || {}
  }
  
  const getFirstValidationError = (error: any, field?: string): string | null => {
    const errors = getValidationErrors(error)
    
    if (field && errors[field]) {
      return Array.isArray(errors[field]) ? errors[field][0] : errors[field]
    }
    
    // Get first error from any field
    const firstFieldErrors = Object.values(errors)[0]
    return Array.isArray(firstFieldErrors) ? firstFieldErrors[0] : firstFieldErrors || null
  }
  
  const isAuthenticationError = (error: any): boolean => {
    return error.response?.status === 401
  }
  
  const isAuthorizationError = (error: any): boolean => {
    return error.response?.status === 403
  }
  
  const isNotFoundError = (error: any): boolean => {
    return error.response?.status === 404
  }
  
  const isServerError = (error: any): boolean => {
    const status = error.response?.status
    return status >= 500 && status < 600
  }
  
  const isNetworkError = (error: any): boolean => {
    return error.code === 'NETWORK_ERROR' || !error.response
  }

  const getErrorMessage = (error: any): string => {
    const errorInfo = handleApiError(error)
    return errorInfo.message
  }
  
  return {
    handleApiError,
    handleAsyncError,
    isValidationError,
    getValidationErrors,
    getFirstValidationError,
    isAuthenticationError,
    isAuthorizationError,
    isNotFoundError,
    isServerError,
    isNetworkError,
    getErrorMessage
  }
}