<template>
  <div class="relative inline-block text-left" ref="dropdownRef">
    <slot />
  </div>
</template>

<script setup lang="ts">
const dropdownRef = ref<HTMLElement>()
const isOpen = ref(false)

const toggleDropdown = () => {
  isOpen.value = !isOpen.value
}

const closeDropdown = () => {
  isOpen.value = false
}

// Provide dropdown context for both trigger and content
provide('dropdownTrigger', {
  isOpen: readonly(isOpen),
  toggle: toggleDropdown,
  close: closeDropdown
})

provide('dropdown', {
  close: closeDropdown
})

// Close on click outside
onMounted(() => {
  const handleClickOutside = (event: MouseEvent) => {
    const target = event.target as HTMLElement
    if (!target.closest('.dropdown-content') && !target.closest('[data-dropdown-trigger]')) {
      isOpen.value = false
    }
  }
  
  document.addEventListener('click', handleClickOutside)
  
  onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside)
  })
})
</script>