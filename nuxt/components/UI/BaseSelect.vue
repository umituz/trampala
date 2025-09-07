<template>
  <div class="mb-4">
    <label v-if="label" :for="selectId" class="block text-sm font-medium text-gray-700 mb-2">
      {{ label }}
      <span v-if="required" class="text-red-500 ml-1">*</span>
    </label>
    
    <select
      :id="selectId"
      :value="modelValue"
      :disabled="disabled"
      :required="required"
      :class="selectClasses"
      @change="handleChange"
      @blur="$emit('blur', $event)"
      @focus="$emit('focus', $event)"
    >
      <option v-if="placeholder" value="" disabled>{{ placeholder }}</option>
      
      <!-- Check if options have optgroups structure -->
      <template v-if="hasOptgroups">
        <optgroup
          v-for="group in options"
          :key="group.label"
          :label="group.label"
        >
          <option
            v-for="option in group.options"
            :key="option.value"
            :value="option.value"
          >
            {{ option.label }}
          </option>
        </optgroup>
      </template>
      
      <!-- Regular options -->
      <template v-else>
        <option
          v-for="option in options"
          :key="getOptionValue(option)"
          :value="getOptionValue(option)"
        >
          {{ getOptionLabel(option) }}
        </option>
      </template>
    </select>
    
    <p v-if="error" class="mt-2 text-sm text-red-600">{{ error }}</p>
    <p v-else-if="hint" class="mt-2 text-sm text-gray-500">{{ hint }}</p>
  </div>
</template>

<script setup lang="ts">
interface Option {
  value: string | number
  label: string
  [key: string]: any
}

interface OptionGroup {
  label: string
  options: Option[]
}

interface Props {
  modelValue?: string | number
  label?: string
  placeholder?: string
  disabled?: boolean
  required?: boolean
  error?: string
  hint?: string
  options: Option[] | string[] | number[] | OptionGroup[]
  valueKey?: string
  labelKey?: string
  size?: 'sm' | 'md' | 'lg'
}

const props = withDefaults(defineProps<Props>(), {
  valueKey: 'value',
  labelKey: 'label',
  size: 'md'
})

const emit = defineEmits<{
  'update:modelValue': [value: string | number]
  change: [value: string | number, option: Option | string | number]
  blur: [event: FocusEvent]
  focus: [event: FocusEvent]
}>()

const { generateFieldId, getFormFieldClasses } = useFormField()

const selectId = generateFieldId('select')

// Check if options have optgroups structure
const hasOptgroups = computed(() => {
  return Array.isArray(props.options) && 
         props.options.length > 0 && 
         typeof props.options[0] === 'object' && 
         'options' in props.options[0]
})

const getOptionValue = (option: Option | string | number): string | number => {
  if (typeof option === 'object' && option !== null) {
    return option[props.valueKey]
  }
  return option
}

const getOptionLabel = (option: Option | string | number): string => {
  if (typeof option === 'object' && option !== null) {
    return option[props.labelKey]
  }
  return String(option)
}

const handleChange = (event: Event) => {
  const target = event.target as HTMLSelectElement
  const value = target.value
  
  let selectedOption = null
  
  if (hasOptgroups.value) {
    // Search in optgroup structure
    for (const group of props.options as OptionGroup[]) {
      if (group.options) {
        selectedOption = group.options.find(option => 
          String(option.value) === value || String(option.value) === String(Number(value))
        )
        if (selectedOption) {
          break
        }
      }
    }
  } else {
    // Search in regular options
    selectedOption = (props.options as (Option | string | number)[]).find(option => 
      String(getOptionValue(option)) === value || String(getOptionValue(option)) === String(Number(value))
    )
  }
  
  emit('update:modelValue', value)
  emit('change', value, selectedOption || value)
}

const selectClasses = computed(() => {
  return getFormFieldClasses(
    props.size,
    !!props.error,
    props.disabled,
    false,
    'appearance-none'
  )
})
</script>

<style scoped>
select {
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
  background-position: right 0.5rem center;
  background-repeat: no-repeat;
  background-size: 1.5em 1.5em;
  padding-right: 2.5rem;
}
</style>