<script setup>
import { computed, ref, onMounted, onUnmounted } from 'vue';
import { useRoute } from 'vue-router';
import { useAuthStore } from '../../stores/auth';
import { Menu, MenuButton, MenuItems, MenuItem } from '@headlessui/vue';

const props = defineProps({
  isOpen: Boolean,
  isCollapsed: Boolean
});

const emit = defineEmits(['close', 'toggle-collapse']);

const route = useRoute();
const authStore = useAuthStore();

const navItems = [
  { name: 'Dashboard', path: '/', icon: 'chart-pie' },
  { name: 'Kendaraan', path: '/kendaraan', icon: 'car-side' },
  { name: 'Pelanggan', path: '/pelanggan', icon: 'users' },
  { name: 'Transaksi', path: '/transaksi', icon: 'file-contract', badge: 3 },
  { name: 'Pengembalian', path: '/pengembalian', icon: 'rotate-left' },
  { name: 'Pembayaran', path: '/pembayaran', icon: 'wallet' },
  { name: 'Users', path: '/users', icon: 'user-shield' },
];

const isCurrentRoute = (path) => {
  if (path === '/') return route.path === '/';
  return route.path.startsWith(path);
};

const toggleCollapse = () => {
  emit('toggle-collapse');
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
        'fixed top-0 left-0 z-50 h-screen bg-light-card dark:bg-dark-card border-r border-light-border dark:border-dark-border transition-all duration-300 ease-in-out flex flex-col',
        isOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
        isCollapsed ? 'w-20' : 'w-64'
      ]"
    >
      <!-- Logo Section -->
      <div class="h-16 flex items-center justify-center px-4 border-b border-light-border dark:border-dark-border">
        <div class="text-accent flex items-center justify-center">
          <font-awesome-icon icon="car-side" class="text-2xl" />
        </div>
        <button @click="emit('close')" class="lg:hidden absolute right-4 p-2 text-light-muted dark:text-dark-muted hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg">
          <font-awesome-icon icon="times" />
        </button>
      </div>

      <!-- Navigation -->
      <div class="flex-1 overflow-y-auto py-6 px-3 space-y-1 custom-scrollbar">
        <router-link 
          v-for="item in navItems" 
          :key="item.path" 
          :to="item.path"
          class="flex items-center gap-3 px-3 py-3 rounded-xl transition-all duration-200 group relative"
          :class="isCurrentRoute(item.path) 
            ? 'bg-accent/10 text-accent font-semibold' 
            : 'text-light-muted dark:text-dark-muted hover:bg-gray-50 dark:hover:bg-dark-border hover:text-light-text dark:hover:text-dark-text'"
          :title="isCollapsed ? item.name : ''"
        >
          <!-- Active Indicator -->
          <div 
            v-if="isCurrentRoute(item.path)"
            class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-8 bg-accent rounded-r-full"
          ></div>
          
          <font-awesome-icon :icon="item.icon" class="w-5 h-5 flex-shrink-0 transition-transform group-hover:scale-110" :class="isCollapsed ? 'mx-auto' : ''" />
          
          <span v-show="!isCollapsed" class="text-sm tracking-wide flex-1">{{ item.name }}</span>
          
          <!-- Badge -->
          <span v-if="item.badge && !isCollapsed" class="bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm">
            {{ item.badge }}
          </span>
          <span v-else-if="item.badge && isCollapsed" class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full"></span>
        </router-link>
      </div>



      <!-- Footer User Info -->
      <div class="border-t border-light-border dark:border-dark-border p-3">
        <Menu as="div" class="relative">
          <MenuButton class="w-full flex items-center gap-3 p-2 rounded-xl hover:bg-gray-50 dark:hover:bg-dark-border transition-colors outline-none" :class="isCollapsed ? 'justify-center' : 'text-left'">
            <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-accent to-purple-500 flex flex-shrink-0 items-center justify-center text-white font-bold shadow-sm">
              {{ authStore.user?.username?.charAt(0).toUpperCase() || 'A' }}
            </div>
            <div v-show="!isCollapsed" class="flex-1 min-w-0">
              <p class="text-sm font-semibold text-light-text dark:text-dark-text truncate">
                {{ authStore.user?.username || 'Administrator' }}
              </p>
              <p class="text-xs text-light-muted dark:text-dark-muted truncate">Admin</p>
            </div>
          </MenuButton>

          <transition
            enter-active-class="transition duration-100 ease-out"
            enter-from-class="transform scale-95 opacity-0"
            enter-to-class="transform scale-100 opacity-100"
            leave-active-class="transition duration-75 ease-in"
            leave-from-class="transform scale-100 opacity-100"
            leave-to-class="transform scale-95 opacity-0"
          >
            <MenuItems class="absolute bottom-full left-0 mb-2 w-full origin-bottom divide-y divide-light-border dark:divide-dark-border rounded-xl bg-light-card dark:bg-dark-card shadow-lg ring-1 ring-black/5 focus:outline-none overflow-hidden border border-light-border dark:border-dark-border z-50 min-w-[200px]">
              <div class="p-1">
                <MenuItem v-slot="{ active }">
                  <button :class="[active ? 'bg-gray-50 dark:bg-dark-border text-light-text dark:text-dark-text' : 'text-light-muted dark:text-dark-muted', 'group flex w-full items-center gap-2 rounded-lg px-3 py-2 text-sm transition-colors']">
                    <font-awesome-icon icon="user" class="w-4 h-4" /> Edit Profil
                  </button>
                </MenuItem>
                <MenuItem v-slot="{ active }">
                  <button :class="[active ? 'bg-gray-50 dark:bg-dark-border text-light-text dark:text-dark-text' : 'text-light-muted dark:text-dark-muted', 'group flex w-full items-center gap-2 rounded-lg px-3 py-2 text-sm transition-colors']">
                    <font-awesome-icon icon="users" class="w-4 h-4" /> Ganti Akun
                  </button>
                </MenuItem>
              </div>
              <div class="p-1">
                <MenuItem v-slot="{ active }">
                  <button @click="authStore.logout()" :class="[active ? 'bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400' : 'text-red-500 dark:text-red-400', 'group flex w-full items-center gap-2 rounded-lg px-3 py-2 text-sm transition-colors']">
                    <font-awesome-icon icon="rotate-left" class="w-4 h-4" /> Keluar
                  </button>
                </MenuItem>
              </div>
            </MenuItems>
          </transition>
        </Menu>
      </div>
    </aside>
  </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(156, 163, 175, 0.3);
  border-radius: 4px;
}
</style>
