<template>
  <div>
    <!-- Main Content with existing functionality -->
    <div class="bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8 text-center">
          <h2 class="text-2xl font-bold text-gray-900">Browse Listings</h2>
          <p class="text-gray-600 mt-2">Discover what others are offering in your community</p>
        </div>

        <!-- Listings Grid -->
        <ListingGrid
          :listings="listings"
          :loading="loading"
          :error="error"
          :pagination="pagination"
          empty-message="No approved listings available at the moment."
          @retry="fetchListings"
          @page-change="onPageChange"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { useListingStore } from '~/stores/useListingStore'

// SEO
useHead({
  title: 'Trampala - Browse Listings',
  meta: [
    { name: 'description', content: 'Browse approved listings on Trampala platform. Find what you need in your local community.' }
  ]
})

const listingStore = useListingStore()

// Reactive data
const loading = ref(false)
const error = ref(null)
const listings = ref([])
const pagination = ref(null)

// Simple pagination
const currentPage = ref(1)
const perPage = 12

// Composables
const { getErrorMessage } = useErrorHandler()

// Methods
const fetchListings = async () => {
  try {
    loading.value = true
    error.value = null
    
    const filterParams = {
      page: currentPage.value,
      per_page: perPage,
      status: 'approved'
    }

    await listingStore.fetchListings(filterParams)
    
    listings.value = listingStore.listings || []
    pagination.value = listingStore.pagination
  } catch (err) {
    error.value = getErrorMessage(err)
    listings.value = []
    pagination.value = null
  } finally {
    loading.value = false
  }
}

const onPageChange = (page) => {
  currentPage.value = page
  fetchListings()
}

// Initialize on mount
onMounted(async () => {
  try {
    // Fetch initial listings
    await fetchListings()
  } catch (err) {
    error.value = 'Failed to load listings'
  }
})
</script>