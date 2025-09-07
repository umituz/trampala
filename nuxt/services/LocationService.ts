import { BaseService } from './BaseService'
import type { AxiosInstance } from 'axios'

export interface Country {
  uuid: string
  name: string
  code: string
  phone_code?: string
  is_active: boolean
  cities?: City[]
  listings_count?: number
  created_at: string
  updated_at: string
}

export interface City {
  uuid: string
  name: string
  plate_code: string
  is_active: boolean
  country_uuid?: string
  country?: {
    uuid: string
    name: string
    code: string
  }
  districts?: District[]
  listings_count?: number
  created_at: string
  updated_at: string
}

export interface District {
  uuid: string
  name: string
  is_active: boolean
  city?: {
    uuid: string
    name: string
    plate_code: string
  }
  listings_count?: number
  created_at: string
  updated_at: string
}

export class LocationService extends BaseService {
  constructor(api: AxiosInstance) {
    super(api)
  }

  /**
   * Get all countries
   */
  async getCountries(): Promise<Country[]> {
    const response = await this.get<Country[]>('/countries')
    return Array.isArray(response) ? response : []
  }

  /**
   * Get country by ID with cities
   */
  async getCountryById(uuid: string): Promise<Country> {
    return this.get<Country>(`/countries/${uuid}`)
  }

  /**
   * Get cities for a specific country
   */
  async getCitiesByCountry(countryUuid: string): Promise<City[]> {
    const response = await this.get<City[]>(`/countries/${countryUuid}/cities`)
    return Array.isArray(response) ? response : []
  }

  /**
   * Get all cities
   */
  async getCities(): Promise<City[]> {
    const response = await this.get<City[]>('/cities')
    return Array.isArray(response) ? response : []
  }

  /**
   * Get city by ID with districts
   */
  async getCityById(uuid: string): Promise<City> {
    return this.get<City>(`/cities/${uuid}`)
  }

  /**
   * Get districts for a specific city
   */
  async getDistrictsByCity(cityUuid: string): Promise<District[]> {
    const city = await this.getCityById(cityUuid)
    return city.districts || []
  }

  /**
   * Search cities by name
   */
  async searchCities(query: string): Promise<City[]> {
    const cities = await this.getCities()
    return cities.filter(city =>
      city.name.toLowerCase().includes(query.toLowerCase()) ||
      city.plate_code.includes(query)
    )
  }

  /**
   * Search districts by name
   */
  async searchDistricts(cityUuid: string, query: string): Promise<District[]> {
    const districts = await this.getDistrictsByCity(cityUuid)
    return districts.filter(district =>
      district.name.toLowerCase().includes(query.toLowerCase())
    )
  }

  /**
   * Get countries formatted for select options
   */
  async getCountrySelectOptions(): Promise<Array<{
    value: string
    label: string
    code: string
  }>> {
    const countries = await this.getCountries()
    if (!Array.isArray(countries)) {
      return []
    }
    return countries
      .filter(country => country && (country.is_active !== false))
      .sort((a, b) => a.name.localeCompare(b.name, 'en'))
      .map(country => ({
        value: country.uuid,
        label: country.name,
        code: country.code
      }))
  }

  /**
   * Get cities formatted for select options by country
   */
  async getCitySelectOptionsByCountry(countryUuid: string): Promise<Array<{
    value: string
    label: string
    plate_code: string
  }>> {
    const cities = await this.getCitiesByCountry(countryUuid)
    if (!Array.isArray(cities)) {
      return []
    }
    return cities
      .filter(city => city && (city.is_active !== false))
      .sort((a, b) => a.name.localeCompare(b.name, 'tr'))
      .map(city => ({
        value: city.uuid,
        label: city.name,
        plate_code: city.plate_code
      }))
  }

  /**
   * Get cities formatted for select options
   */
  async getCitySelectOptions(): Promise<Array<{
    value: string
    label: string
    plate_code: string
  }>> {
    const cities = await this.getCities()
    if (!Array.isArray(cities)) {
      return []
    }
    return cities
      .filter(city => city && (city.is_active !== false))
      .sort((a, b) => a.name.localeCompare(b.name, 'tr'))
      .map(city => ({
        value: city.uuid,
        label: city.name,
        plate_code: city.plate_code
      }))
  }

  /**
   * Get districts formatted for select options
   */
  async getDistrictSelectOptions(cityUuid: string): Promise<Array<{
    value: string
    label: string
  }>> {
    const districts = await this.getDistrictsByCity(cityUuid)
    if (!Array.isArray(districts)) {
      return []
    }
    return districts
      .filter(district => district && (district.is_active !== false))
      .sort((a, b) => a.name.localeCompare(b.name, 'tr'))
      .map(district => ({
        value: district.uuid,
        label: district.name
      }))
  }

  /**
   * Get popular cities (cities with most listings)
   */
  async getPopularCities(limit = 10): Promise<City[]> {
    const cities = await this.getCities()
    if (!Array.isArray(cities)) {
      return []
    }
    return cities
      .filter(city => city && (city.is_active !== false) && (city.listings_count || 0) > 0)
      .sort((a, b) => (b.listings_count || 0) - (a.listings_count || 0))
      .slice(0, limit)
  }

  /**
   * Get location breadcrumb
   */
  async getLocationBreadcrumb(cityUuid: string, districtUuid?: string): Promise<Array<{
    uuid: string
    name: string
    type: 'city' | 'district'
  }>> {
    const breadcrumb: Array<{
      uuid: string
      name: string
      type: 'city' | 'district'
    }> = []

    // Get city
    const city = await this.getCityById(cityUuid)
    breadcrumb.push({
      uuid: city.uuid,
      name: city.name,
      type: 'city'
    })

    // Get district if provided
    if (districtUuid && city.districts) {
      const district = city.districts.find(d => d.uuid === districtUuid)
      if (district) {
        breadcrumb.push({
          uuid: district.uuid,
          name: district.name,
          type: 'district'
        })
      }
    }

    return breadcrumb
  }

  /**
   * Validate city and district combination
   */
  async validateLocation(cityUuid: string, districtUuid?: string): Promise<boolean> {
    try {
      const city = await this.getCityById(cityUuid)
      
      if (!districtUuid) {
        return true
      }

      if (!city.districts) {
        return false
      }

      return city.districts.some(district => district.uuid === districtUuid)
    } catch (error) {
      return false
    }
  }
}