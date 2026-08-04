<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import { useAuthStore } from '../../stores/auth';
import { 
  HomeIcon, 
  TruckIcon, 
  UsersIcon, 
  ClipboardDocumentListIcon, 
  ArrowPathRoundedSquareIcon,
  BanknotesIcon,
  XMarkIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
  isOpen: Boolean,
});

const emit = defineEmits(['close']);

const route = useRoute();
const authStore = useAuthStore();

const navItems = [
  { name: 'Dashboard', path: '/', icon: HomeIcon },
  { name: 'Kendaraan', path: '/kendaraan', icon: TruckIcon },
  { name: 'Pelanggan', path: '/pelanggan', icon: UsersIcon },
  { name: 'Transaksi', path: '/transaksi', icon: ClipboardDocumentListIcon },
  { name: 'Pengembalian', path: '/pengembalian', icon: ArrowPathRoundedSquareIcon },
  { name: 'Pembayaran', path: '/pembayaran', icon: BanknotesIcon },
  { name: 'Users', path: '/users', icon: UsersIcon },
];

const isCurrentRoute = (path) => {
  if (path === '/') return route.path === '/';
  return route.path.startsWith(path);
};
</script>

<template>
  <div>
    <!-- Mobile overlay -->
    <div 
      v-if="isOpen" 
      @click="emit('close')"
      class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-40 lg:hidden transition-opacity"
    ></div>

    <!-- Sidebar -->
    <aside 
      :class="[
        'fixed top-0 left-0 z-50 h-screen w-64 bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800 transition-transform duration-300 ease-in-out flex flex-col',
        isOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
      ]"
    >
      <div class="h-16 flex items-center justify-between px-6 border-b border-gray-200 dark:border-gray-800">
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center">
            <TruckIcon class="w-5 h-5 text-white" />
          </div>
          <span class="text-lg font-bold text-gray-900 dark:text-white">RentalKu</span>
        </div>
        <button @click="emit('close')" class="lg:hidden p-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg">
          <XMarkIcon class="w-5 h-5" />
        </button>
      </div>

      <div class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
        <router-link 
          v-for="item in navItems" 
          :key="item.path" 
          :to="item.path"
          class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 group relative"
          :class="isCurrentRoute(item.path) 
            ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 font-semibold' 
            : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-gray-100'"
        >
          <div 
            v-if="isCurrentRoute(item.path)"
            class="absolute left-0 w-1 h-6 bg-blue-600 rounded-r-full"
          ></div>
          <component :is="item.icon" class="w-5 h-5 flex-shrink-0 transition-transform group-hover:scale-110" />
          <span class="text-sm">{{ item.name }}</span>
        </router-link>
      </div>

      <div class="p-4 border-t border-gray-200 dark:border-gray-800">
        <div class="flex items-center gap-3 px-3 py-2">
          <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-blue-500 to-purple-500 flex items-center justify-center text-white font-bold shadow-md">
            {{ authStore.user?.username?.charAt(0).toUpperCase() || 'U' }}
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
              {{ authStore.user?.username || 'User' }}
            </p>
            <p class="text-xs text-gray-500 truncate">Admin</p>
          </div>
        </div>
      </div>
    </aside>
  </div>
</template>
