<template>
  <BaseCard hover clickable @click="$router.push(`/listings/${listing.uuid}`)">
    <div class="relative">
      <div v-if="listing.thumbnail_url || listing.image_url" class="aspect-w-16 aspect-h-9 mb-4 relative">
        <img
          :src="listing.thumbnail_url || listing.image_url"
          :alt="listing.name"
          class="object-cover rounded-t-lg"
        />
        <!-- Image count badge -->
        <div 
          v-if="listing.image_urls && listing.image_urls.length > 1" 
          class="absolute bottom-2 left-2 bg-black bg-opacity-60 text-white text-xs px-2 py-1 rounded-full flex items-center"
        >
          <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
          {{ listing.image_urls.length }}
        </div>
      </div>
      
      <div v-else class="aspect-w-16 aspect-h-9 mb-4 bg-gray-200 rounded-t-lg flex items-center justify-center">
        <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
      </div>
      
      <div v-if="showActions" class="absolute top-2 right-2">
        <span :class="statusBadgeClasses">
          {{ getStatusText(listing.status) }}
        </span>
      </div>
    </div>
    
    <div class="space-y-3">
      <div>
        <h3 class="text-lg font-semibold text-gray-900 line-clamp-2">
          {{ listing.name }}
        </h3>
        <p class="text-sm text-gray-500 mt-1">
          #{{ listing.unique_number }}
        </p>
      </div>
      
      <p v-if="listing.description" class="text-gray-600 text-sm line-clamp-3">
        {{ listing.description }}
      </p>
      
      <div class="flex items-center justify-between text-sm text-gray-500">
        <div class="flex items-center space-x-2">
          <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.99 1.99 0 013 12V7a4 4 0 014-4z" />
          </svg>
          <span>{{ listing.category?.name }}</span>
        </div>
        
        <div class="flex items-center space-x-2">
          <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
          </svg>
          <span>
            {{ listing.city?.name }}<span v-if="listing.district">, {{ listing.district.name }}</span>
          </span>
        </div>
      </div>
      
      <div class="flex items-center justify-between pt-2 border-t border-gray-100">
        <span class="text-xs text-gray-400">
          {{ formatDate(listing.created_at) }}
        </span>
        
        <div v-if="showActions" class="flex space-x-2">
          <BaseButton
            v-if="listing.status === 'pending' && canApprove"
            variant="success"
            size="sm"
            @click.stop="$emit('approve', listing)"
          >
            Approve
          </BaseButton>
          
          <BaseButton
            v-if="listing.status === 'pending' && canReject"
            variant="danger"
            size="sm"
            @click.stop="$emit('reject', listing)"
          >
            Reject
          </BaseButton>
          
          <BaseButton
            v-if="canEdit"
            variant="secondary"
            size="sm"
            @click.stop="$router.push(`/listings/${listing.uuid}/edit`)"
          >
            Edit
          </BaseButton>
        </div>
      </div>
      
      <!-- View Details Button (always visible for public listings) -->
      <div v-if="!showActions" class="mt-3">
        <BaseButton
          variant="primary"
          size="sm"
          block
          @click.stop="$router.push(`/listings/${listing.uuid}`)"
        >
          <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
          </svg>
          View Details
        </BaseButton>
      </div>
    </div>
  </BaseCard>
</template>

<script setup lang="ts">
interface Props {
  listing: any // Will be properly typed later
  showActions?: boolean
  canApprove?: boolean
  canReject?: boolean
  canEdit?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  showActions: false,
  canApprove: false,
  canReject: false,
  canEdit: false
})

const emit = defineEmits<{
  approve: [listing: any]
  reject: [listing: any]
}>()

const getStatusText = (status: string): string => {
  const statusMap = {
    pending: 'Pending',
    approved: 'Approved',
    rejected: 'Rejected'
  }
  return statusMap[status as keyof typeof statusMap] || status
}

const statusBadgeClasses = computed(() => {
  const baseClasses = 'px-2 py-1 text-xs font-medium rounded-full'
  
  const statusClasses = {
    pending: 'bg-yellow-100 text-yellow-800',
    approved: 'bg-green-100 text-green-800',
    rejected: 'bg-red-100 text-red-800'
  }
  
  return [baseClasses, statusClasses[props.listing.status as keyof typeof statusClasses] || 'bg-gray-100 text-gray-800'].join(' ')
})

const formatDate = (date: string): string => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.aspect-w-16 {
  position: relative;
  width: 100%;
}

.aspect-w-16::before {
  content: '';
  display: block;
  padding-bottom: calc(9 / 16 * 100%);
}

.aspect-h-9 > img {
  position: absolute;
  height: 100%;
  width: 100%;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
}
</style>