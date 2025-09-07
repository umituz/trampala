<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Add Category</h1>
        <p class="text-gray-600">Create a new category or subcategory</p>
      </div>
      <BaseButton @click="$router.push('/admin/categories')" variant="outline">
        <Icon name="lucide:arrow-left" class="h-4 w-4 mr-2" />
        Back to Categories
      </BaseButton>
    </div>

    <!-- Form Card -->
    <BaseCard class="p-6">
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
            :rows="3"
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
            :disabled="loading"
          >
            <Icon v-if="loading" name="lucide:loader-2" class="h-4 w-4 mr-2 animate-spin" />
            {{ loading ? 'Creating...' : 'Create Category' }}
          </BaseButton>
        </div>
      </form>
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

onMounted(() => {
  const user = getCurrentUser()
  if (!user || !isAdmin()) {
    router.push('/auth/login')
  }
})

// State
const loading = ref(false)
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
  return parentCategories.value.map(category => ({
    value: category.uuid,
    label: category.name
  }))
})

// Methods
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
    loading.value = true
    
    const { $api } = useNuxtApp()
    const adminCategoryService = new (await import('~/services/AdminCategoryService')).AdminCategoryService($api)
    
    const data = {
      name: form.name,
      description: form.description,
      parent_uuid: form.type === 'subcategory' ? form.parent_uuid : null,
      status: 1
    }
    
    await adminCategoryService.create(data)
    
    // Success message
    push.success(`Category "${form.name}" created successfully`)
    
    // Redirect back to categories list
    router.push('/admin/categories')
    
  } catch (err) {
    console.error('Failed to create category:', err)
    push.error('Failed to create category. Please try again.')
  } finally {
    loading.value = false
  }
}

// Initialize
onMounted(async () => {
  await fetchParentCategories()
})
</script>