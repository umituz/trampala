<template>
  <div>
    <div v-if="loading" class="text-center py-8">
      <div class="animate-spin rounded-full h-32 w-32 border-b-2 border-primary-600 mx-auto"></div>
      <p class="mt-4 text-gray-500">Loading listings...</p>
    </div>
    
    <div v-else-if="error" class="text-center py-8">
      <div class="text-red-500 mb-4">
        <svg class="h-16 w-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
      </div>
      <h3 class="text-lg font-medium text-gray-900 mb-2">Failed to load listings</h3>
      <p class="text-gray-500 mb-4">{{ error }}</p>
      <BaseButton @click="$emit('retry')">
        Try Again
      </BaseButton>
    </div>
    
    <div v-else-if="listings.length === 0" class="text-center py-12">
      <svg class="h-16 w-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
      </svg>
      <h3 class="text-lg font-medium text-gray-900 mb-2">No listings found</h3>
      <p class="text-gray-500">{{ emptyMessage || 'Try adjusting your search criteria or check back later.' }}</p>
    </div>
    
    <div v-else>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <ListingCard
          v-for="listing in listings"
          :key="listing.uuid"
          :listing="listing"
          :show-actions="showActions"
          :can-approve="canApprove"
          :can-reject="canReject"
          :can-edit="canEdit"
          @approve="$emit('approve', $event)"
          @reject="$emit('reject', $event)"
        />
      </div>
      
      <!-- Pagination -->
      <div v-if="pagination && pagination.total > pagination.per_page" class="mt-8 flex justify-center">
        <nav class="flex items-center space-x-2">
          <BaseButton
            variant="outline"
            size="sm"
            :disabled="!pagination.prev_page_url"
            @click="$emit('page-change', pagination.current_page - 1)"
          >
            Previous
          </BaseButton>
          
          <span class="px-3 py-2 text-sm text-gray-700">
            Page {{ pagination.current_page }} of {{ pagination.last_page }}
          </span>
          
          <BaseButton
            variant="outline"
            size="sm"
            :disabled="!pagination.next_page_url"
            @click="$emit('page-change', pagination.current_page + 1)"
          >
            Next
          </BaseButton>
        </nav>
      </div>
      
      <!-- Results summary -->
      <div v-if="pagination" class="mt-4 text-center text-sm text-gray-500">
        Showing {{ pagination.from }} to {{ pagination.to }} of {{ pagination.total }} results
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
interface Props {
  listings: any[]
  loading?: boolean
  error?: string | null
  emptyMessage?: string
  showActions?: boolean
  canApprove?: boolean
  canReject?: boolean
  canEdit?: boolean
  pagination?: {
    current_page: number
    last_page: number
    per_page: number
    total: number
    from: number
    to: number
    prev_page_url: string | null
    next_page_url: string | null
  } | null
}

withDefaults(defineProps<Props>(), {
  loading: false,
  error: null,
  showActions: false,
  canApprove: false,
  canReject: false,
  canEdit: false,
  pagination: null
})

defineEmits<{
  retry: []
  approve: [listing: any]
  reject: [listing: any]
  'page-change': [page: number]
}>()
</script>