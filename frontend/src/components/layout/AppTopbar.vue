<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useTheme } from '../../composables/useTheme';
import api from '../../api';

const emit = defineEmits(['toggleSidebar', 'toggleCollapse']);

const route = useRoute();
const router = useRouter();
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

// GLOBAL SEARCH LOGIC
const searchInputRef = ref(null);
const searchQuery = ref('');
const isSearchDropdownOpen = ref(false);
const isSearching = ref(false);
const searchResults = ref({
  kendaraan: [],
  pelanggan: [],
  transaksi: []
});
let debounceTimeout = null;

const handleSearchInput = () => {
  if (!searchQuery.value.trim()) {
    isSearchDropdownOpen.value = false;
    return;
  }
  
  isSearchDropdownOpen.value = true;
  isSearching.value = true;
  
  clearTimeout(debounceTimeout);
  debounceTimeout = setTimeout(async () => {
    await performSearch(searchQuery.value.trim().toLowerCase());
  }, 300); // 300ms debounce
};

const performSearch = async (query) => {
  try {
    // Fetch all entities concurrently
    const [kendaraanRes, pelangganRes, transaksiRes] = await Promise.all([
      api.get('/kendaraan').catch(() => ({ data: { data: [] } })),
      api.get('/pelanggan').catch(() => ({ data: { data: [] } })),
      api.get('/transaksi').catch(() => ({ data: { data: [] } }))
    ]);

    const kendaraanData = kendaraanRes.data?.data || kendaraanRes.data || [];
    const pelangganData = pelangganRes.data?.data || pelangganRes.data || [];
    const transaksiData = transaksiRes.data?.data || transaksiRes.data || [];

    // Filter Kendaraan (by nama, plat, merk)
    searchResults.value.kendaraan = kendaraanData.filter(k => 
      k.nama_kendaraan?.toLowerCase().includes(query) || 
      k.plat_nomor?.toLowerCase().includes(query) ||
      k.merk?.toLowerCase().includes(query)
    ).slice(0, 3); // Max 3 items

    // Filter Pelanggan (by nama, no hp, ktp)
    searchResults.value.pelanggan = pelangganData.filter(p => 
      p.nama?.toLowerCase().includes(query) || 
      p.no_hp?.toLowerCase().includes(query) ||
      p.no_ktp?.toLowerCase().includes(query)
    ).slice(0, 3);

    // Filter Transaksi (by ID, nama pelanggan, nama kendaraan)
    searchResults.value.transaksi = transaksiData.filter(t => 
      String(t.id).includes(query) ||
      t.pelanggan?.nama?.toLowerCase().includes(query) ||
      t.kendaraan?.nama_kendaraan?.toLowerCase().includes(query)
    ).slice(0, 3);

  } catch (error) {
    console.error('Search error:', error);
  } finally {
    isSearching.value = false;
  }
};

const hasSearchResults = computed(() => {
  return searchResults.value.kendaraan.length > 0 || 
         searchResults.value.pelanggan.length > 0 || 
         searchResults.value.transaksi.length > 0;
});

const closeSearch = () => {
  isSearchDropdownOpen.value = false;
};

const goToResult = (type, item) => {
  closeSearch();
  // We can pass a query param ?highlight=id to highlight it in the target page
  if (type === 'kendaraan') router.push(`/kendaraan?highlight=${item.id}`);
  if (type === 'pelanggan') router.push(`/pelanggan?highlight=${item.id}`);
  if (type === 'transaksi') router.push(`/transaksi?highlight=${item.id}`);
  searchQuery.value = '';
};

// Keyboard Shortcuts
const handleKeydown = (e) => {
  if (e.key === 'Escape') {
    closeSearch();
  }
  // Ctrl+K or / to focus search
  if ((e.ctrlKey && e.key === 'k') || (e.key === '/' && document.activeElement.tagName !== 'INPUT' && document.activeElement.tagName !== 'TEXTAREA')) {
    e.preventDefault();
    searchInputRef.value?.focus();
  }
};

onMounted(() => {
  window.addEventListener('keydown', handleKeydown);
  document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
  window.removeEventListener('keydown', handleKeydown);
  document.removeEventListener('click', handleClickOutside);
});

const searchContainerRef = ref(null);
const handleClickOutside = (e) => {
  if (searchContainerRef.value && !searchContainerRef.value.contains(e.target)) {
    closeSearch();
  }
};
</script>

<template>
  <header class="h-16 bg-white/70 dark:bg-[#0a0f1c]/70 backdrop-blur-xl border-b border-gray-200/50 dark:border-white/5 flex items-center justify-between px-4 lg:px-6 sticky top-0 z-30 transition-colors">
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
      <!-- App Logo / Name -->
      <div class="flex flex-col truncate ml-2">
        <span class="text-lg font-heading font-bold text-light-text dark:text-dark-text tracking-tight leading-tight">Rental Kendaraan</span>
        <span class="text-[10px] text-light-muted dark:text-dark-muted font-medium uppercase tracking-wider leading-none">Admin Panel</span>
      </div>
    </div>

    <div class="flex items-center gap-3 sm:gap-4">
      <!-- Search Box with Dropdown -->
      <div class="hidden md:block relative" ref="searchContainerRef">
        <div class="relative">
          <input 
            ref="searchInputRef"
            v-model="searchQuery"
            @input="handleSearchInput"
            @focus="handleSearchInput"
            type="text" 
            placeholder="Cari... (Ctrl+K)" 
            class="w-64 pl-10 pr-4 py-1.5 bg-gray-50 dark:bg-dark-bg border border-light-border dark:border-dark-border rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-accent/50 transition-all text-light-text dark:text-dark-text"
          >
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <font-awesome-icon icon="search" class="text-light-muted dark:text-dark-muted text-sm" />
          </div>
          <!-- Shortcut Hint -->
          <div v-if="!searchQuery" class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
            <span class="text-[10px] font-mono font-medium text-gray-400 bg-gray-200 dark:bg-gray-700 px-1.5 py-0.5 rounded border border-gray-300 dark:border-gray-600">/</span>
          </div>
        </div>

        <!-- Search Results Dropdown -->
        <transition
          enter-active-class="transition duration-200 ease-out"
          enter-from-class="transform scale-95 opacity-0"
          enter-to-class="transform scale-100 opacity-100"
          leave-active-class="transition duration-150 ease-in"
          leave-from-class="transform scale-100 opacity-100"
          leave-to-class="transform scale-95 opacity-0"
        >
          <div v-if="isSearchDropdownOpen && searchQuery.trim()" class="absolute top-full right-0 mt-2 w-80 bg-white dark:bg-dark-card border border-light-border dark:border-dark-border rounded-xl shadow-xl overflow-hidden z-50">
            <!-- Loading State -->
            <div v-if="isSearching" class="p-6 flex flex-col items-center justify-center text-center">
              <font-awesome-icon icon="spinner" class="animate-spin text-accent text-2xl mb-2" />
              <p class="text-sm text-light-muted dark:text-dark-muted">Mencari data...</p>
            </div>
            
            <!-- Empty State -->
            <div v-else-if="!hasSearchResults" class="p-6 flex flex-col items-center justify-center text-center">
              <div class="w-12 h-12 bg-gray-100 dark:bg-dark-border rounded-full flex items-center justify-center mb-3">
                <font-awesome-icon icon="box-open" class="text-gray-400 text-xl" />
              </div>
              <p class="text-sm font-medium text-light-text dark:text-dark-text">Tidak ditemukan</p>
              <p class="text-xs text-light-muted dark:text-dark-muted mt-1">Coba gunakan kata kunci lain</p>
            </div>

            <!-- Results -->
            <div v-else class="max-h-[70vh] overflow-y-auto custom-scrollbar">
              
              <!-- Kendaraan Results -->
              <div v-if="searchResults.kendaraan.length > 0">
                <div class="px-3 py-2 bg-gray-50 dark:bg-dark-bg text-xs font-semibold text-light-muted dark:text-dark-muted uppercase tracking-wider sticky top-0">
                  <font-awesome-icon icon="car-side" class="mr-1" /> Kendaraan
                </div>
                <ul class="py-1">
                  <li v-for="k in searchResults.kendaraan" :key="k.id">
                    <button @click="goToResult('kendaraan', k)" class="w-full text-left px-4 py-2 hover:bg-gray-50 dark:hover:bg-dark-border transition-colors group">
                      <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-light-text dark:text-dark-text group-hover:text-accent transition-colors">{{ k.nama_kendaraan || k.merk }}</span>
                        <span class="text-[10px] font-mono px-1.5 py-0.5 rounded bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-light-muted dark:text-dark-muted">{{ k.plat_nomor }}</span>
                      </div>
                      <div class="text-xs text-light-muted dark:text-dark-muted mt-0.5">
                        <span :class="k.status === 'tersedia' ? 'text-green-500' : 'text-amber-500'">●</span> {{ k.status }}
                      </div>
                    </button>
                  </li>
                </ul>
              </div>

              <!-- Pelanggan Results -->
              <div v-if="searchResults.pelanggan.length > 0">
                <div class="px-3 py-2 bg-gray-50 dark:bg-dark-bg text-xs font-semibold text-light-muted dark:text-dark-muted uppercase tracking-wider sticky top-0">
                  <font-awesome-icon icon="users" class="mr-1" /> Pelanggan
                </div>
                <ul class="py-1">
                  <li v-for="p in searchResults.pelanggan" :key="p.id">
                    <button @click="goToResult('pelanggan', p)" class="w-full text-left px-4 py-2 hover:bg-gray-50 dark:hover:bg-dark-border transition-colors group">
                      <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-light-text dark:text-dark-text group-hover:text-accent transition-colors">{{ p.nama }}</span>
                      </div>
                      <div class="text-xs text-light-muted dark:text-dark-muted mt-0.5">
                        <font-awesome-icon icon="phone" class="text-[10px]" /> {{ p.no_hp }}
                      </div>
                    </button>
                  </li>
                </ul>
              </div>

              <!-- Transaksi Results -->
              <div v-if="searchResults.transaksi.length > 0">
                <div class="px-3 py-2 bg-gray-50 dark:bg-dark-bg text-xs font-semibold text-light-muted dark:text-dark-muted uppercase tracking-wider sticky top-0">
                  <font-awesome-icon icon="file-contract" class="mr-1" /> Transaksi
                </div>
                <ul class="py-1">
                  <li v-for="t in searchResults.transaksi" :key="t.id">
                    <button @click="goToResult('transaksi', t)" class="w-full text-left px-4 py-2 hover:bg-gray-50 dark:hover:bg-dark-border transition-colors group">
                      <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-light-text dark:text-dark-text group-hover:text-accent transition-colors font-mono">TRX-#{{ String(t.id).padStart(3, '0') }}</span>
                        <span class="text-[10px] px-1.5 py-0.5 rounded bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">{{ t.status }}</span>
                      </div>
                      <div class="text-xs text-light-muted dark:text-dark-muted mt-0.5 truncate">
                        {{ t.pelanggan?.nama }} • {{ t.kendaraan?.nama_kendaraan || t.kendaraan?.merk }}
                      </div>
                    </button>
                  </li>
                </ul>
              </div>

            </div>
            
            <div class="px-4 py-2 bg-gray-50 dark:bg-dark-bg border-t border-light-border dark:border-dark-border text-center">
              <span class="text-[10px] text-light-muted dark:text-dark-muted font-medium">Tekan <kbd class="font-mono bg-gray-200 dark:bg-gray-700 px-1 rounded border border-gray-300 dark:border-gray-600">Esc</kbd> untuk menutup</span>
            </div>
          </div>
        </transition>
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
