import axios, { type AxiosInstance, type AxiosResponse, type AxiosError } from 'axios'

export default defineNuxtPlugin(() => {
  const config = useRuntimeConfig()
  
  // Create axios instance
  const api: AxiosInstance = axios.create({
    baseURL: config.public.apiBaseUrl,
    timeout: 10000,
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    },
  })

  // Request interceptor
  api.interceptors.request.use(
    (config) => {
      // Get token from localStorage (client-side only)
      if (process.client) {
        const token = localStorage.getItem('auth-token')
        if (token) {
          config.headers.Authorization = `Bearer ${token}`
        }
      }
      
      // Handle FormData - let browser set Content-Type automatically
      if (config.data instanceof FormData) {
        // Remove Content-Type to let browser set multipart/form-data with boundary
        delete config.headers['Content-Type']
      }
      
      
      return config
    },
    (error) => {
      return Promise.reject(error)
    }
  )

  // Response interceptor
  api.interceptors.response.use(
    (response: AxiosResponse) => {
      return response
    },
    async (error: AxiosError) => {
      // Handle different error types
      const { response, request } = error
      
      if (response) {
        // Server responded with error status
        const { status, data } = response
        
        
        switch (status) {
          case 401:
            // Unauthorized - clear token and redirect to login
            if (process.client) {
              localStorage.removeItem('auth-token')
              await navigateTo('/auth/login')
            }
            break
            
          case 403:
            // Forbidden - handled by components
            if (process.client) {
              // You can use a toast notification here
            }
            break
            
          case 422:
            // Validation errors - handled by components
            break
            
          case 500:
            // Server error - handled by components
            if (process.client) {
              // You can use a toast notification here
            }
            break
        }
        
        // Return formatted error response
        return Promise.reject({
          status,
          message: data?.message || 'An error occurred',
          errors: data?.errors || [],
          data: data?.data || null
        })
        
      } else if (request) {
        // Request made but no response received
        return Promise.reject({
          status: 0,
          message: 'Network error. Please check your connection.',
          errors: ['Network connection failed'],
          data: null
        })
        
      } else {
        // Request setup error
        return Promise.reject({
          status: 0,
          message: 'Request failed to initialize',
          errors: [error.message],
          data: null
        })
      }
    }
  )

  // Provide axios instance globally
  return {
    provide: {
      api
    }
  }
})

// Type declaration for $api
declare module '#app' {
  interface NuxtApp {
    $api: AxiosInstance
  }
}

declare module '@vue/runtime-core' {
  interface ComponentCustomProperties {
    $api: AxiosInstance
  }
}