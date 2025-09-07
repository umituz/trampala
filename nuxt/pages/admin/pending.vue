<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Pending Approvals</h1>
        <p class="text-gray-600">Review and approve listings waiting for admin approval</p>
      </div>
      <div class="flex items-center space-x-4">
        <div class="text-sm text-gray-500">
          Total pending: <span class="font-medium text-gray-900">{{ totalPending }}</span>
        </div>
        <BaseButton @click="refreshListings">
          <Icon name="lucide:refresh-cw" class="h-4 w-4 mr-2" />
          Refresh
        </BaseButton>
      </div>
    </div>



      <!-- Success/Error Messages -->
      <div v-if="actionMessage.text" :class="actionMessage.type === 'success' ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200'" class="border rounded-lg p-4 mb-6">
        <div class="flex items-center">
          <svg v-if="actionMessage.type === 'success'" class="h-5 w-5 text-green-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
          </svg>
          <svg v-else class="h-5 w-5 text-red-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
          </svg>
          <p :class="actionMessage.type === 'success' ? 'text-green-800' : 'text-red-800'">
            {{ actionMessage.text }}
          </p>
        </div>
      </div>

      <!-- Listings Grid -->
      <ListingGrid
        :listings="listings"
        :loading="loading"
        :error="error"
        :pagination="pagination"
        :show-actions="true"
        :can-approve="true"
        :can-reject="true"
        empty-message="No listings pending approval at the moment."
        @retry="fetchPendingListings"
        @page-change="onPageChange"
        @approve="handleApprove"
        @reject="handleReject"
      />
  </div>
</template>

<script setup>
// Page meta
definePageMeta({
  layout: 'dashboard',
  middleware: 'auth'
})

import { useListingStore } from '~/stores/useListingStore'


// Auth check using localStorage-based system
const router = useRouter()
const { isAdmin, getCurrentUser } = await import('~/utils/auth')

// Check admin authentication on mount
onMounted(() => {
  const user = getCurrentUser()
  if (!user || !isAdmin()) {
    router.push('/auth/login')
  }
})

const listingStore = useListingStore()

// Reactive data
const loading = ref(false)
const error = ref(null)
const listings = ref([])
const pagination = ref(null)
const actionMessage = ref({ text: '', type: 'success' })

// Filters
const filters = reactive({
  status: 'pending',
  page: 1,
  per_page: 12
})

// Computed
const totalPending = computed(() => pagination.value?.total || 0)

// Methods
const fetchPendingListings = async () => {
  try {
    loading.value = true
    error.value = null
    
    const params = {
      page: filters.page,
      per_page: filters.per_page
    }
    
    await listingStore.fetchPendingListings(params)
    
    // Get data from store
    listings.value = listingStore.pendingListings || []
    pagination.value = listingStore.pagination
    
  } catch (err) {
    error.value = err.message || 'Failed to fetch pending listings'
    listings.value = []
    pagination.value = null
  } finally {
    loading.value = false
  }
}

const handleApprove = async (listing) => {
  try {
    await listingStore.approveListing(listing.uuid)
    
    // Remove from pending list
    const index = listings.value.findIndex(l => l.uuid === listing.uuid)
    if (index > -1) {
      listings.value.splice(index, 1)
    }
    
    // Show success message
    actionMessage.value = {
      text: `Listing "${listing.name}" has been approved successfully.`,
      type: 'success'
    }
    
    // Clear message after 5 seconds
    setTimeout(() => {
      actionMessage.value.text = ''
    }, 5000)
    
  } catch (err) {
    actionMessage.value = {
      text: err.message || 'Failed to approve listing',
      type: 'error'
    }
    
    setTimeout(() => {
      actionMessage.value.text = ''
    }, 5000)
  }
}

const handleReject = async (listing) => {
  try {
    await listingStore.rejectListing(listing.uuid)
    
    // Remove from pending list
    const index = listings.value.findIndex(l => l.uuid === listing.uuid)
    if (index > -1) {
      listings.value.splice(index, 1)
    }
    
    // Show success message
    actionMessage.value = {
      text: `Listing "${listing.name}" has been rejected.`,
      type: 'success'
    }
    
    // Clear message after 5 seconds
    setTimeout(() => {
      actionMessage.value.text = ''
    }, 5000)
    
  } catch (err) {
    actionMessage.value = {
      text: err.message || 'Failed to reject listing',
      type: 'error'
    }
    
    setTimeout(() => {
      actionMessage.value.text = ''
    }, 5000)
  }
}

const onPageChange = (page) => {
  filters.page = page
  fetchPendingListings()
}

const refreshListings = () => {
  fetchPendingListings()
}

// Initialize
onMounted(async () => {
  try {
    await fetchPendingListings()
  } catch (err) {
    error.value = 'Failed to initialize admin panel'
  }
})
</script>