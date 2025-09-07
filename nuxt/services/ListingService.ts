import { BaseService, type PaginatedResponse } from './BaseService'
import type { AxiosInstance } from 'axios'

export interface Listing {
  uuid: string
  unique_number: string
  name: string
  description: string
  image_url: string | null
  image_urls: string[]
  thumbnail_url: string | null
  status: 'pending' | 'approved' | 'rejected'
  status_description: string
  is_approved: boolean
  is_pending: boolean
  is_rejected: boolean
  // Direct UUID fields for form binding
  category_uuid: string
  country_uuid: string
  city_uuid: string
  district_uuid: string
  // Nested objects for display
  category: {
    uuid: string
    name: string
    slug: string
    description?: string
  } | null
  country: {
    uuid: string
    name: string
    code: string
  } | null
  city: {
    uuid: string
    name: string
    plate_code: string
  } | null
  district: {
    uuid: string
    name: string
  } | null
  user: {
    uuid: string
    name: string
    email?: string
  } | null
  approved_by?: {
    uuid: string
    name: string
  } | null
  approved_at?: string | null
  rejection_reason?: string | null
  created_at: string
  updated_at: string
}

export interface ListingCreateData {
  name: string
  description: string
  category_uuid: string
  country_uuid: string
  city_uuid: string
  district_uuid?: string
  image: File
}

export interface ListingUpdateData {
  name?: string
  description?: string
  category_uuid?: string
  country_uuid?: string
  city_uuid?: string
  district_uuid?: string
  image?: File
}

export interface ListingFilters {
  category_uuid?: string
  country_uuid?: string
  city_uuid?: string
  district_uuid?: string
  search?: string
  status?: 'pending' | 'approved' | 'rejected'
  per_page?: number
  page?: number
}

export class ListingService extends BaseService {
  constructor(api: AxiosInstance) {
    super(api)
  }

  /**
   * Get all approved listings (public)
   */
  async getApproved(filters: ListingFilters = {}): Promise<PaginatedResponse<Listing>> {
    return this.getPaginated<Listing>('/listings', filters)
  }

  /**
   * Get listing by ID
   */
  async getById(uuid: string): Promise<Listing> {
    return this.get<Listing>(`/listings/${uuid}`)
  }

  /**
   * Create new listing
   */
  async create(data: ListingCreateData): Promise<Listing> {
    const formData = new FormData()
    formData.append('name', data.name)
    formData.append('description', data.description)
    formData.append('category_uuid', data.category_uuid)
    formData.append('country_uuid', data.country_uuid)
    formData.append('city_uuid', data.city_uuid)
    
    if (data.district_uuid) {
      formData.append('district_uuid', data.district_uuid)
    }
    
    if (data.image) {
      formData.append('image', data.image)
    }
    
    return this.upload<Listing>('/listings', formData)
  }

  /**
   * Update existing listing
   */
  async update(uuid: string, data: ListingUpdateData): Promise<Listing> {
    const formData = new FormData()
    
    Object.entries(data).forEach(([key, value]) => {
      if (value !== undefined && value !== null) {
        if (key === 'image' && value instanceof File) {
          // Add single image
          formData.append('image', value)
        } else if (key !== 'image') {
          formData.append(key, String(value))
        }
      }
    })
    
    formData.append('_method', 'PUT')
    
    try {
      const response = await this.api.post(`/listings/${uuid}`, formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      })
      return this.handleSuccess(response)
    } catch (error) {
      this.handleError(error as any)
    }
  }

  /**
   * Delete listing (soft delete)
   */
  async deleteListing(uuid: string): Promise<void> {
    return this.delete<void>(`/listings/${uuid}`)
  }

  /**
   * Get current user's listings
   */
  async getMyListings(filters: Omit<ListingFilters, 'user_uuid'> = {}): Promise<PaginatedResponse<Listing>> {
    return this.getPaginated<Listing>('/listings/my', filters)
  }

  /**
   * Get pending listings (admin only)
   */
  async getPending(filters: ListingFilters = {}): Promise<PaginatedResponse<Listing>> {
    return this.getPaginated<Listing>('/listings/pending', filters)
  }

  /**
   * Approve listing (admin only)
   */
  async approve(uuid: string): Promise<Listing> {
    return this.post<Listing>(`/listings/${uuid}/approve`)
  }

  /**
   * Reject listing (admin only)
   */
  async reject(uuid: string, reason: string): Promise<Listing> {
    return this.post<Listing>(`/listings/${uuid}/reject`, { reason })
  }


  /**
   * Search listings
   */
  async search(query: string, filters: Omit<ListingFilters, 'search'> = {}): Promise<PaginatedResponse<Listing>> {
    return this.getApproved({ ...filters, search: query })
  }

  /**
   * Get listing statistics (admin only)
   */
  async getStats(): Promise<{
    total: number
    pending: number
    approved: number
    rejected: number
    today: number
    this_week: number
    this_month: number
  }> {
    return this.get<any>('/admin/listings/stats')
  }
}