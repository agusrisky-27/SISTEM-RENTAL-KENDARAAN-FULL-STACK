<script setup>
import { ref } from 'vue';
import { 
  UsersIcon, 
  TruckIcon, 
  BanknotesIcon, 
  ClipboardDocumentCheckIcon,
  ArrowTrendingUpIcon
} from '@heroicons/vue/24/outline';

const stats = ref([
  { name: 'Total Kendaraan', value: '24', icon: TruckIcon, color: 'text-blue-600', bg: 'bg-blue-100 dark:bg-blue-900/30' },
  { name: 'Total Pelanggan', value: '142', icon: UsersIcon, color: 'text-purple-600', bg: 'bg-purple-100 dark:bg-purple-900/30' },
  { name: 'Total Pendapatan', value: 'Rp 12.5M', icon: BanknotesIcon, color: 'text-green-600', bg: 'bg-green-100 dark:bg-green-900/30' },
  { name: 'Transaksi Aktif', value: '8', icon: ClipboardDocumentCheckIcon, color: 'text-orange-600', bg: 'bg-orange-100 dark:bg-orange-900/30' },
]);

const recentTransactions = ref([
  { id: 'TRX-001', customer: 'Budi Santoso', vehicle: 'Toyota Avanza', date: '2023-10-25', status: 'Selesai', amount: 'Rp 450.000' },
  { id: 'TRX-002', customer: 'Siti Aminah', vehicle: 'Honda Brio', date: '2023-10-26', status: 'Aktif', amount: 'Rp 300.000' },
  { id: 'TRX-003', customer: 'Andi Wijaya', vehicle: 'Mitsubishi Xpander', date: '2023-10-26', status: 'Pending', amount: 'Rp 500.000' },
  { id: 'TRX-004', customer: 'Dewi Lestari', vehicle: 'Suzuki Ertiga', date: '2023-10-27', status: 'Aktif', amount: 'Rp 400.000' },
]);

const getStatusColor = (status) => {
  switch (status) {
    case 'Selesai': return 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400';
    case 'Aktif': return 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400';
    case 'Pending': return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400';
    default: return 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-400';
  }
};
</script>

<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Dashboard Overview</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Ringkasan performa penyewaan kendaraan Anda.</p>
      </div>
      <button class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
        <ArrowTrendingUpIcon class="w-5 h-5" />
        Generate Report
      </button>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <div 
        v-for="stat in stats" 
        :key="stat.name"
        class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow group"
      >
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ stat.name }}</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">{{ stat.value }}</p>
          </div>
          <div :class="[stat.bg, stat.color, 'w-12 h-12 rounded-xl flex items-center justify-center transition-transform group-hover:scale-110']">
            <component :is="stat.icon" class="w-6 h-6" />
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Transactions Table -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
      <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Transaksi Terbaru</h3>
        <button class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300">
          Lihat Semua
        </button>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-left text-sm whitespace-nowrap">
          <thead class="bg-gray-50 dark:bg-gray-900/50 text-gray-600 dark:text-gray-400">
            <tr>
              <th scope="col" class="px-6 py-4 font-medium">ID Transaksi</th>
              <th scope="col" class="px-6 py-4 font-medium">Pelanggan</th>
              <th scope="col" class="px-6 py-4 font-medium">Kendaraan</th>
              <th scope="col" class="px-6 py-4 font-medium">Tanggal</th>
              <th scope="col" class="px-6 py-4 font-medium">Total</th>
              <th scope="col" class="px-6 py-4 font-medium">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 dark:divide-gray-700 text-gray-700 dark:text-gray-300">
            <tr v-for="trx in recentTransactions" :key="trx.id" class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
              <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ trx.id }}</td>
              <td class="px-6 py-4">{{ trx.customer }}</td>
              <td class="px-6 py-4">{{ trx.vehicle }}</td>
              <td class="px-6 py-4">{{ trx.date }}</td>
              <td class="px-6 py-4 font-medium">{{ trx.amount }}</td>
              <td class="px-6 py-4">
                <span :class="['px-2.5 py-1 rounded-full text-xs font-medium', getStatusColor(trx.status)]">
                  {{ trx.status }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>
