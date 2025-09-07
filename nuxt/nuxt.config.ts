// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2025-07-15',
  devtools: { enabled: true },
  ssr: false,
  
  // Modules
  modules: [
    '@nuxtjs/tailwindcss',
    '@pinia/nuxt',
    '@nuxt/icon',
    '@vueuse/nuxt',
    '@sidebase/nuxt-auth'
  ],

  // Pinia Configuration
  pinia: {
    storesDirs: ['./stores/**']
  },
  
  // CSS
  css: [
    '~/assets/css/main.css'
  ],
  
  // App Configuration
  app: {
    head: {
      title: 'Trampala - Buy & Sell Marketplace',
      meta: [
        { charset: 'utf-8' },
        { name: 'viewport', content: 'width=device-width, initial-scale=1' },
        { name: 'description', content: 'Trampala - Your trusted marketplace for buying and selling items' }
      ]
    }
  },
  
  // Components
  components: [
    {
      path: '~/components',
      pathPrefix: false,
    }
  ],

  // Auth Configuration
  auth: {
    baseURL: process.env.AUTH_ORIGIN,
    provider: {
      type: 'authjs'
    },
    globalAppMiddleware: {
      isEnabled: false
    }
  },

  // Runtime Config
  runtimeConfig: {
    // Auth secret for JWT
    authSecret: process.env.NUXT_AUTH_SECRET || 'your-super-secret-auth-key-here',
    // Private keys (only available on server-side)
    // Public keys (exposed to client-side)
    public: {
      apiBaseUrl: process.env.NUXT_PUBLIC_API_BASE_URL || 'http://localhost:8080/api',
      appName: 'Trampala',
      authUrl: process.env.NUXT_AUTH_ORIGIN || process.env.AUTH_ORIGIN || 'http://localhost:3000'
    }
  },
  
  // Development server
  devServer: {
    port: 3000,
    host: '0.0.0.0'
  }
})
