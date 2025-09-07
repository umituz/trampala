<template>
  <div
    :class="[
      'rounded-lg p-4 border',
      alertClasses
    ]"
  >
    <div class="flex items-start">
      <!-- Icon -->
      <div class="flex-shrink-0">
        <Icon :name="alertIcon" :class="['h-5 w-5', iconClasses]" />
      </div>

      <!-- Content -->
      <div class="ml-3 flex-1">
        <h3 v-if="title" :class="['text-sm font-medium', titleClasses]">
          {{ title }}
        </h3>
        <div :class="['text-sm', descriptionClasses, title ? 'mt-1' : '']">
          {{ description }}
        </div>
      </div>

      <!-- Dismiss button -->
      <div v-if="dismissible" class="ml-auto pl-3">
        <button
          type="button"
          :class="[
            'inline-flex rounded-md p-1.5 focus:outline-none focus:ring-2 focus:ring-offset-2',
            dismissButtonClasses
          ]"
          @click="$emit('dismiss')"
        >
          <Icon name="heroicons:x-mark" class="h-4 w-4" />
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
interface Props {
  type: 'success' | 'error' | 'warning' | 'info'
  title?: string
  description: string
  dismissible?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  dismissible: false
})

defineEmits<{
  dismiss: []
}>()

const alertClasses = computed(() => {
  const classes = {
    success: 'bg-green-50 border-green-200',
    error: 'bg-red-50 border-red-200',
    warning: 'bg-yellow-50 border-yellow-200',
    info: 'bg-blue-50 border-blue-200'
  }
  return classes[props.type]
})

const iconClasses = computed(() => {
  const classes = {
    success: 'text-green-400',
    error: 'text-red-400',
    warning: 'text-yellow-400',
    info: 'text-blue-400'
  }
  return classes[props.type]
})

const titleClasses = computed(() => {
  const classes = {
    success: 'text-green-800',
    error: 'text-red-800',
    warning: 'text-yellow-800',
    info: 'text-blue-800'
  }
  return classes[props.type]
})

const descriptionClasses = computed(() => {
  const classes = {
    success: 'text-green-700',
    error: 'text-red-700',
    warning: 'text-yellow-700',
    info: 'text-blue-700'
  }
  return classes[props.type]
})

const dismissButtonClasses = computed(() => {
  const classes = {
    success: 'text-green-500 hover:bg-green-100 focus:ring-green-600 focus:ring-offset-green-50',
    error: 'text-red-500 hover:bg-red-100 focus:ring-red-600 focus:ring-offset-red-50',
    warning: 'text-yellow-500 hover:bg-yellow-100 focus:ring-yellow-600 focus:ring-offset-yellow-50',
    info: 'text-blue-500 hover:bg-blue-100 focus:ring-blue-600 focus:ring-offset-blue-50'
  }
  return classes[props.type]
})

const alertIcon = computed(() => {
  const icons = {
    success: 'heroicons:check-circle',
    error: 'heroicons:x-circle',
    warning: 'heroicons:exclamation-triangle',
    info: 'heroicons:information-circle'
  }
  return icons[props.type]
})
</script>