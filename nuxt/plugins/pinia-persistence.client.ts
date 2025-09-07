export default defineNuxtPlugin(() => {
  // Simple persistence helper for Pinia stores
  if (process.client) {
    window.addEventListener('beforeunload', () => {
      // Persistence is handled by individual stores
    })
  }
})