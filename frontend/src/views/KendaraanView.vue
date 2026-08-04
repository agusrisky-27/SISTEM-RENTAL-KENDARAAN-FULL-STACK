<script setup>
import { ref, onMounted, computed, reactive } from 'vue'
import api from '../api'
import { useToast } from '../utils/toast'

const { addToast } = useToast()

const vehicles = ref([])
const viewMode = ref('grid')
const searchQuery = ref('')
const filterStatus = ref('')
const filterJenis = ref('')

// Modal state
const isModalOpen = ref(false)
const isSubmitting = ref(false)
const formTitle = ref('Tambah Kendaraan')
const form = reactive({
  id: null,
  nama: '',
  merk: '',
  jenis: 'Motor',
  plat: '',
  harga: '',
  status: 'tersedia'
})

const fetchVehicles = async () => {
  try {
    const response = await api.get('/kendaraan')
    vehicles.value = response.data.data || response.data || []
  } catch (error) {
    console.error('Failed to fetch vehicles:', error)
    addToast('Gagal memuat data kendaraan', 'error')
  }
}

onMounted(() => {
  fetchVehicles()
})

const filteredVehicles = computed(() => {
  return vehicles.value.filter(v => {
    const matchQ = (v.nama_kendaraan && v.nama_kendaraan.toLowerCase().includes(searchQuery.value.toLowerCase())) || 
                   (v.merk && v.merk.toLowerCase().includes(searchQuery.value.toLowerCase())) ||
                   (v.plat_nomor && v.plat_nomor.toLowerCase().includes(searchQuery.value.toLowerCase()))
    const matchS = filterStatus.value ? v.status === filterStatus.value : true
    const matchJ = filterJenis.value ? v.jenis === filterJenis.value : true
    return matchQ && matchS && matchJ
  })
})

const formatRp = (n) => 'Rp ' + Number(n).toLocaleString('id-ID')

const getIcon = (jenis) => {
  const icons = { Motor: '🏍️', Mobil: '🚗', SUV: '🚙', Van: '🚐', Truck: '🚛' }
  return icons[jenis] || '🚘'
}

const getIconColor = (jenis) => {
  const colors = { Motor: 'ic-amber', Mobil: 'ic-blue', SUV: 'ic-purple', Van: 'ic-cyan', Truck: 'ic-red' }
  return colors[jenis] || 'ic-blue'
}

const openModal = (item = null) => {
  if (item) {
    formTitle.value = 'Edit Kendaraan'
    form.id = item.id_kendaraan
    form.nama = item.nama_kendaraan
    form.merk = item.merk || ''
    form.jenis = item.jenis || 'Motor'
    form.plat = item.plat_nomor || ''
    form.harga = item.harga_sewa || ''
    form.status = item.status || 'tersedia'
  } else {
    formTitle.value = 'Tambah Kendaraan'
    form.id = null
    form.nama = ''
    form.merk = ''
    form.jenis = 'Motor'
    form.plat = ''
    form.harga = ''
    form.status = 'tersedia'
  }
  isModalOpen.value = true
}

const closeModal = () => {
  isModalOpen.value = false
}

const saveKendaraan = async () => {
  if (!form.nama || !form.harga) {
    addToast('Nama dan harga wajib diisi!', 'error')
    return
  }

  isSubmitting.value = true
  try {
    const payload = {
      nama_kendaraan: form.nama,
      merk: form.merk,
      jenis: form.jenis,
      plat_nomor: form.plat,
      harga_sewa: parseInt(form.harga),
      status: form.status
    }

    if (form.id) {
      await api.put(`/kendaraan/${form.id}`, payload)
      addToast('Kendaraan berhasil diperbarui', 'success')
    } else {
      await api.post('/kendaraan', payload)
      addToast('Kendaraan berhasil ditambahkan', 'success')
    }
    
    closeModal()
    fetchVehicles()
  } catch (error) {
    console.error(error)
    addToast('Gagal menyimpan kendaraan', 'error')
  } finally {
    isSubmitting.value = false
  }
}

const deleteKendaraan = async (id) => {
  if (!confirm('Yakin ingin menghapus kendaraan ini?')) return

  try {
    await api.delete(`/kendaraan/${id}`)
    addToast('Kendaraan berhasil dihapus', 'success')
    fetchVehicles()
  } catch (error) {
    console.error(error)
    addToast('Gagal menghapus kendaraan', 'error')
  }
}
</script>

<template>
  <div class="page active">
    <div class="vehicle-page-header">
      <div class="search-box" style="max-width:260px">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" v-model="searchQuery" placeholder="Cari kendaraan..."/>
      </div>
      <select class="filter-select" v-model="filterStatus">
        <option value="">Semua Status</option>
        <option value="tersedia">Tersedia</option>
        <option value="disewa">Disewa</option>
        <option value="servis">Servis</option>
      </select>
      <select class="filter-select" v-model="filterJenis">
        <option value="">Semua Jenis</option>
        <option>Motor</option><option>Mobil</option><option>SUV</option><option>Van</option>
      </select>
      
      <div class="toolbar-spacer"></div>
      
      <button class="btn-primary" @click="openModal()" style="margin-right: 10px;">
        <i class="fa-solid fa-plus"></i> Tambah Kendaraan
      </button>

      <div class="view-toggle">
        <button class="view-btn" :class="{ active: viewMode === 'grid' }" @click="viewMode = 'grid'" title="Grid"><i class="fa-solid fa-grip"></i></button>
        <button class="view-btn" :class="{ active: viewMode === 'list' }" @click="viewMode = 'list'" title="List"><i class="fa-solid fa-list"></i></button>
      </div>
    </div>

    <!-- Grid View -->
    <div v-if="viewMode === 'grid'" class="vehicle-grid">
      <div v-for="k in filteredVehicles" :key="k.id_kendaraan" class="vehicle-card">
        <div class="vc-status-bar" :class="k.status"></div>
        <div class="vc-head">
          <div class="sc-icon" :class="getIconColor(k.jenis)" style="width:50px;height:50px;font-size:22px;border-radius:12px">{{ getIcon(k.jenis) }}</div>
          <span class="badge b-blue" v-if="k.status === 'tersedia'">Tersedia</span>
          <span class="badge b-red" v-else-if="k.status === 'disewa'">Disewa</span>
          <span class="badge b-amber" v-else>Servis</span>
        </div>
        <div class="vc-name">{{ k.nama_kendaraan }}</div>
        <div class="vc-merk">{{ k.merk }} · {{ k.jenis }}</div>
        <div class="vc-meta" style="margin-top:12px">
          <div class="vc-row"><span class="lbl">Plat</span><span class="vc-plat">{{ k.plat_nomor || '-' }}</span></div>
        </div>
        <div class="vc-price-row">
          <div><div class="vc-price">{{ formatRp(k.harga_sewa) }}</div><div class="vc-price-per">per hari</div></div>
        </div>
        <div class="vc-actions">
          <button class="act-btn act-edit" @click="openModal(k)"><i class="fa-solid fa-pen-to-square"></i> Edit</button>
          <button class="act-btn act-delete" @click="deleteKendaraan(k.id_kendaraan)"><i class="fa-solid fa-trash"></i> Hapus</button>
        </div>
      </div>
    </div>
    
    <!-- List View -->
    <div v-else class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Kendaraan</th>
            <th>Jenis</th>
            <th>Plat</th>
            <th>Harga/Hari</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="k in filteredVehicles" :key="k.id_kendaraan">
            <td>
              <div class="td-name">{{ k.nama_kendaraan }}</div>
              <div class="td-sub">{{ k.merk }}</div>
            </td>
            <td><span class="badge b-gray"><i class="fa-solid fa-tag"></i> {{ k.jenis }}</span></td>
            <td><span class="vc-plat">{{ k.plat_nomor || '-' }}</span></td>
            <td class="td-amount">{{ formatRp(k.harga_sewa) }}</td>
            <td>
              <span class="badge b-blue" v-if="k.status === 'tersedia'">Tersedia</span>
              <span class="badge b-red" v-else-if="k.status === 'disewa'">Disewa</span>
              <span class="badge b-amber" v-else>Servis</span>
            </td>
            <td>
              <div class="action-group">
                <button class="act-btn act-edit" @click="openModal(k)"><i class="fa-solid fa-pen-to-square"></i> Edit</button>
                <button class="act-btn act-delete" @click="deleteKendaraan(k.id_kendaraan)"><i class="fa-solid fa-trash"></i></button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal Kendaraan -->
    <div class="overlay" v-if="isModalOpen" @click.self="closeModal">
      <div class="modal">
        <button class="modal-close" @click="closeModal"><i class="fa-solid fa-xmark"></i></button>
        <div class="modal-hdr">
          <div class="modal-icon ic-blue"><i class="fa-solid fa-car-side"></i></div>
          <div>
            <div class="modal-title">{{ formTitle }}</div>
            <div class="modal-subtitle">Isi detail kendaraan</div>
          </div>
        </div>
        
        <div class="form-group">
          <label class="form-label">Nama Kendaraan <span style="color:var(--danger)">*</span></label>
          <input class="form-input" v-model="form.nama" placeholder="cth. Honda Beat Street"/>
        </div>
        
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Merk</label>
            <input class="form-input" v-model="form.merk" placeholder="Honda"/>
          </div>
          <div class="form-group">
            <label class="form-label">Jenis</label>
            <select class="form-select" v-model="form.jenis">
              <option>Motor</option><option>Mobil</option><option>SUV</option><option>Van</option><option>Truck</option>
            </select>
          </div>
        </div>
        
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Plat Nomor</label>
            <input class="form-input" v-model="form.plat" placeholder="DK 1234 AB"/>
          </div>
          <div class="form-group">
            <label class="form-label">Harga Sewa/Hari (Rp) <span style="color:var(--danger)">*</span></label>
            <input class="form-input" type="number" v-model="form.harga" placeholder="150000"/>
          </div>
        </div>
        
        <div class="form-group">
          <label class="form-label">Status</label>
          <select class="form-select" v-model="form.status">
            <option value="tersedia">Tersedia</option>
            <option value="disewa">Disewa</option>
            <option value="servis">Servis</option>
          </select>
        </div>
        
        <div class="modal-footer">
          <button class="btn-ghost" @click="closeModal"><i class="fa-solid fa-xmark"></i> Batal</button>
          <button class="btn-primary" :disabled="isSubmitting" @click="saveKendaraan">
            <i class="fa-solid fa-floppy-disk" v-if="!isSubmitting"></i> 
            <i class="fa-solid fa-spinner fa-spin" v-else></i> 
            Simpan
          </button>
        </div>
      </div>
    </div>
    
  </div>
</template>
