<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth';
import { useTheme } from '../../composables/useTheme';
import { 
  Bars3Icon, 
  SunIcon, 
  MoonIcon,
  ArrowRightOnRectangleIcon,
  BellIcon
} from '@heroicons/vue/24/outline';
import { Menu, MenuButton, MenuItems, MenuItem } from '@headlessui/vue';

const emit = defineEmits(['toggleSidebar']);

const router = useRouter();
const authStore = useAuthStore();
const { isDark, toggleTheme } = useTheme();

const handleLogout = () => {
  authStore.logout();
  router.push('/login');
};
</script>

<template>
  <header class="h-16 bg-white/80 dark:bg-gray-900/80 backdrop-blur-md border-b border-gray-200 dark:border-gray-800 flex items-center justify-between px-4 lg:px-6 sticky top-0 z-30 transition-colors">
    <div class="flex items-center gap-4">
      <button 
        @click="emit('toggleSidebar')"
        class="lg:hidden p-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors"
      >
        <Bars3Icon class="w-6 h-6" />
      </button>
      
      <!-- Search Placeholder (Opsional) -->
      <div class="hidden sm:block relative">
        <input 
          type="text" 
          placeholder="Cari..." 
          class="w-64 pl-4 pr-10 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all dark:text-gray-200"
        >
        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
          <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
          </svg>
        </div>
      </div>
    </div>

    <div class="flex items-center gap-2 sm:gap-4">
      <button 
        class="p-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-full transition-colors relative"
      >
        <BellIcon class="w-5 h-5" />
        <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full"></span>
      </button>

      <button 
        @click="toggleTheme"
        class="p-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-full transition-colors"
      >
        <SunIcon v-if="isDark" class="w-5 h-5" />
        <MoonIcon v-else class="w-5 h-5" />
      </button>

      <Menu as="div" class="relative inline-block text-left">
        <div>
          <MenuButton class="flex items-center gap-2 p-1 pr-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
            <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-blue-500 to-purple-500 flex items-center justify-center text-white text-sm font-bold shadow-sm">
              {{ authStore.user?.username?.charAt(0).toUpperCase() || 'U' }}
            </div>
            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
          </MenuButton>
        </div>

        <transition
          enter-active-class="transition duration-100 ease-out"
          enter-from-class="transform scale-95 opacity-0"
          enter-to-class="transform scale-100 opacity-100"
          leave-active-class="transition duration-75 ease-in"
          leave-from-class="transform scale-100 opacity-100"
          leave-to-class="transform scale-95 opacity-0"
        >
          <MenuItems class="absolute right-0 mt-2 w-48 origin-top-right divide-y divide-gray-100 dark:divide-gray-800 rounded-xl bg-white dark:bg-gray-900 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none overflow-hidden border border-gray-200 dark:border-gray-800">
            <div class="px-4 py-3">
              <p class="text-sm text-gray-900 dark:text-white">Login sebagai</p>
              <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ authStore.user?.username || 'Admin' }}</p>
            </div>
            <div class="p-1">
              <MenuItem v-slot="{ active }">
                <button
                  @click="handleLogout"
                  :class="[
                    active ? 'bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400' : 'text-gray-700 dark:text-gray-300',
                    'group flex w-full items-center gap-2 rounded-lg px-3 py-2 text-sm transition-colors'
                  ]"
                >
                  <ArrowRightOnRectangleIcon class="w-5 h-5" />
                  Logout
                </button>
              </MenuItem>
            </div>
          </MenuItems>
        </transition>
      </Menu>
    </div>
  </header>
</template>
