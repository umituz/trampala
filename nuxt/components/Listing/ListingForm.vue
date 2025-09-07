<template>
  <form @submit.prevent="handleSubmit">
    <div class="space-y-6">
      <BaseInput
        v-model="form.name"
        label="Listing Name"
        required
        :error="errors.name"
        placeholder="Enter listing name"
      />
      
      <BaseSelect
        v-model="form.category_uuid"
        label="Category"
        required
        :options="categoryOptions"
        :error="errors.category_uuid"
        placeholder="Select a category"
        @change="onCategoryChange"
      />
      
      <BaseTextarea
        v-model="form.description"
        label="Description"
        required
        :error="errors.description"
        placeholder="Describe your listing"
        :rows="5"
        :maxlength="1000"
        show-char-count
      />
      
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <BaseSelect
          v-model="form.country_uuid"
          label="Country"
          required
          :options="countryOptions"
          :error="errors.country_uuid"
          placeholder="Select a country"
          @change="onCountryChange"
        />
        
        <BaseSelect
          v-model="form.city_uuid"
          label="City"
          required
          :options="cityOptions"
          :error="errors.city_uuid"
          placeholder="Select a city"
          :disabled="!form.country_uuid"
          @change="onCityChange"
        />
        
        <BaseSelect
          v-model="form.district_uuid"
          label="District"
          :options="districtOptions"
          :error="errors.district_uuid"
          placeholder="Select a district"
          :disabled="!form.city_uuid"
        />
      </div>
      
      <BaseFileInput
        v-model="form.image"
        label="Listing Image"
        :max-files="1"
        accept="image/*"
        :max-size-m-b="2"
        :required="!isEdit"
        :error="errors.image"
        hint="Upload 1 high-quality image (JPEG, PNG, JPG, WebP). Max 2MB."
        @change="handleImageChange"
        @error="handleImageError"
      />
      
      <div class="flex justify-end space-x-3 pt-6">
        <BaseButton
          type="button"
          variant="outline"
          @click="$emit('cancel')"
        >
          Cancel
        </BaseButton>
        
        <BaseButton
          type="submit"
          :loading="loading"
        >
          {{ isEdit ? 'Update Listing' : 'Create Listing' }}
        </BaseButton>
      </div>
    </div>
  </form>
</template>

<script setup lang="ts">
import { useCategoryStore } from '~/stores/useCategoryStore'
import { useLocationStore } from '~/stores/useLocationStore'

interface Props {
  initialData?: any
  loading?: boolean
  isEdit?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  loading: false,
  isEdit: false
})

const emit = defineEmits<{
  submit: [formData: FormData]
  cancel: []
}>()

const categoryStore = useCategoryStore()
const locationStore = useLocationStore()

// Form data
const form = reactive({
  name: props.initialData?.name || '',
  category_uuid: props.initialData?.category_uuid || '',
  description: props.initialData?.description || '',
  country_uuid: props.initialData?.country_uuid || '',
  city_uuid: props.initialData?.city_uuid || '',
  district_uuid: props.initialData?.district_uuid || '',
  image: null as File | null
})


// Form errors
const errors = reactive({
  name: '',
  category_uuid: '',
  description: '',
  country_uuid: '',
  city_uuid: '',
  district_uuid: '',
  image: ''
})

// Options for selects
const categoryOptions = computed(() => {
  return categoryStore.selectOptions
})

const countryOptions = computed(() =>
  locationStore.countrySelectOptions.map(option => ({
    value: option.value,
    label: option.label
  }))
)

const cityOptions = computed(() =>
  locationStore.citySelectOptions.map(option => ({
    value: option.value,
    label: option.label
  }))
)

const districtOptions = computed(() =>
  locationStore.districtSelectOptions.map(option => ({
    value: option.value,
    label: option.label
  }))
)

// Watch for category changes
const onCategoryChange = async (categoryUuid: string) => {
  form.category_uuid = categoryUuid
  clearError('category_uuid')
}

// Watch for country changes
const onCountryChange = async (countryUuid: string) => {
  form.country_uuid = countryUuid
  form.city_uuid = '' // Reset city when country changes
  form.district_uuid = '' // Reset district when country changes
  locationStore.clearCities()
  
  if (countryUuid) {
    try {
      await locationStore.fetchCitySelectOptionsByCountry(countryUuid)
    } catch (error) {
      // Silently handle cities fetch error
    }
  }
  
  clearError('country_uuid')
  clearError('city_uuid')
  clearError('district_uuid')
}

// Watch for city changes
const onCityChange = async (cityUuid: string) => {
  form.city_uuid = cityUuid
  form.district_uuid = '' // Reset district when city changes
  locationStore.clearDistricts()
  
  if (cityUuid) {
    try {
      await locationStore.fetchDistrictSelectOptions(cityUuid)
    } catch (error) {
      // Silently handle districts fetch error
    }
  }
  
  clearError('city_uuid')
  clearError('district_uuid')
}

// Form validation
const validateForm = (): boolean => {
  clearErrors()
  let isValid = true
  
  if (!form.name.trim()) {
    errors.name = 'Listing name is required'
    isValid = false
  }
  
  if (!form.category_uuid) {
    errors.category_uuid = 'Category is required'
    isValid = false
  }
  
  if (!form.description.trim()) {
    errors.description = 'Description is required'
    isValid = false
  } else if (form.description.trim().length < 10) {
    errors.description = 'Description must be at least 10 characters'
    isValid = false
  } else if (form.description.length > 1000) {
    errors.description = 'Description cannot exceed 1000 characters'
    isValid = false
  }
  
  if (!form.country_uuid) {
    errors.country_uuid = 'Country is required'
    isValid = false
  }
  
  if (!form.city_uuid) {
    errors.city_uuid = 'City is required'
    isValid = false
  }
  
  if (!props.isEdit && !form.image) {
    errors.image = 'An image is required'
    isValid = false
  }
  
  return isValid
}

// Clear errors
const clearErrors = () => {
  Object.keys(errors).forEach(key => {
    errors[key as keyof typeof errors] = ''
  })
}

const clearError = (field: keyof typeof errors) => {
  errors[field] = ''
}

// Handle form submission
const handleSubmit = () => {
  
  if (!validateForm()) {
    return
  }
  
  const formData = new FormData()
  formData.append('name', form.name.trim())
  formData.append('category_uuid', form.category_uuid)
  formData.append('description', form.description.trim())
  
  if (form.country_uuid && form.country_uuid !== 'undefined' && form.country_uuid.length > 0) {
    formData.append('country_uuid', form.country_uuid)
  }
  
  formData.append('city_uuid', form.city_uuid)
  
  if (form.district_uuid) {
    formData.append('district_uuid', form.district_uuid)
  }
  
  // Add single image
  if (form.image) {
    formData.append('image', form.image)
  }
  
  
  emit('submit', formData)
}

// Simplified image handling with BaseFileInput
const handleImageChange = (files: File[]) => {
  form.image = files[0] || null
  clearError('image')
}

const handleImageError = (message: string) => {
  errors.image = message
}


// Initialize stores
onMounted(async () => {
  try {
    await Promise.all([
      categoryStore.initialize(),
      locationStore.initialize()
    ])
    
    // If editing and country is selected, load cities
    if (props.isEdit && form.country_uuid) {
      await locationStore.fetchCitySelectOptionsByCountry(form.country_uuid)
      
      // If city is also selected, load districts
      if (form.city_uuid) {
        await locationStore.fetchDistrictSelectOptions(form.city_uuid)
      }
    }
  } catch (error) {
    // Silently handle initialization error
  }
})
</script>