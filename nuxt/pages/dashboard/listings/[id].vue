<template>
  <div>
    <!-- Header -->
    <div class="mb-6">
      <nav class="flex items-center space-x-2 text-sm text-gray-500 mb-4">
        <NuxtLink to="/dashboard" class="hover:text-gray-700">Dashboard</NuxtLink>
        <Icon name="heroicons:chevron-right" class="h-4 w-4" />
        <NuxtLink to="/dashboard/listings" class="hover:text-gray-700">Listings</NuxtLink>
        <Icon name="heroicons:chevron-right" class="h-4 w-4" />
        <span class="text-gray-900">{{ listing?.name || 'Edit Listing' }}</span>
      </nav>
      
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Edit Listing</h1>
          <p class="text-gray-600 mt-1">Update your listing information</p>
        </div>
        <div class="flex items-center space-x-3">
          <span 
            v-if="listing"
            :class="[
              'inline-flex px-3 py-1 text-sm font-medium rounded-full',
              listing.is_approved ? 'bg-green-100 text-green-800' : 
              listing.is_rejected ? 'bg-red-100 text-red-800' :
              'bg-yellow-100 text-yellow-800'
            ]"
          >
            {{ listing.is_approved ? 'Active' : listing.is_rejected ? 'Rejected' : 'Pending' }}
          </span>
          <NuxtLink to="/dashboard/listings">
            <BaseButton variant="outline">
              <Icon name="heroicons:arrow-left" class="h-4 w-4 mr-2" />
              Back to Listings
            </BaseButton>
          </NuxtLink>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loadingListing" class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
      <div class="flex items-center justify-center">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mr-3"></div>
        <p class="text-gray-600">Loading listing details...</p>
      </div>
    </div>

    <!-- Error State -->
    <div v-else-if="loadingError" class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 text-center">
      <Icon name="heroicons:exclamation-triangle" class="h-12 w-12 text-red-400 mx-auto mb-4" />
      <h3 class="text-lg font-medium text-gray-900 mb-2">Failed to Load Listing</h3>
      <p class="text-gray-500 mb-4">{{ loadingError }}</p>
      <div class="flex items-center justify-center space-x-3">
        <BaseButton variant="outline" @click="loadListing">
          Try Again
        </BaseButton>
        <NuxtLink to="/dashboard/listings">
          <BaseButton variant="outline">
            Back to Listings
          </BaseButton>
        </NuxtLink>
      </div>
    </div>

    <!-- Form Card -->
    <div v-else-if="listing" class="bg-white rounded-lg shadow-sm border border-gray-200">
      <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-lg font-medium text-gray-900">Listing Information</h2>
            <p class="text-sm text-gray-500 mt-1">
              Update your listing details below
            </p>
          </div>
          
          <!-- Status Info -->
          <div v-if="listing.is_rejected" class="text-right">
            <p class="text-sm font-medium text-red-600">Rejected</p>
            <p class="text-xs text-gray-500">Make changes and resubmit</p>
          </div>
          <div v-else-if="!listing.is_approved" class="text-right">
            <p class="text-sm font-medium text-yellow-600">Pending Approval</p>
            <p class="text-xs text-gray-500">Under review</p>
          </div>
          <div v-else class="text-right">
            <p class="text-sm font-medium text-green-600">Active</p>
            <p class="text-xs text-gray-500">Live on platform</p>
          </div>
        </div>
      </div>
      
      <div class="p-6">
        <ListingForm
          :loading="updating"
          :is-edit="true"
          :initial-data="formattedListingData"
          @submit="handleSubmit"
          @cancel="handleCancel"
        />
      </div>
    </div>

    <!-- Success Modal -->
    <BaseModal v-model="showSuccessModal" title="Listing Updated Successfully!" max-width="md">
      <div class="text-center py-4">
        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <Icon name="heroicons:check" class="h-8 w-8 text-green-600" />
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">
          Your listing has been updated!
        </h3>
        <p class="text-gray-500 mb-6">
          Your changes have been saved and will be reviewed if needed.
        </p>
        <div class="flex items-center justify-center space-x-3">
          <BaseButton variant="outline" @click="showSuccessModal = false">
            Continue Editing
          </BaseButton>
          <BaseButton @click="viewListings">
            View My Listings
          </BaseButton>
        </div>
      </div>
    </BaseModal>

    <!-- Error Alert -->
    <BaseAlert
      v-if="error"
      type="error"
      :title="errorTitle"
      :description="error"
      class="mt-6"
      dismissible
      @dismiss="error = ''"
    />
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
  category_uuid: string
  city_uuid: string
  district_uuid?: string
  image_url?: string
  is_approved: boolean
  is_rejected?: boolean
  created_at: string
  updated_at: string
}

const route = useRoute()
const router = useRouter()
const listingStore = useListingStore()

const listingId = route.params.id as string

const loadingListing = ref(true)
const loadingError = ref('')
const updating = ref(false)
const error = ref('')
const errorTitle = ref('Error')
const showSuccessModal = ref(false)
const listing = ref<Listing | null>(null)

// Format listing data for the form
const formattedListingData = computed(() => {
  if (!listing.value) return null
  
  return {
    name: listing.value.name,
    description: listing.value.description,
    category_uuid: listing.value.category_uuid,
    country_uuid: listing.value.country_uuid,
    city_uuid: listing.value.city_uuid,
    district_uuid: listing.value.district_uuid || '',
    // Note: image is not included as it would need to be handled differently for edits
  }
})

const loadListing = async () => {
  try {
    loadingListing.value = true
    loadingError.value = ''
    
    await listingStore.fetchListing(listingId)
    listing.value = listingStore.currentListing
    
    if (!listing.value) {
      throw new Error('Listing not found')
    }
    
  } catch (err: any) {
    console.error('Failed to load listing:', err)
    loadingError.value = err.message || 'Failed to load listing'
  } finally {
    loadingListing.value = false
  }
}

const handleSubmit = async (formData: FormData) => {
  try {
    updating.value = true
    error.value = ''
    
    const updatedListing = await listingStore.updateListing(listingId, formData)
    listing.value = updatedListing
    showSuccessModal.value = true
    
  } catch (err: any) {
    console.error('Failed to update listing:', err)
    
    errorTitle.value = 'Failed to Update Listing'
    
    if (err.errors && typeof err.errors === 'object') {
      // Handle validation errors
      const errorMessages = Object.values(err.errors).flat()
      error.value = errorMessages.join('. ')
    } else if (err.message) {
      error.value = err.message
    } else {
      error.value = 'An unexpected error occurred. Please try again.'
    }
    
    // Scroll to top to show error
    window.scrollTo({ top: 0, behavior: 'smooth' })
    
  } finally {
    updating.value = false
  }
}

const handleCancel = () => {
  router.push('/dashboard/listings')
}

const viewListings = () => {
  showSuccessModal.value = false
  router.push('/dashboard/listings')
}

// Load listing on mount
onMounted(() => {
  loadListing()
})

</script>