<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Loading State -->
    <div v-if="loading" class="flex items-center justify-center min-h-screen">
      <div class="text-center">
        <div class="animate-spin rounded-full h-32 w-32 border-b-2 border-primary-600 mx-auto"></div>
        <p class="mt-4 text-gray-500">Loading listing...</p>
      </div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="flex items-center justify-center min-h-screen">
      <div class="text-center">
        <div class="text-red-500 mb-4">
          <svg class="h-16 w-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Failed to load listing</h3>
        <p class="text-gray-500 mb-4">{{ error }}</p>
        <div class="space-x-4">
          <BaseButton @click="fetchListing">Try Again</BaseButton>
          <BaseButton variant="outline" @click="$router.push('/')">Go Home</BaseButton>
        </div>
      </div>
    </div>

    <!-- Listing Content -->
    <div v-else-if="listing" class="py-8">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumb -->
        <nav class="mb-8" aria-label="Breadcrumb">
          <ol class="flex items-center space-x-2 text-sm">
            <li>
              <NuxtLink to="/" class="text-gray-400 hover:text-gray-600">Home</NuxtLink>
            </li>
            <li class="text-gray-400">/</li>
            <li class="text-gray-600 truncate">{{ listing.name }}</li>
          </ol>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Main Content -->
          <div class="lg:col-span-2">
            <!-- Image -->
            <div v-if="listing.image_url" class="mb-6">
              <img
                :src="listing.image_url"
                :alt="listing.name"
                class="w-full h-96 object-cover rounded-lg shadow-lg"
              />
            </div>
            
            <div v-else class="mb-6 h-96 bg-gray-200 rounded-lg flex items-center justify-center">
              <svg class="h-24 w-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
            </div>

            <!-- Title and Basic Info -->
            <div class="mb-6">
              <div class="flex items-start justify-between">
                <div>
                  <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ listing.name }}</h1>
                  <p class="text-gray-500">Listing #{{ listing.unique_number }}</p>
                </div>
                
                <span :class="statusBadgeClasses">
                  {{ getStatusText(listing.status) }}
                </span>
              </div>
            </div>

            <!-- Description -->
            <BaseCard class="mb-6">
              <template #header>
                <h2 class="text-xl font-semibold text-gray-900">Description</h2>
              </template>
              
              <div class="prose prose-gray max-w-none">
                <p class="whitespace-pre-wrap">{{ listing.description }}</p>
              </div>
            </BaseCard>

            <!-- Additional Details -->
            <BaseCard>
              <template #header>
                <h2 class="text-xl font-semibold text-gray-900">Details</h2>
              </template>
              
              <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <dt class="text-sm font-medium text-gray-500">Category</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ listing.category?.name || 'N/A' }}</dd>
                </div>
                
                <div>
                  <dt class="text-sm font-medium text-gray-500">Location</dt>
                  <dd class="mt-1 text-sm text-gray-900">
                    {{ listing.city?.name }}<span v-if="listing.district">, {{ listing.district.name }}</span>
                  </dd>
                </div>
                
                <div>
                  <dt class="text-sm font-medium text-gray-500">Created</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ formatDate(listing.created_at) }}</dd>
                </div>
                
                <div v-if="listing.approved_at">
                  <dt class="text-sm font-medium text-gray-500">Approved</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ formatDate(listing.approved_at) }}</dd>
                </div>
              </dl>
            </BaseCard>
          </div>

          <!-- Sidebar -->
          <div class="lg:col-span-1">
            <!-- Contact Information -->
            <BaseCard class="mb-6" v-if="listing.user">
              <template #header>
                <h3 class="text-lg font-semibold text-gray-900">Contact Information</h3>
              </template>
              
              <div class="space-y-3">
                <div class="flex items-center space-x-3">
                  <div class="flex-shrink-0">
                    <div class="h-10 w-10 bg-primary-100 rounded-full flex items-center justify-center">
                      <svg class="h-6 w-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                      </svg>
                    </div>
                  </div>
                  <div>
                    <p class="text-sm font-medium text-gray-900">{{ listing.user.name }}</p>
                    <p class="text-sm text-gray-500">{{ listing.user.email }}</p>
                  </div>
                </div>
                
              </div>
            </BaseCard>

            <!-- Quick Stats -->
            <BaseCard class="mb-6">
              <template #header>
                <h3 class="text-lg font-semibold text-gray-900">Quick Stats</h3>
              </template>
              
              <div class="space-y-3">
                <div class="flex justify-between">
                  <span class="text-sm text-gray-500">Created</span>
                  <span class="text-sm font-medium text-gray-900">{{ formatDateShort(listing.created_at) }}</span>
                </div>
                
                <div class="flex justify-between">
                  <span class="text-sm text-gray-500">Status</span>
                  <span :class="getStatusTextClasses(listing.status)">{{ getStatusText(listing.status) }}</span>
                </div>
              </div>
            </BaseCard>

            <!-- Actions (for owners/admins) -->
            <BaseCard v-if="canManageListing">
              <template #header>
                <h3 class="text-lg font-semibold text-gray-900">Actions</h3>
              </template>
              
              <div class="space-y-3">
                <BaseButton 
                  v-if="canEdit"
                  variant="secondary" 
                  block 
                  @click="$router.push(`/listings/${listing.uuid}/edit`)"
                >
                  Edit Listing
                </BaseButton>
                
                <BaseButton 
                  v-if="canApprove && listing.status === 'pending'"
                  variant="success" 
                  block 
                  @click="approveListing"
                  :loading="actionLoading"
                >
                  Approve
                </BaseButton>
                
                <BaseButton 
                  v-if="canReject && listing.status === 'pending'"
                  variant="danger" 
                  block 
                  @click="rejectListing"
                  :loading="actionLoading"
                >
                  Reject
                </BaseButton>
              </div>
            </BaseCard>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useListingStore } from '~/stores/useListingStore'

const route = useRoute()
const router = useRouter()
const listingStore = useListingStore()
const { data } = useAuth()

// Reactive state
const listing = ref(null)
const loading = ref(true)
const error = ref('')
const actionLoading = ref(false)

// Get listing ID from route
const listingId = route.params.id

// Computed properties
const canManageListing = computed(() => {
  if (!{ data }.user || !listing.value) return false
  
  const isOwner = { data }.user.uuid === listing.value.data.value?.user.uuid
  const isAdmin = { data }.user.role === 'admin'
  
  return isOwner || isAdmin
})

const canEdit = computed(() => {
  if (!{ data }.user || !listing.value) return false
  return { data }.user.uuid === listing.value.data.value?.user.uuid
})

const canApprove = computed(() => {
  return { data }.data.value?.user.role === 'admin'
})

const canReject = computed(() => {
  return { data }.data.value?.user.role === 'admin'
})

const statusBadgeClasses = computed(() => {
  if (!listing.value) return ''
  
  const baseClasses = 'px-3 py-1 text-sm font-medium rounded-full'
  const statusClasses = {
    pending: 'bg-yellow-100 text-yellow-800',
    approved: 'bg-green-100 text-green-800',
    rejected: 'bg-red-100 text-red-800'
  }
  
  return [baseClasses, statusClasses[listing.value.status] || 'bg-gray-100 text-gray-800'].join(' ')
})

// Methods
const getStatusText = (status) => {
  const statusMap = {
    pending: 'Pending Review',
    approved: 'Approved',
    rejected: 'Rejected'
  }
  return statusMap[status] || status
}

const getStatusTextClasses = (status) => {
  const statusClasses = {
    pending: 'text-sm font-medium text-yellow-600',
    approved: 'text-sm font-medium text-green-600',
    rejected: 'text-sm font-medium text-red-600'
  }
  return statusClasses[status] || 'text-sm font-medium text-gray-600'
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const formatDateShort = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  })
}

const fetchListing = async () => {
  try {
    loading.value = true
    error.value = ''
    
    await listingStore.fetchListing(listingId)
    listing.value = listingStore.currentListing
    
    // Update page meta with listing info
    useHead({
      title: `${listing.value.name} - Trampala`,
      meta: [
        { name: 'description', content: listing.value.description.substring(0, 160) }
      ]
    })
    
  } catch (err) {
    error.value = err.message || 'Listing not found'
  } finally {
    loading.value = false
  }
}

const approveListing = async () => {
  try {
    actionLoading.value = true
    await listingStore.approveListing(listing.value.uuid)
    
    // Refresh listing data
    await fetchListing()
    
    // Show success message (you might want to add a toast notification here)
    
  } catch (err) {
    error.value = err.message || 'Failed to approve listing'
  } finally {
    actionLoading.value = false
  }
}

const rejectListing = async () => {
  try {
    actionLoading.value = true
    await listingStore.rejectListing(listing.value.uuid)
    
    // Refresh listing data
    await fetchListing()
    
    // Show success message (you might want to add a toast notification here)
    
  } catch (err) {
    error.value = err.message || 'Failed to reject listing'
  } finally {
    actionLoading.value = false
  }
}

// Fetch listing on mount
onMounted(async () => {
  await fetchListing()
})
</script>