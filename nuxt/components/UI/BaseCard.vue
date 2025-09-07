<template>
  <div :class="cardClasses">
    <div v-if="$slots.header" class="px-4 py-5 border-b border-gray-200 sm:px-6">
      <slot name="header" />
    </div>
    
    <div :class="bodyClasses">
      <slot />
    </div>
    
    <div v-if="$slots.footer" class="px-4 py-4 border-t border-gray-200 bg-gray-50 sm:px-6">
      <slot name="footer" />
    </div>
  </div>
</template>

<script setup lang="ts">
interface Props {
  shadow?: 'none' | 'sm' | 'md' | 'lg'
  padding?: 'none' | 'sm' | 'md' | 'lg'
  hover?: boolean
  clickable?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  shadow: 'md',
  padding: 'md',
  hover: false,
  clickable: false
})

const emit = defineEmits<{
  click: [event: MouseEvent]
}>()

const handleClick = (event: MouseEvent) => {
  if (props.clickable) {
    emit('click', event)
  }
}

const cardClasses = computed(() => {
  const baseClasses = 'bg-white rounded-lg border border-gray-200 overflow-hidden'
  
  const shadowClasses = {
    none: '',
    sm: 'shadow-sm',
    md: 'shadow-md',
    lg: 'shadow-lg'
  }
  
  const hoverClasses = props.hover ? 'hover:shadow-lg transition-shadow duration-200' : ''
  const clickableClasses = props.clickable ? 'cursor-pointer' : ''
  
  return [
    baseClasses,
    shadowClasses[props.shadow],
    hoverClasses,
    clickableClasses
  ].filter(Boolean).join(' ')
})

const bodyClasses = computed(() => {
  const paddingClasses = {
    none: '',
    sm: 'p-3',
    md: 'p-4',
    lg: 'p-6'
  }
  
  return paddingClasses[props.padding]
})
</script>