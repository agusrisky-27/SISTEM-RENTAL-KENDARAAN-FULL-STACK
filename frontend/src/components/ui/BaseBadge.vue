<script setup>
import { computed } from 'vue';

const props = defineProps({
  type: {
    type: String,
    default: 'gray', // success, danger, warning, info, gray
  },
  label: {
    type: String,
    required: true,
  },
  icon: {
    type: String,
    default: null,
  }
});

const colorClasses = computed(() => {
  switch (props.type) {
    case 'success':
      return 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 border-green-200 dark:border-green-800';
    case 'danger':
      return 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 border-red-200 dark:border-red-800';
    case 'warning':
      return 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 border-yellow-200 dark:border-yellow-800';
    case 'info':
      return 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 border-blue-200 dark:border-blue-800';
    case 'gray':
    default:
      return 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300 border-gray-200 dark:border-gray-700';
  }
});
</script>

<template>
  <span 
    :class="[
      'inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold border',
      colorClasses
    ]"
  >
    <font-awesome-icon v-if="icon" :icon="icon" class="text-[10px]" />
    {{ label }}
  </span>
</template>
