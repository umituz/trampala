<template>
  <div class="space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Category Management</h1>
          <p class="text-gray-600">Manage platform categories and subcategories</p>
        </div>
        <BaseButton @click="$router.push('/admin/categories/create')" class="gradient-purple text-white">
          <Icon name="lucide:plus" class="h-4 w-4 mr-2" />
          Add Category
        </BaseButton>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <BaseCard class="p-6">
          <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-100 mr-4">
              <Icon name="lucide:tag" class="h-6 w-6 text-blue-600" />
            </div>
            <div>
              <p class="text-2xl font-bold text-gray-900">{{ stats.total || 0 }}</p>
              <p class="text-sm text-gray-500">Total Categories</p>
            </div>
          </div>
        </BaseCard>

        <BaseCard class="p-6">
          <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-100 mr-4">
              <Icon name="lucide:check-circle" class="h-6 w-6 text-green-600" />
            </div>
            <div>
              <p class="text-2xl font-bold text-gray-900">{{ stats.active || 0 }}</p>
              <p class="text-sm text-gray-500">Active</p>
            </div>
          </div>
        </BaseCard>

        <BaseCard class="p-6">
          <div class="flex items-center">
            <div class="p-3 rounded-full bg-purple-100 mr-4">
              <Icon name="lucide:layers" class="h-6 w-6 text-purple-600" />
            </div>
            <div>
              <p class="text-2xl font-bold text-gray-900">{{ stats.parent_categories || 0 }}</p>
              <p class="text-sm text-gray-500">Parent Categories</p>
            </div>
          </div>
        </BaseCard>

        <BaseCard class="p-6">
          <div class="flex items-center">
            <div class="p-3 rounded-full bg-orange-100 mr-4">
              <Icon name="lucide:list" class="h-6 w-6 text-orange-600" />
            </div>
            <div>
              <p class="text-2xl font-bold text-gray-900">{{ stats.subcategories || 0 }}</p>
              <p class="text-sm text-gray-500">Subcategories</p>
            </div>
          </div>
        </BaseCard>
      </div>

      <!-- Info Banner -->
      <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex">
          <Icon name="lucide:info" class="h-5 w-5 text-blue-600 mr-3 mt-0.5" />
          <div>
            <h3 class="text-sm font-medium text-blue-800">Category Management Info</h3>
            <p class="text-sm text-blue-700 mt-1">
              Categories that contain listings or subcategories cannot be deleted. Please remove all associated content before deletion.
            </p>
          </div>
        </div>
      </div>

      <!-- Categories Table -->
      <BaseCard class="overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
          <h2 class="text-lg font-medium text-gray-900">Categories</h2>
        </div>
        
        <div v-if="loading" class="p-8 text-center">
          <Icon name="lucide:loader-2" class="h-8 w-8 animate-spin mx-auto text-gray-400" />
          <p class="text-gray-500 mt-2">Loading categories...</p>
        </div>

        <div v-else-if="error" class="p-8 text-center text-red-600">
          <Icon name="lucide:alert-circle" class="h-8 w-8 mx-auto mb-2" />
          <p>Failed to load categories</p>
          <BaseButton @click="fetchCategories" class="mt-4">Try Again</BaseButton>
        </div>

        <div v-else-if="!categories.length" class="p-8 text-center text-gray-500">
          <Icon name="lucide:tag" class="h-8 w-8 mx-auto mb-2" />
          <p>No categories found</p>
        </div>

        <div v-else class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr v-for="category in categories" :key="category.uuid" class="hover:bg-gray-50">
                <td class="px-6 py-4">
                  <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full bg-trampala-purple/10 flex items-center justify-center mr-3">
                      <Icon name="lucide:tag" class="h-5 w-5 text-trampala-purple" />
                    </div>
                    <div>
                      <p class="font-medium text-gray-900">{{ category.name }}</p>
                      <p class="text-sm text-gray-500">{{ category.slug }}</p>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4">
                  <span class="px-2 py-1 text-xs rounded-full" :class="category.parent_uuid ? 'bg-orange-100 text-orange-800' : 'bg-blue-100 text-blue-800'">
                    {{ category.parent_uuid ? 'Subcategory' : 'Parent' }}
                  </span>
                </td>
                <td class="px-6 py-4">
                  <span class="px-2 py-1 text-xs rounded-full" :class="category.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                    {{ category.status || 'active' }}
                  </span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-500">
                  {{ formatDate(category.created_at) }}
                </td>
                <td class="px-6 py-4">
                  <div class="flex space-x-2">
                    <BaseButton variant="ghost" size="sm" @click="editCategory(category)" title="Edit category">
                      <Icon name="lucide:edit" class="h-4 w-4" />
                    </BaseButton>
                    <BaseButton 
                      variant="ghost" 
                      size="sm" 
                      @click="toggleCategoryStatus(category)" 
                      :class="category.status === 'active' ? 'text-red-600 hover:text-red-700' : 'text-green-600 hover:text-green-700'"
                      :title="category.status === 'active' ? 'Deactivate category' : 'Activate category'"
                    >
                      <Icon :name="category.status === 'active' ? 'lucide:eye-off' : 'lucide:eye'" class="h-4 w-4" />
                    </BaseButton>
                    <BaseButton 
                      variant="outline" 
                      size="sm" 
                      @click="confirmDeleteCategory(category)" 
                      class="text-red-600 hover:text-red-700 hover:border-red-300 border-red-200"
                      title="Delete category (Note: Categories with listings or subcategories cannot be deleted)"
                    >
                      <Icon name="lucide:trash-2" class="h-4 w-4" />
                      <Icon v-if="category.parent_uuid" name="lucide:alert-triangle" class="h-3 w-3 ml-1 text-yellow-500" title="This category may have dependencies" />
                    </BaseButton>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="pagination && pagination.last_page > 1" class="px-6 py-4 border-t border-gray-200">
          <div class="flex items-center justify-between">
            <p class="text-sm text-gray-500">
              Showing {{ pagination.from }} to {{ pagination.to }} of {{ pagination.total }} categories
            </p>
            <div class="flex space-x-2">
              <BaseButton 
                variant="ghost" 
                size="sm" 
                @click="changePage(pagination.current_page - 1)"
                :disabled="pagination.current_page <= 1"
              >
                Previous
              </BaseButton>
              <BaseButton 
                variant="ghost" 
                size="sm" 
                @click="changePage(pagination.current_page + 1)"
                :disabled="pagination.current_page >= pagination.last_page"
              >
                Next
              </BaseButton>
            </div>
          </div>
        </div>
      </BaseCard>
  </div>

  <!-- Delete Confirmation Modal -->
  <BaseModal v-model="showDeleteModal" title="Delete Category">
    <div class="sm:flex sm:items-start">
      <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10 mb-4 sm:mb-0">
        <Icon name="lucide:triangle-alert" class="h-6 w-6 text-red-600" />
      </div>
      <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
        <h3 class="text-lg font-medium text-gray-900 mb-2">
          Delete "{{ categoryToDelete?.name }}"
        </h3>
        <p class="text-sm text-gray-500 mb-3">
          Are you sure you want to delete this category? This action cannot be undone.
        </p>
        <div v-if="categoryToDelete?.listings_count > 0 || categoryToDelete?.children?.length > 0" class="p-3 bg-yellow-50 border border-yellow-200 rounded-md">
          <p class="text-sm text-yellow-700">
            <Icon name="lucide:alert-triangle" class="h-4 w-4 inline mr-1" />
            Categories with listings or subcategories cannot be deleted.
          </p>
        </div>
      </div>
    </div>

    <template #footer>
      <div class="flex items-center justify-end space-x-3">
        <BaseButton variant="outline" @click="showDeleteModal = false">
          Cancel
        </BaseButton>
        <BaseButton 
          :loading="deleting"
          @click="deleteCategoryConfirmed"
          class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md font-medium"
        >
          Delete Category
        </BaseButton>
      </div>
    </template>
  </BaseModal>
</template>

<script setup>
// Page meta
definePageMeta({
  layout: 'dashboard',
  middleware: 'auth'
})


// Auth check
const { isAdmin, getCurrentUser } = await import('~/utils/auth')
const router = useRouter()

onMounted(() => {
  const user = getCurrentUser()
  if (!user || !isAdmin()) {
    router.push('/auth/login')
  }
})

// Toast system
const { success: showSuccess, error: showError } = useToast()

// State
const loading = ref(false)
const error = ref(null)
const categories = ref([])
const pagination = ref(null)
const stats = ref({
  total: 0,
  active: 0,
  parent_categories: 0,
  subcategories: 0
})

// Delete modal state
const showDeleteModal = ref(false)
const categoryToDelete = ref(null)
const deleting = ref(false)

// Filters
const filters = reactive({
  page: 1,
  per_page: 15
})


// Methods
const fetchCategories = async () => {
  try {
    loading.value = true
    error.value = null

    const { $api } = useNuxtApp()
    const adminCategoryService = new (await import('~/services/AdminCategoryService')).AdminCategoryService($api)
    
    // Fetch paginated categories
    const response = await adminCategoryService.getAll({
      per_page: filters.per_page,
      page: filters.page
    })

    // Map categories for display
    categories.value = response.data.map(category => ({
      uuid: category.uuid,
      name: category.name,
      slug: category.slug,
      status: 'active', // All categories are considered active since is_active is null
      parent_uuid: category.parent_uuid,
      created_at: category.created_at
    }))

    pagination.value = {
      current_page: response.meta.current_page,
      last_page: response.meta.last_page,
      per_page: response.meta.per_page,
      total: response.meta.total,
      from: response.meta.from,
      to: response.meta.to
    }

  } catch (err) {
    error.value = 'Failed to fetch categories'
    console.error('Failed to fetch categories:', err)
  } finally {
    loading.value = false
  }
}

const changePage = (page) => {
  filters.page = page
  fetchCategories()
}


const editCategory = (category) => {
  router.push(`/admin/categories/edit/${category.uuid}`)
}

const fetchCategoryStats = async () => {
  try {
    const { $api } = useNuxtApp()
    const adminCategoryService = new (await import('~/services/AdminCategoryService')).AdminCategoryService($api)
    stats.value = await adminCategoryService.getStats()
  } catch (err) {
    console.error('Failed to fetch category stats:', err)
  }
}

const toggleCategoryStatus = async (category) => {
  try {
    const { $api } = useNuxtApp()
    const adminCategoryService = new (await import('~/services/AdminCategoryService')).AdminCategoryService($api)
    
    const newStatus = category.status === 'active' ? 0 : 1
    await adminCategoryService.updateStatus(category.uuid, newStatus)
    
    // Update local category object
    category.status = newStatus ? 'active' : 'inactive'
    
    // Show success message
    const statusText = newStatus ? 'activated' : 'deactivated'
    showSuccess(`Category "${category.name}" ${statusText} successfully`)
    
    // Refresh stats
    await fetchCategoryStats()
  } catch (err) {
    console.error('Failed to update category status:', err)
    showError('Failed to update category status. Please try again.')
  }
}

const confirmDeleteCategory = (category) => {
  categoryToDelete.value = category
  showDeleteModal.value = true
}

const deleteCategoryConfirmed = async () => {
  if (!categoryToDelete.value) return
  
  try {
    deleting.value = true
    const { $api } = useNuxtApp()
    const adminCategoryService = new (await import('~/services/AdminCategoryService')).AdminCategoryService($api)
    
    await adminCategoryService.deleteCategory(categoryToDelete.value.uuid)
    
    showSuccess(`Category "${categoryToDelete.value.name}" deleted successfully`)
    
    // Refresh data
    await fetchCategories()
    await fetchCategoryStats()
  } catch (err) {
    console.error('Failed to delete category:', err)
    
    // Enhanced error handling
    if (err.errors && err.errors.length > 0) {
      const errorMsg = err.errors[0]
      if (errorMsg.includes('listings')) {
        showError(`Cannot delete "${categoryToDelete.value.name}"`, 'This category contains listings. Please move or delete all listings first.')
      } else if (errorMsg.includes('child categories') || errorMsg.includes('children') || errorMsg.includes('subcategories')) {
        showError(`Cannot delete "${categoryToDelete.value.name}"`, 'This category has subcategories. Please delete subcategories first.')
      } else {
        showError(`Cannot delete "${categoryToDelete.value.name}"`, errorMsg)
      }
    } else {
      showError(`Failed to delete "${categoryToDelete.value.name}"`, 'Please try again.')
    }
  } finally {
    // Always clean up modal state
    deleting.value = false
    showDeleteModal.value = false
    categoryToDelete.value = null
  }
}

const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}


// Initialize
onMounted(async () => {
  await Promise.all([
    fetchCategories(),
    fetchCategoryStats()
  ])
})
</script>