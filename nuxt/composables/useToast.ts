interface Toast {
  id: string
  title: string
  message: string
  type: 'success' | 'error' | 'warning' | 'info'
  duration?: number
  persistent?: boolean
}

interface ToastState {
  toasts: Toast[]
}

const state = reactive<ToastState>({
  toasts: []
})

let toastIdCounter = 0

export const useToast = () => {
  const generateId = (): string => {
    return `toast-${++toastIdCounter}-${Date.now()}`
  }
  
  const addToast = (toast: Omit<Toast, 'id'>): string => {
    const id = generateId()
    const newToast: Toast = {
      ...toast,
      id,
      duration: toast.duration ?? (toast.type === 'error' ? 8000 : 5000)
    }
    
    state.toasts.push(newToast)
    
    // Auto remove toast after duration (if not persistent)
    if (!newToast.persistent && newToast.duration) {
      setTimeout(() => {
        removeToast(id)
      }, newToast.duration)
    }
    
    return id
  }
  
  const removeToast = (id: string) => {
    const index = state.toasts.findIndex(toast => toast.id === id)
    if (index > -1) {
      state.toasts.splice(index, 1)
    }
  }
  
  const clearAll = () => {
    state.toasts.length = 0
  }
  
  // Convenience methods
  const success = (title: string, message?: string, options?: Partial<Toast>) => {
    return addToast({
      title,
      message: message || '',
      type: 'success',
      ...options
    })
  }
  
  const error = (title: string, message?: string, options?: Partial<Toast>) => {
    return addToast({
      title,
      message: message || '',
      type: 'error',
      ...options
    })
  }
  
  const warning = (title: string, message?: string, options?: Partial<Toast>) => {
    return addToast({
      title,
      message: message || '',
      type: 'warning',
      ...options
    })
  }
  
  const info = (title: string, message?: string, options?: Partial<Toast>) => {
    return addToast({
      title,
      message: message || '',
      type: 'info',
      ...options
    })
  }
  
  return {
    toasts: readonly(state.toasts),
    addToast,
    removeToast,
    clearAll,
    success,
    error,
    warning,
    info
  }
}