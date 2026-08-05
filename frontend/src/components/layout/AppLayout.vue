<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import AppSidebar from './AppSidebar.vue';
import AppTopbar from './AppTopbar.vue';

const isSidebarOpen = ref(false);
const isSidebarCollapsed = ref(false);

const toggleSidebar = () => {
  isSidebarOpen.value = !isSidebarOpen.value;
};

const closeSidebar = () => {
  isSidebarOpen.value = false;
};

const toggleCollapse = () => {
  isSidebarCollapsed.value = !isSidebarCollapsed.value;
};

// Auto collapse on small screens
const checkScreen = () => {
  if (window.innerWidth < 1024) {
    isSidebarCollapsed.value = false;
  }
};

onMounted(() => {
  window.addEventListener('resize', checkScreen);
});
onUnmounted(() => {
  window.removeEventListener('resize', checkScreen);
});
</script>

<template>
  <div class="min-h-screen bg-light-bg dark:bg-dark-bg transition-colors duration-200">
    <AppSidebar 
      :is-open="isSidebarOpen" 
      :is-collapsed="isSidebarCollapsed"
      @close="closeSidebar" 
      @toggle-collapse="toggleCollapse"
    />
    
    <div 
      :class="[
        'flex flex-col min-h-screen transition-all duration-300',
        isSidebarCollapsed ? 'lg:pl-20' : 'lg:pl-64'
      ]"
    >
      <AppTopbar @toggle-sidebar="toggleSidebar" />
      
      <main class="flex-1 p-4 lg:p-6">
        <div class="mx-auto max-w-7xl">
          <slot />
        </div>
      </main>
    </div>
  </div>
</template>
