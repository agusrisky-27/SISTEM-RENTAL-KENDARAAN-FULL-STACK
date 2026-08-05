<script setup>
import { ref, onMounted, computed } from 'vue';
import { storeToRefs } from 'pinia';
import { usePelangganStore } from '../stores/pelanggan';
import BaseModal from '../components/ui/BaseModal.vue';
import { 
  PlusIcon, 
  PencilSquareIcon, 
  TrashIcon,
  MagnifyingGlassIcon,
  ExclamationTriangleIcon,
  UsersIcon
} from '@heroicons/vue/24/outline';

const store = usePelangganStore();
const { items, loading } = storeToRefs(store);

const searchQuery = ref('');
const isModalOpen = ref(false);
const isDeleteModalOpen = ref(false);
const editingId = ref(null);
const deletingId = ref(null);

const form = ref({
  nama: '',
  no_hp: '',
  no_identitas: '',
  alamat: ''
});

const resetForm = () => {
  form.value = {
    nama: '',
    no_hp: '',
    no_identitas: '',
    alamat: ''
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
    success = await store.updatePelanggan(editingId.value, form.value);
  } else {
    success = await store.createPelanggan(form.value);
  }

  if (success) {
    isModalOpen.value = false;
    resetForm();
  }
};

const handleDelete = async () => {
  if (deletingId.value) {
    await store.deletePelanggan(deletingId.value);
    isDeleteModalOpen.value = false;
    deletingId.value = null;
  }
};

const filteredItems = computed(() => {
  if (!searchQuery.value) return items.value;
  const q = searchQuery.value.toLowerCase();
  return items.value.filter(item => 
    item.nama.toLowerCase().includes(q) ||
    item.no_hp.toLowerCase().includes(q) ||
    item.no_identitas.toLowerCase().includes(q)
  );
});

onMounted(() => {
  store.fetchPelanggan();
});
</script>

<template>
  <div class="space-y-6">
    <!-- Header & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Data Pelanggan</h1>
        <p class="mt-2 text-base text-gray-500 dark:text-gray-400">Kelola daftar pelanggan rental kendaraan Anda.</p>
      </div>
      
      <div class="flex flex-col sm:flex-row items-center gap-3">
        <!-- Search -->
        <div class="relative w-full sm:w-72">
          <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
            <MagnifyingGlassIcon class="h-5 w-5 text-gray-400" />
          </div>
          <input 
            v-model="searchQuery"
            type="text" 
            placeholder="Cari pelanggan..." 
            class="block w-full pl-11 pr-4 py-2.5 border border-gray-200 dark:border-gray-700 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 text-base transition-all shadow-sm"
          >
        </div>
        
        <!-- Add Button -->
        <button 
          @click="openAddModal"
          class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-base font-medium rounded-xl transition-all shadow-sm shadow-blue-500/20 active:scale-95"
        >
          <PlusIcon class="w-5 h-5" />
          Tambah Data
        </button>
      </div>
    </div>

    <!-- Data Grid/List -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden min-h-[400px]">
      
      <div v-if="loading" class="flex flex-col items-center justify-center py-20">
        <svg class="animate-spin h-10 w-10 text-blue-600 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <p class="text-base text-gray-500 dark:text-gray-400">Memuat data pelanggan...</p>
      </div>

      <div v-else-if="filteredItems.length === 0" class="flex flex-col items-center justify-center py-20">
        <div class="w-20 h-20 bg-gray-50 dark:bg-gray-900 rounded-full flex items-center justify-center mb-4">
          <UsersIcon class="w-10 h-10 text-gray-400" />
        </div>
        <h3 class="text-xl font-medium text-gray-900 dark:text-white">Tidak ada data</h3>
        <p class="text-base text-gray-500 dark:text-gray-400 mt-2">Data pelanggan belum tersedia atau tidak ditemukan.</p>
      </div>

      <!-- Table View for Pelanggan -->
      <div v-else class="overflow-x-auto">
        <table class="w-full text-left text-base whitespace-nowrap">
          <thead class="bg-gray-50 dark:bg-gray-900/50 text-gray-600 dark:text-gray-400">
            <tr>
              <th scope="col" class="px-6 py-4 font-semibold">Nama Lengkap</th>
              <th scope="col" class="px-6 py-4 font-semibold">No. HP</th>
              <th scope="col" class="px-6 py-4 font-semibold">No. Identitas (KTP)</th>
              <th scope="col" class="px-6 py-4 font-semibold">Alamat</th>
              <th scope="col" class="px-6 py-4 font-semibold text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 dark:divide-gray-700 text-gray-700 dark:text-gray-300">
            <tr v-for="item in filteredItems" :key="item.id" class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
              <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ item.nama }}</td>
              <td class="px-6 py-4">{{ item.no_hp }}</td>
              <td class="px-6 py-4">{{ item.no_identitas }}</td>
              <td class="px-6 py-4 truncate max-w-xs">{{ item.alamat }}</td>
              <td class="px-6 py-4 text-right space-x-2">
                <button @click="openEditModal(item)" class="p-2 text-blue-600 hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-blue-900/30 rounded-lg transition-colors">
                  <PencilSquareIcon class="w-5 h-5" />
                </button>
                <button @click="confirmDelete(item.id)" class="p-2 text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/30 rounded-lg transition-colors">
                  <TrashIcon class="w-5 h-5" />
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal Form -->
    <BaseModal 
      :is-open="isModalOpen" 
      @close="isModalOpen = false"
      :title="editingId ? 'Edit Pelanggan' : 'Tambah Pelanggan'"
    >
      <form @submit.prevent="handleSubmit" class="space-y-4">
        <div class="space-y-1.5">
          <label class="text-base font-medium text-gray-700 dark:text-gray-300">Nama Lengkap</label>
          <input v-model="form.nama" required type="text" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none text-base dark:text-white">
        </div>
        <div class="space-y-1.5">
          <label class="text-base font-medium text-gray-700 dark:text-gray-300">Nomor HP</label>
          <input v-model="form.no_hp" required type="text" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none text-base dark:text-white">
        </div>
        <div class="space-y-1.5">
          <label class="text-base font-medium text-gray-700 dark:text-gray-300">No. Identitas (KTP)</label>
          <input v-model="form.no_identitas" required type="text" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none text-base dark:text-white">
        </div>
        <div class="space-y-1.5">
          <label class="text-base font-medium text-gray-700 dark:text-gray-300">Alamat</label>
          <textarea v-model="form.alamat" required rows="3" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none text-base dark:text-white"></textarea>
        </div>
        
        <div class="pt-4 flex justify-end gap-3 mt-6 border-t border-gray-100 dark:border-gray-700">
          <button type="button" @click="isModalOpen = false" class="px-5 py-2.5 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-colors">Batal</button>
          <button type="submit" class="px-5 py-2.5 text-base font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-sm transition-colors shadow-blue-500/30">Simpan</button>
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
        <div class="w-16 h-16 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center mb-4">
          <ExclamationTriangleIcon class="w-8 h-8 text-red-600 dark:text-red-400" />
        </div>
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Hapus Pelanggan?</h3>
        <p class="text-base text-gray-500 dark:text-gray-400">Tindakan ini tidak dapat dibatalkan. Data yang dihapus akan hilang permanen.</p>
      </div>
      <div class="pt-6 flex gap-4 w-full">
        <button @click="isDeleteModalOpen = false" class="flex-1 px-5 py-2.5 text-base font-medium text-gray-700 dark:text-gray-300 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 rounded-xl transition-colors">Batal</button>
        <button @click="handleDelete" class="flex-1 px-5 py-2.5 text-base font-medium text-white bg-red-600 hover:bg-red-700 rounded-xl shadow-sm transition-colors shadow-red-500/30">Ya, Hapus</button>
      </div>
    </BaseModal>

  </div>
</template>
