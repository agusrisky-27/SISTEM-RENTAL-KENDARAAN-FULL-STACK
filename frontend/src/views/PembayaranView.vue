<script setup>
import { ref, onMounted, computed, reactive } from 'vue'
import api from '../api'
import { useToast } from '../utils/toast'
import { 
  PlusIcon, 
  TrashIcon,
  MagnifyingGlassIcon,
  BanknotesIcon
} from '@heroicons/vue/24/outline'

const { addToast } = useToast()

const pembayarans = ref([])
const transaksiOptions = ref([])
const searchQuery = ref('')
const loading = ref(true)

const isModalOpen = ref(false)
const isSubmitting = ref(false)
const isDeleteModalOpen = ref(false)
const deletingId = ref(null)

const form = reactive({
  id_transaksi: '',
  tanggal_bayar: '',
  jumlah: '',
  metode_pembayaran: 'Transfer Bank'
})

const fetchPembayaran = async () => {
  loading.value = true
  try {
    const res = await api.get('/pembayaran')
    pembayarans.value = res.data.data || res.data || []
  } catch (error) {
    console.error(error)
    addToast('Gagal memuat data pembayaran', 'error')
  } finally {
    loading.value = false
  }
}

const fetchOptions = async () => {
  try {
    const res = await api.get('/transaksi')
    const allTransaksi = res.data.data || res.data || []
    transaksiOptions.value = allTransaksi.filter(t => t.status === 'aktif' || t.status === 'terlambat')
  } catch (error) {
    console.error(error)
  }
}

onMounted(() => {
  fetchPembayaran()
  fetchOptions()
})

const filteredPembayaran = computed(() => {
  if (!searchQuery.value) return pembayarans.value;
  const q = searchQuery.value.toLowerCase();
  return pembayarans.value.filter(p => {
    return p.transaksi?.pelanggan?.nama?.toLowerCase().includes(q) ||
           p.metode_pembayaran?.toLowerCase().includes(q) ||
           String(p.id_pembayaran).includes(q)
  })
})

const openModal = () => {
  const today = new Date().toISOString().split('T')[0]
  form.id_transaksi = ''
  form.tanggal_bayar = today
  form.jumlah = ''
  form.metode_pembayaran = 'Transfer Bank'
  isModalOpen.value = true
  fetchOptions()
}

const closeModal = () => {
  isModalOpen.value = false
}

const selectedTransaksi = computed(() => {
  return transaksiOptions.value.find(t => t.id == form.id_transaksi || t.id_transaksi == form.id_transaksi)
})

const setFullAmount = () => {
  if (selectedTransaksi.value) {
    form.jumlah = selectedTransaksi.value.total_harga
  }
}

const formatRp = (n) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(n)

const savePembayaran = async () => {
  if (!form.id_transaksi || !form.jumlah) {
    addToast('Pilih transaksi dan masukkan jumlah bayar', 'error')
    return
  }
  
  isSubmitting.value = true
  try {
    await api.post('/pembayaran', {
      id_transaksi: parseInt(form.id_transaksi),
      tanggal_bayar: form.tanggal_bayar,
      jumlah: parseFloat(form.jumlah),
      metode_pembayaran: form.metode_pembayaran
    })
    addToast('Pembayaran berhasil dicatat', 'success')
    closeModal()
    fetchPembayaran()
  } catch (error) {
    console.error(error)
    addToast(error.response?.data?.message || 'Gagal menyimpan pembayaran', 'error')
  } finally {
    isSubmitting.value = false
  }
}

const confirmDelete = (id) => {
  deletingId.value = id;
  isDeleteModalOpen.value = true;
};

const deletePembayaran = async () => {
  if (!deletingId.value) return;
  try {
    await api.delete(`/pembayaran/${deletingId.value}`)
    addToast('Pembayaran dihapus', 'success')
    fetchPembayaran()
    isDeleteModalOpen.value = false
    deletingId.value = null
  } catch (error) {
    console.error(error)
    addToast('Gagal menghapus pembayaran', 'error')
  }
}
</script>

<template>
  <div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Data Pembayaran</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Catat dan kelola pembayaran penyewaan kendaraan.</p>
      </div>
      
      <div class="flex flex-col sm:flex-row items-center gap-3">
        <div class="relative w-full sm:w-64">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <MagnifyingGlassIcon class="h-4 w-4 text-gray-400" />
          </div>
          <input 
            v-model="searchQuery"
            type="text" 
            placeholder="Cari pembayaran..." 
            class="block w-full pl-10 pr-3 py-2 border border-gray-200 dark:border-gray-700 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 text-sm transition-all shadow-sm"
          >
        </div>
        
        <button 
          @click="openModal"
          class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-xl transition-all shadow-sm shadow-blue-500/20 active:scale-95"
        >
          <PlusIcon class="w-5 h-5" />
          Catat Pembayaran
        </button>
      </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden min-h-[400px]">
      <div v-if="loading" class="flex flex-col items-center justify-center py-20">
        <svg class="animate-spin h-8 w-8 text-blue-600 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <p class="text-sm text-gray-500 dark:text-gray-400">Memuat data pembayaran...</p>
      </div>

      <div v-else-if="filteredPembayaran.length === 0" class="flex flex-col items-center justify-center py-20">
        <div class="w-16 h-16 bg-gray-50 dark:bg-gray-900 rounded-full flex items-center justify-center mb-4">
          <BanknotesIcon class="w-8 h-8 text-gray-400" />
        </div>
        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Tidak ada data</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Belum ada riwayat pembayaran yang tercatat.</p>
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-gray-50/50 dark:bg-gray-700/50 border-b border-gray-100 dark:border-gray-700">
              <th class="py-4 px-6 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">ID Pembayaran</th>
              <th class="py-4 px-6 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Transaksi Ref</th>
              <th class="py-4 px-6 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tgl Bayar</th>
              <th class="py-4 px-6 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Metode</th>
              <th class="py-4 px-6 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-right">Jumlah</th>
              <th class="py-4 px-6 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
            <tr v-for="p in filteredPembayaran" :key="p.id_pembayaran" class="hover:bg-gray-50 dark:hover:bg-gray-700/25 transition-colors">
              <td class="py-4 px-6 text-sm text-gray-900 dark:text-gray-300 font-medium">#{{ String(p.id_pembayaran).padStart(3, '0') }}</td>
              <td class="py-4 px-6 text-sm text-gray-900 dark:text-gray-300">
                <div class="flex flex-col">
                  <span>Trx #{{ p.transaksi ? String(p.transaksi.id).padStart(3, '0') : '-' }}</span>
                  <span class="text-xs text-gray-400">{{ p.transaksi?.pelanggan?.nama }}</span>
                </div>
              </td>
              <td class="py-4 px-6 text-sm text-gray-500 dark:text-gray-400">{{ p.tanggal_bayar }}</td>
              <td class="py-4 px-6">
                <span class="px-2.5 py-1 rounded-full text-xs font-medium border bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-600">
                  {{ p.metode_pembayaran }}
                </span>
              </td>
              <td class="py-4 px-6 text-sm font-semibold text-gray-900 dark:text-gray-300 text-right">{{ formatRp(p.jumlah) }}</td>
              <td class="py-4 px-6 text-right space-x-2">
                <button @click="confirmDelete(p.id_pembayaran)" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition-colors" title="Hapus">
                  <TrashIcon class="w-5 h-5" />
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal Form Pembayaran -->
    <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm" @click.self="closeModal">
      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-md overflow-hidden transform transition-all">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50/50 dark:bg-gray-800">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 flex items-center justify-center">
              <BanknotesIcon class="w-5 h-5" />
            </div>
            <div>
              <h3 class="text-lg font-bold text-gray-900 dark:text-white leading-tight">Terima Pembayaran</h3>
              <p class="text-xs text-gray-500 dark:text-gray-400">Catat pembayaran dari pelanggan</p>
            </div>
          </div>
          <button @click="closeModal" class="text-gray-400 hover:text-gray-500 focus:outline-none">
            <span class="sr-only">Close</span>
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
          </button>
        </div>
        
        <div class="p-6 space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pilih Transaksi Aktif <span class="text-red-500">*</span></label>
            <select v-model="form.id_transaksi" @change="setFullAmount" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
              <option disabled value="">Pilih Transaksi</option>
              <option v-for="t in transaksiOptions" :key="t.id" :value="t.id">
                #{{ String(t.id).padStart(3, '0') }} - {{ t.pelanggan?.nama }} ({{ formatRp(t.total_harga) }})
              </option>
            </select>
          </div>
          
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Metode</label>
              <select v-model="form.metode_pembayaran" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                <option>Tunai / Cash</option>
                <option>Transfer Bank</option>
                <option>Kartu Kredit/Debit</option>
                <option>E-Wallet (OVO/Gopay)</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tanggal Bayar</label>
              <input v-model="form.tanggal_bayar" type="date" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
            </div>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Jumlah Dibayar (Rp) <span class="text-red-500">*</span></label>
            <div class="flex gap-2">
              <input v-model="form.jumlah" type="number" placeholder="Contoh: 150000" class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
              <button v-if="selectedTransaksi" @click="setFullAmount" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-lg transition-colors whitespace-nowrap">
                Lunas ({{ formatRp(selectedTransaksi.total_harga) }})
              </button>
            </div>
          </div>
        </div>
        
        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 flex justify-end gap-3 rounded-b-2xl">
          <button @click="closeModal" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors shadow-sm">Batal</button>
          <button @click="savePembayaran" :disabled="isSubmitting" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-xl hover:bg-blue-700 shadow-sm shadow-blue-500/20 transition-all active:scale-95 disabled:opacity-70 flex items-center gap-2">
            <svg v-if="isSubmitting" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            <span v-else>Simpan</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Modal Hapus -->
    <div v-if="isDeleteModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm" @click.self="isDeleteModalOpen = false">
      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-sm overflow-hidden transform transition-all p-6 text-center">
        <div class="w-16 h-16 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
          <TrashIcon class="w-8 h-8 text-red-600 dark:text-red-400" />
        </div>
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Hapus Pembayaran?</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Tindakan ini tidak dapat dibatalkan.</p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
          <button @click="isDeleteModalOpen = false" class="w-full sm:w-auto px-6 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-200 bg-gray-100 dark:bg-gray-700 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">Batal</button>
          <button @click="deletePembayaran" class="w-full sm:w-auto px-6 py-2.5 text-sm font-medium text-white bg-red-600 rounded-xl hover:bg-red-700 shadow-sm shadow-red-500/20 transition-all active:scale-95">Ya, Hapus</button>
        </div>
      </div>
    </div>

  </div>
</template>
