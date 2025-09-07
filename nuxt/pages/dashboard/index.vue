<template>
  <div>
    <!-- Welcome Section -->
    <div class="mb-8">
      <h1 class="text-3xl font-bold tracking-tight text-gray-900 mb-2">
        Welcome back, {{ authData.authUser.value?.name }}!
      </h1>
      <p class="text-gray-600">
        Here's an overview of your account activity and recent updates.
      </p>
    </div>

    <!-- Stats Cards -->
    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3 mb-8">
      <!-- Listings Count -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-600">My Listings</p>
            <div class="flex items-center mt-1">
              <p class="text-2xl font-semibold text-gray-900">
                {{ stats.totalListings || 0 }}
              </p>
              <span 
                v-if="stats.newListingsThisMonth > 0"
                class="ml-2 text-sm text-green-600 font-medium"
              >
                +{{ stats.newListingsThisMonth }}
              </span>
            </div>
          </div>
          <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
            <Icon name="heroicons:rectangle-stack" class="h-6 w-6 text-blue-600" />
          </div>
        </div>
        <div class="mt-4">
          <div class="flex items-center text-sm text-gray-500">
            <Icon name="heroicons:arrow-up" class="h-4 w-4 text-green-500 mr-1" />
            <span>{{ stats.newListingsThisMonth || 0 }} new this month</span>
          </div>
        </div>
      </div>

      <!-- Active Listings -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-600">Active</p>
            <div class="flex items-center mt-1">
              <p class="text-2xl font-semibold text-gray-900">
                {{ stats.activeListings || 0 }}
              </p>
            </div>
          </div>
          <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
            <Icon name="heroicons:check-circle" class="h-6 w-6 text-green-600" />
          </div>
        </div>
        <div class="mt-4">
          <div class="flex items-center text-sm text-gray-500">
            <span>{{ Math.round((stats.activeListings / (stats.totalListings || 1)) * 100) }}% of total listings</span>
          </div>
        </div>
      </div>

      <!-- Pending Listings -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-600">Pending</p>
            <div class="flex items-center mt-1">
              <p class="text-2xl font-semibold text-gray-900">
                {{ stats.pendingListings || 0 }}
              </p>
            </div>
          </div>
          <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
            <Icon name="heroicons:clock" class="h-6 w-6 text-yellow-600" />
          </div>
        </div>
        <div class="mt-4">
          <div class="flex items-center text-sm text-gray-500">
            <span>Awaiting approval</span>
          </div>
        </div>
      </div>

    </div>

    <div class="grid gap-8 lg:grid-cols-3">
      <!-- Recent Listings -->
      <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
          <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
              <h2 class="text-lg font-semibold text-gray-900">Recent Listings (Last 3)</h2>
              <NuxtLink to="/dashboard/listings">
                <BaseButton variant="outline" size="sm">
                  View All
                </BaseButton>
              </NuxtLink>
            </div>
          </div>
          
          <div class="p-6">
            <div v-if="loading" class="space-y-4">
              <div v-for="i in 3" :key="i" class="animate-pulse">
                <div class="flex items-center space-x-4">
                  <div class="w-16 h-16 bg-gray-200 rounded-lg"></div>
                  <div class="flex-1">
                    <div class="h-4 bg-gray-200 rounded mb-2"></div>
                    <div class="h-3 bg-gray-200 rounded w-3/4"></div>
                  </div>
                </div>
              </div>
            </div>

            <div v-else-if="recentListings.length === 0" class="text-center py-8">
              <Icon name="heroicons:rectangle-stack" class="h-12 w-12 text-gray-300 mx-auto mb-4" />
              <h3 class="text-sm font-medium text-gray-900 mb-2">No listings yet</h3>
              <p class="text-sm text-gray-500 mb-4">
                Get started by creating your first listing.
              </p>
              <NuxtLink to="/dashboard/listings/create">
                <BaseButton>Create Listing</BaseButton>
              </NuxtLink>
            </div>

            <div v-else class="space-y-4">
              <div 
                v-for="listing in recentListings" 
                :key="listing.uuid"
                class="flex items-center space-x-4 p-4 border border-gray-100 rounded-lg hover:bg-gray-50 transition-colors"
              >
                <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center overflow-hidden">
                  <img 
                    v-if="listing.image_url" 
                    :src="listing.image_url" 
                    :alt="listing.name"
                    class="w-full h-full object-cover"
                  >
                  <Icon v-else name="heroicons:photo" class="h-6 w-6 text-gray-400" />
                </div>
                <div class="flex-1 min-w-0">
                  <h4 class="font-medium text-gray-900 truncate">
                    {{ listing.name }}
                  </h4>
                  <p class="text-sm text-gray-500 truncate">
                    {{ listing.description }}
                  </p>
                  <div class="flex items-center mt-2 space-x-4">
                    <span 
                      :class="[
                        'inline-flex px-2 py-1 text-xs font-medium rounded-full',
                        listing.is_approved ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'
                      ]"
                    >
                      {{ listing.is_approved ? 'Active' : 'Pending' }}
                    </span>
                    <span class="text-xs text-gray-500">
                      {{ formatDate(listing.created_at) }}
                    </span>
                  </div>
                </div>
                <div class="flex items-center space-x-2">
                  <NuxtLink :to="`/dashboard/listings/${listing.uuid}`">
                    <BaseButton variant="ghost" size="sm">
                      <Icon name="heroicons:pencil" class="h-4 w-4" />
                    </BaseButton>
                  </NuxtLink>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="space-y-6">
        <!-- Quick Actions Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <h2 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h2>
          <div class="space-y-3">
            <NuxtLink to="/dashboard/listings/create" class="block">
              <BaseButton variant="outline" class="w-full justify-start">
                <Icon name="heroicons:plus-circle" class="h-4 w-4 mr-2" />
                Create New Listing
              </BaseButton>
            </NuxtLink>
            <NuxtLink to="/dashboard/listings" class="block">
              <BaseButton variant="outline" class="w-full justify-start">
                <Icon name="heroicons:rectangle-stack" class="h-4 w-4 mr-2" />
                Manage Listings
              </BaseButton>
            </NuxtLink>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  layout: 'dashboard',
  middleware: 'auth'
})

interface DashboardStats {
  totalListings: number
  activeListings: number
  pendingListings: number
  newListingsThisMonth: number
}

interface Listing {
  uuid: string
  name: string
  description: string
  image_url?: string
  is_approved: boolean
  created_at: string
}

const authData = useAuthData()
const listingStore = useListingStore()

const loading = ref(true)
const stats = ref<DashboardStats>({
  totalListings: 0,
  activeListings: 0,
  pendingListings: 0,
  newListingsThisMonth: 0
})
const recentListings = ref<Listing[]>([])


// Format date helper
const formatDate = (dateString: string): string => {
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', { 
    month: 'short', 
    day: 'numeric',
    year: 'numeric'
  })
}

// Load dashboard data
const loadDashboardData = async () => {
  try {
    loading.value = true
    
    // Load user's listings
    await listingStore.fetchMyListings()
    const userListings = listingStore.myListings
    
    // Calculate stats
    stats.value = {
      totalListings: userListings.length,
      activeListings: userListings.filter(l => l.is_approved).length,
      pendingListings: userListings.filter(l => !l.is_approved).length,
      newListingsThisMonth: userListings.filter(l => {
        const listingDate = new Date(l.created_at)
        const now = new Date()
        const monthAgo = new Date(now.getFullYear(), now.getMonth(), 1)
        return listingDate >= monthAgo
      }).length
    }
    
    // Get recent listings (last 3)
    recentListings.value = userListings
      .sort((a, b) => new Date(b.created_at).getTime() - new Date(a.created_at).getTime())
      .slice(0, 3)
    
  } catch (error) {
    console.error('Failed to load dashboard data:', error)
  } finally {
    loading.value = false
  }
}

// Load data on mount
onMounted(() => {
  authData.initAuth()
  loadDashboardData()
})
</script>