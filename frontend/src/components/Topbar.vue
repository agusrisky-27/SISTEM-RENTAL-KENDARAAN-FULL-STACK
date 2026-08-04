<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'

const emit = defineEmits(['open-mobile-sidebar'])
const route = useRoute()

const toggleTheme = () => {
  const html = document.documentElement
  const isDark = html.getAttribute('data-theme') === 'dark'
  html.setAttribute('data-theme', isDark ? 'light' : 'dark')
  localStorage.setItem('theme', isDark ? 'light' : 'dark')
}

const pageTitle = computed(() => {
  const titles = {
    '/dashboard': 'Dashboard',
    '/kendaraan': 'Kendaraan',
    '/pelanggan': 'Pelanggan',
    '/users': 'Users & Akses',
    '/transaksi': 'Transaksi Sewa',
    '/pengembalian': 'Pengembalian',
    '/pembayaran': 'Pembayaran'
  }
  return titles[route.path] || 'Panel'
})

</script>

<template>
  <header class="topbar">
    <div class="topbar-left">
      <button class="hamburger" @click="emit('open-mobile-sidebar')">
        <i class="fa-solid fa-bars"></i>
      </button>
      <div class="breadcrumb">
        <span class="breadcrumb-item">Panel</span>
        <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right" style="font-size:9px"></i></span>
        <span class="breadcrumb-item active">{{ pageTitle }}</span>
      </div>
    </div>
    <div class="topbar-right">
      <div class="theme-toggle" @click="toggleTheme" title="Ganti tema">
        <div class="theme-thumb">
          <i class="fa-solid fa-moon"></i>
        </div>
      </div>
      <div class="icon-btn" title="Notifikasi">
        <i class="fa-solid fa-bell"></i>
        <div class="notif-dot"></div>
      </div>
    </div>
  </header>
</template>
