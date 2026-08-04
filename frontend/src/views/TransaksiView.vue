<script setup>
import { ref, onMounted, computed, reactive, watch } from 'vue'
import api from '../api'
import { useToast } from '../utils/toast'

const { addToast } = useToast()

const transaksis = ref([])
const pelangganOptions = ref([])
const kendaraanOptions = ref([])

const searchQuery = ref('')
const filterStatus = ref('')

const isModalOpen = ref(false)
const isSubmitting = ref(false)
const form = reactive({
  id_pelanggan: '',
  id_kendaraan: '',
  tanggal_sewa: '',
  tanggal_kembali: ''
})

const fetchTransaksi = async () => {
  try {
    const response = await api.get('/transaksi')
    transaksis.value = response.data.data || response.data || []
  } catch (error) {
    console.error('Failed to fetch transaksi:', error)
    addToast('Gagal memuat data transaksi', 'error')
  }
}

const fetchOptions = async () => {
  try {
    const [pelRes, kenRes] = await Promise.all([
      api.get('/pelanggan'),
      api.get('/kendaraan')
    ])
    pelangganOptions.value = pelRes.data.data || pelRes.data || []
    kendaraanOptions.value = (kenRes.data.data || kenRes.data || []).filter(k => k.status === 'tersedia')
  } catch (error) {
    console.error('Failed to fetch options', error)
  }
}

onMounted(() => {
  fetchTransaksi()
  fetchOptions()
})

const filteredTransaksi = computed(() => {
  return transaksis.value.filter(t => {
    const searchString = `${t.pelanggan?.nama || ''} ${t.kendaraan?.nama_kendaraan || ''}`.toLowerCase()
    const matchQ = searchString.includes(searchQuery.value.toLowerCase())
    const matchS = filterStatus.value ? t.status === filterStatus.value : true
    return matchQ && matchS
  })
})

const pad3 = (n) => '#' + String(n).padStart(3, '0')
const formatRp = (n) => 'Rp ' + Number(n).toLocaleString('id-ID')

const openModal = () => {
  const today = new Date().toISOString().split('T')[0]
  form.id_pelanggan = ''
  form.id_kendaraan = ''
  form.tanggal_sewa = today
  form.tanggal_kembali = today
  isModalOpen.value = true
  fetchOptions() // refresh options
}

const closeModal = () => {
  isModalOpen.value = false
}

// Compute dynamic totals in form
const selectedKendaraan = computed(() => {
  return kendaraanOptions.value.find(k => k.id_kendaraan == form.id_kendaraan)
})

const estimasiLama = computed(() => {
  if (form.tanggal_sewa && form.tanggal_kembali && form.tanggal_sewa < form.tanggal_kembali) {
    const ts = new Date(form.tanggal_sewa)
    const tk = new Date(form.tanggal_kembali)
    return Math.round((tk - ts) / (86400000))
  }
  return 0
})

const estimasiTotal = computed(() => {
  if (estimasiLama.value > 0 && selectedKendaraan.value) {
    return estimasiLama.value * selectedKendaraan.value.harga_sewa
  }
  return 0
})

const saveTransaksi = async () => {
  if (!form.id_pelanggan || !form.id_kendaraan) {
    addToast('Pelanggan dan Kendaraan wajib dipilih', 'error')
    return
  }
  if (!form.tanggal_sewa || !form.tanggal_kembali || form.tanggal_sewa >= form.tanggal_kembali) {
    addToast('Tanggal kembali harus setelah tanggal sewa!', 'error')
    return
  }

  isSubmitting.value = true
  try {
    await api.post('/transaksi', {
      id_pelanggan: parseInt(form.id_pelanggan),
      id_kendaraan: parseInt(form.id_kendaraan),
      tanggal_sewa: form.tanggal_sewa,
      tanggal_kembali: form.tanggal_kembali
    })
    addToast('Transaksi berhasil dibuat!', 'success')
    closeModal()
    fetchTransaksi()
  } catch (error) {
    console.error(error)
    addToast('Gagal membuat transaksi', 'error')
  } finally {
    isSubmitting.value = false
  }
}

const deleteTransaksi = async (id) => {
  if (!confirm('Yakin ingin menghapus transaksi ini?')) return
  try {
    await api.delete(`/transaksi/${id}`)
    addToast('Transaksi berhasil dihapus', 'success')
    fetchTransaksi()
  } catch (error) {
    console.error(error)
    addToast('Gagal menghapus transaksi', 'error')
  }
}

const returnVehicle = async (id) => {
  if (!confirm('Tandai kendaraan ini sebagai dikembalikan?')) return
  try {
    await api.post(`/transaksi/${id}/kembalikan`)
    addToast('Kendaraan berhasil dikembalikan!', 'success')
    fetchTransaksi()
  } catch (error) {
    console.error(error)
    addToast('Gagal memproses pengembalian', 'error')
  }
}
</script>

<template>
  <div class="page active">
    <div class="table-wrap">
      <div class="table-toolbar">
        <div class="search-box">
          <i class="fa-solid fa-magnifying-glass"></i>
          <input type="text" v-model="searchQuery" placeholder="Cari transaksi..."/>
        </div>
        <select class="filter-select" v-model="filterStatus">
          <option value="">Semua Status</option>
          <option value="aktif">Aktif</option>
          <option value="selesai">Selesai</option>
          <option value="terlambat">Terlambat</option>
        </select>
        <div class="toolbar-spacer"></div>
        <button class="btn-primary" @click="openModal()" style="margin-right: 15px;">
          <i class="fa-solid fa-plus"></i> Buat Transaksi
        </button>
        <span style="font-size:12px;color:var(--text-3)">{{ filteredTransaksi.length }} transaksi</span>
      </div>
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Pelanggan</th>
            <th>Kendaraan</th>
            <th>Tgl Sewa</th>
            <th>Tgl Kembali</th>
            <th>Lama</th>
            <th>Total</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="t in filteredTransaksi" :key="t.id_transaksi">
            <td class="td-id">{{ pad3(t.id_transaksi) }}</td>
            <td><div class="td-name">{{ t.pelanggan?.nama || '-' }}</div></td>
            <td>
              <div class="td-name">{{ t.kendaraan?.nama_kendaraan || '-' }}</div>
              <div class="td-sub">{{ t.kendaraan?.jenis || '' }}</div>
            </td>
            <td class="td-mono">{{ t.tanggal_sewa }}</td>
            <td class="td-mono">{{ t.tanggal_kembali }}</td>
            <td><span class="badge b-blue"><i class="fa-solid fa-calendar-days"></i> {{ t.lama_sewa || 0 }} hari</span></td>
            <td class="td-amount">{{ formatRp(t.total_harga || 0) }}</td>
            <td>
              <span v-if="t.status === 'aktif'" class="badge b-blue"><i class="fa-solid fa-spinner"></i> Aktif</span>
              <span v-else-if="t.status === 'selesai'" class="badge b-green"><i class="fa-solid fa-check"></i> Selesai</span>
              <span v-else class="badge b-red"><i class="fa-solid fa-clock"></i> Terlambat</span>
            </td>
            <td>
              <div class="action-group">
                <button v-if="t.status === 'aktif' || t.status === 'terlambat'" class="act-btn act-return" @click="returnVehicle(t.id_transaksi)"><i class="fa-solid fa-rotate-left"></i> Kembalikan</button>
                <button class="act-btn act-delete" @click="deleteTransaksi(t.id_transaksi)"><i class="fa-solid fa-trash"></i></button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal Transaksi -->
    <div class="overlay" v-if="isModalOpen" @click.self="closeModal">
      <div class="modal">
        <button class="modal-close" @click="closeModal"><i class="fa-solid fa-xmark"></i></button>
        <div class="modal-hdr">
          <div class="modal-icon ic-cyan"><i class="fa-solid fa-file-contract"></i></div>
          <div>
            <div class="modal-title">Buat Transaksi Sewa</div>
            <div class="modal-subtitle">Input data sewa kendaraan</div>
          </div>
        </div>
        
        <div class="form-group">
          <label class="form-label">Pelanggan <span style="color:var(--danger)">*</span></label>
          <select class="form-select" v-model="form.id_pelanggan">
            <option disabled value="">Pilih Pelanggan</option>
            <option v-for="p in pelangganOptions" :key="p.id_pelanggan" :value="p.id_pelanggan">{{ p.nama }}</option>
          </select>
        </div>
        
        <div class="form-group">
          <label class="form-label">Kendaraan (tersedia) <span style="color:var(--danger)">*</span></label>
          <select class="form-select" v-model="form.id_kendaraan">
            <option disabled value="">Pilih Kendaraan</option>
            <option v-for="k in kendaraanOptions" :key="k.id_kendaraan" :value="k.id_kendaraan">
              {{ k.nama_kendaraan }} — {{ formatRp(k.harga_sewa) }}/hari
            </option>
          </select>
        </div>
        
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Tanggal Sewa</label>
            <input class="form-input" type="date" v-model="form.tanggal_sewa"/>
          </div>
          <div class="form-group">
            <label class="form-label">Tanggal Kembali</label>
            <input class="form-input" type="date" v-model="form.tanggal_kembali"/>
          </div>
        </div>
        
        <div class="total-box">
          <div class="total-label"><i class="fa-solid fa-calculator"></i> Estimasi Total</div>
          <div class="total-value">{{ estimasiTotal > 0 ? formatRp(estimasiTotal) : 'Rp 0' }}</div>
          <div class="total-detail" v-if="estimasiLama > 0 && selectedKendaraan">
            {{ estimasiLama }} hari × {{ formatRp(selectedKendaraan.harga_sewa) }}/hari
          </div>
          <div class="total-detail" v-else>Pilih kendaraan dan tanggal valid</div>
        </div>
        
        <div class="modal-footer">
          <button class="btn-ghost" @click="closeModal"><i class="fa-solid fa-xmark"></i> Batal</button>
          <button class="btn-primary" :disabled="isSubmitting" @click="saveTransaksi">
            <i class="fa-solid fa-check" v-if="!isSubmitting"></i> 
            <i class="fa-solid fa-spinner fa-spin" v-else></i> 
            Buat Transaksi
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
