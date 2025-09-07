<template>
  <div class="min-h-screen flex">
    <!-- Left side - Register Form -->
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
          <h1 class="text-2xl font-bold text-gray-900">Hesap oluşturun</h1>
          <p class="text-gray-600">
            Takas dünyasına katılın ve fırsatları keşfedin
          </p>
        </div>

        <!-- Register Form -->
        <form @submit.prevent="handleRegister" class="space-y-4">
          <div class="space-y-2">
            <UiLabel htmlFor="name" class="text-sm font-medium">
              Ad Soyad <span class="text-red-500">*</span>
            </UiLabel>
            <BaseInput
              id="name"
              v-model="form.name"
              type="text"
              placeholder="Adınız Soyadınız"
              required
              class="w-full"
              :error="errors.name"
            />
          </div>

          <div class="space-y-2">
            <UiLabel htmlFor="email" class="text-sm font-medium">
              E-posta adresi <span class="text-red-500">*</span>
            </UiLabel>
            <BaseInput
              id="email"
              v-model="form.email"
              type="email"
              placeholder="example@gmail.com"
              required
              class="w-full"
              :error="errors.email"
            />
          </div>

          <div class="space-y-2">
            <UiLabel htmlFor="password" class="text-sm font-medium">
              Şifre <span class="text-red-500">*</span>
            </UiLabel>
            <BaseInput
              id="password"
              v-model="form.password"
              type="password"
              placeholder="••••••"
              required
              class="w-full"
              :error="errors.password"
            />
          </div>

          <div class="space-y-2">
            <UiLabel htmlFor="password_confirmation" class="text-sm font-medium">
              Şifre Tekrarı <span class="text-red-500">*</span>
            </UiLabel>
            <BaseInput
              id="password_confirmation"
              v-model="form.password_confirmation"
              type="password"
              placeholder="••••••"
              required
              class="w-full"
              :error="errors.password_confirmation"
            />
          </div>

          <div class="flex items-start">
            <UiCheckbox
              id="terms"
              v-model:checked="form.terms_accepted"
            >
              <span class="text-sm">
                <a href="#" class="text-trampala-purple hover:text-trampala-purple-dark font-medium">
                  Kullanım Şartları
                </a>
                ve
                <a href="#" class="text-trampala-purple hover:text-trampala-purple-dark font-medium">
                  Gizlilik Politikası
                </a>
                'nı kabul ediyorum
              </span>
            </UiCheckbox>
          </div>

          <BaseButton 
            type="submit" 
            :disabled="loading || !form.terms_accepted"
            class="w-full gradient-purple text-white hover:opacity-90 transition-opacity shadow-lg"
          >
            <span v-if="loading">Hesap oluşturuluyor...</span>
            <span v-else>Hesap oluştur →</span>
          </BaseButton>

          <BaseButton
            type="button"
            variant="outline"
            @click="handleGoogleRegister"
            class="w-full flex items-center justify-center space-x-2"
          >
            <svg class="w-5 h-5" viewBox="0 0 24 24">
              <path fill="#4285f4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
              <path fill="#34a853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
              <path fill="#fbbc05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
              <path fill="#ea4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
            </svg>
            <span>Google ile kayıt ol</span>
          </BaseButton>
        </form>

        <!-- Login Link -->
        <div class="text-center">
          <p class="text-sm text-gray-600">
            Already have an account?
            <NuxtLink to="/auth/login" class="text-trampala-purple hover:text-trampala-purple-dark font-medium ml-1">
              Sign in
            </NuxtLink>
          </p>
        </div>

        <!-- Error Display -->
        <div v-if="error" class="rounded-md bg-red-50 p-4">
          <div class="flex">
            <div class="ml-3">
              <h3 class="text-sm font-medium text-red-800">
                Kayıt başarısız
              </h3>
              <div class="mt-2 text-sm text-red-700">
                <p>{{ error }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Success Display -->
        <div v-if="success" class="rounded-md bg-green-50 p-4">
          <div class="flex">
            <div class="ml-3">
              <h3 class="text-sm font-medium text-green-800">
                Kayıt başarılı!
              </h3>
              <div class="mt-2 text-sm text-green-700">
                <p>Hesabınız oluşturuldu. Giriş sayfasına yönlendiriliyorsunuz...</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Right side - Join Community Content -->
    <div class="hidden lg:flex flex-1 bg-gradient-to-br from-indigo-600 via-purple-600 to-trampala-purple items-center justify-center relative overflow-hidden">
      <!-- Background Pattern -->
      <div class="absolute inset-0 opacity-10">
        <svg class="w-full h-full" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
          <defs>
            <pattern id="dots" width="20" height="20" patternUnits="userSpaceOnUse">
              <circle cx="10" cy="10" r="1.5" fill="white"/>
            </pattern>
          </defs>
          <rect width="200" height="200" fill="url(#dots)" />
        </svg>
      </div>

      <!-- Floating Elements -->
      <div class="absolute top-16 right-20 w-20 h-20 bg-gradient-to-r from-yellow-400 to-orange-500 rounded-full opacity-20 animate-pulse"></div>
      <div class="absolute bottom-20 left-16 w-16 h-16 bg-gradient-to-r from-green-400 to-blue-500 rounded-2xl opacity-20 animate-bounce transform rotate-12" style="animation-delay: 0.7s;"></div>
      <div class="absolute top-1/3 right-8 w-14 h-14 bg-gradient-to-r from-pink-400 to-purple-500 rounded-full opacity-20 animate-pulse" style="animation-delay: 1.2s;"></div>
      <div class="absolute bottom-1/3 left-8 w-12 h-12 bg-gradient-to-r from-blue-400 to-cyan-500 rounded-lg opacity-20 transform -rotate-12 animate-pulse" style="animation-delay: 0.3s;"></div>
      
      <div class="relative z-10 text-center text-white p-12 max-w-lg">
        <!-- Main Illustration -->
        <div class="mb-8 flex justify-center">
          <div class="relative">
            <!-- Central Hub -->
            <div class="w-28 h-28 bg-white/20 rounded-3xl flex items-center justify-center backdrop-blur-sm relative">
              <!-- Shopping/Marketplace Icon -->
              <svg class="w-14 h-14 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l-1 8H6L5 9z"/>
              </svg>
              
              <!-- Orbiting Elements -->
              <div class="absolute -top-3 -right-3 w-8 h-8 bg-yellow-400 rounded-full flex items-center justify-center animate-spin" style="animation-duration: 3s;">
                <span class="text-xs">🛍️</span>
              </div>
              <div class="absolute -bottom-3 -left-3 w-8 h-8 bg-green-400 rounded-full flex items-center justify-center animate-spin" style="animation-duration: 4s; animation-direction: reverse;">
                <span class="text-xs">🔄</span>
              </div>
            </div>
          </div>
        </div>

        <h2 class="text-4xl font-bold mb-3">Join Our Community</h2>
        <h3 class="text-xl font-medium mb-8 opacity-90">Discover the Trading World</h3>
        
        <!-- Platform Stats -->
        <div class="grid grid-cols-2 gap-6 mb-8">
          <div class="text-center">
            <div class="text-3xl font-bold mb-1">50K+</div>
            <div class="text-sm opacity-80">Active Users</div>
          </div>
          <div class="text-center">
            <div class="text-3xl font-bold mb-1">200K+</div>
            <div class="text-sm opacity-80">Listings</div>
          </div>
          <div class="text-center">
            <div class="text-3xl font-bold mb-1">100+</div>
            <div class="text-sm opacity-80">Categories</div>
          </div>
          <div class="text-center">
            <div class="text-3xl font-bold mb-1">98%</div>
            <div class="text-sm opacity-80">Satisfaction</div>
          </div>
        </div>

        <!-- Call to Action -->
        <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-4 mb-6">
          <p class="text-sm opacity-90">
            Thousands of users are safely trading their items. Join us and find what you're looking for!
          </p>
        </div>

        <!-- Progress Indicator -->
        <div class="flex justify-center space-x-2">
          <div class="w-2 h-2 bg-white/40 rounded-full"></div>
          <div class="w-2 h-2 bg-white rounded-full"></div>
          <div class="w-6 h-2 bg-white/40 rounded-full"></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
const router = useRouter()
const { data } = useAuth()

// Use auth layout (no header/footer)
definePageMeta({
  layout: 'auth'
})

// SEO
useHead({
  title: 'Sign Up - Trampala',
  meta: [
    { name: 'description', content: 'Create your new Trampala account' }
  ]
})

// Form data
const form = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  terms_accepted: false
})

// State
const loading = ref(false)
const error = ref(null)
const success = ref(false)
const errors = reactive({
  name: null,
  email: null,
  password: null,
  password_confirmation: null
})

// Handle registration
const handleRegister = async () => {
  if (loading.value) return
  
  loading.value = true
  error.value = null
  success.value = false
  
  // Reset errors
  Object.keys(errors).forEach(key => {
    errors[key] = null
  })

  // Validate password confirmation
  if (form.password !== form.password_confirmation) {
    errors.password_confirmation = 'Şifreler eşleşmiyor'
    loading.value = false
    return
  }

  // Validate terms acceptance
  if (!form.terms_accepted) {
    error.value = 'Kullanım şartlarını kabul etmelisiniz'
    loading.value = false
    return
  }

  try {
    const config = useRuntimeConfig()
    const response = await $fetch('/register', {
      method: 'POST',
      baseURL: config.public.apiBaseUrl,
      body: {
        name: form.name,
        email: form.email,
        password: form.password,
        password_confirmation: form.password_confirmation
      }
    })

    
    if (response && response.success) {
      success.value = true
      
      // Redirect to login after 2 seconds
      setTimeout(() => {
        router.push('/auth/login')
      }, 2000)
    } else {
      error.value = response?.message || 'Kayıt olurken bir hata oluştu.'
    }
  } catch (err) {
    
    // Handle validation errors
    if (err.status === 422 && err.data?.errors) {
      Object.keys(err.data.errors).forEach(field => {
        if (errors.hasOwnProperty(field)) {
          errors[field] = err.data.errors[field][0]
        }
      })
    } else {
      error.value = err.data?.message || 'Kayıt olurken bir hata oluştu. Lütfen tekrar deneyin.'
    }
  } finally {
    loading.value = false
  }
}

// Handle Google registration
const handleGoogleRegister = () => {
  // TODO: Implement Google registration
  error.value = null
  alert('Google ile kayıt özelliği yakında aktif olacak.')
}

// Redirect if already authenticated
onMounted(() => {
  if (data.value) {
    router.push('/')
  }
})
</script>