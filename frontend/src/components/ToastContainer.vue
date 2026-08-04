<script setup>
import { useToast } from '../utils/toast'

const { toasts, removeToast } = useToast()

const getIcon = (type) => {
  return type === 'success' ? 'fa-circle-check' : type === 'error' ? 'fa-circle-xmark' : 'fa-circle-info'
}
</script>

<template>
  <div id="toast-container">
    <div 
      v-for="t in toasts" 
      :key="t.id" 
      class="toast" 
      :class="'toast-' + t.type"
    >
      <div class="toast-icon">
        <i class="fa-solid" :class="getIcon(t.type)"></i>
      </div>
      <div class="toast-msg">{{ t.msg }}</div>
    </div>
  </div>
</template>
