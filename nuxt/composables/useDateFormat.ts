export const useDateFormat = () => {
  const formatDate = (date: string | Date | null | undefined): string => {
    if (!date) return 'N/A'
    
    try {
      return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      })
    } catch {
      return 'Invalid date'
    }
  }

  const formatDateShort = (date: string | Date | null | undefined): string => {
    if (!date) return 'N/A'
    
    try {
      return new Date(date).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric'
      })
    } catch {
      return 'Invalid date'
    }
  }

  const formatTime = (date: string | Date | null | undefined): string => {
    if (!date) return 'N/A'
    
    try {
      return new Date(date).toLocaleTimeString('en-US', {
        hour: '2-digit',
        minute: '2-digit'
      })
    } catch {
      return 'Invalid time'
    }
  }

  const formatRelative = (date: string | Date | null | undefined): string => {
    if (!date) return 'N/A'
    
    try {
      const now = new Date()
      const then = new Date(date)
      const diff = now.getTime() - then.getTime()
      
      const seconds = Math.floor(diff / 1000)
      const minutes = Math.floor(seconds / 60)
      const hours = Math.floor(minutes / 60)
      const days = Math.floor(hours / 24)
      const weeks = Math.floor(days / 7)
      const months = Math.floor(days / 30)
      const years = Math.floor(days / 365)
      
      if (seconds < 60) return 'just now'
      if (minutes < 60) return `${minutes} minute${minutes !== 1 ? 's' : ''} ago`
      if (hours < 24) return `${hours} hour${hours !== 1 ? 's' : ''} ago`
      if (days < 7) return `${days} day${days !== 1 ? 's' : ''} ago`
      if (weeks < 4) return `${weeks} week${weeks !== 1 ? 's' : ''} ago`
      if (months < 12) return `${months} month${months !== 1 ? 's' : ''} ago`
      return `${years} year${years !== 1 ? 's' : ''} ago`
    } catch {
      return 'Invalid date'
    }
  }

  const formatDateOnly = (date: string | Date | null | undefined): string => {
    if (!date) return 'N/A'
    
    try {
      return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit'
      })
    } catch {
      return 'Invalid date'
    }
  }

  const formatMonthYear = (date: string | Date | null | undefined): string => {
    if (!date) return 'N/A'
    
    try {
      return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long'
      })
    } catch {
      return 'Invalid date'
    }
  }

  const isToday = (date: string | Date | null | undefined): boolean => {
    if (!date) return false
    
    try {
      const today = new Date()
      const checkDate = new Date(date)
      
      return today.toDateString() === checkDate.toDateString()
    } catch {
      return false
    }
  }

  const isThisWeek = (date: string | Date | null | undefined): boolean => {
    if (!date) return false
    
    try {
      const now = new Date()
      const checkDate = new Date(date)
      const weekAgo = new Date(now.getTime() - 7 * 24 * 60 * 60 * 1000)
      
      return checkDate >= weekAgo && checkDate <= now
    } catch {
      return false
    }
  }

  const isThisMonth = (date: string | Date | null | undefined): boolean => {
    if (!date) return false
    
    try {
      const now = new Date()
      const checkDate = new Date(date)
      
      return now.getMonth() === checkDate.getMonth() && 
             now.getFullYear() === checkDate.getFullYear()
    } catch {
      return false
    }
  }

  return {
    formatDate,
    formatDateShort,
    formatTime,
    formatRelative,
    formatDateOnly,
    formatMonthYear,
    isToday,
    isThisWeek,
    isThisMonth
  }
}