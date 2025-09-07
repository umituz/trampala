export const useListingStatus = () => {
  const getStatusText = (status: string): string => {
    const statusMap: Record<string, string> = {
      pending: 'Pending Review',
      approved: 'Approved',
      rejected: 'Rejected'
    }
    return statusMap[status] || status
  }

  const getStatusBadgeClasses = (status: string): string => {
    const baseClasses = 'px-3 py-1 text-sm font-medium rounded-full'
    const statusClasses: Record<string, string> = {
      pending: 'bg-yellow-100 text-yellow-800',
      approved: 'bg-green-100 text-green-800',
      rejected: 'bg-red-100 text-red-800'
    }
    
    return `${baseClasses} ${statusClasses[status] || 'bg-gray-100 text-gray-800'}`
  }

  const getStatusTextClasses = (status: string): string => {
    const statusClasses: Record<string, string> = {
      pending: 'text-sm font-medium text-yellow-600',
      approved: 'text-sm font-medium text-green-600',
      rejected: 'text-sm font-medium text-red-600'
    }
    return statusClasses[status] || 'text-sm font-medium text-gray-600'
  }

  const getStatusIcon = (status: string): string => {
    const statusIcons: Record<string, string> = {
      pending: 'heroicons:clock',
      approved: 'heroicons:check-circle',
      rejected: 'heroicons:x-circle'
    }
    return statusIcons[status] || 'heroicons:question-mark-circle'
  }

  const getStatusColor = (status: string): string => {
    const statusColors: Record<string, string> = {
      pending: 'yellow',
      approved: 'green',
      rejected: 'red'
    }
    return statusColors[status] || 'gray'
  }

  return {
    getStatusText,
    getStatusBadgeClasses,
    getStatusTextClasses,
    getStatusIcon,
    getStatusColor
  }
}