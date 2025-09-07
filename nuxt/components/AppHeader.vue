<template>
  <header class="w-full bg-white border-b border-gray-100 sticky top-0 z-50 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-14">
        <!-- Logo -->
        <NuxtLink to="/" class="flex items-center space-x-2">
          <div class="w-7 h-7 gradient-purple rounded-md flex items-center justify-center">
            <span class="text-white font-bold text-sm">T</span>
          </div>
          <span class="text-xl font-bold text-gray-900">Trampala</span>
        </NuxtLink>

        <!-- Right Side -->
        <div class="flex items-center space-x-3">
          <!-- Authentication Controls -->
          <div v-if="authData.authUser.value" class="flex items-center space-x-2">
            <!-- User Profile -->
            <UiDropdownMenu>
              <UiDropdownMenuTrigger as-child>
                <BaseButton variant="ghost" size="sm" class="flex items-center px-2 py-1.5">
                  <UserAvatar 
                    :user-name="authData.authUser.value?.name" 
                    :show-name="true" 
                    size="sm"
                    class="hidden sm:flex"
                  />
                  <!-- Mobile - Avatar only -->
                  <UserAvatar 
                    :user-name="authData.authUser.value?.name" 
                    :show-name="false" 
                    size="sm"
                    class="sm:hidden"
                  />
                </BaseButton>
              </UiDropdownMenuTrigger>
              <UiDropdownMenuContent align="end" class="w-48 bg-white border shadow-md mt-1">
                <UiDropdownMenuItem as-child>
                  <NuxtLink to="/dashboard" class="flex items-center px-3 py-2 text-sm">
                    <Icon name="lucide:layout-dashboard" class="w-4 h-4 mr-2 text-gray-400" />
                    Dashboard
                  </NuxtLink>
                </UiDropdownMenuItem>
                <div class="border-t border-gray-100 my-1"></div>
                <UiDropdownMenuItem @click="handleLogout" class="flex items-center px-3 py-2 text-sm text-red-600 hover:bg-red-50 cursor-pointer">
                  <Icon name="lucide:log-out" class="w-4 h-4 mr-2" />
                  Sign Out
                </UiDropdownMenuItem>
              </UiDropdownMenuContent>
            </UiDropdownMenu>
          </div>

          <!-- Login/Register for non-authenticated users -->
          <div v-else class="flex items-center space-x-2">
            <NuxtLink to="/auth/login">
              <BaseButton variant="ghost" size="sm" class="text-gray-600 hover:text-trampala-purple text-sm px-3 py-1.5">
                Sign In
              </BaseButton>
            </NuxtLink>
            <NuxtLink to="/auth/register">
              <BaseButton size="sm" class="gradient-purple text-white px-3 py-1.5 text-sm font-medium">
                Sign Up
              </BaseButton>
            </NuxtLink>
          </div>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup lang="ts">
const router = useRouter()

// Use unified auth composable
const authData = useAuthData()

const handleLogout = async () => {
  authData.clearSession()
  await router.push('/auth/login')
}

// Initialize auth on mount
onMounted(() => {
  authData.initAuth()
})
</script>