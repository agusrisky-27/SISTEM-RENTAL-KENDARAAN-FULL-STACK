<script setup>
import { ref, onMounted, computed, reactive } from 'vue'
import api from '../api'
import { useToast } from '../utils/toast'

const { addToast } = useToast()

const pelangganList = ref([])
const searchQuery = ref('')

const isModalOpen = ref(false)
const isSubmitting = ref(false)
const formTitle = ref('Tambah Pelanggan')
const form = reactive({
  id: null,
  nama: '',
  hp: '',
  nik: '',
  alamat: ''
})

const fetchPelanggan = async () => {
  try {
    const response = await api.get('/pelanggan')
    pelangganList.value = response.data.data || response.data || []
  } catch (error) {
    console.error('Failed to fetch pelanggan:', error)
    addToast('Gagal memuat data pelanggan', 'error')
  }
}

onMounted(() => {
  fetchPelanggan()
})

const filteredPelanggan = computed(() => {
  return pelangganList.value.filter(p => {
    const q = searchQuery.value.toLowerCase()
    return (p.nama && p.nama.toLowerCase().includes(q)) ||
           (p.no_hp && p.no_hp.includes(q)) ||
           (p.nomor_identitas && p.nomor_identitas.includes(q))
  })
})

const pad3 = (n) => '#' + String(n).padStart(3, '0')

const openModal = (item = null) => {
  if (item) {
    formTitle.value = 'Edit Pelanggan'
    form.id = item.id_pelanggan
    form.nama = item.nama
    form.hp = item.no_hp || ''
    form.nik = item.nomor_identitas || ''
    form.alamat = item.alamat || ''
  } else {
    formTitle.value = 'Tambah Pelanggan'
    form.id = null
    form.nama = ''
    form.hp = ''
    form.nik = ''
    form.alamat = ''
  }
  isModalOpen.value = true
}

const closeModal = () => {
  isModalOpen.value = false
}

const savePelanggan = async () => {
  if (!form.nama || !form.hp || !form.nik) {
    addToast('Nama, No HP, dan No Identitas wajib diisi!', 'error')
    return
  }

  isSubmitting.value = true
  try {
    const payload = {
      nama: form.nama,
      no_hp: form.hp,
      nomor_identitas: form.nik,
      alamat: form.alamat
    }

    if (form.id) {
      await api.put(`/pelanggan/${form.id}`, payload)
      addToast('Pelanggan berhasil diperbarui', 'success')
    } else {
      await api.post('/pelanggan', payload)
      addToast('Pelanggan berhasil didaftarkan', 'success')
    }
    
    closeModal()
    fetchPelanggan()
  } catch (error) {
    console.error(error)
    addToast('Gagal menyimpan pelanggan', 'error')
  } finally {
    isSubmitting.value = false
  }
}

const deletePelanggan = async (id) => {
  if (!confirm('Yakin ingin menghapus pelanggan ini?')) return

  try {
    await api.delete(`/pelanggan/${id}`)
    addToast('Pelanggan berhasil dihapus', 'success')
    fetchPelanggan()
  } catch (error) {
    console.error(error)
    addToast('Gagal menghapus pelanggan', 'error')
  }
}
</script>

<template>
  <div class="page active">
    <div class="table-wrap">
      <div class="table-toolbar">
        <div class="search-box">
          <i class="fa-solid fa-magnifying-glass"></i>
          <input type="text" v-model="searchQuery" placeholder="Cari pelanggan..."/>
        </div>
        <div class="toolbar-spacer"></div>
        <button class="btn-primary" @click="openModal()" style="margin-right: 15px;">
          <i class="fa-solid fa-plus"></i> Tambah Pelanggan
        </button>
        <span style="font-size:12px;color:var(--text-3)">{{ filteredPelanggan.length }} pelanggan</span>
      </div>
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>Nama Pelanggan</th>
            <th>No HP</th>
            <th>No Identitas</th>
            <th>Alamat</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="p in filteredPelanggan" :key="p.id_pelanggan">
            <td class="td-id">{{ pad3(p.id_pelanggan) }}</td>
            <td><div class="td-name">{{ p.nama }}</div></td>
            <td class="td-mono"><i class="fa-solid fa-phone" style="color:var(--text-3);font-size:11px;margin-right:5px"></i>{{ p.no_hp }}</td>
            <td class="td-mono"><i class="fa-solid fa-id-card" style="color:var(--text-3);font-size:11px;margin-right:5px"></i>{{ p.nomor_identitas }}</td>
            <td style="max-width:160px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;color:var(--text-3);font-size:12.5px">{{ p.alamat }}</td>
            <td>
              <div class="action-group">
                <button class="act-btn act-edit" @click="openModal(p)"><i class="fa-solid fa-pen-to-square"></i> Edit</button>
                <button class="act-btn act-delete" @click="deletePelanggan(p.id_pelanggan)"><i class="fa-solid fa-trash"></i></button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal Pelanggan -->
    <div class="overlay" v-if="isModalOpen" @click.self="closeModal">
      <div class="modal">
        <button class="modal-close" @click="closeModal"><i class="fa-solid fa-xmark"></i></button>
        <div class="modal-hdr">
          <div class="modal-icon ic-purple"><i class="fa-solid fa-user-plus"></i></div>
          <div>
            <div class="modal-title">{{ formTitle }}</div>
            <div class="modal-subtitle">Daftarkan pelanggan baru</div>
          </div>
        </div>
        
        <div class="form-group">
          <label class="form-label">Nama Lengkap <span style="color:var(--danger)">*</span></label>
          <input class="form-input" v-model="form.nama" placeholder="I Made Surya"/>
        </div>
        
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">No HP <span style="color:var(--danger)">*</span></label>
            <input class="form-input" v-model="form.hp" placeholder="0812..."/>
          </div>
          <div class="form-group">
            <label class="form-label">No Identitas (KTP/SIM) <span style="color:var(--danger)">*</span></label>
            <input class="form-input" v-model="form.nik" placeholder="5101..."/>
          </div>
        </div>
        
        <div class="form-group">
          <label class="form-label">Alamat</label>
          <textarea class="form-textarea" v-model="form.alamat" placeholder="Jl. Sudirman No.1, Denpasar..."></textarea>
        </div>
        
        <div class="modal-footer">
          <button class="btn-ghost" @click="closeModal"><i class="fa-solid fa-xmark"></i> Batal</button>
          <button class="btn-primary" :disabled="isSubmitting" @click="savePelanggan">
            <i class="fa-solid fa-floppy-disk" v-if="!isSubmitting"></i> 
            <i class="fa-solid fa-spinner fa-spin" v-else></i> 
            Simpan
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
