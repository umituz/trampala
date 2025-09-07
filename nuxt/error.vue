<template>
  <div class="min-h-screen bg-gray-50 flex items-center justify-center">
    <div class="max-w-md w-full text-center px-4">
      <div class="mb-8">
        <div class="mx-auto h-24 w-24 bg-red-100 rounded-full flex items-center justify-center mb-6">
          <svg class="h-12 w-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        
        <h1 class="text-4xl font-bold text-gray-900 mb-2">{{ errorTitle }}</h1>
        <p class="text-gray-600 mb-6">{{ errorDescription }}</p>
      </div>
      
      <div class="space-y-4">
        <button
          @click="handleError"
          class="w-full bg-primary-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-primary-700 transition-colors duration-200"
        >
          {{ primaryActionText }}
        </button>
        
        <NuxtLink
          to="/"
          class="block w-full bg-gray-100 text-gray-700 px-6 py-3 rounded-lg font-medium hover:bg-gray-200 transition-colors duration-200"
        >
          Go to Homepage
        </NuxtLink>
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  error: Object
})

const errorTitle = computed(() => {
  if (props.error?.statusCode === 404) {
    return 'Page Not Found'
  } else if (props.error?.statusCode === 500) {
    return 'Server Error'
  } else if (props.error?.statusCode === 403) {
    return 'Access Forbidden'
  } else if (props.error?.statusCode === 401) {
    return 'Unauthorized'
  } else {
    return 'Something went wrong'
  }
})

const errorDescription = computed(() => {
  if (props.error?.statusCode === 404) {
    return "The page you're looking for doesn't exist or has been moved."
  } else if (props.error?.statusCode === 500) {
    return "We're experiencing technical difficulties. Please try again later."
  } else if (props.error?.statusCode === 403) {
    return "You don't have permission to access this resource."
  } else if (props.error?.statusCode === 401) {
    return "You need to be logged in to access this page."
  } else {
    return props.error?.message || "An unexpected error occurred. Please try again."
  }
})

const primaryActionText = computed(() => {
  if (props.error?.statusCode === 404) {
    return 'Go Back'
  } else if (props.error?.statusCode === 500) {
    return 'Retry'
  } else if (props.error?.statusCode === 403 || props.error?.statusCode === 401) {
    return 'Login'
  } else {
    return 'Try Again'
  }
})

const handleError = () => {
  if (props.error?.statusCode === 404) {
    window.history.back()
  } else if (props.error?.statusCode === 500) {
    window.location.reload()
  } else if (props.error?.statusCode === 403 || props.error?.statusCode === 401) {
    navigateTo('/auth/login')
  } else {
    window.location.reload()
  }
}

// Set page title
useHead({
  title: `${errorTitle.value} - Trampala`
})
</script>