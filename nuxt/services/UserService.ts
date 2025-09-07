import { BaseService } from './BaseService'
import type { AxiosInstance } from 'axios'

export interface User {
  uuid: string
  name: string
  email: string
  email_verified_at: string | null
  is_active: boolean
  role_uuid: string
  role?: {
    uuid: string
    name: string
    display_name: string
  }
  created_at: string
  updated_at: string
}

export interface UserStats {
  total: number
  active: number
  admins: number
  new_this_month: number
  new_this_week: number
  new_today: number
}

export interface UserFilters {
  search?: string
  role?: string
  status?: string
  per_page?: number
  page?: number
}

export class UserService extends BaseService {
  constructor(api: AxiosInstance) {
    super(api)
  }

  /**
   * Get paginated list of users for admin
   */
  async getAll(filters: UserFilters = {}): Promise<{
    data: User[]
    current_page: number
    last_page: number
    per_page: number
    total: number
    from: number
    to: number
  }> {
    const params = new URLSearchParams()
    
    if (filters.search) params.append('search', filters.search)
    if (filters.role) params.append('role', filters.role)
    if (filters.status) params.append('status', filters.status)
    if (filters.per_page) params.append('per_page', filters.per_page.toString())
    if (filters.page) params.append('page', filters.page.toString())

    const queryString = params.toString()
    const url = `/admin/users${queryString ? `?${queryString}` : ''}`
    
    return this.get(url)
  }

  /**
   * Get user statistics for admin dashboard
   */
  async getStats(): Promise<UserStats> {
    return this.get<UserStats>('/admin/users/stats')
  }

  /**
   * Get specific user details
   */
  async getById(uuid: string): Promise<User> {
    return this.get<User>(`/admin/users/${uuid}`)
  }

  /**
   * Update user details
   */
  async update(uuid: string, data: {
    name?: string
    email?: string
    password?: string
    password_confirmation?: string
    role_uuid?: string
    is_active?: boolean
  }): Promise<User> {
    return this.put<User>(`/admin/users/${uuid}`, data)
  }

  /**
   * Update user status (active/inactive)
   */
  async updateStatus(uuid: string, is_active: boolean): Promise<User> {
    return this.put<User>(`/admin/users/${uuid}/status`, { is_active })
  }

  /**
   * Soft delete user
   */
  async deleteUser(uuid: string): Promise<void> {
    return this.delete<void>(`/admin/users/${uuid}`)
  }
}