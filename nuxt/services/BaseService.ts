import type { AxiosInstance, AxiosResponse } from 'axios'

export interface ApiResponse<T = any> {
  success: boolean
  message: string
  data: T
  errors: string[]
}

export interface PaginatedResponse<T = any> {
  data: T[]
  links: {
    first: string | null
    last: string | null
    prev: string | null
    next: string | null
  }
  meta: {
    current_page: number
    from: number | null
    last_page: number
    per_page: number
    to: number | null
    total: number
    count: number
  }
}

export interface ApiError {
  status: number
  message: string
  errors: string[]
  data: any
}

export abstract class BaseService {
  protected api: AxiosInstance

  constructor(api: AxiosInstance) {
    this.api = api
  }

  /**
   * Handle successful API response
   */
  protected handleSuccess<T>(response: AxiosResponse<ApiResponse<T>>): T {
    return response.data.data
  }

  /**
   * Handle successful paginated API response  
   */
  protected handlePaginatedSuccess<T>(response: AxiosResponse<ApiResponse<T[]>>): PaginatedResponse<T> {
    return {
      data: response.data.data,
      meta: response.data.meta || {},
      links: response.data.links || {}
    }
  }

  /**
   * Handle API error response
   */
  protected handleError(error: ApiError): never {
    // Re-throw formatted error
    throw {
      status: error.status,
      message: error.message,
      errors: error.errors || [],
      data: error.data
    }
  }

  /**
   * Build query parameters for GET requests
   */
  protected buildQueryParams(params: Record<string, any>): string {
    const searchParams = new URLSearchParams()
    
    Object.entries(params).forEach(([key, value]) => {
      if (value !== null && value !== undefined && value !== '') {
        if (Array.isArray(value)) {
          value.forEach(item => searchParams.append(`${key}[]`, item))
        } else {
          searchParams.append(key, String(value))
        }
      }
    })
    
    return searchParams.toString()
  }

  /**
   * Generic GET request
   */
  protected async get<T>(url: string, params?: Record<string, any>): Promise<T> {
    try {
      const queryString = params ? this.buildQueryParams(params) : ''
      const fullUrl = queryString ? `${url}?${queryString}` : url
      
      const response = await this.api.get<ApiResponse<T>>(fullUrl)
      return this.handleSuccess(response)
    } catch (error) {
      this.handleError(error as ApiError)
    }
  }

  /**
   * Generic paginated GET request
   */
  protected async getPaginated<T>(url: string, params?: Record<string, any>): Promise<PaginatedResponse<T>> {
    try {
      const queryString = params ? this.buildQueryParams(params) : ''
      const fullUrl = queryString ? `${url}?${queryString}` : url
      
      const response = await this.api.get<ApiResponse<T[]>>(fullUrl)
      return this.handlePaginatedSuccess(response)
    } catch (error) {
      this.handleError(error as ApiError)
    }
  }

  /**
   * Generic POST request
   */
  protected async post<T>(url: string, data?: any): Promise<T> {
    try {
      const response = await this.api.post<ApiResponse<T>>(url, data)
      return this.handleSuccess(response)
    } catch (error) {
      this.handleError(error as ApiError)
    }
  }

  /**
   * Generic PUT request
   */
  protected async put<T>(url: string, data?: any): Promise<T> {
    try {
      const response = await this.api.put<ApiResponse<T>>(url, data)
      return this.handleSuccess(response)
    } catch (error) {
      this.handleError(error as ApiError)
    }
  }

  /**
   * Generic DELETE request
   */
  protected async delete<T>(url: string): Promise<T> {
    try {
      const response = await this.api.delete<ApiResponse<T>>(url)
      return this.handleSuccess(response)
    } catch (error) {
      this.handleError(error as ApiError)
    }
  }

  /**
   * File upload using FormData
   */
  protected async upload<T>(url: string, formData: FormData): Promise<T> {
    try {
      // Don't set Content-Type manually - let browser set it with boundary
      const response = await this.api.post<ApiResponse<T>>(url, formData)
      return this.handleSuccess(response)
    } catch (error) {
      this.handleError(error as ApiError)
    }
  }
}