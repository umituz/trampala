import { defineStore } from 'pinia'
import type { Country, City, District } from '~/services/LocationService'

interface LocationState {
  countries: Country[]
  cities: City[]
  districts: District[]
  currentCountry: Country | null
  currentCity: City | null
  popularCities: City[]
  loading: boolean
  error: string | null
  countrySelectOptions: Array<{
    value: string
    label: string
    code: string
  }>
  citySelectOptions: Array<{
    value: string
    label: string
    plate_code: string
  }>
  districtSelectOptions: Array<{
    value: string
    label: string
  }>
}

export const useLocationStore = defineStore('location', {
  state: (): LocationState => ({
    countries: [],
    cities: [],
    districts: [],
    currentCountry: null,
    currentCity: null,
    popularCities: [],
    loading: false,
    error: null,
    countrySelectOptions: [],
    citySelectOptions: [],
    districtSelectOptions: []
  }),

  getters: {
    activeCountries: (state): Country[] => 
      state.countries.filter(country => country.is_active),
    
    activeCities: (state): City[] => 
      state.cities.filter(city => city.is_active),
    
    activeDistricts: (state): District[] => 
      state.districts.filter(district => district.is_active),
    
    hasCountries: (state): boolean => 
      state.countries.length > 0,
    
    hasCities: (state): boolean => 
      state.cities.length > 0,
    
    getCountryByUuid: (state) => (uuid: string): Country | undefined =>
      state.countries.find(country => country.uuid === uuid),
    
    getCityByUuid: (state) => (uuid: string): City | undefined =>
      state.cities.find(city => city.uuid === uuid),
    
    getDistrictByUuid: (state) => (uuid: string): District | undefined =>
      state.districts.find(district => district.uuid === uuid),
    
    getCityDistricts: (state) => (cityUuid: string): District[] => {
      const city = state.cities.find(c => c.uuid === cityUuid)
      return city?.districts || []
    }
  },

  actions: {
    /**
     * Fetch all countries
     */
    async fetchCountries(): Promise<void> {
      this.loading = true
      this.error = null

      try {
        const { $api } = useNuxtApp()
        const locationService = new (await import('~/services/LocationService')).LocationService($api)
        
        this.countries = await locationService.getCountries()
      } catch (error: any) {
        this.error = error.message || 'Failed to fetch countries'
        throw error
      } finally {
        this.loading = false
      }
    },

    /**
     * Fetch cities for a specific country
     */
    async fetchCitiesByCountry(countryUuid: string): Promise<void> {
      this.loading = true
      this.error = null

      try {
        const { $api } = useNuxtApp()
        const locationService = new (await import('~/services/LocationService')).LocationService($api)
        
        this.cities = await locationService.getCitiesByCountry(countryUuid)
      } catch (error: any) {
        this.error = error.message || 'Failed to fetch cities'
        throw error
      } finally {
        this.loading = false
      }
    },

    /**
     * Fetch all cities
     */
    async fetchCities(): Promise<void> {
      this.loading = true
      this.error = null

      try {
        const { $api } = useNuxtApp()
        const locationService = new (await import('~/services/LocationService')).LocationService($api)
        
        this.cities = await locationService.getCities()
      } catch (error: any) {
        this.error = error.message || 'Failed to fetch cities'
        throw error
      } finally {
        this.loading = false
      }
    },

    /**
     * Fetch city by ID with districts
     */
    async fetchCity(uuid: string): Promise<void> {
      this.loading = true
      this.error = null

      try {
        const { $api } = useNuxtApp()
        const locationService = new (await import('~/services/LocationService')).LocationService($api)
        
        this.currentCity = await locationService.getCityById(uuid)
      } catch (error: any) {
        this.error = error.message || 'Failed to fetch city'
        this.currentCity = null
        throw error
      } finally {
        this.loading = false
      }
    },

    /**
     * Fetch districts for a specific city
     */
    async fetchDistrictsByCity(cityUuid: string): Promise<void> {
      this.loading = true
      this.error = null

      try {
        const { $api } = useNuxtApp()
        const locationService = new (await import('~/services/LocationService')).LocationService($api)
        
        this.districts = await locationService.getDistrictsByCity(cityUuid)
      } catch (error: any) {
        this.error = error.message || 'Failed to fetch districts'
        throw error
      } finally {
        this.loading = false
      }
    },

    /**
     * Fetch popular cities
     */
    async fetchPopularCities(limit = 10): Promise<void> {
      this.loading = true
      this.error = null

      try {
        const { $api } = useNuxtApp()
        const locationService = new (await import('~/services/LocationService')).LocationService($api)
        
        this.popularCities = await locationService.getPopularCities(limit)
      } catch (error: any) {
        this.error = error.message || 'Failed to fetch popular cities'
        throw error
      } finally {
        this.loading = false
      }
    },

    /**
     * Fetch country select options
     */
    async fetchCountrySelectOptions(): Promise<void> {
      this.loading = true
      this.error = null

      try {
        const { $api } = useNuxtApp()
        const locationService = new (await import('~/services/LocationService')).LocationService($api)
        
        this.countrySelectOptions = await locationService.getCountrySelectOptions()
      } catch (error: any) {
        this.error = error.message || 'Failed to fetch country options'
        throw error
      } finally {
        this.loading = false
      }
    },

    /**
     * Fetch city select options by country
     */
    async fetchCitySelectOptionsByCountry(countryUuid: string): Promise<void> {
      this.loading = true
      this.error = null

      try {
        const { $api } = useNuxtApp()
        const locationService = new (await import('~/services/LocationService')).LocationService($api)
        
        this.citySelectOptions = await locationService.getCitySelectOptionsByCountry(countryUuid)
      } catch (error: any) {
        this.error = error.message || 'Failed to fetch city options'
        throw error
      } finally {
        this.loading = false
      }
    },

    /**
     * Fetch city select options
     */
    async fetchCitySelectOptions(): Promise<void> {
      this.loading = true
      this.error = null

      try {
        const { $api } = useNuxtApp()
        const locationService = new (await import('~/services/LocationService')).LocationService($api)
        
        this.citySelectOptions = await locationService.getCitySelectOptions()
      } catch (error: any) {
        this.error = error.message || 'Failed to fetch city options'
        throw error
      } finally {
        this.loading = false
      }
    },

    /**
     * Fetch district select options for a city
     */
    async fetchDistrictSelectOptions(cityUuid: string): Promise<void> {
      this.loading = true
      this.error = null

      try {
        const { $api } = useNuxtApp()
        const locationService = new (await import('~/services/LocationService')).LocationService($api)
        
        this.districtSelectOptions = await locationService.getDistrictSelectOptions(cityUuid)
      } catch (error: any) {
        this.error = error.message || 'Failed to fetch district options'
        throw error
      } finally {
        this.loading = false
      }
    },

    /**
     * Search cities by name
     */
    async searchCities(query: string): Promise<City[]> {
      try {
        const { $api } = useNuxtApp()
        const locationService = new (await import('~/services/LocationService')).LocationService($api)
        
        return await locationService.searchCities(query)
      } catch (error: any) {
        this.error = error.message || 'Failed to search cities'
        throw error
      }
    },

    /**
     * Search districts by name within a city
     */
    async searchDistricts(cityUuid: string, query: string): Promise<District[]> {
      try {
        const { $api } = useNuxtApp()
        const locationService = new (await import('~/services/LocationService')).LocationService($api)
        
        return await locationService.searchDistricts(cityUuid, query)
      } catch (error: any) {
        this.error = error.message || 'Failed to search districts'
        throw error
      }
    },

    /**
     * Get location breadcrumb
     */
    async getLocationBreadcrumb(cityUuid: string, districtUuid?: string): Promise<Array<{
      uuid: string
      name: string
      type: 'city' | 'district'
    }>> {
      try {
        const { $api } = useNuxtApp()
        const locationService = new (await import('~/services/LocationService')).LocationService($api)
        
        return await locationService.getLocationBreadcrumb(cityUuid, districtUuid)
      } catch (error: any) {
        this.error = error.message || 'Failed to get location breadcrumb'
        throw error
      }
    },

    /**
     * Validate city and district combination
     */
    async validateLocation(cityUuid: string, districtUuid?: string): Promise<boolean> {
      try {
        const { $api } = useNuxtApp()
        const locationService = new (await import('~/services/LocationService')).LocationService($api)
        
        return await locationService.validateLocation(cityUuid, districtUuid)
      } catch (error: any) {
        this.error = error.message || 'Failed to validate location'
        return false
      }
    },

    /**
     * Initialize store (fetch essential data)
     */
    async initialize(): Promise<void> {
      if (this.countries.length === 0) {
        await this.fetchCountries()
      }
      if (this.countrySelectOptions.length === 0) {
        await this.fetchCountrySelectOptions()
      }
    },

    /**
     * Clear cities when country changes
     */
    clearCities() {
      this.cities = []
      this.citySelectOptions = []
      this.clearDistricts()
    },

    /**
     * Clear districts when city changes
     */
    clearDistricts() {
      this.districts = []
      this.districtSelectOptions = []
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
      this.countries = []
      this.cities = []
      this.districts = []
      this.currentCountry = null
      this.currentCity = null
      this.popularCities = []
      this.countrySelectOptions = []
      this.citySelectOptions = []
      this.districtSelectOptions = []
      this.error = null
    }
  }
})