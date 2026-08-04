<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import api from '../api'
import { useToast } from '../utils/toast'

const router = useRouter()
const { addToast } = useToast()

const isRegister = ref(false)
const isLoading = ref(false)

const form = reactive({
  nama: '',
  email: '',
  password: ''
})

const handleSubmit = async () => {
  if (!form.email || !form.password) {
    addToast('Email dan Password wajib diisi', 'error')
    return
  }

  isLoading.value = true
  try {
    if (isRegister.value) {
      if (!form.nama) {
        addToast('Nama wajib diisi', 'error')
        isLoading.value = false
        return
      }
      await api.post('/register', {
        nama: form.nama,
        email: form.email,
        password: form.password,
        role: 'admin'
      })
      addToast('Registrasi berhasil, silakan login', 'success')
      isRegister.value = false
    } else {
      const res = await api.post('/login', {
        email: form.email,
        password: form.password
      })
      
      const { token, user } = res.data.data
      localStorage.setItem('token', token)
      localStorage.setItem('user', JSON.stringify(user))
      
      addToast('Login berhasil', 'success')
      
      // Reload or push to dashboard
      window.location.href = '/dashboard'
    }
  } catch (error) {
    console.error(error)
    addToast(error.response?.data?.message || 'Terjadi kesalahan', 'error')
  } finally {
    isLoading.value = false
  }
}
</script>

<template>
  <div class="login-wrapper">
    <div class="login-box">
      <div class="login-header">
        <div class="logo-wordmark" style="justify-content: center; margin-bottom: 24px;">
          <div class="logo-title" style="font-size: 24px;">Rental Kendaraan</div>
          <div class="logo-sub">Admin Panel</div>
        </div>
        <h2 style="color: var(--text-1); font-weight: 600; margin-bottom: 8px;">{{ isRegister ? 'Buat Akun Baru' : 'Selamat Datang' }}</h2>
        <p style="color: var(--text-3); font-size: 14px;">{{ isRegister ? 'Daftarkan akun admin baru Anda' : 'Silakan masuk ke akun Anda' }}</p>
      </div>

      <form @submit.prevent="handleSubmit" class="login-form">
        <div class="form-group" v-if="isRegister">
          <label class="form-label">Nama Lengkap</label>
          <input type="text" class="form-input" v-model="form.nama" placeholder="Agus Risky" />
        </div>
        <div class="form-group">
          <label class="form-label">Email</label>
          <input type="email" class="form-input" v-model="form.email" placeholder="admin@rentalken.id" />
        </div>
        <div class="form-group">
          <label class="form-label">Password</label>
          <input type="password" class="form-input" v-model="form.password" placeholder="••••••••" />
        </div>
        
        <button type="submit" class="btn-primary" style="width: 100%; justify-content: center; padding: 12px; margin-top: 16px;" :disabled="isLoading">
          <i class="fa-solid fa-circle-notch fa-spin" v-if="isLoading"></i>
          <span v-else>{{ isRegister ? 'Daftar' : 'Masuk' }}</span>
        </button>
      </form>

      <div style="text-align: center; margin-top: 24px; font-size: 14px; color: var(--text-3);">
        {{ isRegister ? 'Sudah punya akun?' : 'Belum punya akun?' }}
        <a href="#" @click.prevent="isRegister = !isRegister" style="color: var(--primary); text-decoration: none; font-weight: 600;">
          {{ isRegister ? 'Masuk di sini' : 'Daftar sekarang' }}
        </a>
      </div>
    </div>
  </div>
</template>

<style scoped>
.login-wrapper {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--bg-body);
  background-image: radial-gradient(circle at top right, rgba(99, 102, 241, 0.15) 0%, transparent 40%),
                    radial-gradient(circle at bottom left, rgba(59, 130, 246, 0.1) 0%, transparent 40%);
}

.login-box {
  background: var(--bg-card);
  border: 1px solid var(--border-color);
  padding: 40px;
  border-radius: 24px;
  width: 100%;
  max-width: 420px;
  box-shadow: 0 20px 40px rgba(0,0,0,0.2);
  backdrop-filter: blur(10px);
}

.login-header {
  text-align: center;
  margin-bottom: 32px;
}
</style>
