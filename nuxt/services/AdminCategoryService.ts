import { BaseService } from './BaseService'
import type { AxiosInstance } from 'axios'
import type { Category } from './CategoryService'

export interface CategoryStats {
  total: number
  active: number
  parent_categories: number
  subcategories: number
  new_this_month: number
}

export interface CategoryFilters {
  search?: string
  status?: string
  type?: string
  per_page?: number
  page?: number
}

export class AdminCategoryService extends BaseService {
  constructor(api: AxiosInstance) {
    super(api)
  }

  /**
   * Get paginated list of categories for admin
   */
  async getAll(filters: CategoryFilters = {}): Promise<{
    data: Category[]
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
  }> {
    const params: Record<string, any> = {}
    
    if (filters.search) params.search = filters.search
    if (filters.status) params.status = filters.status
    if (filters.type) params.type = filters.type
    if (filters.per_page) params.per_page = filters.per_page
    if (filters.page) params.page = filters.page
    
    return this.getPaginated<Category>('/categories', params)
  }

  /**
   * Get category statistics for admin dashboard
   */
  async getStats(): Promise<CategoryStats> {
    return this.get<CategoryStats>('/admin/categories/stats')
  }

  /**
   * Create a new category
   */
  async create(data: {
    name: string
    description?: string
    parent_uuid?: string
    status?: number
  }): Promise<Category> {
    return this.post<Category>('/admin/categories', data)
  }

  /**
   * Update category
   */
  async update(uuid: string, data: {
    name?: string
    description?: string
    parent_uuid?: string
    status?: number
  }): Promise<Category> {
    return this.put<Category>(`/admin/categories/${uuid}`, data)
  }

  /**
   * Update category status
   */
  async updateStatus(uuid: string, status: number): Promise<Category> {
    return this.put<Category>(`/admin/categories/${uuid}/status`, { status })
  }

  /**
   * Delete category
   */
  async deleteCategory(uuid: string): Promise<void> {
    return super.delete(`/admin/categories/${uuid}`)
  }
}