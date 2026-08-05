<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import AppLayout from './components/layout/AppLayout.vue';
import BaseToast from './components/ui/BaseToast.vue';

const route = useRoute();

const isAuthRoute = computed(() => route.meta.requiresAuth);
</script>

<template>
  <template v-if="isAuthRoute">
    <AppLayout>
      <router-view v-slot="{ Component }">
        <transition 
          name="fade" 
          mode="out-in"
        >
          <component :is="Component" />
        </transition>
      </router-view>
    </AppLayout>
  </template>
  <template v-else>
    <router-view />
  </template>
  <BaseToast />
</template>

<style>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
