<template>
  <label
    :for="htmlFor"
    :class="labelClasses"
  >
    <slot />
  </label>
</template>

<script setup lang="ts">
interface Props {
  htmlFor?: string
  size?: 'sm' | 'md' | 'lg'
  required?: boolean
  disabled?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  size: 'md',
  required: false,
  disabled: false
})

const labelClasses = computed(() => {
  const baseClasses = 'block font-medium text-gray-700'
  
  const sizeClasses = {
    sm: 'text-xs',
    md: 'text-sm',
    lg: 'text-base'
  }
  
  const disabledClass = props.disabled ? 'text-gray-400 cursor-not-allowed' : 'cursor-pointer'
  
  return [
    baseClasses,
    sizeClasses[props.size],
    disabledClass
  ].filter(Boolean).join(' ')
})
</script>