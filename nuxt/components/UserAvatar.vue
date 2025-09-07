<template>
  <div class="flex items-center space-x-2">
    <!-- Avatar with initials -->
    <div 
      :class="[
        'rounded-full flex items-center justify-center gradient-purple',
        size === 'sm' ? 'w-6 h-6' : 'w-8 h-8'
      ]"
    >
      <span 
        :class="[
          'text-white font-medium',
          size === 'sm' ? 'text-xs' : 'text-xs'
        ]"
      >
        {{ initials }}
      </span>
    </div>

    <!-- User name (optional) -->
    <span 
      v-if="showName && userName" 
      :class="[
        'font-medium text-gray-900',
        size === 'sm' ? 'text-sm' : 'text-sm'
      ]"
    >
      {{ userName }}
    </span>
  </div>
</template>

<script setup lang="ts">
interface Props {
  userName?: string
  showName?: boolean
  size?: 'sm' | 'md'
}

const props = withDefaults(defineProps<Props>(), {
  showName: false,
  size: 'md'
})

const { getUserInitials } = useAuthData()

const initials = computed(() => getUserInitials(props.userName))
</script>