<template>
  <footer class="bg-gray-50 border-t border-gray-200 mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="py-12">
        <div class="text-center">
          <!-- Logo and Description -->
          <div class="flex justify-center items-center space-x-3 mb-4">
            <div class="w-9 h-9 gradient-purple rounded-lg flex items-center justify-center shadow-sm">
              <span class="text-white font-bold text-lg">T</span>
            </div>
            <span class="text-2xl font-bold text-gray-900">Trampala</span>
          </div>
          <p class="text-gray-600 text-sm max-w-md mx-auto mb-6">
            The most trusted platform for trading second-hand items. Easily exchange everything you need through bartering.
          </p>
          <div class="flex justify-center space-x-4">
            <a href="#" class="text-gray-400 hover:text-trampala-purple transition-colors">
              <span class="sr-only">Facebook</span>
              <Icon name="lucide:facebook" class="w-5 h-5" />
            </a>
            <a href="#" class="text-gray-400 hover:text-trampala-purple transition-colors">
              <span class="sr-only">Twitter</span>
              <Icon name="lucide:twitter" class="w-5 h-5" />
            </a>
            <a href="#" class="text-gray-400 hover:text-trampala-purple transition-colors">
              <span class="sr-only">Instagram</span>
              <Icon name="lucide:instagram" class="w-5 h-5" />
            </a>
          </div>
        </div>

        <div class="border-t border-gray-200 pt-8 mt-8">
          <div class="text-center">
            <p class="text-sm text-gray-500">
              © {{ new Date().getFullYear() }} Trampala. All rights reserved.
            </p>
          </div>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup lang="ts">
// No additional logic needed
</script>