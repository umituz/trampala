<template>
  <div class="space-y-6">
      <!-- Header -->
      <div>
        <h1 class="text-2xl font-bold text-gray-900">User Management</h1>
        <p class="text-gray-600">Manage platform users and their roles</p>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <BaseCard class="p-6">
          <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-100 mr-4">
              <Icon name="lucide:users" class="h-6 w-6 text-blue-600" />
            </div>
            <div>
              <p class="text-2xl font-bold text-gray-900">{{ stats.total || 0 }}</p>
              <p class="text-sm text-gray-500">Total Users</p>
            </div>
          </div>
        </BaseCard>

        <BaseCard class="p-6">
          <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-100 mr-4">
              <Icon name="lucide:user-check" class="h-6 w-6 text-green-600" />
            </div>
            <div>
              <p class="text-2xl font-bold text-gray-900">{{ stats.active || 0 }}</p>
              <p class="text-sm text-gray-500">Active Users</p>
            </div>
          </div>
        </BaseCard>

        <BaseCard class="p-6">
          <div class="flex items-center">
            <div class="p-3 rounded-full bg-purple-100 mr-4">
              <Icon name="lucide:crown" class="h-6 w-6 text-purple-600" />
            </div>
            <div>
              <p class="text-2xl font-bold text-gray-900">{{ stats.admins || 0 }}</p>
              <p class="text-sm text-gray-500">Admins</p>
            </div>
          </div>
        </BaseCard>
      </div>


      <!-- Users Table -->
      <BaseCard class="overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
          <h2 class="text-lg font-medium text-gray-900">Users</h2>
        </div>
        
        <div v-if="loading" class="p-8 text-center">
          <Icon name="lucide:loader-2" class="h-8 w-8 animate-spin mx-auto text-gray-400" />
          <p class="text-gray-500 mt-2">Loading users...</p>
        </div>

        <div v-else-if="error" class="p-8 text-center text-red-600">
          <Icon name="lucide:alert-circle" class="h-8 w-8 mx-auto mb-2" />
          <p>{{ error }}</p>
          <BaseButton @click="fetchUsers" class="mt-4">Try Again</BaseButton>
        </div>

        <div v-else-if="!users.length" class="p-8 text-center text-gray-500">
          <Icon name="lucide:users" class="h-8 w-8 mx-auto mb-2" />
          <p>No users found</p>
        </div>

        <div v-else class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Joined</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr v-for="user in users" :key="user.uuid" class="hover:bg-gray-50">
                <td class="px-6 py-4">
                  <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full bg-trampala-purple/10 flex items-center justify-center mr-3">
                      <span class="text-trampala-purple font-medium text-sm">{{ getInitials(user.name) }}</span>
                    </div>
                    <div>
                      <p class="font-medium text-gray-900">{{ user.name }}</p>
                      <p class="text-sm text-gray-500">{{ user.email }}</p>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4">
                  <span class="px-2 py-1 text-xs rounded-full" :class="user.role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800'">
                    {{ user.role }}
                  </span>
                </td>
                <td class="px-6 py-4">
                  <span class="px-2 py-1 text-xs rounded-full" :class="user.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                    {{ user.status || 'active' }}
                  </span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-500">
                  {{ formatDate(user.created_at) }}
                </td>
                <td class="px-6 py-4">
                  <div class="flex space-x-2">
                    <BaseButton variant="ghost" size="sm" @click="editUser(user)">
                      <Icon name="lucide:edit" class="h-4 w-4" />
                    </BaseButton>
                    <BaseButton variant="ghost" size="sm" @click="toggleUserStatus(user)" :class="user.status === 'active' ? 'text-red-600 hover:text-red-700' : 'text-green-600 hover:text-green-700'">
                      <Icon :name="user.status === 'active' ? 'lucide:user-x' : 'lucide:user-check'" class="h-4 w-4" />
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
              Showing {{ pagination.from }} to {{ pagination.to }} of {{ pagination.total }} users
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
const error = ref(null)
const users = ref([])
const pagination = ref(null)
const stats = ref({
  total: 0,
  active: 0,
  admins: 0,
  new_this_month: 0
})

// Filters
const filters = reactive({
  search: '',
  role: '',
  status: '',
  page: 1,
  per_page: 15
})

// Options
const roleOptions = [
  { value: '', label: 'All Roles' },
  { value: 'admin', label: 'Admin' },
  { value: 'user', label: 'User' }
]

const statusOptions = [
  { value: '', label: 'All Status' },
  { value: 'active', label: 'Active' },
  { value: 'inactive', label: 'Inactive' }
]

// Methods
const fetchUsers = async () => {
  try {
    loading.value = true
    error.value = null

    const { $api } = useNuxtApp()
    const userService = new (await import('~/services/UserService')).UserService($api)
    
    // Fetch paginated users
    const response = await userService.getAll({
      search: filters.search || undefined,
      role: filters.role || undefined,
      status: filters.status || undefined,
      per_page: filters.per_page,
      page: filters.page
    })

    // Map users for display
    users.value = response.data.map(user => ({
      uuid: user.uuid,
      name: user.name,
      email: user.email,
      role: user.roles?.[0]?.name?.toLowerCase() || 'user',
      status: user.deleted_at ? 'inactive' : 'active', // Soft delete check
      created_at: user.created_at
    }))

    pagination.value = {
      current_page: response.current_page,
      last_page: response.last_page,
      per_page: response.per_page,
      total: response.total,
      from: response.from,
      to: response.to
    }

  } catch (err) {
    error.value = err.message || 'Failed to fetch users'
  } finally {
    loading.value = false
  }
}

const changePage = (page) => {
  filters.page = page
  fetchUsers()
}

const refreshUsers = () => {
  filters.page = 1
  fetchUsers()
}

const debouncedSearch = debounce(() => {
  filters.page = 1
  fetchUsers()
}, 300)

const editUser = (user) => {
  // TODO: Implement user edit modal
}

const fetchUserStats = async () => {
  try {
    const { $api } = useNuxtApp()
    const userService = new (await import('~/services/UserService')).UserService($api)
    stats.value = await userService.getStats()
  } catch (err) {
    // Silently handle stats fetch error
  }
}

const toggleUserStatus = async (user) => {
  try {
    const { $api } = useNuxtApp()
    const userService = new (await import('~/services/UserService')).UserService($api)
    
    if (user.status === 'active') {
      // Soft delete user
      await userService.delete(user.uuid)
      user.status = 'inactive'
      push.success(`User "${user.name}" deactivated successfully`)
    } else {
      // Restore user
      await userService.restore(user.uuid)
      user.status = 'active'
      push.success(`User "${user.name}" activated successfully`)
    }
    
    // Refresh stats
    await fetchUserStats()
  } catch (err) {
    error.value = 'Failed to update user status'
    push.error('Failed to update user status. Please try again.')
  }
}

const getInitials = (name) => {
  return name.split(' ').map(n => n[0]).join('').toUpperCase()
}

const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

function debounce(func, wait) {
  let timeout
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout)
      func(...args)
    }
    clearTimeout(timeout)
    timeout = setTimeout(later, wait)
  }
}

// Initialize
onMounted(async () => {
  await Promise.all([
    fetchUsers(),
    fetchUserStats()
  ])
})
</script>