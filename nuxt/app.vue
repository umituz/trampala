<template>
  <NuxtLayout>
    <NuxtPage />
  </NuxtLayout>
  <UiToastContainer />
</template>

<script setup>
// Explicitly import the ToastContainer component
import UiToastContainer from '~/components/UI/ToastContainer.vue'

// Global error handling
const { handleApiError } = useErrorHandler()
const nuxtApp = useNuxtApp()

// Handle global errors - only log, don't show toasts for everything
const handleError = (error, instance, info) => {
  console.error('Global error:', error, info)
  // Don't show toast notifications for all errors - handle them specifically where needed
}

// Handle Vue errors
nuxtApp.vueApp.config.errorHandler = handleError
</script>