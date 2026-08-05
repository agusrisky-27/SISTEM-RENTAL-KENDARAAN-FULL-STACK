<script setup>
import { onMounted, onUnmounted } from 'vue';

const props = defineProps({
  isOpen: Boolean,
  title: String,
  subtitle: String,
  icon: String, // fontawesome icon
  iconColor: {
    type: String,
    default: 'text-accent bg-accent/10',
  }
});

const emit = defineEmits(['close']);

const close = () => {
  emit('close');
};

const handleKeydown = (e) => {
  if (e.key === 'Escape' && props.isOpen) {
    close();
  }
};

onMounted(() => {
  document.addEventListener('keydown', handleKeydown);
});

onUnmounted(() => {
  document.removeEventListener('keydown', handleKeydown);
});
</script>

<template>
  <transition name="modal-fade">
    <div 
      v-if="isOpen" 
      class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6"
    >
      <!-- Backdrop -->
      <div 
        class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" 
        @click="close"
      ></div>

      <!-- Modal Panel -->
      <transition name="modal-scale">
        <div 
          v-if="isOpen"
          class="relative w-full max-w-lg bg-light-card dark:bg-dark-card rounded-2xl shadow-2xl border border-light-border dark:border-dark-border overflow-hidden flex flex-col max-h-[90vh]"
        >
          <!-- Close Button -->
          <button 
            @click="close" 
            class="absolute top-4 right-4 p-2 text-light-muted dark:text-dark-muted hover:bg-gray-100 dark:hover:bg-dark-border rounded-full transition-colors z-10"
          >
            <font-awesome-icon icon="times" />
          </button>

          <!-- Header -->
          <div class="px-6 py-5 border-b border-light-border dark:border-dark-border flex items-start gap-4">
            <div v-if="icon" :class="['w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0 text-xl', iconColor]">
              <font-awesome-icon :icon="icon" />
            </div>
            <div>
              <h3 class="text-xl font-heading font-bold text-light-text dark:text-dark-text">
                {{ title }}
              </h3>
              <p v-if="subtitle" class="mt-1 text-sm text-light-muted dark:text-dark-muted">
                {{ subtitle }}
              </p>
            </div>
          </div>

          <!-- Body -->
          <div class="p-6 overflow-y-auto custom-scrollbar flex-1">
            <slot></slot>
          </div>

          <!-- Footer -->
          <div v-if="$slots.footer" class="px-6 py-4 border-t border-light-border dark:border-dark-border bg-gray-50/50 dark:bg-dark-bg/50 flex justify-end gap-3 rounded-b-2xl">
            <slot name="footer"></slot>
          </div>
        </div>
      </transition>
    </div>
  </transition>
</template>

<style scoped>
.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 0.3s ease;
}
.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
}

.modal-scale-enter-active {
  transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.modal-scale-leave-active {
  transition: all 0.2s ease-in;
}
.modal-scale-enter-from {
  opacity: 0;
  transform: scale(0.95) translateY(10px);
}
.modal-scale-leave-to {
  opacity: 0;
  transform: scale(0.95) translateY(10px);
}

.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(156, 163, 175, 0.5);
  border-radius: 10px;
}
</style>
