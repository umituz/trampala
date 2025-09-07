<template>
  <div class="space-y-6">
      <!-- Welcome Header -->
      <div class="bg-gradient-to-r from-trampala-purple to-purple-700 rounded-lg p-6 text-white">
        <h1 class="text-2xl font-bold mb-2">Welcome to Admin Dashboard</h1>
        <p class="opacity-90">Manage your platform listings and users</p>
      </div>

      <!-- Quick Stats Grid -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Total Listings -->
        <BaseCard class="p-6">
          <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-100 mr-4">
              <Icon name="lucide:list" class="h-6 w-6 text-blue-600" />
            </div>
            <div>
              <p class="text-2xl font-bold text-gray-900">{{ stats.total || 0 }}</p>
              <p class="text-sm text-gray-500">Total Listings</p>
            </div>
          </div>
        </BaseCard>

        <!-- Pending Listings -->
        <BaseCard class="p-6">
          <div class="flex items-center">
            <div class="p-3 rounded-full bg-yellow-100 mr-4">
              <Icon name="lucide:clock" class="h-6 w-6 text-yellow-600" />
            </div>
            <div>
              <p class="text-2xl font-bold text-gray-900">{{ stats.pending || 0 }}</p>
              <p class="text-sm text-gray-500">Pending Approval</p>
            </div>
          </div>
        </BaseCard>

        <!-- Approved Listings -->
        <BaseCard class="p-6">
          <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-100 mr-4">
              <Icon name="lucide:check-circle" class="h-6 w-6 text-green-600" />
            </div>
            <div>
              <p class="text-2xl font-bold text-gray-900">{{ stats.approved || 0 }}</p>
              <p class="text-sm text-gray-500">Approved</p>
            </div>
          </div>
        </BaseCard>

      </div>

      <!-- Quick Actions -->
      <BaseCard class="p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <NuxtLink to="/admin/pending">
            <BaseButton class="w-full justify-start bg-yellow-50 text-yellow-700 hover:bg-yellow-100 border-yellow-200">
              <Icon name="lucide:clock" class="h-4 w-4 mr-2" />
              Review Pending Listings
            </BaseButton>
          </NuxtLink>
          
          <NuxtLink to="/admin/users">
            <BaseButton class="w-full justify-start bg-blue-50 text-blue-700 hover:bg-blue-100 border-blue-200">
              <Icon name="lucide:users" class="h-4 w-4 mr-2" />
              Manage Users
            </BaseButton>
          </NuxtLink>
          
          <NuxtLink to="/admin/categories">
            <BaseButton class="w-full justify-start bg-green-50 text-green-700 hover:bg-green-100 border-green-200">
              <Icon name="lucide:tag" class="h-4 w-4 mr-2" />
              Manage Categories
            </BaseButton>
          </NuxtLink>
        </div>
      </BaseCard>
  </div>
</template>

<script setup>
import { useListingStore } from '~/stores/useListingStore'

// Page meta
definePageMeta({
  layout: 'dashboard',
  middleware: 'auth'
})


// Auth check
const { isAdmin, getCurrentUser } = await import('~/utils/auth')
const router = useRouter()

// Check admin authentication
onMounted(() => {
  const user = getCurrentUser()
  if (!user || !isAdmin()) {
    router.push('/auth/login')
  }
})

const listingStore = useListingStore()

// State
const loading = ref(false)
const stats = ref({
  total: 0,
  pending: 0,
  approved: 0,
  rejected: 0,
  today: 0,
  this_week: 0,
  this_month: 0
})
const recentListings = ref([])

// Methods
const fetchStats = async () => {
  try {
    await listingStore.fetchStats()
    if (listingStore.stats) {
      stats.value = listingStore.stats
    }
  } catch (error) {
    console.error('Failed to fetch stats:', error)
  }
}

const fetchRecentListings = async () => {
  try {
    loading.value = true
    await listingStore.fetchListings({ per_page: 5, page: 1 })
    recentListings.value = listingStore.listings.slice(0, 5)
  } catch (error) {
    console.error('Failed to fetch recent listings:', error)
  } finally {
    loading.value = false
  }
}

const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

// Initialize
onMounted(async () => {
  await Promise.all([
    fetchStats(),
    fetchRecentListings()
  ])
})
</script>