<script setup>
import { ref, onMounted, computed, reactive } from 'vue';
import api from '../api';
import { useToast } from '../utils/toast';
import { 
  PlusIcon, 
  PencilSquareIcon, 
  TrashIcon,
  MagnifyingGlassIcon,
  UserGroupIcon
} from '@heroicons/vue/24/outline';

const { addToast } = useToast();

const users = ref([]);
const loading = ref(true);
const searchQuery = ref('');

const isModalOpen = ref(false);
const isDeleteModalOpen = ref(false);

const editingId = ref(null);
const deletingId = ref(null);

const form = reactive({
  name: '',
  email: '',
  password: '',
  role: 'staff'
});

const resetForm = () => {
  form.name = '';
  form.email = '';
  form.password = '';
  form.role = 'staff';
  editingId.value = null;
};

const fetchUsers = async () => {
  loading.value = true;
  try {
    const res = await api.get('/users');
    users.value = res.data.data || res.data || [];
  } catch (error) {
    console.error(error);
    addToast('Gagal memuat data pengguna', 'error');
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchUsers();
});

const openAddModal = () => {
  resetForm();
  isModalOpen.value = true;
};

const openEditModal = (item) => {
  editingId.value = item.id;
  form.name = item.name;
  form.email = item.email;
  form.password = ''; // don't load password
  form.role = item.role;
  isModalOpen.value = true;
};

const confirmDelete = (id) => {
  deletingId.value = id;
  isDeleteModalOpen.value = true;
};

const handleSubmit = async () => {
  if (!form.name || !form.email || (!editingId.value && !form.password)) {
    addToast('Harap lengkapi field wajib', 'error');
    return;
  }
  
  try {
    if (editingId.value) {
      const payload = {
        name: form.name,
        email: form.email,
        role: form.role
      };
      if (form.password) payload.password = form.password;
      
      await api.put(`/users/${editingId.value}`, payload);
      addToast('Pengguna berhasil diperbarui', 'success');
    } else {
      await api.post('/users', form);
      addToast('Pengguna berhasil ditambahkan', 'success');
    }
    
    isModalOpen.value = false;
    resetForm();
    fetchUsers();
  } catch (error) {
    console.error(error);
    addToast(error.response?.data?.message || 'Gagal menyimpan data', 'error');
  }
};

const handleDelete = async () => {
  if (!deletingId.value) return;
  try {
    await api.delete(`/users/${deletingId.value}`);
    addToast('Pengguna berhasil dihapus', 'success');
    isDeleteModalOpen.value = false;
    deletingId.value = null;
    fetchUsers();
  } catch (error) {
    console.error(error);
    addToast(error.response?.data?.message || 'Gagal menghapus pengguna', 'error');
  }
};

const filteredUsers = computed(() => {
  if (!searchQuery.value) return users.value;
  const q = searchQuery.value.toLowerCase();
  return users.value.filter(u => 
    u.name.toLowerCase().includes(q) ||
    u.email.toLowerCase().includes(q)
  );
});
</script>

<template>
  <div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Manajemen Pengguna</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kelola akun admin dan staff aplikasi.</p>
      </div>
      
      <div class="flex flex-col sm:flex-row items-center gap-3">
        <div class="relative w-full sm:w-64">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <MagnifyingGlassIcon class="h-4 w-4 text-gray-400" />
          </div>
          <input 
            v-model="searchQuery"
            type="text" 
            placeholder="Cari pengguna..." 
            class="block w-full pl-10 pr-3 py-2 border border-gray-200 dark:border-gray-700 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 text-sm transition-all shadow-sm"
          >
        </div>
        
        <button 
          @click="openAddModal"
          class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-xl transition-all shadow-sm shadow-blue-500/20 active:scale-95"
        >
          <PlusIcon class="w-5 h-5" />
          Tambah Pengguna
        </button>
      </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden min-h-[400px]">
      <div v-if="loading" class="flex flex-col items-center justify-center py-20">
        <svg class="animate-spin h-8 w-8 text-blue-600 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <p class="text-sm text-gray-500 dark:text-gray-400">Memuat data pengguna...</p>
      </div>

      <div v-else-if="filteredUsers.length === 0" class="flex flex-col items-center justify-center py-20">
        <div class="w-16 h-16 bg-gray-50 dark:bg-gray-900 rounded-full flex items-center justify-center mb-4">
          <UserGroupIcon class="w-8 h-8 text-gray-400" />
        </div>
        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Tidak ada data</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Data pengguna belum tersedia atau tidak ditemukan.</p>
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-gray-50/50 dark:bg-gray-700/50 border-b border-gray-100 dark:border-gray-700">
              <th class="py-4 px-6 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nama</th>
              <th class="py-4 px-6 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Email</th>
              <th class="py-4 px-6 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Role</th>
              <th class="py-4 px-6 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
            <tr v-for="user in filteredUsers" :key="user.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/25 transition-colors">
              <td class="py-4 px-6 text-sm text-gray-900 dark:text-gray-300 font-medium">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400 flex items-center justify-center font-bold text-xs uppercase">
                    {{ user.name.substring(0, 2) }}
                  </div>
                  {{ user.name }}
                </div>
              </td>
              <td class="py-4 px-6 text-sm text-gray-500 dark:text-gray-400">{{ user.email }}</td>
              <td class="py-4 px-6">
                <span :class="[
                  'px-2.5 py-1 rounded-full text-xs font-medium border',
                  user.role === 'admin' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400 border-purple-200 dark:border-purple-800' : 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400 border-green-200 dark:border-green-800'
                ]">
                  {{ user.role === 'admin' ? 'Administrator' : 'Staff' }}
                </span>
              </td>
              <td class="py-4 px-6 text-right space-x-2">
                <button @click="openEditModal(user)" class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg transition-colors" title="Edit">
                  <PencilSquareIcon class="w-5 h-5" />
                </button>
                <button @click="confirmDelete(user.id)" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition-colors" title="Hapus">
                  <TrashIcon class="w-5 h-5" />
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal Form User -->
    <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm" @click.self="isModalOpen = false">
      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-md overflow-hidden transform transition-all">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
          <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ editingId ? 'Edit Pengguna' : 'Pengguna Baru' }}</h3>
          <button @click="isModalOpen = false" class="text-gray-400 hover:text-gray-500 focus:outline-none">
            <span class="sr-only">Close</span>
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
          </button>
        </div>
        
        <div class="p-6 space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Lengkap</label>
            <input v-model="form.name" type="text" placeholder="Masukkan nama" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Alamat Email</label>
            <input v-model="form.email" type="email" placeholder="email@contoh.com" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              Password <span v-if="editingId" class="text-xs text-gray-400 font-normal">(Kosongkan jika tidak ingin mengubah)</span>
            </label>
            <input v-model="form.password" type="password" placeholder="Minimal 6 karakter" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Role/Peran</label>
            <select v-model="form.role" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
              <option value="staff">Staff</option>
              <option value="admin">Administrator</option>
            </select>
          </div>
        </div>
        
        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 flex justify-end gap-3 rounded-b-2xl">
          <button @click="isModalOpen = false" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors shadow-sm">Batal</button>
          <button @click="handleSubmit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-xl hover:bg-blue-700 shadow-sm shadow-blue-500/20 transition-all active:scale-95">Simpan</button>
        </div>
      </div>
    </div>

    <!-- Modal Hapus -->
    <div v-if="isDeleteModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm" @click.self="isDeleteModalOpen = false">
      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-sm overflow-hidden transform transition-all p-6 text-center">
        <div class="w-16 h-16 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
          <TrashIcon class="w-8 h-8 text-red-600 dark:text-red-400" />
        </div>
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Hapus Pengguna?</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Tindakan ini tidak dapat dibatalkan.</p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
          <button @click="isDeleteModalOpen = false" class="w-full sm:w-auto px-6 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-200 bg-gray-100 dark:bg-gray-700 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">Batal</button>
          <button @click="handleDelete" class="w-full sm:w-auto px-6 py-2.5 text-sm font-medium text-white bg-red-600 rounded-xl hover:bg-red-700 shadow-sm shadow-red-500/20 transition-all active:scale-95">Ya, Hapus</button>
        </div>
      </div>
    </div>

  </div>
</template>
