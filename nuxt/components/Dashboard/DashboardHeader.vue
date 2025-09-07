<template>
  <header class="bg-white border-b border-gray-200 px-4 py-3">
    <div class="flex items-center justify-between">
      <!-- Mobile menu button -->
      <div class="flex items-center space-x-4">
        <BaseButton
          variant="ghost"
          size="sm"
          class="md:hidden p-2"
          @click="mobileMenuState?.toggle()"
        >
          <Icon name="lucide:menu" class="h-5 w-5" />
        </BaseButton>

        <!-- Page title area -->
        <div>
          <h1 class="text-xl font-semibold text-gray-900">
            {{ pageTitle }}
          </h1>
          <p v-if="pageDescription" class="text-sm text-gray-500 mt-1">
            {{ pageDescription }}
          </p>
        </div>
      </div>

      <!-- Header actions -->
      <div class="flex items-center space-x-4">

        <!-- User menu -->
        <div class="relative">
          <BaseButton
            variant="ghost"
            size="sm"
            class="flex items-center space-x-2 p-2"
            @click="showUserMenu = !showUserMenu"
          >
            <UserAvatar 
              :user-name="authData.authUser.value?.name" 
              :show-name="true" 
              size="md"
            />
            <Icon name="lucide:chevron-down" class="h-4 w-4 text-gray-400" />
          </BaseButton>

          <!-- User dropdown menu -->
          <div 
            v-if="showUserMenu"
            class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-50"
          >
            <div class="p-2">
              <BaseButton 
                variant="ghost" 
                size="sm" 
                class="w-full justify-start text-red-600 hover:text-red-700"
                @click="handleLogout"
              >
                <Icon name="lucide:log-out" class="h-4 w-4 mr-2" />
                Sign Out
              </BaseButton>
            </div>
          </div>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup lang="ts">

const route = useRoute()
const router = useRouter()
const authData = useAuthData()

// Get mobile menu state from parent layout
const mobileMenuState = inject('mobileMenuState', null)

const showUserMenu = ref(false)

// Page title based on current route
const pageTitle = computed(() => {
  const routeName = route.name as string
  const pathSegments = route.path.split('/').filter(Boolean)
  
  if (pathSegments.length === 1) return 'Dashboard'
  
  const titleMap: Record<string, string> = {
    'dashboard-listings': 'My Listings',
    'dashboard-listings-create': 'Create Listing',
    'dashboard-listings-id': 'Edit Listing',
    'dashboard-admin-listings': 'All Listings',
    'dashboard-admin-users': 'Users',
    'dashboard-admin-categories': 'Categories',
    'dashboard-admin-analytics': 'Analytics'
  }
  
  return titleMap[routeName] || 'Dashboard'
})

// Page description based on current route
const pageDescription = computed(() => {
  const routeName = route.name as string
  
  const descriptionMap: Record<string, string> = {
    'dashboard': 'Welcome to your dashboard',
    'dashboard-listings': 'Manage your listings',
    'dashboard-listings-create': 'Create a new listing',
    'dashboard-admin-listings': 'Manage all platform listings',
    'dashboard-admin-users': 'Manage platform users',
    'dashboard-admin-categories': 'Manage listing categories',
    'dashboard-admin-analytics': 'View platform analytics'
  }
  
  return descriptionMap[routeName] || ''
})

// Handle logout
const handleLogout = async () => {
  showUserMenu.value = false
  try {
    authData.clearSession()
    await router.push('/auth/login')
  } catch (error) {
    console.error('Logout error:', error)
  }
}

// Initialize auth and close dropdowns when clicking outside
onMounted(() => {
  // Initialize auth
  authData.initAuth()
  
  // Handle click outside
  const handleClickOutside = (event: MouseEvent) => {
    const target = event.target as HTMLElement
    if (!target.closest('.relative')) {
      showUserMenu.value = false
    }
  }
  
  document.addEventListener('click', handleClickOutside)
  
  onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside)
  })
})

// Close dropdowns on route change
watch(() => route.path, () => {
  showUserMenu.value = false
})
</script>