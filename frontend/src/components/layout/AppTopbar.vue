<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import { useTheme } from '../../composables/useTheme';

const emit = defineEmits(['toggleSidebar', 'toggleCollapse']);

const route = useRoute();
const { isDark, toggleTheme } = useTheme();

const pageName = computed(() => {
  if (route.name) {
    return route.name.charAt(0).toUpperCase() + route.name.slice(1);
  }
  return 'Dashboard';
});

// For + Tambah button
const showAddButton = computed(() => {
  return ['kendaraan', 'pelanggan', 'transaksi', 'pengembalian', 'pembayaran', 'users'].includes(route.name);
});

const getAddButtonText = () => {
  switch (route.name) {
    case 'kendaraan': return 'Tambah Kendaraan';
    case 'pelanggan': return 'Tambah Pelanggan';
    case 'transaksi': return 'Buat Transaksi';
    case 'pengembalian': return 'Catat Pengembalian';
    case 'pembayaran': return 'Catat Pembayaran';
    case 'users': return 'Tambah User';
    default: return 'Tambah';
  }
};
</script>

<template>
  <header class="h-16 bg-light-card dark:bg-dark-card border-b border-light-border dark:border-dark-border flex items-center justify-between px-4 lg:px-6 sticky top-0 z-30 transition-colors">
    <div class="flex items-center gap-4">
      <!-- Mobile Toggle -->
      <button 
        @click="emit('toggleSidebar')"
        class="lg:hidden p-2 text-light-muted dark:text-dark-muted hover:bg-gray-100 dark:hover:bg-dark-border rounded-lg transition-colors"
      >
        <font-awesome-icon icon="bars" class="w-5 h-5" />
      </button>

      <!-- Desktop Collapse Toggle -->
      <button 
        @click="emit('toggleCollapse')"
        class="hidden lg:flex p-2 text-light-muted dark:text-dark-muted hover:bg-gray-100 dark:hover:bg-dark-border rounded-lg transition-colors items-center justify-center"
      >
        <font-awesome-icon icon="bars" class="w-5 h-5" />
      </button>
      <!-- Breadcrumb -->
      <nav class="hidden sm:flex" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2 text-sm font-medium">
          <li>
            <span class="text-light-muted dark:text-dark-muted">Panel</span>
          </li>
          <li>
            <span class="text-light-muted dark:text-dark-muted mx-2">/</span>
          </li>
          <li>
            <span class="text-light-text dark:text-dark-text font-bold">{{ pageName }}</span>
          </li>
        </ol>
      </nav>
    </div>

    <div class="flex items-center gap-3 sm:gap-4">
      <!-- Search Placeholder -->
      <div class="hidden md:block relative">
        <input 
          type="text" 
          placeholder="Cari..." 
          class="w-64 pl-4 pr-10 py-1.5 bg-gray-50 dark:bg-dark-bg border border-light-border dark:border-dark-border rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-accent/50 transition-all text-light-text dark:text-dark-text"
        >
        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
          <font-awesome-icon icon="search" class="text-light-muted dark:text-dark-muted text-sm" />
        </div>
      </div>

      <div class="h-6 w-px bg-light-border dark:bg-dark-border hidden md:block mx-1"></div>

      <!-- Add Button -->
      <button v-if="showAddButton" class="btn-primary py-1.5 text-sm hidden sm:inline-flex">
        <font-awesome-icon icon="plus" class="text-xs" />
        <span>{{ getAddButtonText() }}</span>
      </button>
      <button v-if="showAddButton" class="btn-primary p-1.5 sm:hidden rounded-full aspect-square flex items-center justify-center">
        <font-awesome-icon icon="plus" class="text-xs" />
      </button>

      <!-- Theme Toggle -->
      <button 
        @click="toggleTheme"
        class="p-2 w-9 h-9 text-light-muted dark:text-dark-muted hover:bg-gray-100 dark:hover:bg-dark-border rounded-full transition-colors relative overflow-hidden flex items-center justify-center"
      >
        <transition name="fade" mode="out-in">
          <font-awesome-icon v-if="isDark" icon="sun" class="text-[1.1rem]" />
          <font-awesome-icon v-else icon="moon" class="text-[1.1rem]" />
        </transition>
      </button>

      <!-- Notification -->
      <button 
        class="p-2 w-9 h-9 text-light-muted dark:text-dark-muted hover:bg-gray-100 dark:hover:bg-dark-border rounded-full transition-colors relative flex items-center justify-center"
      >
        <font-awesome-icon icon="bell" class="text-[1.1rem]" />
        <span class="absolute top-1.5 right-2 w-2 h-2 bg-red-500 rounded-full border-2 border-light-card dark:border-dark-card"></span>
      </button>
    </div>
  </header>
</template>
