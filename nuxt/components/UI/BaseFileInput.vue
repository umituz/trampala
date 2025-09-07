<template>
  <div>
    <label v-if="label" class="block text-sm font-medium text-gray-700 mb-2">
      {{ label }}
      <span v-if="required" class="text-red-500 ml-1">*</span>
    </label>
    
    <!-- Single File Upload -->
    <div v-if="!multiple">
      <div 
        class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-primary-400 transition-colors cursor-pointer"
        @click="triggerFileSelect"
        @dragover.prevent="dragover = true"
        @dragleave.prevent="dragover = false"
        @drop.prevent="handleDrop"
        :class="{ 'border-primary-400 bg-primary-50': dragover }"
      >
        <div v-if="singlePreview">
          <img :src="singlePreview" :alt="singleFileName" class="max-h-32 mx-auto rounded-lg mb-2" />
          <p class="text-sm text-gray-600">{{ singleFileName }}</p>
          <button type="button" @click.stop="removeSingleFile" class="text-red-600 text-sm mt-1">Remove</button>
        </div>
        
        <div v-else>
          <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
          </svg>
          <p class="text-sm text-gray-600">{{ hint || 'Click to upload or drag and drop' }}</p>
          <p class="text-xs text-gray-500 mt-1">{{ acceptText || `${accept} up to ${maxSizeMB}MB` }}</p>
        </div>
      </div>
    </div>

    <!-- Multiple Files Upload -->
    <div v-else>
      <div class="space-y-4">
        <!-- Upload Area -->
        <div 
          class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-primary-400 transition-colors cursor-pointer"
          @click="triggerFileSelect"
          @dragover.prevent="dragover = true"
          @dragleave.prevent="dragover = false"
          @drop.prevent="handleDrop"
          :class="{ 'border-primary-400 bg-primary-50': dragover }"
        >
          <svg class="mx-auto h-8 w-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
          </svg>
          <p class="text-sm text-gray-600">{{ hint || 'Click to select or drag and drop files' }}</p>
          <p class="text-xs text-gray-500 mt-1">
            {{ acceptText || `${accept} up to ${maxSizeMB}MB each` }}
            {{ maxFiles > 1 ? `(max ${maxFiles} files)` : '' }}
          </p>
        </div>

        <!-- File Preview Grid -->
        <div v-if="files.length > 0" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
          <div 
            v-for="(fileWrapper, index) in files" 
            :key="`${fileWrapper.file.name || 'file'}-${index}`"
            class="relative group border border-gray-200 rounded-lg overflow-hidden aspect-square"
          >
            <!-- Image Preview -->
            <img 
              v-if="fileWrapper.preview"
              :src="fileWrapper.preview"
              :alt="fileWrapper.file.name"
              class="w-full h-full object-cover"
            />
            
            <!-- File Info for non-images -->
            <div v-else class="w-full h-full bg-gray-100 flex items-center justify-center p-2">
              <div class="text-center">
                <svg class="w-8 h-8 text-gray-400 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="text-xs text-gray-500 truncate">{{ fileWrapper.file.name }}</p>
              </div>
            </div>

            <!-- Remove Button -->
            <button
              type="button"
              @click="removeFile(index)"
              class="absolute top-1 right-1 bg-red-500 hover:bg-red-600 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity"
            >
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>

            <!-- Primary Badge -->
            <div v-if="index === 0 && files.length > 1" class="absolute bottom-1 left-1 bg-blue-500 text-white text-xs px-1 py-0.5 rounded">
              Primary
            </div>
          </div>
        </div>

        <!-- File Counter -->
        <div v-if="files.length > 0" class="text-sm text-gray-500">
          {{ files.length }}/{{ maxFiles }} files selected
        </div>
      </div>
    </div>

    <!-- Hidden File Input -->
    <input
      :id="inputId"
      ref="fileInput"
      type="file"
      accept=".jpg,.jpeg,.png,.webp"
      :multiple="multiple"
      :required="required && files.length === 0"
      :disabled="disabled"
      class="hidden"
      @change="handleFileChange"
    />

    <p v-if="error" class="mt-2 text-sm text-red-600">{{ error }}</p>
    <p v-else-if="hint && !multiple" class="mt-2 text-sm text-gray-500">{{ hint }}</p>
  </div>
</template>

<script setup lang="ts">
interface FileWithPreview {
  file: File
  preview?: string
}

interface Props {
  modelValue?: File | File[] | null
  label?: string
  accept?: string
  multiple?: boolean
  required?: boolean
  disabled?: boolean
  error?: string
  hint?: string
  maxSizeMB?: number
  maxFiles?: number
  acceptText?: string
}

const props = withDefaults(defineProps<Props>(), {
  accept: 'image/*',
  multiple: false,
  maxSizeMB: 5,
  maxFiles: 5
})

const emit = defineEmits<{
  'update:modelValue': [value: File | File[] | null]
  change: [files: File[]]
  error: [message: string]
}>()

const inputId = `file-input-${Math.random().toString(36).substr(2, 9)}`
const fileInput = ref<HTMLInputElement>()
const dragover = ref(false)

// Unified files state for both single and multiple modes
const files = ref<FileWithPreview[]>([])

// Single file computed properties
const singlePreview = computed(() => files.value[0]?.preview || '')
const singleFileName = computed(() => files.value[0]?.file?.name || '')

// Watch modelValue changes to sync internal state
watch(() => props.modelValue, (newValue) => {
  if (!newValue) {
    files.value = []
  } else if (Array.isArray(newValue)) {
    files.value = newValue.map(file => ({ file }))
  } else {
    files.value = [{ file: newValue }]
  }
}, { immediate: true })

// File processing helper
const processFiles = async (newFiles: File[]) => {
  // Validate file count for multiple mode
  if (props.multiple && files.value.length + newFiles.length > props.maxFiles) {
    emit('error', `Maximum ${props.maxFiles} files allowed`)
    return
  }
  
  // Validate file types and sizes
  for (const file of newFiles) {
    // Validate file type
    if (props.accept === 'image/*') {
      const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp']
      if (!allowedTypes.includes(file.type.toLowerCase())) {
        emit('error', `File "${file.name}" must be JPEG, PNG, JPG, or WebP format`)
        return
      }
    }
    
    // Validate file size
    if (file.size > props.maxSizeMB * 1024 * 1024) {
      emit('error', `File "${file.name}" exceeds ${props.maxSizeMB}MB limit`)
      return
    }
  }
  
  // Process files with previews
  const processedFiles: FileWithPreview[] = []
  for (const file of newFiles) {
    
    const fileWithPreview: FileWithPreview = { file }
    
    // Generate preview for images
    if (file.type.startsWith('image/')) {
      try {
        const preview = await new Promise<string>((resolve, reject) => {
          const reader = new FileReader()
          reader.onload = (e) => {
            const result = e.target?.result as string
            resolve(result)
          }
          reader.onerror = (error) => {
            reject(error)
          }
          reader.readAsDataURL(file)
        })
        fileWithPreview.preview = preview
      } catch (error) {
        // Silently handle preview generation failure
      }
    }
    
    processedFiles.push(fileWithPreview)
  }
  
  // Update files array
  if (props.multiple) {
    files.value = [...files.value, ...processedFiles]
    const fileArray = files.value.map(f => f.file)
    emit('update:modelValue', fileArray)
    emit('change', fileArray)
  } else {
    files.value = [processedFiles[0]]
    const singleFile = processedFiles[0].file
    emit('update:modelValue', singleFile)
    emit('change', [singleFile])
  }
}

// Event handlers
const triggerFileSelect = () => {
  if (props.disabled) return
  fileInput.value?.click()
}

const handleFileChange = async (event: Event) => {
  const target = event.target as HTMLInputElement
  const selectedFiles = target.files ? Array.from(target.files) : []
  
  if (selectedFiles.length === 0) return
  
  await processFiles(selectedFiles)
  
  // Clear input for reselection
  if (fileInput.value) {
    fileInput.value.value = ''
  }
}

const handleDrop = async (event: DragEvent) => {
  if (props.disabled) return
  
  dragover.value = false
  const droppedFiles = event.dataTransfer?.files ? Array.from(event.dataTransfer.files) : []
  
  if (droppedFiles.length === 0) return
  
  // Filter by accepted file types
  const acceptedFiles = droppedFiles.filter(file => {
    if (props.accept === 'image/*') {
      const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp']
      return allowedTypes.includes(file.type.toLowerCase())
    }
    return file.type.startsWith('image/')
  })
  
  if (acceptedFiles.length === 0) {
    emit('error', 'No valid files found')
    return
  }
  
  await processFiles(acceptedFiles)
}

// File management
const removeFile = (index: number) => {
  files.value.splice(index, 1)
  
  if (files.value.length === 0) {
    emit('update:modelValue', null)
    emit('change', [])
  } else if (props.multiple) {
    const fileArray = files.value.map(f => f.file)
    emit('update:modelValue', fileArray)
    emit('change', fileArray)
  } else {
    const singleFile = files.value[0].file
    emit('update:modelValue', singleFile)
    emit('change', [singleFile])
  }
}

const removeSingleFile = () => removeFile(0)

// Clear all files
const clearFile = () => {
  files.value = []
  emit('update:modelValue', null)
  emit('change', [])
  
  if (fileInput.value) {
    fileInput.value.value = ''
  }
}

defineExpose({
  clearFile
})
</script>