<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import { UserIcon, LockClosedIcon, ArrowRightOnRectangleIcon, ExclamationCircleIcon, SparklesIcon } from '@heroicons/vue/24/outline';

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
  <div class="min-h-screen flex items-center justify-center bg-[#0a0f1c] px-4 sm:px-6 lg:px-8 relative overflow-hidden font-sans">
    
    <!-- Premium Dynamic Background -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
      <div class="absolute -top-[20%] -left-[10%] w-[70vw] h-[70vw] rounded-full bg-gradient-to-br from-indigo-600/20 via-purple-600/10 to-transparent blur-3xl animate-slow-spin mix-blend-screen"></div>
      <div class="absolute -bottom-[20%] -right-[10%] w-[60vw] h-[60vw] rounded-full bg-gradient-to-tl from-blue-600/20 via-cyan-600/10 to-transparent blur-3xl animate-slow-spin-reverse mix-blend-screen"></div>
      <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMiIgY3k9IjIiIHI9IjEiIGZpbGw9InJnYmEoMjU1LDI1NSwyNTUsMC4wMykiLz48L3N2Zz4=')] opacity-50"></div>
    </div>

    <!-- Login Card Container -->
    <div class="w-full max-w-md relative z-10 perspective-1000">
      
      <!-- Card Element -->
      <div class="bg-white/5 backdrop-blur-2xl rounded-3xl shadow-2xl shadow-indigo-500/10 border border-white/10 overflow-hidden transform transition-all duration-700 hover:scale-[1.02] group p-8 sm:p-12">
        
        <!-- Animated border gradient -->
        <div class="absolute inset-0 bg-gradient-to-r from-indigo-500/20 to-purple-500/20 opacity-0 group-hover:opacity-100 transition-opacity duration-700 pointer-events-none"></div>

        <!-- Header -->
        <div class="text-center mb-10 relative">
          <div class="mx-auto w-20 h-20 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center mb-6 shadow-lg shadow-indigo-500/30 transform transition-transform duration-500 group-hover:rotate-12 relative overflow-hidden">
            <div class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-500 ease-in-out"></div>
            <SparklesIcon class="w-10 h-10 text-white relative z-10" />
          </div>
          <h2 class="text-3xl font-bold text-white tracking-tight font-heading">Sistem Admin</h2>
          <p class="mt-2 text-sm text-indigo-200/70 font-medium">Masuk untuk mengelola rental kendaraan</p>
        </div>

        <!-- Error Message -->
        <transition name="fade-slide">
          <div v-if="authStore.error" class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20 flex items-start gap-3 backdrop-blur-md">
            <ExclamationCircleIcon class="w-5 h-5 text-red-400 mt-0.5 shrink-0" />
            <p class="text-sm text-red-200">{{ authStore.error }}</p>
          </div>
        </transition>

        <!-- Form -->
        <form @submit.prevent="handleLogin" class="space-y-6 relative z-10">
          
          <!-- Username Field -->
          <div class="space-y-2">
            <label for="username" class="block text-sm font-medium text-indigo-100/80 tracking-wide">Username</label>
            <div class="relative group/input">
              <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none transition-transform duration-300 group-focus-within/input:scale-110">
                <UserIcon class="h-5 w-5 text-indigo-300/50 group-focus-within/input:text-indigo-400 transition-colors" />
              </div>
              <input 
                id="username" 
                v-model="username"
                type="text" 
                required
                class="block w-full pl-11 pr-4 py-3.5 bg-black/20 border border-white/5 rounded-xl text-white placeholder-indigo-200/30 focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500/50 transition-all sm:text-sm outline-none backdrop-blur-sm shadow-inner"
                placeholder="Masukkan username"
              />
            </div>
          </div>

          <!-- Password Field -->
          <div class="space-y-2">
            <label for="password" class="block text-sm font-medium text-indigo-100/80 tracking-wide">Password</label>
            <div class="relative group/input">
              <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none transition-transform duration-300 group-focus-within/input:scale-110">
                <LockClosedIcon class="h-5 w-5 text-indigo-300/50 group-focus-within/input:text-indigo-400 transition-colors" />
              </div>
              <input 
                id="password" 
                v-model="password"
                type="password" 
                required
                class="block w-full pl-11 pr-4 py-3.5 bg-black/20 border border-white/5 rounded-xl text-white placeholder-indigo-200/30 focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500/50 transition-all sm:text-sm outline-none backdrop-blur-sm shadow-inner"
                placeholder="••••••••"
              />
            </div>
          </div>

          <!-- Submit Button -->
          <div class="pt-4">
            <button 
              type="submit" 
              :disabled="authStore.loading"
              class="relative w-full flex justify-center items-center gap-2 py-4 px-4 border border-transparent rounded-xl text-sm font-bold text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-[#0a0f1c] focus:ring-indigo-500 transition-all duration-300 shadow-[0_0_20px_rgba(79,70,229,0.3)] hover:shadow-[0_0_30px_rgba(79,70,229,0.5)] overflow-hidden group/btn disabled:opacity-70 disabled:cursor-not-allowed"
            >
              <div class="absolute inset-0 w-full h-full bg-white/20 translate-y-full group-hover/btn:translate-y-0 transition-transform duration-300 ease-in-out"></div>
              
              <span class="relative z-10 flex items-center gap-2">
                {{ authStore.loading ? 'Mengautentikasi...' : 'Masuk ke Dashboard' }}
                <ArrowRightOnRectangleIcon 
                  v-if="!authStore.loading"
                  class="h-5 w-5 transform transition-transform duration-300 group-hover/btn:translate-x-1" 
                />
                <svg v-else class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
              </span>
            </button>
          </div>
        </form>
      </div>
      
      <!-- Footer Text -->
      <p class="text-center mt-8 text-indigo-200/50 text-xs tracking-wider">
        © 2024 Sistem Rental Kendaraan. All rights reserved.
      </p>
    </div>
  </div>
</template>

<style scoped>
@keyframes slow-spin {
  0% { transform: rotate(0deg) scale(1); }
  50% { transform: rotate(180deg) scale(1.2); }
  100% { transform: rotate(360deg) scale(1); }
}

@keyframes slow-spin-reverse {
  0% { transform: rotate(360deg) scale(1); }
  50% { transform: rotate(180deg) scale(1.1); }
  100% { transform: rotate(0deg) scale(1); }
}

.animate-slow-spin {
  animation: slow-spin 25s infinite linear;
}

.animate-slow-spin-reverse {
  animation: slow-spin-reverse 30s infinite linear;
}

.fade-slide-enter-active,
.fade-slide-leave-active {
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.fade-slide-enter-from,
.fade-slide-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>
