<template>
  <div
    :class="cn(
      'shrink-0 bg-border',
      orientation === 'horizontal' ? 'h-[1px] w-full' : 'h-full w-[1px]',
      $attrs.class
    )"
  />
</template>

<script setup lang="ts">
import { cn } from '~/utils/cn'

interface Props {
  orientation?: 'horizontal' | 'vertical'
}

const props = withDefaults(defineProps<Props>(), {
  orientation: 'horizontal',
})
</script>