// Sidebar state management composable
export const useSidebar = () => {
  // Global sidebar state
  const isMobileOpen = ref(false)
  const isDesktopOpen = ref(true)

  // Toggle mobile sidebar
  const setMobileOpen = (open: boolean) => {
    isMobileOpen.value = open
  }

  // Toggle desktop sidebar
  const setDesktopOpen = (open: boolean) => {
    isDesktopOpen.value = open
  }

  // Close mobile sidebar (used for navigation)
  const closeMobile = () => {
    isMobileOpen.value = false
  }

  // Toggle mobile sidebar
  const toggleMobile = () => {
    isMobileOpen.value = !isMobileOpen.value
  }

  // Toggle desktop sidebar
  const toggleDesktop = () => {
    isDesktopOpen.value = !isDesktopOpen.value
  }

  return {
    isMobileOpen: readonly(isMobileOpen),
    isDesktopOpen: readonly(isDesktopOpen),
    setMobileOpen,
    setDesktopOpen,
    closeMobile,
    toggleMobile,
    toggleDesktop
  }
}