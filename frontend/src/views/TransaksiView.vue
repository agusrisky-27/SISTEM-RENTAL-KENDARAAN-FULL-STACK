<script setup>
import { 
  ClipboardDocumentListIcon
} from '@heroicons/vue/24/outline';
</script>

<template>
  <div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Data Transaksi</h1>
        <p class="mt-2 text-base text-gray-500 dark:text-gray-400">Halaman ini sedang dalam tahap pengembangan.</p>
      </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden flex flex-col items-center justify-center py-20 min-h-[400px]">
      <div class="w-20 h-20 bg-blue-50 dark:bg-blue-900/30 rounded-full flex items-center justify-center mb-6">
        <ClipboardDocumentListIcon class="w-10 h-10 text-blue-600 dark:text-blue-400" />
      </div>
      <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Segera Hadir</h3>
      <p class="text-base text-gray-500 dark:text-gray-400 mt-2 text-center max-w-md">Modul manajemen transaksi sedang dibangun. Semua logika penyewaan dan pengembalian akan ada di sini.</p>
    </div>
  </div>
</template>
