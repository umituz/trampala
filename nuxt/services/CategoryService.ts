import { BaseService } from './BaseService'
import type { AxiosInstance } from 'axios'

export interface Category {
  uuid: string
  name: string
  description: string | null
  slug: string
  is_active: boolean
  sort_order: number
  icon: string | null
  parent_uuid: string | null
  parent?: {
    uuid: string
    name: string
    slug: string
  } | null
  children?: Category[]
  listings_count?: number
  created_at: string
  updated_at: string
}

export interface CategoryHierarchy extends Category {
  children: Category[]
}

export class CategoryService extends BaseService {
  constructor(api: AxiosInstance) {
    super(api)
  }

  /**
   * Get all categories
   */
  async getAll(): Promise<Category[]> {
    const response = await this.getPaginated<Category>('/categories')
    return response.data
  }

  /**
   * Get category by ID
   */
  async getById(uuid: string): Promise<Category> {
    return this.get<Category>(`/categories/${uuid}`)
  }

  /**
   * Get root categories (categories without parent)
   */
  async getRoots(): Promise<Category[]> {
    const response = await this.get<{ data: Category[] }>('/categories/roots')
    return response.data
  }

  /**
   * Get children of a specific category
   */
  async getChildren(parentUuid: string): Promise<Category[]> {
    const response = await this.get<{ data: Category[] }>(`/categories/${parentUuid}/children`)
    return response.data
  }

  /**
   * Get complete category hierarchy
   */
  async getHierarchy(): Promise<CategoryHierarchy[]> {
    return this.get<CategoryHierarchy[]>('/categories/hierarchy')
  }

  /**
   * Get active categories only
   */
  async getActive(): Promise<Category[]> {
    const allCategories = await this.getAll()
    return allCategories.filter(category => category.is_active !== false)
  }

  /**
   * Search categories by name
   */
  async search(query: string): Promise<Category[]> {
    const allCategories = await this.getAll()
    return allCategories.filter(category => 
      category.name.toLowerCase().includes(query.toLowerCase()) ||
      (category.description && category.description.toLowerCase().includes(query.toLowerCase()))
    )
  }

  /**
   * Get categories formatted for select options with optgroups
   */
  async getSelectOptions(): Promise<Array<{
    label: string
    options: Array<{
      value: string
      label: string
    }>
  }>> {
    const hierarchy = await this.getHierarchy()
    const groups: Array<{
      label: string
      options: Array<{
        value: string
        label: string
      }>
    }> = []

    if (hierarchy && Array.isArray(hierarchy)) {
      hierarchy.forEach(category => {
        const group = {
          label: category.name,
          options: [] as Array<{
            value: string
            label: string
          }>
        }

        // Add main category as first option
        group.options.push({
          value: category.uuid,
          label: category.name
        })

        // Add children
        if (category.children && category.children.length > 0) {
          category.children.forEach(child => {
            group.options.push({
              value: child.uuid,
              label: child.name
            })
          })
        }

        groups.push(group)
      })
    }

    return groups
  }

  /**
   * Get category breadcrumb path
   */
  async getBreadcrumb(categoryUuid: string): Promise<Array<{
    uuid: string
    name: string
    slug: string
  }>> {
    const category = await this.getById(categoryUuid)
    const breadcrumb = [
      {
        uuid: category.uuid,
        name: category.name,
        slug: category.slug
      }
    ]

    // If category has parent, recursively get parent breadcrumb
    if (category.parent) {
      const parentBreadcrumb = await this.getBreadcrumb(category.parent.uuid)
      breadcrumb.unshift(...parentBreadcrumb)
    }

    return breadcrumb
  }

  /**
   * Check if category has children
   */
  async hasChildren(categoryUuid: string): Promise<boolean> {
    const children = await this.getChildren(categoryUuid)
    return children.length > 0
  }
}