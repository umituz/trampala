<template>
  <Teleport to="body">
    <div
      v-if="triggerState?.isOpen.value"
      ref="contentRef"
      :class="[
        'dropdown-content absolute z-50 min-w-32 bg-white rounded-md border border-gray-200 shadow-lg py-1 transition-all duration-200',
        'transform origin-top-right',
        triggerState?.isOpen.value ? 'opacity-100 scale-100' : 'opacity-0 scale-95',
        alignmentClasses,
        props.class
      ]"
      :style="contentStyle"
    >
      <slot />
    </div>
  </Teleport>
</template>

<script setup lang="ts">
interface Props {
  align?: 'start' | 'center' | 'end'
  side?: 'top' | 'bottom' | 'left' | 'right'
  class?: string
}

const props = withDefaults(defineProps<Props>(), {
  align: 'start',
  side: 'bottom'
})

const contentRef = ref<HTMLElement>()
const contentStyle = ref({})

// Get trigger state
const triggerState = inject('dropdownTrigger')

const alignmentClasses = computed(() => {
  const alignMap = {
    start: 'left-0',
    center: 'left-1/2 transform -translate-x-1/2',
    end: 'right-0'
  }
  return alignMap[props.align] || alignMap.start
})

// Position the dropdown content
const updatePosition = () => {
  if (!contentRef.value) return
  
  const trigger = document.querySelector('[data-dropdown-trigger]')
  if (!trigger) return
  
  const triggerRect = trigger.getBoundingClientRect()
  const contentRect = contentRef.value.getBoundingClientRect()
  
  let top = 0
  let left = 0
  
  switch (props.side) {
    case 'bottom':
      top = triggerRect.bottom + 4
      break
    case 'top':
      top = triggerRect.top - contentRect.height - 4
      break
    case 'left':
      left = triggerRect.left - contentRect.width - 4
      top = triggerRect.top
      break
    case 'right':
      left = triggerRect.right + 4
      top = triggerRect.top
      break
  }
  
  switch (props.align) {
    case 'start':
      if (props.side === 'bottom' || props.side === 'top') {
        left = triggerRect.left
      }
      break
    case 'center':
      if (props.side === 'bottom' || props.side === 'top') {
        left = triggerRect.left + (triggerRect.width - contentRect.width) / 2
      }
      break
    case 'end':
      if (props.side === 'bottom' || props.side === 'top') {
        left = triggerRect.right - contentRect.width
      }
      break
  }
  
  // Keep within viewport
  const padding = 8
  top = Math.max(padding, Math.min(top, window.innerHeight - contentRect.height - padding))
  left = Math.max(padding, Math.min(left, window.innerWidth - contentRect.width - padding))
  
  contentStyle.value = {
    top: `${top}px`,
    left: `${left}px`,
    position: 'fixed'
  }
}

watch(() => triggerState?.isOpen.value, (isOpen) => {
  if (isOpen) {
    nextTick(() => {
      updatePosition()
    })
  }
})
</script>