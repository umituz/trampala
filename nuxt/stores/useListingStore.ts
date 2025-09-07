import { defineStore } from 'pinia'
import type { Listing, ListingCreateData, ListingUpdateData, ListingFilters } from '~/services/ListingService'
import type { PaginatedResponse } from '~/services/BaseService'

interface ListingState {
  listings: Listing[]
  currentListing: Listing | null
  myListings: Listing[]
  pendingListings: Listing[]
  loading: boolean
  creating: boolean
  updating: boolean
  deleting: boolean
  error: string | null
  pagination: {
    current_page: number
    last_page: number
    per_page: number
    total: number
    from: number | null
    to: number | null
  } | null
  filters: ListingFilters
  stats: {
    total: number
    pending: number
    approved: number
    rejected: number
    today: number
    this_week: number
    this_month: number
  } | null
}

export const useListingStore = defineStore('listing', {
  state: (): ListingState => ({
    listings: [],
    currentListing: null,
    myListings: [],
    pendingListings: [],
    loading: false,
    creating: false,
    updating: false,
    deleting: false,
    error: null,
    pagination: null,
    filters: {
      per_page: 20,
      page: 1
    },
    stats: null
  }),

  getters: {
    approvedListings: (state): Listing[] => 
      state.listings.filter(listing => listing.is_approved),
    
    pendingListingsCount: (state): number => 
      state.pendingListings.length,
    
    hasListings: (state): boolean => 
      state.listings.length > 0,
    
    hasMyListings: (state): boolean => 
      state.myListings.length > 0,
    
    isLastPage: (state): boolean => 
      !state.pagination || state.pagination.current_page >= state.pagination.last_page
  },

  actions: {
    /**
     * Fetch approved listings (public)
     */
    async fetchListings(filters: ListingFilters = {}): Promise<void> {
      this.loading = true
      this.error = null

      try {
        const { $api } = useNuxtApp()
        const listingService = new (await import('~/services/ListingService')).ListingService($api)
        
        const mergedFilters = { ...this.filters, ...filters }
        const response: PaginatedResponse<Listing> = await listingService.getApproved(mergedFilters)
        
        if (mergedFilters.page === 1) {
          this.listings = response.data
        } else {
          // Append for pagination
          this.listings.push(...response.data)
        }
        
        this.pagination = {
          ...response.meta,
          prev_page_url: response.links?.prev,
          next_page_url: response.links?.next
        }
        this.filters = mergedFilters
        
      } catch (error: any) {
        this.error = error.message || 'Failed to fetch listings'
        throw error
      } finally {
        this.loading = false
      }
    },

    /**
     * Fetch listing by ID
     */
    async fetchListing(uuid: string): Promise<void> {
      this.loading = true
      this.error = null

      try {
        const { $api } = useNuxtApp()
        const listingService = new (await import('~/services/ListingService')).ListingService($api)
        
        this.currentListing = await listingService.getById(uuid)
        
      } catch (error: any) {
        this.error = error.message || 'Failed to fetch listing'
        this.currentListing = null
        throw error
      } finally {
        this.loading = false
      }
    },

    /**
     * Create new listing
     */
    async createListing(formData: FormData | ListingCreateData): Promise<Listing> {
      this.creating = true
      this.error = null

      try {
        const { $api } = useNuxtApp()
        const listingService = new (await import('~/services/ListingService')).ListingService($api)
        
        let newListing: Listing
        
        if (formData instanceof FormData) {
          // Convert FormData to ListingCreateData
          const image = formData.get('image') as File
          
          const data: ListingCreateData = {
            name: formData.get('name') as string,
            description: formData.get('description') as string,
            category_uuid: formData.get('category_uuid') as string,
            country_uuid: formData.get('country_uuid') as string,
            city_uuid: formData.get('city_uuid') as string,
            district_uuid: formData.get('district_uuid') as string,
            image
          }
          
          newListing = await listingService.create(data)
        } else {
          newListing = await listingService.create(formData)
        }
        
        // Add to my listings
        this.myListings.unshift(newListing)
        
        return newListing
      } catch (error: any) {
        this.error = error.message || 'Failed to create listing'
        throw error
      } finally {
        this.creating = false
      }
    },

    /**
     * Update existing listing
     */
    async updateListing(uuid: string, formData: FormData | ListingUpdateData): Promise<Listing> {
      this.updating = true
      this.error = null

      try {
        const { $api } = useNuxtApp()
        const listingService = new (await import('~/services/ListingService')).ListingService($api)
        
        let updatedListing: Listing
        
        if (formData instanceof FormData) {
          // Convert FormData to ListingUpdateData
          const image = formData.get('image') as File
          
          const data: ListingUpdateData = {}
          
          // Add only provided fields
          const name = formData.get('name')
          const description = formData.get('description')
          const categoryUuid = formData.get('category_uuid')
          const countryUuid = formData.get('country_uuid')
          const cityUuid = formData.get('city_uuid')
          const districtUuid = formData.get('district_uuid')
          
          if (name) data.name = name as string
          if (description) data.description = description as string
          if (categoryUuid) data.category_uuid = categoryUuid as string
          if (countryUuid) data.country_uuid = countryUuid as string
          if (cityUuid) data.city_uuid = cityUuid as string
          if (districtUuid) data.district_uuid = districtUuid as string
          if (image) data.image = image
          
          updatedListing = await listingService.update(uuid, data)
        } else {
          updatedListing = await listingService.update(uuid, formData)
        }
        
        // Update in current listing
        if (this.currentListing?.uuid === uuid) {
          this.currentListing = updatedListing
        }
        
        // Update in my listings
        const myListingIndex = this.myListings.findIndex(l => l.uuid === uuid)
        if (myListingIndex !== -1) {
          this.myListings[myListingIndex] = updatedListing
        }
        
        // Update in main listings if present
        const listingIndex = this.listings.findIndex(l => l.uuid === uuid)
        if (listingIndex !== -1) {
          this.listings[listingIndex] = updatedListing
        }
        
        return updatedListing
      } catch (error: any) {
        this.error = error.message || 'Failed to update listing'
        throw error
      } finally {
        this.updating = false
      }
    },

    /**
     * Delete listing (soft delete)
     */
    async deleteListing(uuid: string): Promise<void> {
      this.deleting = true
      this.error = null

      try {
        const { $api } = useNuxtApp()
        const listingService = new (await import('~/services/ListingService')).ListingService($api)
        
        await listingService.deleteListing(uuid)
        
        // Remove from all arrays
        this.listings = this.listings.filter(l => l.uuid !== uuid)
        this.myListings = this.myListings.filter(l => l.uuid !== uuid)
        this.pendingListings = this.pendingListings.filter(l => l.uuid !== uuid)
        
        // Clear current listing if it's the deleted one
        if (this.currentListing?.uuid === uuid) {
          this.currentListing = null
        }
        
      } catch (error: any) {
        this.error = error.message || 'Failed to delete listing'
        throw error
      } finally {
        this.deleting = false
      }
    },

    /**
     * Fetch user's listings
     */
    async fetchMyListings(filters: Omit<ListingFilters, 'user_uuid'> = {}): Promise<void> {
      this.loading = true
      this.error = null

      try {
        const { $api } = useNuxtApp()
        const listingService = new (await import('~/services/ListingService')).ListingService($api)
        
        const response: PaginatedResponse<Listing> = await listingService.getMyListings(filters)
        this.myListings = response.data
        
      } catch (error: any) {
        this.error = error.message || 'Failed to fetch your listings'
        throw error
      } finally {
        this.loading = false
      }
    },

    /**
     * Fetch pending listings (admin only)
     */
    async fetchPendingListings(filters: ListingFilters = {}): Promise<void> {
      this.loading = true
      this.error = null

      try {
        const { $api } = useNuxtApp()
        const listingService = new (await import('~/services/ListingService')).ListingService($api)
        
        const response: PaginatedResponse<Listing> = await listingService.getPending(filters)
        this.pendingListings = response.data
        
      } catch (error: any) {
        this.error = error.message || 'Failed to fetch pending listings'
        throw error
      } finally {
        this.loading = false
      }
    },

    /**
     * Approve listing (admin only)
     */
    async approveListing(uuid: string): Promise<void> {
      this.loading = true
      this.error = null

      try {
        const { $api } = useNuxtApp()
        const listingService = new (await import('~/services/ListingService')).ListingService($api)
        
        const approvedListing = await listingService.approve(uuid)
        
        // Update in pending listings
        const pendingIndex = this.pendingListings.findIndex(l => l.uuid === uuid)
        if (pendingIndex !== -1) {
          this.pendingListings[pendingIndex] = approvedListing
        }
        
        // Update current listing if it's the approved one
        if (this.currentListing?.uuid === uuid) {
          this.currentListing = approvedListing
        }
        
      } catch (error: any) {
        this.error = error.message || 'Failed to approve listing'
        throw error
      } finally {
        this.loading = false
      }
    },

    /**
     * Reject listing (admin only)
     */
    async rejectListing(uuid: string, reason: string): Promise<void> {
      this.loading = true
      this.error = null

      try {
        const { $api } = useNuxtApp()
        const listingService = new (await import('~/services/ListingService')).ListingService($api)
        
        const rejectedListing = await listingService.reject(uuid, reason)
        
        // Update in pending listings
        const pendingIndex = this.pendingListings.findIndex(l => l.uuid === uuid)
        if (pendingIndex !== -1) {
          this.pendingListings[pendingIndex] = rejectedListing
        }
        
        // Update current listing if it's the rejected one
        if (this.currentListing?.uuid === uuid) {
          this.currentListing = rejectedListing
        }
        
      } catch (error: any) {
        this.error = error.message || 'Failed to reject listing'
        throw error
      } finally {
        this.loading = false
      }
    },


    /**
     * Fetch listing statistics (admin only)
     */
    async fetchStats(): Promise<void> {
      try {
        const { $api } = useNuxtApp()
        const listingService = new (await import('~/services/ListingService')).ListingService($api)
        
        this.stats = await listingService.getStats()
      } catch (error: any) {
        this.error = error.message || 'Failed to fetch statistics'
        throw error
      }
    },

    /**
     * Clear error state
     */
    clearError() {
      this.error = null
    },


    /**
     * Reset store state
     */
    reset() {
      this.listings = []
      this.currentListing = null
      this.myListings = []
      this.pendingListings = []
      this.pagination = null
      this.filters = { per_page: 20, page: 1 }
      this.error = null
      this.stats = null
    }
  }
})