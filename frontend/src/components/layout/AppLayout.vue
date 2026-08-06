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
  <div class="min-h-screen bg-slate-50 dark:bg-[#050811] transition-colors duration-300 relative overflow-hidden">
    <!-- Subtle Global Background Gradients -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none z-0">
      <div class="absolute -top-[30%] -left-[10%] w-[50vw] h-[50vw] rounded-full bg-indigo-500/5 dark:bg-indigo-600/10 blur-[100px] mix-blend-multiply dark:mix-blend-screen"></div>
      <div class="absolute top-[20%] -right-[20%] w-[40vw] h-[40vw] rounded-full bg-purple-500/5 dark:bg-purple-600/10 blur-[100px] mix-blend-multiply dark:mix-blend-screen"></div>
    </div>

    <AppSidebar 
      :is-open="isSidebarOpen" 
      :is-collapsed="isSidebarCollapsed"
      @close="closeSidebar" 
      @toggle-collapse="toggleCollapse"
    />
    
    <div 
      :class="[
        'flex flex-col min-h-screen transition-all duration-300 relative z-10',
        isSidebarCollapsed ? 'lg:pl-20' : 'lg:pl-64'
      ]"
    >
      <AppTopbar @toggle-sidebar="toggleSidebar" @toggle-collapse="toggleCollapse" />
      
      <main class="flex-1 p-4 lg:p-6 lg:pt-8">
        <div class="mx-auto max-w-7xl">
          <slot />
        </div>
      </main>
    </div>
  </div>
</template>
