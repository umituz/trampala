import { defineStore } from 'pinia'
import type { Category, CategoryHierarchy } from '~/services/CategoryService'

interface CategoryState {
  categories: Category[]
  hierarchy: CategoryHierarchy[]
  rootCategories: Category[]
  currentCategory: Category | null
  loading: boolean
  error: string | null
  selectOptions: Array<{
    label: string
    options: Array<{
      value: string
      label: string
    }>
  }>
}

export const useCategoryStore = defineStore('category', {
  state: (): CategoryState => ({
    categories: [],
    hierarchy: [],
    rootCategories: [],
    currentCategory: null,
    loading: false,
    error: null,
    selectOptions: []
  }),

  getters: {
    activeCategories: (state): Category[] => 
      state.categories.filter(category => category.is_active),
    
    hasCategories: (state): boolean => 
      state.categories.length > 0,
    
    getCategoryByUuid: (state) => (uuid: string): Category | undefined =>
      state.categories.find(category => category.uuid === uuid),
    
    getChildCategories: (state) => (parentUuid: string): Category[] =>
      state.categories.filter(category => category.parent_uuid === parentUuid),
    
    getRootCategoriesWithChildren: (state): CategoryHierarchy[] =>
      state.hierarchy
  },

  actions: {
    /**
     * Fetch all categories
     */
    async fetchCategories(): Promise<void> {
      this.loading = true
      this.error = null

      try {
        const { $api } = useNuxtApp()
        const categoryService = new (await import('~/services/CategoryService')).CategoryService($api)
        
        this.categories = await categoryService.getAll()
      } catch (error: any) {
        this.error = error.message || 'Failed to fetch categories'
        throw error
      } finally {
        this.loading = false
      }
    },

    /**
     * Fetch category by ID
     */
    async fetchCategory(uuid: string): Promise<void> {
      this.loading = true
      this.error = null

      try {
        const { $api } = useNuxtApp()
        const categoryService = new (await import('~/services/CategoryService')).CategoryService($api)
        
        this.currentCategory = await categoryService.getById(uuid)
      } catch (error: any) {
        this.error = error.message || 'Failed to fetch category'
        this.currentCategory = null
        throw error
      } finally {
        this.loading = false
      }
    },

    /**
     * Fetch root categories
     */
    async fetchRootCategories(): Promise<void> {
      this.loading = true
      this.error = null

      try {
        const { $api } = useNuxtApp()
        const categoryService = new (await import('~/services/CategoryService')).CategoryService($api)
        
        this.rootCategories = await categoryService.getRoots()
      } catch (error: any) {
        this.error = error.message || 'Failed to fetch root categories'
        throw error
      } finally {
        this.loading = false
      }
    },

    /**
     * Fetch category hierarchy
     */
    async fetchHierarchy(): Promise<void> {
      this.loading = true
      this.error = null

      try {
        const { $api } = useNuxtApp()
        const categoryService = new (await import('~/services/CategoryService')).CategoryService($api)
        
        this.hierarchy = await categoryService.getHierarchy()
      } catch (error: any) {
        this.error = error.message || 'Failed to fetch category hierarchy'
        throw error
      } finally {
        this.loading = false
      }
    },

    /**
     * Fetch children of a specific category
     */
    async fetchChildren(parentUuid: string): Promise<Category[]> {
      try {
        const { $api } = useNuxtApp()
        const categoryService = new (await import('~/services/CategoryService')).CategoryService($api)
        
        return await categoryService.getChildren(parentUuid)
      } catch (error: any) {
        this.error = error.message || 'Failed to fetch category children'
        throw error
      }
    },

    /**
     * Fetch select options for forms
     */
    async fetchSelectOptions(includeChildren = true): Promise<void> {
      this.loading = true
      this.error = null

      try {
        const { $api } = useNuxtApp()
        const categoryService = new (await import('~/services/CategoryService')).CategoryService($api)
        
        this.selectOptions = await categoryService.getSelectOptions()
      } catch (error: any) {
        this.error = error.message || 'Failed to fetch category options'
        throw error
      } finally {
        this.loading = false
      }
    },

    /**
     * Get category breadcrumb
     */
    async getCategoryBreadcrumb(categoryUuid: string): Promise<Array<{
      uuid: string
      name: string
      slug: string
    }>> {
      try {
        const { $api } = useNuxtApp()
        const categoryService = new (await import('~/services/CategoryService')).CategoryService($api)
        
        return await categoryService.getBreadcrumb(categoryUuid)
      } catch (error: any) {
        this.error = error.message || 'Failed to get category breadcrumb'
        throw error
      }
    },

    /**
     * Search categories by name
     */
    async searchCategories(query: string): Promise<Category[]> {
      try {
        const { $api } = useNuxtApp()
        const categoryService = new (await import('~/services/CategoryService')).CategoryService($api)
        
        return await categoryService.search(query)
      } catch (error: any) {
        this.error = error.message || 'Failed to search categories'
        throw error
      }
    },

    /**
     * Check if category has children
     */
    async hasChildren(categoryUuid: string): Promise<boolean> {
      try {
        const { $api } = useNuxtApp()
        const categoryService = new (await import('~/services/CategoryService')).CategoryService($api)
        
        return await categoryService.hasChildren(categoryUuid)
      } catch (error: any) {
        this.error = error.message || 'Failed to check category children'
        throw error
      }
    },

    /**
     * Initialize store (fetch essential data)
     */
    async initialize(): Promise<void> {
      if (!this.categories || this.categories.length === 0) {
        await this.fetchCategories()
      }
      if (!this.hierarchy || this.hierarchy.length === 0) {
        await this.fetchHierarchy()
      }
      if (!this.selectOptions || this.selectOptions.length === 0) {
        await this.fetchSelectOptions()
      }
    },

    /**
     * Clear error state
     */
    clearError() {
      this.error = null
    },

    /**
     * Reset store state
     */
    reset() {
      this.categories = []
      this.hierarchy = []
      this.rootCategories = []
      this.currentCategory = null
      this.selectOptions = []
      this.error = null
    }
  }
})