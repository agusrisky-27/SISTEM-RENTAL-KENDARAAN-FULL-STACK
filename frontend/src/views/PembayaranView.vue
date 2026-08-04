<script setup>
import { ref, onMounted, computed, reactive } from 'vue'
import api from '../api'
import { useToast } from '../utils/toast'

const { addToast } = useToast()

const pembayarans = ref([])
const transaksiOptions = ref([])
const searchQuery = ref('')

const isModalOpen = ref(false)
const isSubmitting = ref(false)
const form = reactive({
  id_transaksi: '',
  tanggal_bayar: '',
  jumlah: '',
  metode_pembayaran: 'Transfer Bank'
})

const fetchPembayaran = async () => {
  try {
    const res = await api.get('/pembayaran')
    pembayarans.value = res.data.data || res.data || []
  } catch (error) {
    console.error(error)
    addToast('Gagal memuat data pembayaran', 'error')
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
  return pembayarans.value.filter(p => {
    return true // Simple pass for now, can add search logic later
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
  return transaksiOptions.value.find(t => t.id_transaksi == form.id_transaksi)
})

const setFullAmount = () => {
  if (selectedTransaksi.value) {
    form.jumlah = selectedTransaksi.value.total_harga
  }
}

const formatRp = (n) => 'Rp ' + Number(n).toLocaleString('id-ID')
const pad3 = (n) => '#' + String(n).padStart(3, '0')

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

const deletePembayaran = async (id) => {
  if (!confirm('Yakin ingin menghapus data pembayaran ini?')) return
  try {
    await api.delete(`/pembayaran/${id}`)
    addToast('Pembayaran dihapus', 'success')
    fetchPembayaran()
  } catch (error) {
    console.error(error)
    addToast('Gagal menghapus pembayaran', 'error')
  }
}
</script>

<template>
  <div class="page active">
    <div class="table-wrap">
      <div class="table-toolbar">
        <div class="search-box">
          <i class="fa-solid fa-magnifying-glass"></i>
          <input type="text" v-model="searchQuery" placeholder="Cari pembayaran..."/>
        </div>
        <div class="toolbar-spacer"></div>
        <button class="btn-primary" @click="openModal" style="margin-right: 15px;">
          <i class="fa-solid fa-plus"></i> Catat Pembayaran
        </button>
      </div>
      
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Transaksi Ref</th>
            <th>Tgl Bayar</th>
            <th>Jumlah</th>
            <th>Metode</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="p in filteredPembayaran" :key="p.id_pembayaran">
            <td class="td-id">{{ pad3(p.id_pembayaran) }}</td>
            <td class="td-id">{{ p.transaksi ? pad3(p.transaksi.id_transaksi) : '-' }}</td>
            <td class="td-mono">{{ p.tanggal_bayar }}</td>
            <td class="td-amount">{{ formatRp(p.jumlah) }}</td>
            <td><span class="badge b-gray">{{ p.metode_pembayaran }}</span></td>
            <td>
              <div class="action-group">
                <button class="act-btn act-delete" @click="deletePembayaran(p.id_pembayaran)"><i class="fa-solid fa-trash"></i></button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal Pembayaran -->
    <div class="overlay" v-if="isModalOpen" @click.self="closeModal">
      <div class="modal">
        <button class="modal-close" @click="closeModal"><i class="fa-solid fa-xmark"></i></button>
        <div class="modal-hdr">
          <div class="modal-icon ic-green"><i class="fa-solid fa-wallet"></i></div>
          <div>
            <div class="modal-title">Terima Pembayaran</div>
            <div class="modal-subtitle">Catat pembayaran dari pelanggan</div>
          </div>
        </div>
        
        <div class="form-group">
          <label class="form-label">Pilih Transaksi Aktif <span style="color:var(--danger)">*</span></label>
          <select class="form-select" v-model="form.id_transaksi" @change="setFullAmount">
            <option disabled value="">Pilih Transaksi</option>
            <option v-for="t in transaksiOptions" :key="t.id_transaksi" :value="t.id_transaksi">
              {{ pad3(t.id_transaksi) }} - {{ t.pelanggan?.nama }} (Tagihan: {{ formatRp(t.total_harga) }})
            </option>
          </select>
        </div>
        
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Metode Pembayaran</label>
            <select class="form-select" v-model="form.metode_pembayaran">
              <option>Tunai / Cash</option>
              <option>Transfer Bank</option>
              <option>Kartu Kredit/Debit</option>
              <option>E-Wallet (OVO/Gopay)</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Tanggal Bayar</label>
            <input class="form-input" type="date" v-model="form.tanggal_bayar"/>
          </div>
        </div>
        
        <div class="form-group">
          <label class="form-label">Jumlah Dibayar (Rp) <span style="color:var(--danger)">*</span></label>
          <div style="display:flex;gap:10px;">
            <input class="form-input" type="number" v-model="form.jumlah" placeholder="Contoh: 150000"/>
            <button class="btn-ghost" @click="setFullAmount" v-if="selectedTransaksi" style="white-space:nowrap;">Lunas ({{ formatRp(selectedTransaksi.total_harga) }})</button>
          </div>
        </div>
        
        <div class="modal-footer">
          <button class="btn-ghost" @click="closeModal"><i class="fa-solid fa-xmark"></i> Batal</button>
          <button class="btn-primary" :disabled="isSubmitting" @click="savePembayaran">
            <i class="fa-solid fa-check" v-if="!isSubmitting"></i> 
            <i class="fa-solid fa-spinner fa-spin" v-else></i> 
            Simpan
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
