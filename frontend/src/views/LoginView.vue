<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import { UserIcon, LockClosedIcon, ArrowRightOnRectangleIcon, ExclamationCircleIcon } from '@heroicons/vue/24/outline';

const router = useRouter();
const authStore = useAuthStore();

const username = ref('');
const password = ref('');

const handleLogin = async () => {
  if (!username.value || !password.value) {
    authStore.error = "Mohon isi username dan password.";
    return;
  }
  
  const success = await authStore.login({
    username: username.value,
    password: password.value,
  });

  if (success) {
    router.push('/');
  }
};
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100 dark:bg-gray-900 px-4 transition-colors duration-300 relative overflow-hidden">
    <!-- Decorative background elements -->
    <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-blue-500 rounded-full mix-blend-multiply filter blur-[128px] opacity-50 dark:opacity-30 animate-blob"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-96 h-96 bg-purple-500 rounded-full mix-blend-multiply filter blur-[128px] opacity-50 dark:opacity-30 animate-blob animation-delay-2000"></div>

    <div class="w-full max-w-md bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-3xl shadow-2xl overflow-hidden border border-gray-200 dark:border-gray-700 z-10 p-8 sm:p-10 transform transition-all duration-500">
      
      <!-- Header -->
      <div class="text-center mb-8">
        <div class="mx-auto w-16 h-16 bg-blue-600 rounded-2xl flex items-center justify-center mb-6 shadow-lg shadow-blue-500/30">
          <ArrowRightOnRectangleIcon class="w-8 h-8 text-white" />
        </div>
        <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Selamat Datang</h2>
        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Silakan login untuk mengakses Admin Panel</p>
      </div>

      <!-- Error Message -->
      <div v-if="authStore.error" class="mb-6 p-4 rounded-xl bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 flex items-start gap-3">
        <ExclamationCircleIcon class="w-5 h-5 text-red-600 dark:text-red-400 mt-0.5 shrink-0" />
        <p class="text-sm text-red-700 dark:text-red-300">{{ authStore.error }}</p>
      </div>

      <!-- Form -->
      <form @submit.prevent="handleLogin" class="space-y-6">
        <div>
          <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Username</label>
          <div class="relative group">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
              <UserIcon class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors" />
            </div>
            <input 
              id="username" 
              v-model="username"
              type="text" 
              required
              class="block w-full pl-11 pr-4 py-3 bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all sm:text-sm outline-none"
              placeholder="Masukkan username Anda"
            />
          </div>
        </div>

        <div>
          <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Password</label>
          <div class="relative group">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
              <LockClosedIcon class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors" />
            </div>
            <input 
              id="password" 
              v-model="password"
              type="password" 
              required
              class="block w-full pl-11 pr-4 py-3 bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all sm:text-sm outline-none"
              placeholder="••••••••"
            />
          </div>
        </div>

        <div class="pt-2">
          <button 
            type="submit" 
            :disabled="authStore.loading"
            class="group relative w-full flex justify-center py-3 px-4 border border-transparent rounded-xl text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all overflow-hidden shadow-lg shadow-blue-500/30 disabled:opacity-70 disabled:cursor-not-allowed"
          >
            <span class="absolute right-0 inset-y-0 flex items-center pr-3">
              <ArrowRightOnRectangleIcon 
                v-if="!authStore.loading"
                class="h-5 w-5 text-blue-500 group-hover:text-blue-400 transition ease-in-out duration-150" 
              />
              <svg v-else class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
            </span>
            {{ authStore.loading ? 'Memproses...' : 'Masuk' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<style scoped>
@keyframes blob {
  0% { transform: translate(0px, 0px) scale(1); }
  33% { transform: translate(30px, -50px) scale(1.1); }
  66% { transform: translate(-20px, 20px) scale(0.9); }
  100% { transform: translate(0px, 0px) scale(1); }
}
.animate-blob {
  animation: blob 7s infinite;
}
.animation-delay-2000 {
  animation-delay: 2s;
}
</style>
