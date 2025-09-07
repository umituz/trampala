<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Edit Category</h1>
        <p class="text-gray-600">Update category information</p>
      </div>
      <div class="flex space-x-3">
        <BaseButton @click="deleteCategory" variant="outline" class="text-red-600 hover:text-red-700">
          <Icon name="lucide:trash-2" class="h-4 w-4 mr-2" />
          Delete
        </BaseButton>
        <BaseButton @click="$router.push('/admin/categories')" variant="outline">
          <Icon name="lucide:arrow-left" class="h-4 w-4 mr-2" />
          Back to Categories
        </BaseButton>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="p-8 text-center">
      <Icon name="lucide:loader-2" class="h-8 w-8 animate-spin mx-auto text-gray-400" />
      <p class="text-gray-500 mt-2">Loading category...</p>
    </div>

    <!-- Form Card -->
    <BaseCard v-else-if="category" class="p-6">
      <form @submit.prevent="submitForm" class="space-y-6">
        <!-- Category Type -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Category Type</label>
          <div class="flex space-x-4">
            <label class="flex items-center">
              <input
                type="radio"
                v-model="form.type"
                value="parent"
                class="mr-2"
              />
              <span>Parent Category</span>
            </label>
            <label class="flex items-center">
              <input
                type="radio"
                v-model="form.type"
                value="subcategory"
                class="mr-2"
              />
              <span>Subcategory</span>
            </label>
          </div>
        </div>

        <!-- Parent Category (only for subcategories) -->
        <div v-if="form.type === 'subcategory'">
          <label class="block text-sm font-medium text-gray-700 mb-2">Parent Category</label>
          <BaseSelect
            v-model="form.parent_uuid"
            :options="parentCategoryOptions"
            placeholder="Select parent category"
            required
          />
        </div>

        <!-- Category Name -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Category Name</label>
          <BaseInput
            v-model="form.name"
            type="text"
            placeholder="Enter category name"
            required
          />
        </div>

        <!-- Description -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
          <BaseTextarea
            v-model="form.description"
            placeholder="Enter category description"
            rows="3"
          />
        </div>

        <!-- Submit Buttons -->
        <div class="flex justify-end space-x-4">
          <BaseButton
            type="button"
            variant="outline"
            @click="$router.push('/admin/categories')"
          >
            Cancel
          </BaseButton>
          <BaseButton
            type="submit"
            class="gradient-purple text-white"
            :disabled="saving"
          >
            <Icon v-if="saving" name="lucide:loader-2" class="h-4 w-4 mr-2 animate-spin" />
            {{ saving ? 'Updating...' : 'Update Category' }}
          </BaseButton>
        </div>
      </form>
    </BaseCard>

    <!-- Error State -->
    <BaseCard v-else class="p-8 text-center">
      <Icon name="lucide:alert-circle" class="h-8 w-8 mx-auto text-red-400 mb-4" />
      <h3 class="text-lg font-medium text-gray-900 mb-2">Category Not Found</h3>
      <p class="text-gray-500 mb-4">The category you're looking for doesn't exist.</p>
      <BaseButton @click="$router.push('/admin/categories')">
        Back to Categories
      </BaseButton>
    </BaseCard>
  </div>
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
const route = useRoute()

onMounted(() => {
  const user = getCurrentUser()
  if (!user || !isAdmin()) {
    router.push('/auth/login')
  }
})

// State
const loading = ref(true)
const saving = ref(false)
const category = ref(null)
const parentCategories = ref([])

// Form data
const form = reactive({
  type: 'parent',
  name: '',
  description: '',
  parent_uuid: null
})

// Computed
const parentCategoryOptions = computed(() => {
  return parentCategories.value
    .filter(cat => cat.uuid !== route.params.uuid) // Exclude current category
    .map(cat => ({
      value: cat.uuid,
      label: cat.name
    }))
})

// Methods
const fetchCategory = async () => {
  try {
    loading.value = true
    
    const { $api } = useNuxtApp()
    const adminCategoryService = new (await import('~/services/AdminCategoryService')).AdminCategoryService($api)
    
    const response = await adminCategoryService.getAll()
    const foundCategory = response.data.find(cat => cat.uuid === route.params.uuid)
    
    if (foundCategory) {
      category.value = foundCategory
      form.name = foundCategory.name
      form.description = foundCategory.description || ''
      form.parent_uuid = foundCategory.parent_uuid
      form.type = foundCategory.parent_uuid ? 'subcategory' : 'parent'
    }
    
  } catch (err) {
    console.error('Failed to fetch category:', err)
  } finally {
    loading.value = false
  }
}

const fetchParentCategories = async () => {
  try {
    const { $api } = useNuxtApp()
    const adminCategoryService = new (await import('~/services/AdminCategoryService')).AdminCategoryService($api)
    
    const response = await adminCategoryService.getAll()
    parentCategories.value = response.data.filter(category => !category.parent_uuid)
  } catch (err) {
    console.error('Failed to fetch parent categories:', err)
  }
}

const submitForm = async () => {
  try {
    saving.value = true
    
    const { $api } = useNuxtApp()
    const adminCategoryService = new (await import('~/services/AdminCategoryService')).AdminCategoryService($api)
    
    const data = {
      name: form.name,
      description: form.description,
      parent_uuid: form.type === 'subcategory' ? form.parent_uuid : null,
      status: 1
    }
    
    await adminCategoryService.update(route.params.uuid, data)
    
    // Success message
    push.success(`Category "${form.name}" updated successfully`)
    
    // Redirect back to categories list
    router.push('/admin/categories')
    
  } catch (err) {
    console.error('Failed to update category:', err)
    push.error('Failed to update category. Please try again.')
  } finally {
    saving.value = false
  }
}

const deleteCategory = async () => {
  if (!confirm('Are you sure you want to delete this category? This action cannot be undone.')) {
    return
  }
  
  try {
    const { $api } = useNuxtApp()
    const adminCategoryService = new (await import('~/services/AdminCategoryService')).AdminCategoryService($api)
    
    await adminCategoryService.deleteCategory(route.params.uuid)
    
    // Success message
    push.success('Category deleted successfully')
    
    // Redirect back to categories list
    router.push('/admin/categories')
    
  } catch (err) {
    console.error('Failed to delete category:', err)
    if (err.errors && err.errors.length > 0) {
      const errorMsg = err.errors[0]
      if (errorMsg.includes('listings')) {
        push.error('Cannot delete this category - It contains listings. Please move or delete all listings first.')
      } else if (errorMsg.includes('children') || errorMsg.includes('subcategories')) {
        push.error('Cannot delete this category - It has subcategories. Please delete subcategories first.')
      } else {
        push.error(`Cannot delete category: ${errorMsg}`)
      }
    } else {
      push.error('Failed to delete category. Please try again or contact support.')
    }
  }
}

// Initialize
onMounted(async () => {
  await Promise.all([
    fetchParentCategories(),
    fetchCategory()
  ])
})
</script>