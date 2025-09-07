<template>
  <div @click="handleClick" class="cursor-pointer" data-dropdown-trigger>
    <slot />
  </div>
</template>

<script setup lang="ts">
interface Props {
  asChild?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  asChild: false
})

// Inject dropdown context from parent UiDropdownMenu
const dropdownContext = inject('dropdownTrigger', null)

const handleClick = () => {
  if (dropdownContext) {
    dropdownContext.toggle()
  }
}
</script>