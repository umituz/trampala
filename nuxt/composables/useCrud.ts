import type { Ref } from 'vue'

export interface CrudOptions<T> {
  service: any
  storeName?: string
  idField?: string
}

export interface CrudState<T> {
  items: Ref<T[]>
  currentItem: Ref<T | null>
  loading: Ref<boolean>
  creating: Ref<boolean>
  updating: Ref<boolean>
  deleting: Ref<boolean>
  error: Ref<string | null>
  pagination: Ref<any>
}

export const useCrud = <T extends { uuid?: string; id?: string }>(
  options: CrudOptions<T>
) => {
  const { service, idField = 'uuid' } = options
  
  // State
  const items = ref<T[]>([])
  const currentItem = ref<T | null>(null)
  const loading = ref(false)
  const creating = ref(false)
  const updating = ref(false)
  const deleting = ref(false)
  const error = ref<string | null>(null)
  const pagination = ref<any>(null)

  // Actions
  const fetchItems = async (filters: Record<string, any> = {}) => {
    loading.value = true
    error.value = null
    
    try {
      const response = await service.getAll(filters)
      
      if (response.data) {
        items.value = response.data
        pagination.value = response.meta || response.pagination || null
      } else {
        items.value = response
      }
      
      return items.value
    } catch (err: any) {
      error.value = err.message || 'Failed to fetch items'
      throw err
    } finally {
      loading.value = false
    }
  }

  const fetchItem = async (id: string) => {
    loading.value = true
    error.value = null
    
    try {
      currentItem.value = await service.getById(id)
      return currentItem.value
    } catch (err: any) {
      error.value = err.message || 'Failed to fetch item'
      throw err
    } finally {
      loading.value = false
    }
  }

  const createItem = async (data: Partial<T>) => {
    creating.value = true
    error.value = null
    
    try {
      const newItem = await service.create(data)
      items.value.unshift(newItem)
      currentItem.value = newItem
      return newItem
    } catch (err: any) {
      error.value = err.message || 'Failed to create item'
      throw err
    } finally {
      creating.value = false
    }
  }

  const updateItem = async (id: string, data: Partial<T>) => {
    updating.value = true
    error.value = null
    
    try {
      const updatedItem = await service.update(id, data)
      
      // Update in items array
      const index = items.value.findIndex(item => item[idField] === id)
      if (index !== -1) {
        items.value[index] = updatedItem
      }
      
      // Update current item if it's the same
      if (currentItem.value?.[idField] === id) {
        currentItem.value = updatedItem
      }
      
      return updatedItem
    } catch (err: any) {
      error.value = err.message || 'Failed to update item'
      throw err
    } finally {
      updating.value = false
    }
  }

  const deleteItem = async (id: string) => {
    deleting.value = true
    error.value = null
    
    try {
      await service.delete(id)
      
      // Remove from items array
      const index = items.value.findIndex(item => item[idField] === id)
      if (index !== -1) {
        items.value.splice(index, 1)
      }
      
      // Clear current item if it's the deleted one
      if (currentItem.value?.[idField] === id) {
        currentItem.value = null
      }
      
      return true
    } catch (err: any) {
      error.value = err.message || 'Failed to delete item'
      throw err
    } finally {
      deleting.value = false
    }
  }

  const clearError = () => {
    error.value = null
  }

  const resetState = () => {
    items.value = []
    currentItem.value = null
    loading.value = false
    creating.value = false
    updating.value = false
    deleting.value = false
    error.value = null
    pagination.value = null
  }

  // Computed
  const hasItems = computed(() => items.value.length > 0)
  const itemCount = computed(() => items.value.length)
  const isLoading = computed(() => loading.value || creating.value || updating.value || deleting.value)
  const hasError = computed(() => !!error.value)

  return {
    // State
    items: readonly(items),
    currentItem: readonly(currentItem),
    loading: readonly(loading),
    creating: readonly(creating),
    updating: readonly(updating),
    deleting: readonly(deleting),
    error: readonly(error),
    pagination: readonly(pagination),
    
    // Computed
    hasItems,
    itemCount,
    isLoading,
    hasError,
    
    // Actions
    fetchItems,
    fetchItem,
    createItem,
    updateItem,
    deleteItem,
    clearError,
    resetState
  }
}