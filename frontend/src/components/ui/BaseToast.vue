<script setup>
import { useToast } from '../../composables/useToast';

const { toasts, removeToast } = useToast();

const getIcon = (type) => {
  switch (type) {
    case 'success': return 'check';
    case 'error': return 'exclamation-triangle';
    case 'info':
    default: return 'info-circle';
  }
};

const getToastClass = (type) => {
  switch (type) {
    case 'success': return 'border-l-4 border-l-green-500 bg-green-50 dark:bg-green-900/20 text-green-800 dark:text-green-300';
    case 'error': return 'border-l-4 border-l-red-500 bg-red-50 dark:bg-red-900/20 text-red-800 dark:text-red-300';
    case 'info':
    default: return 'border-l-4 border-l-blue-500 bg-blue-50 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300';
  }
};
</script>

<template>
  <div class="fixed bottom-4 right-4 z-[100] flex flex-col gap-2 pointer-events-none">
    <transition-group name="toast-slide">
      <div 
        v-for="toast in toasts" 
        :key="toast.id"
        :class="[
          'pointer-events-auto min-w-[280px] max-w-sm w-full bg-light-card dark:bg-dark-card shadow-lg rounded-lg p-4 flex items-start gap-3 border border-light-border dark:border-dark-border overflow-hidden',
          getToastClass(toast.type)
        ]"
      >
        <font-awesome-icon :icon="getIcon(toast.type)" class="mt-0.5" />
        <div class="flex-1 text-sm font-medium">
          {{ toast.message }}
        </div>
        <button 
          @click="removeToast(toast.id)" 
          class="opacity-60 hover:opacity-100 transition-opacity"
        >
          <font-awesome-icon icon="times" />
        </button>
      </div>
    </transition-group>
  </div>
</template>

<style scoped>
.toast-slide-enter-active,
.toast-slide-leave-active {
  transition: all 0.3s cubic-bezier(0.23, 1, 0.32, 1);
}
.toast-slide-enter-from {
  opacity: 0;
  transform: translateX(100%);
}
.toast-slide-leave-to {
  opacity: 0;
  transform: translateX(100%) scale(0.9);
}
</style>
