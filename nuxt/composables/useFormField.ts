/**
 * Composable for common form field functionality
 */
export const useFormField = () => {
  /**
   * Generate a unique field ID
   */
  const generateFieldId = (prefix = 'field') => {
    return `${prefix}-${Math.random().toString(36).substr(2, 9)}`
  }

  /**
   * Common size classes for form elements
   */
  const getSizeClasses = (size: 'sm' | 'md' | 'lg' = 'md') => {
    const sizeClasses = {
      sm: 'px-3 py-1.5 text-sm',
      md: 'px-4 py-2 text-base',
      lg: 'px-6 py-3 text-lg'
    }
    return sizeClasses[size]
  }

  /**
   * Common base classes for form inputs/selects
   */
  const getBaseFieldClasses = () => {
    return 'block w-full border rounded-lg focus:ring-2 focus:ring-offset-2 focus:outline-none transition-colors duration-200'
  }

  /**
   * Get state-dependent classes (error/normal states)
   */
  const getStateClasses = (hasError: boolean) => {
    return hasError
      ? 'border-red-300 text-red-900 placeholder-red-300 focus:ring-red-500 focus:border-red-500'
      : 'border-gray-300 text-gray-900 placeholder-gray-400 focus:ring-primary-500 focus:border-primary-500'
  }

  /**
   * Get disabled/readonly classes
   */
  const getDisabledClasses = (isDisabled: boolean, isReadonly: boolean = false) => {
    return isDisabled || isReadonly
      ? 'bg-gray-50 cursor-not-allowed'
      : 'bg-white'
  }

  /**
   * Combine all classes for form fields
   */
  const getFormFieldClasses = (
    size: 'sm' | 'md' | 'lg' = 'md',
    hasError: boolean = false,
    isDisabled: boolean = false,
    isReadonly: boolean = false,
    additionalClasses: string = ''
  ) => {
    return [
      getBaseFieldClasses(),
      getSizeClasses(size),
      getStateClasses(hasError),
      getDisabledClasses(isDisabled, isReadonly),
      additionalClasses
    ].filter(Boolean).join(' ')
  }

  return {
    generateFieldId,
    getSizeClasses,
    getBaseFieldClasses,
    getStateClasses,
    getDisabledClasses,
    getFormFieldClasses
  }
}