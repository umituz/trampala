<template>
  <div
    :class="[
      'block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 cursor-pointer transition-colors',
      'focus:outline-none focus:bg-gray-100 focus:text-gray-900',
      disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer'
    ]"
    :tabindex="disabled ? -1 : 0"
    @click="handleClick"
    @keydown.enter="handleClick"
    @keydown.space.prevent="handleClick"
  >
    <slot />
  </div>
</template>

<script setup lang="ts">
interface Props {
  asChild?: boolean
  disabled?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  asChild: false,
  disabled: false
})

const emit = defineEmits<{
  click: [event: MouseEvent]
}>()

// Get dropdown context
const triggerState = inject('dropdownTrigger')

const handleClick = (event: MouseEvent | KeyboardEvent) => {
  if (props.disabled) return
  
  emit('click', event as MouseEvent)
  
  // Close dropdown after click
  if (triggerState?.close) {
    triggerState.close()
  }
}
</script>