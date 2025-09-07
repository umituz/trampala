/**
 * Composable for handling common filtering logic
 */
export const useFilters = () => {
  /**
   * Create clean filter parameters, removing empty/undefined values
   */
  const createFilterParams = (filters: Record<string, any>) => {
    const cleanParams: Record<string, any> = {}
    
    Object.entries(filters).forEach(([key, value]) => {
      if (value !== null && value !== undefined && value !== '') {
        if (typeof value === 'string' && value.trim() === '') {
          return // Skip empty strings
        }
        cleanParams[key] = value
      }
    })
    
    return cleanParams
  }

  /**
   * Reset pagination when filters change
   */
  const resetPagination = (filters: { page: number }) => {
    filters.page = 1
  }

  return {
    createFilterParams,
    resetPagination
  }
}