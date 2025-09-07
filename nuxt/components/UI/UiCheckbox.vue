<template>
  <div class="flex items-center">
    <input
      :id="id"
      v-model="isChecked"
      type="checkbox"
      :disabled="disabled"
      :class="checkboxClasses"
      @change="handleChange"
    />
    <label
      v-if="$slots.default"
      :for="id"
      :class="labelClasses"
    >
      <slot />
    </label>
  </div>
</template>

<script setup lang="ts">
interface Props {
  id?: string
  checked?: boolean
  disabled?: boolean
  size?: 'sm' | 'md' | 'lg'
}

const props = withDefaults(defineProps<Props>(), {
  checked: false,
  disabled: false,
  size: 'md'
})

const emit = defineEmits<{
  'update:checked': [value: boolean]
  change: [value: boolean]
}>()

const isChecked = computed({
  get: () => props.checked,
  set: (value: boolean) => {
    emit('update:checked', value)
    emit('change', value)
  }
})

const checkboxClasses = computed(() => {
  const baseClasses = 'rounded border-gray-300 text-trampala-purple focus:ring-trampala-purple focus:ring-2 focus:ring-offset-0 transition-colors'
  
  const sizeClasses = {
    sm: 'h-3 w-3',
    md: 'h-4 w-4',
    lg: 'h-5 w-5'
  }
  
  const disabledClass = props.disabled ? 'cursor-not-allowed opacity-50' : 'cursor-pointer'
  
  return [
    baseClasses,
    sizeClasses[props.size],
    disabledClass
  ].filter(Boolean).join(' ')
})

const labelClasses = computed(() => {
  const baseClasses = 'ml-2 text-sm text-gray-700 select-none'
  const disabledClass = props.disabled ? 'cursor-not-allowed opacity-50' : 'cursor-pointer'
  
  return [baseClasses, disabledClass].filter(Boolean).join(' ')
})

const handleChange = (event: Event) => {
  const target = event.target as HTMLInputElement
  isChecked.value = target.checked
}
</script>