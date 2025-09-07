<template>
  <div class="mb-4">
    <label v-if="label" :for="textareaId" class="block text-sm font-medium text-gray-700 mb-2">
      {{ label }}
      <span v-if="required" class="text-red-500 ml-1">*</span>
    </label>
    
    <textarea
      :id="textareaId"
      :value="modelValue"
      :placeholder="placeholder"
      :disabled="disabled"
      :readonly="readonly"
      :required="required"
      :rows="rows"
      :maxlength="maxlength"
      :class="textareaClasses"
      @input="handleInput"
      @blur="$emit('blur', $event)"
      @focus="$emit('focus', $event)"
    />
    
    <div v-if="maxlength && showCharCount" class="mt-1 text-right">
      <span class="text-sm text-gray-500">
        {{ currentLength }}/{{ maxlength }}
      </span>
    </div>
    
    <p v-if="error" class="mt-2 text-sm text-red-600">{{ error }}</p>
    <p v-else-if="hint" class="mt-2 text-sm text-gray-500">{{ hint }}</p>
  </div>
</template>

<script setup lang="ts">
interface Props {
  modelValue?: string
  label?: string
  placeholder?: string
  disabled?: boolean
  readonly?: boolean
  required?: boolean
  error?: string
  hint?: string
  rows?: number
  maxlength?: number
  showCharCount?: boolean
  size?: 'sm' | 'md' | 'lg'
}

const props = withDefaults(defineProps<Props>(), {
  rows: 4,
  showCharCount: false,
  size: 'md'
})

const emit = defineEmits<{
  'update:modelValue': [value: string]
  blur: [event: FocusEvent]
  focus: [event: FocusEvent]
}>()

const { generateFieldId, getFormFieldClasses } = useFormField()

const textareaId = generateFieldId('textarea')

const currentLength = computed(() => (props.modelValue || '').length)

const handleInput = (event: Event) => {
  const target = event.target as HTMLTextAreaElement
  emit('update:modelValue', target.value)
}

const textareaClasses = computed(() => {
  return getFormFieldClasses(
    props.size,
    !!props.error,
    props.disabled,
    props.readonly,
    'resize-vertical'
  )
})
</script>