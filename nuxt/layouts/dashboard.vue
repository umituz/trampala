<template>
  <div class="min-h-screen bg-gray-50">
    <div class="flex h-screen">
      <!-- Mobile menu overlay -->
      <div 
        v-if="isMobileOpen"
        class="fixed inset-0 bg-black/30 backdrop-blur-sm z-40 md:hidden transition-all duration-300"
        @click="isMobileOpen = false"
      />

      <!-- Sidebar -->
      <aside 
        :class="[
          'fixed left-0 top-0 z-50 h-full transform transition-all duration-300 ease-out md:relative md:translate-x-0 w-64',
          isMobileOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'
        ]"
      >
        <DashboardSidebar />
      </aside>

      <!-- Main content area -->
      <div class="flex flex-1 flex-col overflow-hidden">
        <!-- Enhanced Header -->
        <DashboardHeader />
        
        <!-- Page content with better spacing -->
        <main class="flex-1 overflow-y-auto bg-gray-50">
          <div class="px-4 py-6 md:px-6 md:py-8 lg:px-8">
            <slot />
          </div>
        </main>
      </div>
    </div>

  </div>
</template>

<script setup lang="ts">
// Dashboard layout for both admin and user panels

// Simple mobile menu state
const isMobileOpen = ref(false)

// Close mobile menu on route change
const route = useRoute()
watch(() => route.path, () => {
  isMobileOpen.value = false
})

// Provide mobile state to child components if needed
provide('mobileMenuState', {
  isOpen: readonly(isMobileOpen),
  toggle: () => { isMobileOpen.value = !isMobileOpen.value },
  close: () => { isMobileOpen.value = false }
})
</script>

<style>
/* Custom scrollbar for better UX */
::-webkit-scrollbar {
  width: 6px;
}

::-webkit-scrollbar-track {
  background: #f1f5f9;
}

::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}

/* Smooth transitions for layout changes */
.dashboard-layout {
  transition: margin-left 0.3s ease-out;
}
</style>