<script setup>
import { ref } from 'vue'
import { useRouter, useRoute } from 'vue-router'

const props = defineProps({
  collapsed: Boolean,
  mobileOpen: Boolean
})

const emit = defineEmits(['toggle', 'close-mobile'])

const router = useRouter()
const route = useRoute()

const accountDropOpen = ref(false)

const toggleAccountDrop = () => {
  accountDropOpen.value = !accountDropOpen.value
}

const closeAccountDrop = () => {
  accountDropOpen.value = false
}

const navigate = (path) => {
  router.push(path)
  emit('close-mobile')
}
</script>

<template>
  <aside class="sidebar" :class="{ collapsed: collapsed, 'mobile-open': mobileOpen }">
    <div class="sidebar-logo">
      <div class="logo-wordmark">
        <div class="logo-title">Rental Kendaraan</div>
        <div class="logo-sub">Admin Panel</div>
      </div>
      <button class="toggle-btn" @click="emit('toggle')" title="Toggle sidebar">
        <i class="fa-solid fa-bars"></i>
      </button>
    </div>

    <nav class="nav">
      <div class="nav-section-label">Utama</div>
      <div class="nav-item" :class="{ active: route.path === '/dashboard' }" @click="navigate('/dashboard')">
        <i class="fa-solid fa-chart-pie nav-icon"></i>
        <span class="nav-label">Dashboard</span>
      </div>

      <div class="nav-section-label">Master Data</div>
      <div class="nav-item" :class="{ active: route.path === '/kendaraan' }" @click="navigate('/kendaraan')">
        <i class="fa-solid fa-car nav-icon"></i>
        <span class="nav-label">Kendaraan</span>
      </div>
      <div class="nav-item" :class="{ active: route.path === '/pelanggan' }" @click="navigate('/pelanggan')">
        <i class="fa-solid fa-users nav-icon"></i>
        <span class="nav-label">Pelanggan</span>
      </div>
      <div class="nav-item" :class="{ active: route.path === '/users' }" @click="navigate('/users')">
        <i class="fa-solid fa-user-shield nav-icon"></i>
        <span class="nav-label">Users & Akses</span>
      </div>

      <div class="nav-section-label">Operasional</div>
      <div class="nav-item" :class="{ active: route.path === '/transaksi' }" @click="navigate('/transaksi')">
        <i class="fa-solid fa-file-contract nav-icon"></i>
        <span class="nav-label">Transaksi Sewa</span>
      </div>
      <div class="nav-item" :class="{ active: route.path === '/pengembalian' }" @click="navigate('/pengembalian')">
        <i class="fa-solid fa-rotate-left nav-icon"></i>
        <span class="nav-label">Pengembalian</span>
      </div>
      <div class="nav-item" :class="{ active: route.path === '/pembayaran' }" @click="navigate('/pembayaran')">
        <i class="fa-solid fa-wallet nav-icon"></i>
        <span class="nav-label">Pembayaran</span>
      </div>
    </nav>

    <div class="sidebar-footer">
      <div class="account-btn" @click="toggleAccountDrop">
        <div class="avatar avatar-admin">AR</div>
        <div class="account-info">
          <div class="account-name">Agus Risky</div>
          <div class="account-role">Administrator</div>
        </div>
        <i class="fa-solid fa-chevron-up account-caret"></i>
      </div>

      <div class="account-dropdown" :class="{ open: accountDropOpen }">
        <div class="dropdown-header">
          <div class="dh-name">Agus Risky</div>
          <div class="dh-email">agusrisky@rentalken.id</div>
        </div>
        <div class="dropdown-section">
          <div class="dd-item danger" @click="closeAccountDrop">
            <i class="fa-solid fa-arrow-right-from-bracket"></i> Keluar
          </div>
        </div>
      </div>
    </div>
  </aside>
</template>
