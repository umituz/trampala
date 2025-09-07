<template>
  <div>
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">My Listings</h1>
        <p class="text-gray-600 mt-1">Manage your listings and track their performance</p>
      </div>
      <NuxtLink to="/dashboard/listings/create">
        <BaseButton>
          <Icon name="heroicons:plus" class="h-4 w-4 mr-2" />
          Create Listing
        </BaseButton>
      </NuxtLink>
    </div>

    <!-- Stats Summary -->
    <div class="grid gap-4 md:grid-cols-4 mb-6">
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex items-center">
          <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
            <Icon name="heroicons:rectangle-stack" class="h-5 w-5 text-blue-600" />
          </div>
          <div>
            <p class="text-sm text-gray-600">Total</p>
            <p class="text-xl font-semibold text-gray-900">{{ totalListings }}</p>
          </div>
        </div>
      </div>
      
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex items-center">
          <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
            <Icon name="heroicons:check-circle" class="h-5 w-5 text-green-600" />
          </div>
          <div>
            <p class="text-sm text-gray-600">Active</p>
            <p class="text-xl font-semibold text-gray-900">{{ activeListings }}</p>
          </div>
        </div>
      </div>
      
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex items-center">
          <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center mr-3">
            <Icon name="heroicons:clock" class="h-5 w-5 text-yellow-600" />
          </div>
          <div>
            <p class="text-sm text-gray-600">Pending</p>
            <p class="text-xl font-semibold text-gray-900">{{ pendingListings }}</p>
          </div>
        </div>
      </div>
      
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex items-center">
          <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center mr-3">
            <Icon name="heroicons:x-circle" class="h-5 w-5 text-red-600" />
          </div>
          <div>
            <p class="text-sm text-gray-600">Rejected</p>
            <p class="text-xl font-semibold text-gray-900">{{ rejectedListings }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Listings Table/Grid -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
      <!-- Table Header -->
      <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
          <h2 class="text-lg font-medium text-gray-900">
            Listings ({{ listings.length }})
          </h2>
          <div class="flex items-center space-x-2">
            <BaseButton
              variant="ghost"
              size="sm"
              :class="{ 'bg-gray-100': viewMode === 'table' }"
              @click="viewMode = 'table'"
            >
              <Icon name="heroicons:list-bullet" class="h-4 w-4" />
            </BaseButton>
            <BaseButton
              variant="ghost"
              size="sm"
              :class="{ 'bg-gray-100': viewMode === 'grid' }"
              @click="viewMode = 'grid'"
            >
              <Icon name="heroicons:squares-2x2" class="h-4 w-4" />
            </BaseButton>
          </div>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="p-6">
        <div class="space-y-4">
          <div v-for="i in 5" :key="i" class="animate-pulse">
            <div class="flex items-center space-x-4">
              <div class="w-16 h-16 bg-gray-200 rounded-lg"></div>
              <div class="flex-1">
                <div class="h-4 bg-gray-200 rounded mb-2"></div>
                <div class="h-3 bg-gray-200 rounded w-3/4"></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else-if="listings.length === 0" class="p-12 text-center">
        <Icon name="heroicons:rectangle-stack" class="h-12 w-12 text-gray-300 mx-auto mb-4" />
        <h3 class="text-lg font-medium text-gray-900 mb-2">No listings yet</h3>
        <p class="text-gray-500 mb-6">Get started by creating your first listing.</p>
        <NuxtLink to="/dashboard/listings/create">
          <BaseButton>Create Your First Listing</BaseButton>
        </NuxtLink>
      </div>

      <!-- Table View -->
      <div v-else-if="viewMode === 'table'" class="overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Listing
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Category
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Status
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Created
              </th>
              <th scope="col" class="relative px-6 py-3">
                <span class="sr-only">Actions</span>
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="listing in listings" :key="listing.uuid" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center overflow-hidden mr-4">
                    <img 
                      v-if="listing.image_url" 
                      :src="listing.image_url" 
                      :alt="listing.name"
                      class="w-full h-full object-cover"
                    >
                    <Icon v-else name="heroicons:photo" class="h-5 w-5 text-gray-400" />
                  </div>
                  <div>
                    <div class="text-sm font-medium text-gray-900">{{ listing.name }}</div>
                    <div class="text-sm text-gray-500 truncate max-w-xs">{{ listing.description }}</div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">{{ listing.category?.name || 'N/A' }}</div>
                <div class="text-sm text-gray-500">{{ listing.city?.name || 'N/A' }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span 
                  :class="[
                    'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                    getStatusClasses(listing.is_approved, listing.is_rejected)
                  ]"
                >
                  {{ getStatusText(listing.is_approved, listing.is_rejected) }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ formatDate(listing.created_at) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <div class="flex items-center justify-end space-x-2">
                  <NuxtLink :to="`/dashboard/listings/${listing.uuid}`">
                    <BaseButton variant="ghost" size="sm">
                      <Icon name="heroicons:pencil" class="h-4 w-4" />
                    </BaseButton>
                  </NuxtLink>
                  <BaseButton 
                    variant="ghost" 
                    size="sm"
                    class="text-red-600 hover:text-red-700"
                    @click="confirmDelete(listing)"
                  >
                    <Icon name="heroicons:trash" class="h-4 w-4" />
                  </BaseButton>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Grid View -->
      <div v-else class="p-6">
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
          <div 
            v-for="listing in listings" 
            :key="listing.uuid"
            class="bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-md transition-shadow"
          >
            <div class="aspect-w-16 aspect-h-9">
              <img 
                v-if="listing.image_url" 
                :src="listing.image_url" 
                :alt="listing.name"
                class="w-full h-48 object-cover"
              >
              <div v-else class="w-full h-48 bg-gray-100 flex items-center justify-center">
                <Icon name="heroicons:photo" class="h-8 w-8 text-gray-400" />
              </div>
            </div>
            <div class="p-4">
              <div class="flex items-start justify-between mb-2">
                <h3 class="font-medium text-gray-900 truncate">{{ listing.name }}</h3>
                <span 
                  :class="[
                    'inline-flex px-2 py-1 text-xs font-semibold rounded-full ml-2',
                    getStatusClasses(listing.is_approved, listing.is_rejected)
                  ]"
                >
                  {{ getStatusText(listing.is_approved, listing.is_rejected) }}
                </span>
              </div>
              <p class="text-sm text-gray-500 mb-2 line-clamp-2">{{ listing.description }}</p>
              <div class="flex items-center justify-between text-xs text-gray-500 mb-3">
                <span>{{ listing.category?.name || 'N/A' }}</span>
                <span>{{ formatDate(listing.created_at) }}</span>
              </div>
              <div class="flex items-center space-x-2">
                <NuxtLink :to="`/dashboard/listings/${listing.uuid}`" class="flex-1">
                  <BaseButton variant="outline" size="sm" class="w-full">
                    Edit
                  </BaseButton>
                </NuxtLink>
                <BaseButton 
                  variant="ghost" 
                  size="sm"
                  class="text-red-600 hover:text-red-700"
                  @click="confirmDelete(listing)"
                >
                  <Icon name="heroicons:trash" class="h-4 w-4" />
                </BaseButton>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <BaseModal v-model="showDeleteModal" title="Delete Listing">
      <div class="sm:flex sm:items-start">
        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10 mb-4 sm:mb-0">
          <Icon name="heroicons:exclamation-triangle" class="h-6 w-6 text-red-600" />
        </div>
        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
          <h3 class="text-lg font-medium text-gray-900 mb-2">
            Delete "{{ listingToDelete?.name }}"
          </h3>
          <p class="text-sm text-gray-500">
            Are you sure you want to delete this listing? This action cannot be undone.
          </p>
        </div>
      </div>

      <template #footer>
        <div class="flex items-center justify-end space-x-3">
          <BaseButton variant="outline" @click="showDeleteModal = false">
            Cancel
          </BaseButton>
          <BaseButton 
            variant="destructive" 
            :loading="deleting"
            @click="deleteListing"
          >
            Delete Listing
          </BaseButton>
        </div>
      </template>
    </BaseModal>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  layout: 'dashboard',
  middleware: 'auth'
})

interface Listing {
  uuid: string
  name: string
  description: string
  image_url?: string
  is_approved: boolean
  is_rejected?: boolean
  created_at: string
  category?: { name: string }
  city?: { name: string }
}

const listingStore = useListingStore()

const loading = ref(true)
const deleting = ref(false)
const viewMode = ref<'table' | 'grid'>('table')

// Delete modal
const showDeleteModal = ref(false)
const listingToDelete = ref<Listing | null>(null)

// Computed properties
const listings = computed(() => listingStore.myListings || [])

const totalListings = computed(() => listings.value.length)
const activeListings = computed(() => listings.value.filter(l => l.is_approved).length)
const pendingListings = computed(() => listings.value.filter(l => !l.is_approved && !l.is_rejected).length)
const rejectedListings = computed(() => listings.value.filter(l => l.is_rejected).length)

// Methods
const getStatusClasses = (isApproved: boolean, isRejected?: boolean): string => {
  if (isApproved) return 'bg-green-100 text-green-800'
  if (isRejected) return 'bg-red-100 text-red-800'
  return 'bg-yellow-100 text-yellow-800'
}

const getStatusText = (isApproved: boolean, isRejected?: boolean): string => {
  if (isApproved) return 'Active'
  if (isRejected) return 'Rejected'
  return 'Pending'
}

const formatDate = (dateString: string): string => {
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', { 
    month: 'short', 
    day: 'numeric',
    year: 'numeric'
  })
}


const confirmDelete = (listing: Listing) => {
  listingToDelete.value = listing
  showDeleteModal.value = true
}

const deleteListing = async () => {
  if (!listingToDelete.value) return
  
  try {
    deleting.value = true
    await listingStore.deleteListing(listingToDelete.value.uuid)
    showDeleteModal.value = false
    listingToDelete.value = null
  } catch (error) {
    console.error('Failed to delete listing:', error)
  } finally {
    deleting.value = false
  }
}

// Load data
const loadData = async () => {
  try {
    loading.value = true
    await listingStore.fetchMyListings()
  } catch (error) {
    console.error('Failed to load listings:', error)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadData()
})
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>