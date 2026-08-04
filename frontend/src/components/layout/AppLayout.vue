<script setup>
import { ref } from 'vue';
import AppSidebar from './AppSidebar.vue';
import AppTopbar from './AppTopbar.vue';

const isSidebarOpen = ref(false);

const toggleSidebar = () => {
  isSidebarOpen.value = !isSidebarOpen.value;
};

const closeSidebar = () => {
  isSidebarOpen.value = false;
};
</script>

<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
    <AppSidebar :is-open="isSidebarOpen" @close="closeSidebar" />
    
    <div class="lg:pl-64 flex flex-col min-h-screen transition-all duration-300">
      <AppTopbar @toggle-sidebar="toggleSidebar" />
      
      <main class="flex-1 p-4 lg:p-8">
        <div class="mx-auto max-w-7xl">
          <slot />
        </div>
      </main>
    </div>
  </div>
</template>
