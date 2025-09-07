// Navigation utilities for active route detection

/**
 * Check if a route is active with precise matching logic
 * This utility prevents multiple active links in sidebar navigation
 */
export const isActiveRoute = (href: string, currentPath: string): boolean => {
  // Normalize paths by removing trailing slashes
  const normalizedHref = href.replace(/\/$/, '') || '/'
  const normalizedCurrentPath = currentPath.replace(/\/$/, '') || '/'
  
  // For exact root paths like /admin or /dashboard
  if (normalizedHref === '/admin' || normalizedHref === '/dashboard') {
    return normalizedCurrentPath === normalizedHref
  }
  
  // For exact matches
  if (normalizedCurrentPath === normalizedHref) {
    return true
  }
  
  // For nested routes - but avoid partial matches
  // We need to check if current path is a child of href, but not a sibling
  if (normalizedCurrentPath.startsWith(normalizedHref + '/')) {
    // Additional check: make sure we're not matching siblings
    // For example: /dashboard/listings should NOT match /dashboard/listings/create
    const remainingPath = normalizedCurrentPath.slice(normalizedHref.length + 1)
    
    // If there's more path after removing the href, check if it's a direct child
    // This prevents /dashboard/listings from being active when on /dashboard/listings/create
    const pathSegments = remainingPath.split('/')
    
    // If href ends with a specific action (like /create, /edit), it should only match exactly
    if (normalizedHref.endsWith('/create') || normalizedHref.endsWith('/edit') || normalizedHref.includes('/create/') || normalizedHref.includes('/edit/')) {
      return false
    }
    
    // For parent routes, only match if we're on a direct child page
    // But exclude cases where the child is also a parent route
    if (pathSegments.length === 1) {
      // Single segment child (e.g., /dashboard/listings/123)
      return !isNaN(Number(pathSegments[0])) || !['create', 'edit'].includes(pathSegments[0])
    }
    
    // Multiple segments - likely a nested route, don't match parent
    return false
  }
  
  return false
}

/**
 * Enhanced active route checker specifically for dashboard navigation
 */
export const isDashboardRouteActive = (href: string, currentPath: string): boolean => {
  const normalizedHref = href.replace(/\/$/, '') || '/'
  const normalizedCurrentPath = currentPath.replace(/\/$/, '') || '/'
  
  // Exact match first
  if (normalizedCurrentPath === normalizedHref) {
    return true
  }
  
  // Special cases to prevent conflicts
  const specialCases: Record<string, boolean> = {
    // When on /dashboard/listings/create, only create listing should be active
    [`${normalizedCurrentPath}:${normalizedHref}`]: false
  }
  
  // Prevent /dashboard/listings from being active when on /dashboard/listings/create
  if (normalizedCurrentPath === '/dashboard/listings/create' && normalizedHref === '/dashboard/listings') {
    return false
  }
  
  // Prevent /admin/categories from being active when on /admin/categories/create
  if (normalizedCurrentPath === '/admin/categories/create' && normalizedHref === '/admin/categories') {
    return false
  }
  
  // For root paths like /admin or /dashboard, only match exactly
  if (normalizedHref === '/admin' || normalizedHref === '/dashboard') {
    return normalizedCurrentPath === normalizedHref
  }
  
  // For child routes with IDs (like /admin/users/123)
  if (normalizedCurrentPath.startsWith(normalizedHref + '/')) {
    const remainingPath = normalizedCurrentPath.slice(normalizedHref.length + 1)
    const segments = remainingPath.split('/')
    
    // Allow single segment that's likely an ID (number or UUID)
    if (segments.length === 1 && (segments[0].match(/^\d+$/) || segments[0].match(/^[0-9a-f-]{36}$/))) {
      return true
    }
    
    // Don't match for create/edit routes
    return false
  }
  
  return false
}