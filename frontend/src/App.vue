<script setup>
import { ref, onMounted } from 'vue'
import Sidebar from './components/Sidebar.vue'
import Topbar from './components/Topbar.vue'
import ToastContainer from './components/ToastContainer.vue'
import api from './api'

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

const performAutoLogin = async () => {
  try {
    const res = await api.post('/login', { email: 'agusrisky@rentalken.id', password: 'password123' })
    localStorage.setItem('token', res.data.data.token)
  } catch (err) {
    // If login fails, try to register
    try {
      await api.post('/register', { nama: 'Agus Risky', email: 'agusrisky@rentalken.id', password: 'password123', role: 'admin' })
      const res2 = await api.post('/login', { email: 'agusrisky@rentalken.id', password: 'password123' })
      localStorage.setItem('token', res2.data.data.token)
    } catch (regErr) {
      console.error('Auto register/login failed', regErr)
    }
  }
}

onMounted(() => {
  performAutoLogin()
  
  const saved = localStorage.getItem('theme')
  if (saved) {
    document.documentElement.setAttribute('data-theme', saved)
  } else {
    document.documentElement.setAttribute('data-theme', 'dark')
  }
})
</script>

<template>
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
  
  <ToastContainer />
</template>
