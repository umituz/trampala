<template>
  <div>
    <!-- Header -->
    <div class="mb-6">
      <nav class="flex items-center space-x-2 text-sm text-gray-500 mb-4">
        <NuxtLink to="/dashboard" class="hover:text-gray-700">Dashboard</NuxtLink>
        <Icon name="heroicons:chevron-right" class="h-4 w-4" />
        <NuxtLink to="/dashboard/listings" class="hover:text-gray-700">Listings</NuxtLink>
        <Icon name="heroicons:chevron-right" class="h-4 w-4" />
        <span class="text-gray-900">Create Listing</span>
      </nav>
      
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Create New Listing</h1>
          <p class="text-gray-600 mt-1">Fill in the details below to create your listing</p>
        </div>
        <NuxtLink to="/dashboard/listings">
          <BaseButton variant="outline">
            <Icon name="heroicons:arrow-left" class="h-4 w-4 mr-2" />
            Back to Listings
          </BaseButton>
        </NuxtLink>
      </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
      <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-medium text-gray-900">Listing Information</h2>
        <p class="text-sm text-gray-500 mt-1">
          Provide accurate information to help users find your listing
        </p>
      </div>
      
      <div class="p-6">
        <ListingForm
          :loading="loading"
          :is-edit="false"
          @submit="handleSubmit"
          @cancel="handleCancel"
        />
      </div>
    </div>

    <!-- Success Modal -->
    <BaseModal v-model="showSuccessModal" title="Listing Created Successfully!" max-width="md">
      <div class="text-center py-4">
        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <Icon name="heroicons:check" class="h-8 w-8 text-green-600" />
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">
          Your listing has been created!
        </h3>
        <p class="text-gray-500 mb-6">
          Your listing is now pending approval. We'll review it and notify you once it's live.
        </p>
        <div class="flex items-center justify-center space-x-3">
          <BaseButton variant="outline" @click="createAnother">
            Create Another
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

const router = useRouter()
const listingStore = useListingStore()

const loading = ref(false)
const error = ref('')
const errorTitle = ref('Error')
const showSuccessModal = ref(false)
const createdListing = ref(null)

const handleSubmit = async (formData: FormData) => {
  try {
    loading.value = true
    error.value = ''
    
    const newListing = await listingStore.createListing(formData)
    createdListing.value = newListing
    showSuccessModal.value = true
    
  } catch (err: any) {
    console.error('Failed to create listing:', err)
    
    errorTitle.value = 'Failed to Create Listing'
    
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
    loading.value = false
  }
}

const handleCancel = () => {
  router.push('/dashboard/listings')
}

const createAnother = () => {
  showSuccessModal.value = false
  // Reset form by reloading the page
  window.location.reload()
}

const viewListings = () => {
  showSuccessModal.value = false
  router.push('/dashboard/listings')
}

</script>