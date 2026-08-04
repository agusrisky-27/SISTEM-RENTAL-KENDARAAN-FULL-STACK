<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import Sidebar from './components/Sidebar.vue'
import Topbar from './components/Topbar.vue'
import ToastContainer from './components/ToastContainer.vue'
import api from './api'

const route = useRoute()
const router = useRouter()

const isLoginPage = computed(() => route.path === '/login')

const sidebarCollapsed = ref(false)
const mobileSidebarOpen = ref(false)

const toggleSidebar = () => {
  sidebarCollapsed.value = !sidebarCollapsed.value
  if (sidebarCollapsed.value) {
    document.body.classList.add('sidebar-collapsed')
  } else {
    document.body.classList.remove('sidebar-collapsed')
  }
}

const openMobileSidebar = () => {
  mobileSidebarOpen.value = true
}

const closeMobileSidebar = () => {
  mobileSidebarOpen.value = false
}

onMounted(() => {
  const saved = localStorage.getItem('theme')
  if (saved) {
    document.documentElement.setAttribute('data-theme', saved)
  } else {
    document.documentElement.setAttribute('data-theme', 'dark')
  }
})
</script>

<template>
  <div v-if="!isLoginPage">
    <div id="sidebar-overlay" :class="{ visible: mobileSidebarOpen }" @click="closeMobileSidebar"></div>
    
    <Sidebar 
      :collapsed="sidebarCollapsed" 
      :mobileOpen="mobileSidebarOpen" 
      @toggle="toggleSidebar"
      @close-mobile="closeMobileSidebar"
    />

    <main class="main">
      <Topbar @open-mobile-sidebar="openMobileSidebar" />
      <div class="content">
        <router-view />
      </div>
    </main>
  </div>
  <div v-else>
    <router-view />
  </div>
  
  <ToastContainer />
</template>
