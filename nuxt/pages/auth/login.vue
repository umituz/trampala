<template>
  <div class="min-h-screen flex">
    <!-- Left side - Login Form -->
    <div class="flex-1 flex items-center justify-center p-8 bg-white">
      <div class="w-full max-w-md space-y-6">
        <!-- Logo -->
        <div class="text-center mb-8">
          <NuxtLink to="/" class="inline-flex items-center space-x-2">
            <div class="w-10 h-10 gradient-purple rounded-lg flex items-center justify-center">
              <span class="text-white font-bold text-lg">T</span>
            </div>
            <span class="text-2xl font-bold text-trampala-purple">Trampala</span>
          </NuxtLink>
        </div>

        <!-- Welcome Text -->
        <div class="text-center space-y-2">
          <h1 class="text-2xl font-bold text-gray-900">Welcome to Trampala</h1>
          <p class="text-gray-600">
            Sign in to access trading opportunities and more
          </p>
        </div>

        <!-- Login Form -->
        <form @submit.prevent="handleLogin" class="space-y-4">
          <div class="space-y-2">
            <UiLabel htmlFor="email" class="text-sm font-medium">
              Email address <span class="text-red-500">*</span>
            </UiLabel>
            <BaseInput
              id="email"
              v-model="form.email"
              type="email"
              placeholder="example@gmail.com"
              required
              class="w-full"
              :error="error"
            />
          </div>

          <div class="space-y-2">
            <UiLabel htmlFor="password" class="text-sm font-medium">
              Password <span class="text-red-500">*</span>
            </UiLabel>
            <BaseInput
              id="password"
              v-model="form.password"
              type="password"
              placeholder="••••••"
              required
              class="w-full"
              :error="error"
            />
          </div>


          <BaseButton 
            type="submit" 
            :loading="loading"
            variant="primary"
            size="md"
            block
            class="gradient-purple text-white hover:opacity-90 transition-opacity shadow-lg"
          >
            <span v-if="loading">Signing in...</span>
            <span v-else>Sign in →</span>
          </BaseButton>
        </form>

        <!-- Register Link -->
        <div class="text-center">
          <p class="text-sm text-gray-600">
            Don't have an account yet?
            <NuxtLink to="/auth/register" class="text-trampala-purple hover:text-trampala-purple-dark font-medium ml-1">
              Sign up
            </NuxtLink>
          </p>
        </div>

        <!-- Test Accounts -->
        <div class="bg-gray-50 rounded-lg p-4 text-xs text-gray-600">
          <div class="font-medium mb-2 text-center">Test Accounts:</div>
          <div class="grid grid-cols-1 gap-2">
            <div>
              <span class="font-medium">Admin:</span> admin@test.com | admin123
            </div>
            <div>
              <span class="font-medium">User:</span> user@test.com | user123
            </div>
          </div>
        </div>

        <!-- Error Display -->
        <div v-if="error" class="rounded-md bg-red-50 p-4">
          <div class="flex">
            <div class="ml-3">
              <h3 class="text-sm font-medium text-red-800">
                Sign in failed
              </h3>
              <div class="mt-2 text-sm text-red-700">
                <p>{{ error }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Right side - Welcome Content -->
    <div class="hidden lg:flex flex-1 bg-gradient-to-br from-trampala-purple via-purple-600 to-indigo-700 items-center justify-center relative overflow-hidden">
      <!-- Background Pattern -->
      <div class="absolute inset-0 opacity-10">
        <svg class="w-full h-full" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
          <defs>
            <pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse">
              <path d="M 10 0 L 0 0 0 10" fill="none" stroke="white" stroke-width="0.5"/>
            </pattern>
          </defs>
          <rect width="100" height="100" fill="url(#grid)" />
        </svg>
      </div>

      <!-- Floating Shapes -->
      <div class="absolute top-20 left-20 w-24 h-24 bg-white/10 rounded-2xl transform rotate-12 animate-pulse"></div>
      <div class="absolute bottom-32 right-16 w-16 h-16 bg-white/10 rounded-full animate-bounce" style="animation-delay: 0.5s;"></div>
      <div class="absolute top-1/2 left-8 w-12 h-12 bg-white/10 rounded-lg transform -rotate-45 animate-pulse" style="animation-delay: 1s;"></div>
      
      <div class="relative z-10 text-center text-white p-12 max-w-lg">
        <!-- Main Icon -->
        <div class="mb-8 flex justify-center">
          <div class="relative">
            <div class="w-24 h-24 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm">
              <!-- Exchange/Trading Icon -->
              <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
              </svg>
            </div>
            <div class="absolute -top-2 -right-2 w-6 h-6 bg-green-400 rounded-full flex items-center justify-center">
              <span class="text-xs">💰</span>
            </div>
          </div>
        </div>

        <h2 class="text-4xl font-bold mb-3">Welcome Back!</h2>
        <h3 class="text-xl font-medium mb-6 opacity-90">Continue Your Trading Journey</h3>
        
        <!-- Features -->
        <div class="space-y-4 mb-8">
          <div class="flex items-center justify-center space-x-3">
            <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
              </svg>
            </div>
            <span class="text-sm">Secure Trading System</span>
          </div>
          <div class="flex items-center justify-center space-x-3">
            <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <span class="text-sm">Verified Listings</span>
          </div>
          <div class="flex items-center justify-center space-x-3">
            <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM3 4h1.5l.9 2H18a1 1 0 01.9 1.4L17.7 10H6.9l-.9-2H4V4z"/>
              </svg>
            </div>
            <span class="text-sm">Thousands of Products</span>
          </div>
        </div>

        <!-- Progress Indicator -->
        <div class="flex justify-center space-x-2">
          <div class="w-2 h-2 bg-white rounded-full"></div>
          <div class="w-6 h-2 bg-white/40 rounded-full"></div>
          <div class="w-2 h-2 bg-white/40 rounded-full"></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
const { getSession } = useAuth()
const router = useRouter()

// Use auth layout (no header/footer)
definePageMeta({
  layout: 'auth'
})

// SEO
useHead({
  title: 'Sign In - Trampala',
  meta: [
    { name: 'description', content: 'Sign in to your Trampala account' }
  ]
})

// Form data
const form = reactive({
  email: 'admin@test.com',
  password: 'admin123'
})

// State
const loading = ref(false)
const error = ref(null)

// Handle login
const handleLogin = async () => {
  if (loading.value) return
  
  loading.value = true
  error.value = null

  try {
    const config = useRuntimeConfig()
    const response = await $fetch('/login', {
      method: 'POST',
      baseURL: config.public.apiBaseUrl,
      body: {
        email: form.email,
        password: form.password
      }
    })

    
    if (response && response.success && response.data) {
      
      // Session'a user bilgisini kaydet - role'u roles array'den al
      const userRole = response.data.user.roles && response.data.user.roles.length > 0 
        ? response.data.user.roles[0].toLowerCase() 
        : response.data.user.role || 'student'
      
      const sessionData = {
        user: {
          id: response.data.user.uuid,
          name: response.data.user.name,
          email: response.data.user.email,
          role: userRole
        },
        accessToken: response.data.token
      }
      
      
      // localStorage'a kaydet
      if (process.client) {
        localStorage.setItem('auth-session', JSON.stringify(sessionData))
        localStorage.setItem('auth-token', response.data.token) // Axios için ayrı token
      }
      
      
      // Role-based redirect
      const { getDashboardRoute } = await import('~/utils/auth')
      const dashboardRoute = getDashboardRoute()
      
      await router.push(dashboardRoute)
    } else {
      error.value = 'Invalid credentials. Please check your email and password.'
    }
  } catch (err) {
    error.value = 'Invalid credentials. Please check your email and password.'
  } finally {
    loading.value = false
  }
}

// Redirect if already authenticated
onMounted(async () => {
  const session = await getSession()
  if (session) {
    await router.push('/dashboard')
  }
})
</script>