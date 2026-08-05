<script setup>
import { computed } from 'vue';
import BaseModal from './BaseModal.vue';

const props = defineProps({
  isOpen: Boolean,
  title: String,
  message: String,
  emoji: {
    type: String,
    default: '⚠️'
  },
  confirmText: {
    type: String,
    default: 'Hapus'
  },
  cancelText: {
    type: String,
    default: 'Batal'
  },
  isDestructive: {
    type: Boolean,
    default: true
  },
  isLoading: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['close', 'confirm']);

const confirmButtonClass = computed(() => {
  return props.isDestructive 
    ? 'bg-red-600 hover:bg-red-700 text-white focus:ring-red-500' 
    : 'btn-primary';
});
</script>

<template>
  <BaseModal 
    :is-open="isOpen"
    @close="emit('close')"
  >
    <div class="flex flex-col items-center text-center py-4">
      <div class="text-5xl mb-4 select-none">{{ emoji }}</div>
      <h3 class="text-xl font-heading font-bold text-light-text dark:text-dark-text mb-2">
        {{ title }}
      </h3>
      <p class="text-light-muted dark:text-dark-muted">
        {{ message }}
      </p>
    </div>

    <template #footer>
      <button 
        class="btn-ghost" 
        @click="emit('close')"
        :disabled="isLoading"
      >
        {{ cancelText }}
      </button>
      <button 
        class="btn px-5" 
        :class="confirmButtonClass"
        @click="emit('confirm')"
        :disabled="isLoading"
      >
        <font-awesome-icon v-if="isLoading" icon="spinner" class="fa-spin mr-2" />
        {{ confirmText }}
      </button>
    </template>
  </BaseModal>
</template>
