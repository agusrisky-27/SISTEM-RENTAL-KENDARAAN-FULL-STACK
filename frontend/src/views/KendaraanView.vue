<script setup>
import { ref, onMounted, computed } from 'vue';
import { storeToRefs } from 'pinia';
import { useKendaraanStore } from '../stores/kendaraan';
import BaseModal from '../components/ui/BaseModal.vue';
import { 
  PlusIcon, 
  PencilSquareIcon, 
  TrashIcon,
  MagnifyingGlassIcon,
  ExclamationTriangleIcon
} from '@heroicons/vue/24/outline';

const store = useKendaraanStore();
const { items, loading } = storeToRefs(store);

const searchQuery = ref('');
const isModalOpen = ref(false);
const isDeleteModalOpen = ref(false);
const editingId = ref(null);
const deletingId = ref(null);

const form = ref({
  nomor_polisi: '',
  merk: '',
  tipe: '',
  tahun: new Date().getFullYear(),
  harga_sewa: 0,
  status: 'Tersedia'
});

const resetForm = () => {
  form.value = {
    nomor_polisi: '',
    merk: '',
    tipe: '',
    tahun: new Date().getFullYear(),
    harga_sewa: 0,
    status: 'Tersedia'
  };
  editingId.value = null;
};

const openAddModal = () => {
  resetForm();
  isModalOpen.value = true;
};

const openEditModal = (item) => {
  form.value = { ...item };
  editingId.value = item.id;
  isModalOpen.value = true;
};

const confirmDelete = (id) => {
  deletingId.value = id;
  isDeleteModalOpen.value = true;
};

const handleSubmit = async () => {
  let success = false;
  if (editingId.value) {
    success = await store.updateKendaraan(editingId.value, form.value);
  } else {
    success = await store.createKendaraan(form.value);
  }

  if (success) {
    isModalOpen.value = false;
    resetForm();
  }
};

const handleDelete = async () => {
  if (deletingId.value) {
    await store.deleteKendaraan(deletingId.value);
    isDeleteModalOpen.value = false;
    deletingId.value = null;
  }
};

const filteredItems = computed(() => {
  if (!searchQuery.value) return items.value;
  const q = searchQuery.value.toLowerCase();
  return items.value.filter(item => 
    item.nomor_polisi.toLowerCase().includes(q) ||
    item.merk.toLowerCase().includes(q) ||
    item.tipe.toLowerCase().includes(q)
  );
});

onMounted(() => {
  store.fetchKendaraan();
});

const formatRp = (val) => {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(val);
};

const getStatusColor = (status) => {
  if (status === 'Tersedia') return 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400 border-green-200 dark:border-green-800';
  if (status === 'Disewa') return 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400 border-blue-200 dark:border-blue-800';
  return 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 border-red-200 dark:border-red-800';
};
</script>

<template>
  <div class="space-y-6">
    <!-- Header & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Data Kendaraan</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kelola daftar kendaraan yang tersedia untuk disewa.</p>
      </div>
      
      <div class="flex flex-col sm:flex-row items-center gap-3">
        <!-- Search -->
        <div class="relative w-full sm:w-64">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <MagnifyingGlassIcon class="h-4 w-4 text-gray-400" />
          </div>
          <input 
            v-model="searchQuery"
            type="text" 
            placeholder="Cari kendaraan..." 
            class="block w-full pl-10 pr-3 py-2 border border-gray-200 dark:border-gray-700 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 text-sm transition-all shadow-sm"
          >
        </div>
        
        <!-- Add Button -->
        <button 
          @click="openAddModal"
          class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-xl transition-all shadow-sm shadow-blue-500/20 active:scale-95"
        >
          <PlusIcon class="w-5 h-5" />
          Tambah Data
        </button>
      </div>
    </div>

    <!-- Data Grid/List -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden min-h-[400px]">
      
      <div v-if="loading" class="flex flex-col items-center justify-center py-20">
        <svg class="animate-spin h-8 w-8 text-blue-600 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <p class="text-sm text-gray-500 dark:text-gray-400">Memuat data kendaraan...</p>
      </div>

      <div v-else-if="filteredItems.length === 0" class="flex flex-col items-center justify-center py-20">
        <div class="w-16 h-16 bg-gray-50 dark:bg-gray-900 rounded-full flex items-center justify-center mb-4">
          <TruckIcon class="w-8 h-8 text-gray-400" />
        </div>
        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Tidak ada data</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Data kendaraan belum tersedia atau tidak ditemukan.</p>
      </div>

      <div v-else class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 p-6">
        <div 
          v-for="item in filteredItems" 
          :key="item.id"
          class="group bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden hover:shadow-xl transition-all duration-300 flex flex-col"
        >
          <!-- Image Placeholder -->
          <div class="h-48 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 flex items-center justify-center relative overflow-hidden">
            <TruckIcon class="w-16 h-16 text-gray-300 dark:text-gray-600 transition-transform duration-500 group-hover:scale-110" />
            <div class="absolute top-3 right-3">
              <span :class="['px-3 py-1 rounded-full text-xs font-semibold border backdrop-blur-md', getStatusColor(item.status)]">
                {{ item.status }}
              </span>
            </div>
          </div>
          
          <div class="p-5 flex-1 flex flex-col">
            <div class="flex items-start justify-between mb-2">
              <div>
                <h3 class="font-bold text-gray-900 dark:text-white text-lg line-clamp-1">{{ item.merk }} {{ item.tipe }}</h3>
                <p class="text-sm font-medium text-blue-600 dark:text-blue-400 mt-1">{{ item.nomor_polisi }}</p>
              </div>
              <div class="text-right">
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ item.tahun }}</p>
              </div>
            </div>
            
            <div class="mt-4 mb-6">
              <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ formatRp(item.harga_sewa) }}<span class="text-sm font-normal text-gray-500">/hari</span></p>
            </div>

            <div class="mt-auto pt-4 border-t border-gray-100 dark:border-gray-700 flex gap-2">
              <button 
                @click="openEditModal(item)"
                class="flex-1 inline-flex justify-center items-center gap-2 px-3 py-2 bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-xl transition-colors"
              >
                <PencilSquareIcon class="w-4 h-4" />
                Edit
              </button>
              <button 
                @click="confirmDelete(item.id)"
                class="flex-1 inline-flex justify-center items-center gap-2 px-3 py-2 bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/40 text-red-600 dark:text-red-400 text-sm font-medium rounded-xl transition-colors"
              >
                <TrashIcon class="w-4 h-4" />
                Hapus
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Form -->
    <BaseModal 
      :is-open="isModalOpen" 
      @close="isModalOpen = false"
      :title="editingId ? 'Edit Kendaraan' : 'Tambah Kendaraan'"
    >
      <form @submit.prevent="handleSubmit" class="space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div class="space-y-1.5">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Nomor Polisi</label>
            <input v-model="form.nomor_polisi" required type="text" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none text-sm dark:text-white">
          </div>
          <div class="space-y-1.5">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Merk</label>
            <input v-model="form.merk" required type="text" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none text-sm dark:text-white">
          </div>
          <div class="space-y-1.5">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Tipe</label>
            <input v-model="form.tipe" required type="text" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none text-sm dark:text-white">
          </div>
          <div class="space-y-1.5">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Tahun</label>
            <input v-model="form.tahun" required type="number" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none text-sm dark:text-white">
          </div>
          <div class="space-y-1.5">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Harga Sewa / Hari</label>
            <input v-model="form.harga_sewa" required type="number" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none text-sm dark:text-white">
          </div>
          <div class="space-y-1.5">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
            <select v-model="form.status" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none text-sm dark:text-white">
              <option value="Tersedia">Tersedia</option>
              <option value="Disewa">Disewa</option>
              <option value="Perbaikan">Perbaikan</option>
            </select>
          </div>
        </div>
        
        <div class="pt-4 flex justify-end gap-3 mt-4 border-t border-gray-100 dark:border-gray-700">
          <button type="button" @click="isModalOpen = false" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-colors">Batal</button>
          <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-sm transition-colors shadow-blue-500/30">Simpan</button>
        </div>
      </form>
    </BaseModal>

    <!-- Modal Konfirmasi Hapus -->
    <BaseModal 
      :is-open="isDeleteModalOpen" 
      @close="isDeleteModalOpen = false"
      title="Konfirmasi Hapus"
      max-width="max-w-sm"
    >
      <div class="flex flex-col items-center text-center py-4">
        <div class="w-12 h-12 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center mb-4">
          <ExclamationTriangleIcon class="w-6 h-6 text-red-600 dark:text-red-400" />
        </div>
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Hapus Kendaraan?</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400">Tindakan ini tidak dapat dibatalkan. Data yang dihapus akan hilang permanen.</p>
      </div>
      <div class="pt-4 flex gap-3 w-full">
        <button @click="isDeleteModalOpen = false" class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 rounded-xl transition-colors">Batal</button>
        <button @click="handleDelete" class="flex-1 px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-xl shadow-sm transition-colors shadow-red-500/30">Ya, Hapus</button>
      </div>
    </BaseModal>

  </div>
</template>
