<script setup>
import { ref, onMounted, computed, reactive } from 'vue'
import api from '../api'
import { useToast } from '../utils/toast'

const { addToast } = useToast()

const pengembalians = ref([])
const transaksiOptions = ref([])
const searchQuery = ref('')

const isModalOpen = ref(false)
const isSubmitting = ref(false)
const form = reactive({
  id_transaksi: '',
  tanggal_kembali: '',
  kondisi_kendaraan: 'Baik',
  catatan: ''
})

const fetchPengembalian = async () => {
  try {
    const res = await api.get('/pengembalian')
    pengembalians.value = res.data.data || res.data || []
  } catch (error) {
    console.error(error)
    addToast('Gagal memuat data pengembalian', 'error')
  }
}

const fetchOptions = async () => {
  try {
    const res = await api.get('/transaksi')
    const allTransaksi = res.data.data || res.data || []
    transaksiOptions.value = allTransaksi.filter(t => t.status === 'menunggu_pengembalian' || t.status === 'aktif')
  } catch (error) {
    console.error(error)
  }
}

onMounted(() => {
  fetchPengembalian()
  fetchOptions()
})

const formatRp = (n) => 'Rp ' + Number(n).toLocaleString('id-ID')
const pad3 = (n) => '#' + String(n).padStart(3, '0')

const openModal = () => {
  const today = new Date().toISOString().split('T')[0]
  form.id_transaksi = ''
  form.tanggal_kembali = today
  form.kondisi_kendaraan = 'Baik'
  form.catatan = ''
  isModalOpen.value = true
  fetchOptions()
}

const closeModal = () => {
  isModalOpen.value = false
}

const savePengembalian = async () => {
  if (!form.id_transaksi) {
    addToast('Pilih transaksi', 'error')
    return
  }
  
  isSubmitting.value = true
  try {
    await api.post('/pengembalian', {
      id_transaksi: parseInt(form.id_transaksi),
      tanggal_kembali: form.tanggal_kembali,
      kondisi_kendaraan: form.kondisi_kendaraan,
      catatan: form.catatan
    })
    addToast('Pengembalian berhasil dicatat', 'success')
    closeModal()
    fetchPengembalian()
  } catch (error) {
    console.error(error)
    addToast(error.response?.data?.message || 'Gagal menyimpan pengembalian', 'error')
  } finally {
    isSubmitting.value = false
  }
}

const deletePengembalian = async (id) => {
  if (!confirm('Yakin ingin menghapus data pengembalian ini?')) return
  try {
    await api.delete(`/pengembalian/${id}`)
    addToast('Pengembalian dihapus', 'success')
    fetchPengembalian()
  } catch (error) {
    console.error(error)
    addToast('Gagal menghapus pengembalian', 'error')
  }
}
</script>

<template>
  <div class="page active">
    <div class="table-wrap">
      <div class="table-toolbar">
        <div class="search-box">
          <i class="fa-solid fa-magnifying-glass"></i>
          <input type="text" v-model="searchQuery" placeholder="Cari pengembalian..."/>
        </div>
        <div class="toolbar-spacer"></div>
        <button class="btn-primary" @click="openModal" style="margin-right: 15px;">
          <i class="fa-solid fa-plus"></i> Catat Pengembalian
        </button>
      </div>
      
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Transaksi Ref</th>
            <th>Tgl Kembali (Real)</th>
            <th>Kondisi</th>
            <th>Catatan</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="p in pengembalians" :key="p.id_pengembalian">
            <td class="td-id">{{ pad3(p.id_pengembalian) }}</td>
            <td class="td-id">{{ p.transaksi ? pad3(p.transaksi.id_transaksi) : '-' }}</td>
            <td class="td-mono">{{ p.tanggal_kembali }}</td>
            <td>
              <span class="badge b-green" v-if="p.kondisi_kendaraan === 'Baik' || p.kondisi_kendaraan === 'baik'">Baik</span>
              <span class="badge b-red" v-else-if="p.kondisi_kendaraan === 'Rusak' || p.kondisi_kendaraan === 'rusak'">Rusak</span>
              <span class="badge b-amber" v-else>{{ p.kondisi_kendaraan }}</span>
            </td>
            <td><span class="td-sub">{{ p.catatan }}</span></td>
            <td>
              <div class="action-group">
                <button class="act-btn act-delete" @click="deletePengembalian(p.id_pengembalian)"><i class="fa-solid fa-trash"></i></button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal Pengembalian -->
    <div class="overlay" v-if="isModalOpen" @click.self="closeModal">
      <div class="modal">
        <button class="modal-close" @click="closeModal"><i class="fa-solid fa-xmark"></i></button>
        <div class="modal-hdr">
          <div class="modal-icon ic-blue"><i class="fa-solid fa-rotate-left"></i></div>
          <div>
            <div class="modal-title">Form Pengembalian</div>
            <div class="modal-subtitle">Inspeksi kendaraan kembali</div>
          </div>
        </div>
        
        <div class="form-group">
          <label class="form-label">Pilih Transaksi <span style="color:var(--danger)">*</span></label>
          <select class="form-select" v-model="form.id_transaksi">
            <option disabled value="">Pilih Transaksi</option>
            <option v-for="t in transaksiOptions" :key="t.id_transaksi" :value="t.id_transaksi">
              {{ pad3(t.id_transaksi) }} - {{ t.pelanggan?.nama }} - {{ t.kendaraan?.nama_kendaraan }}
            </option>
          </select>
        </div>
        
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Tanggal Kembali Fisik</label>
            <input class="form-input" type="date" v-model="form.tanggal_kembali"/>
          </div>
          <div class="form-group">
            <label class="form-label">Kondisi Kendaraan</label>
            <select class="form-select" v-model="form.kondisi_kendaraan">
              <option>Baik</option>
              <option>Lecet / Kotor</option>
              <option>Rusak</option>
            </select>
          </div>
        </div>
        
        <div class="form-group">
          <label class="form-label">Catatan</label>
          <textarea class="form-textarea" v-model="form.catatan" placeholder="Detail kondisi atau denda jika ada..."></textarea>
        </div>
        
        <div class="modal-footer">
          <button class="btn-ghost" @click="closeModal"><i class="fa-solid fa-xmark"></i> Batal</button>
          <button class="btn-primary" :disabled="isSubmitting" @click="savePengembalian">
            <i class="fa-solid fa-check" v-if="!isSubmitting"></i> 
            <i class="fa-solid fa-spinner fa-spin" v-else></i> 
            Selesaikan
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
