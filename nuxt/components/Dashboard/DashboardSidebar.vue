<template>
  <div class="flex flex-col bg-white border-r border-gray-200 h-full w-64">
    <!-- Logo/Brand -->
    <header class="flex items-center p-4 border-b border-gray-200 min-h-[65px]">
      <div class="flex items-center space-x-2">
        <div class="h-8 w-8 rounded-lg bg-gradient-to-br from-trampala-purple to-purple-700 flex items-center justify-center shadow-sm">
          <span class="text-white font-bold text-lg">T</span>
        </div>
        <div>
          <h2 class="text-lg font-bold bg-gradient-to-r from-trampala-purple to-purple-700 bg-clip-text text-transparent">
            {{ userStore.data.value?.user.role?.name === 'admin' ? 'Admin Panel' : 'Dashboard' }}
          </h2>
        </div>
      </div>
    </header>

    <!-- Back to site link -->
    <div class="p-3 border-b border-gray-200">
      <NuxtLink to="/" class="block">
        <BaseButton 
          variant="ghost" 
          class="w-full text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-all justify-start px-3"
        >
          <Icon name="lucide:home" class="h-4 w-4 shrink-0" />
          <span class="ml-3">Back to Site</span>
        </BaseButton>
      </NuxtLink>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto py-4">
      <div class="space-y-1 px-3">
        <!-- Main Navigation -->
        <div class="space-y-1">
          <template v-for="item in navigation" :key="item.href">
            <NuxtLink 
              :to="item.href" 
              class="block"
              @click="handleMobileClose"
            >
              <div
                :class="[
                  'relative w-full transition-all duration-200 group/menu-item flex items-center rounded-lg cursor-pointer justify-start px-3 h-10',
                  isActiveRoute(item.href) 
                    ? 'bg-gradient-to-r from-trampala-purple/10 to-purple-500/5 text-trampala-purple font-medium' 
                    : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'
                ]"
              >
                <!-- Active indicator bar -->
                <div 
                  v-if="isActiveRoute(item.href)"
                  class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-trampala-purple rounded-r-full"
                />
                <Icon 
                  :name="item.icon" 
                  :class="[
                    'h-4 w-4 shrink-0 transition-transform',
                    isActiveRoute(item.href) ? 'text-trampala-purple' : ''
                  ]" 
                />
                <span class="ml-3 flex-1 text-left text-sm font-medium">
                  {{ item.title }}
                </span>
                <span 
                  v-if="item.badge" 
                  class="ml-auto text-xs bg-trampala-purple/10 text-trampala-purple px-2 py-1 rounded-full font-medium"
                >
                  {{ item.badge }}
                </span>
              </div>
            </NuxtLink>
          </template>
        </div>

      </div>
    </nav>

    <!-- Logout -->
    <footer class="p-4 border-t border-gray-200">
      <BaseButton
        variant="ghost"
        class="w-full text-red-600 hover:text-red-700 hover:bg-red-50 transition-all justify-start px-3"
        @click="handleLogout"
      >
        <Icon name="lucide:log-out" class="h-4 w-4 shrink-0" />
        <span class="ml-3 text-sm font-medium">Sign Out</span>
      </BaseButton>
    </footer>
  </div>
</template>

<script setup lang="ts">
interface NavItem {
  title: string
  href: string
  icon: string
  badge?: string
  adminOnly?: boolean
}

const route = useRoute()
const router = useRouter()
const userStore = useAuth()

// Import auth utilities
const { getNavigationItems } = await import('~/utils/auth')

// Navigation items from auth utilities
const navigation = computed((): NavItem[] => {
  return getNavigationItems()
})

// Import navigation utilities
const { isDashboardRouteActive } = await import('~/utils/navigation')

// Check if route is active using enhanced utility
const isActiveRoute = (href: string): boolean => {
  return isDashboardRouteActive(href, route.path)
}

// Handle mobile menu close - for mobile overlay (will be handled in layout)
const handleMobileClose = () => {
  // Mobile close logic will be handled in dashboard layout
}

// Handle logout
const handleLogout = async () => {
  try {
    // Clear localStorage
    if (process.client) {
      localStorage.removeItem('auth-session')
      localStorage.removeItem('auth-token')
    }
    await router.push('/auth/login')
  } catch (error) {
    console.error('Logout error:', error)
  }
}
</script>