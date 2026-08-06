<script setup>
import { ref } from 'vue';
import { 
  UsersIcon, 
  TruckIcon, 
  BanknotesIcon, 
  ClipboardDocumentCheckIcon,
  ArrowTrendingUpIcon,
  SparklesIcon
} from '@heroicons/vue/24/outline';

const stats = ref([
  { name: 'Total Kendaraan', value: '24', icon: TruckIcon, color: 'text-blue-500', bg: 'bg-blue-500/10', border: 'border-blue-500/20' },
  { name: 'Total Pelanggan', value: '142', icon: UsersIcon, color: 'text-purple-500', bg: 'bg-purple-500/10', border: 'border-purple-500/20' },
  { name: 'Total Pendapatan', value: 'Rp 12.5M', icon: BanknotesIcon, color: 'text-emerald-500', bg: 'bg-emerald-500/10', border: 'border-emerald-500/20' },
  { name: 'Transaksi Aktif', value: '8', icon: ClipboardDocumentCheckIcon, color: 'text-amber-500', bg: 'bg-amber-500/10', border: 'border-amber-500/20' },
]);

const recentTransactions = ref([
  { id: 'TRX-001', customer: 'Budi Santoso', vehicle: 'Toyota Avanza', date: '2023-10-25', status: 'Selesai', amount: 'Rp 450.000' },
  { id: 'TRX-002', customer: 'Siti Aminah', vehicle: 'Honda Brio', date: '2023-10-26', status: 'Aktif', amount: 'Rp 300.000' },
  { id: 'TRX-003', customer: 'Andi Wijaya', vehicle: 'Mitsubishi Xpander', date: '2023-10-26', status: 'Pending', amount: 'Rp 500.000' },
  { id: 'TRX-004', customer: 'Dewi Lestari', vehicle: 'Suzuki Ertiga', date: '2023-10-27', status: 'Aktif', amount: 'Rp 400.000' },
]);

const getStatusColor = (status) => {
  switch (status) {
    case 'Selesai': return 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20';
    case 'Aktif': return 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20';
    case 'Pending': return 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20';
    default: return 'bg-gray-500/10 text-gray-600 dark:text-gray-400 border border-gray-500/20';
  }
};
</script>

<template>
  <div class="space-y-8 animate-fade-in pb-10">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-6 relative z-10">
      <div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/10 border border-indigo-500/20 text-indigo-600 dark:text-indigo-400 text-xs font-semibold tracking-wider uppercase mb-3">
          <SparklesIcon class="w-3.5 h-3.5" /> Overview
        </div>
        <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight font-heading">Dashboard Analytics</h1>
        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 font-medium">Ringkasan performa penyewaan kendaraan hari ini.</p>
      </div>
      <button class="group relative inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white text-sm font-bold rounded-xl transition-all shadow-[0_0_20px_rgba(79,70,229,0.2)] hover:shadow-[0_0_30px_rgba(79,70,229,0.4)] overflow-hidden">
        <div class="absolute inset-0 w-full h-full bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-300 ease-in-out"></div>
        <ArrowTrendingUpIcon class="w-5 h-5 relative z-10 group-hover:-translate-y-0.5 transition-transform" />
        <span class="relative z-10">Generate Report</span>
      </button>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <div 
        v-for="(stat, idx) in stats" 
        :key="stat.name"
        class="relative bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-2xl p-6 border border-gray-100 dark:border-gray-700/50 shadow-sm hover:shadow-xl hover:scale-[1.02] transition-all duration-300 group overflow-hidden"
        :style="{ animationDelay: `${idx * 100}ms` }"
      >
        <!-- Subtle gradient background -->
        <div class="absolute inset-0 bg-gradient-to-br from-white/40 to-transparent dark:from-white/5 opacity-50 pointer-events-none"></div>
        
        <div class="relative z-10 flex items-center justify-between">
          <div>
            <p class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ stat.name }}</p>
            <p class="text-3xl font-black text-gray-900 dark:text-white mt-2 font-heading tracking-tight">{{ stat.value }}</p>
          </div>
          <div :class="[stat.bg, stat.color, stat.border, 'w-14 h-14 rounded-2xl flex items-center justify-center transition-all duration-500 group-hover:rotate-6 group-hover:scale-110 shadow-inner']">
            <component :is="stat.icon" class="w-7 h-7 stroke-[1.5]" />
          </div>
        </div>
        
        <!-- Decorative blob -->
        <div :class="[stat.bg, 'absolute -bottom-6 -right-6 w-24 h-24 rounded-full blur-2xl opacity-50 group-hover:opacity-100 transition-opacity duration-500']"></div>
      </div>
    </div>

    <!-- Recent Transactions Table -->
    <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-3xl border border-gray-100 dark:border-gray-700/50 shadow-lg overflow-hidden relative">
      <div class="px-8 py-6 border-b border-gray-100 dark:border-gray-700/50 flex items-center justify-between bg-gray-50/50 dark:bg-gray-900/20">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
          <span class="w-2 h-6 rounded-full bg-indigo-500 block"></span>
          Transaksi Terbaru
        </h3>
        <button class="text-sm font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 transition-colors flex items-center gap-1 group">
          Lihat Semua 
          <span class="transform transition-transform group-hover:translate-x-1">→</span>
        </button>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-left text-sm whitespace-nowrap">
          <thead class="bg-transparent text-gray-500 dark:text-gray-400 font-semibold tracking-wider uppercase text-xs">
            <tr>
              <th scope="col" class="px-8 py-5">ID Transaksi</th>
              <th scope="col" class="px-8 py-5">Pelanggan</th>
              <th scope="col" class="px-8 py-5">Kendaraan</th>
              <th scope="col" class="px-8 py-5">Tanggal</th>
              <th scope="col" class="px-8 py-5">Total</th>
              <th scope="col" class="px-8 py-5">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 dark:divide-gray-800/60 text-gray-700 dark:text-gray-300">
            <tr v-for="trx in recentTransactions" :key="trx.id" class="hover:bg-gray-50/80 dark:hover:bg-gray-700/30 transition-colors group cursor-pointer">
              <td class="px-8 py-5 font-bold text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">{{ trx.id }}</td>
              <td class="px-8 py-5 font-medium">{{ trx.customer }}</td>
              <td class="px-8 py-5">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-gray-100 dark:bg-gray-700/50 text-gray-600 dark:text-gray-300 text-xs font-medium border border-gray-200 dark:border-gray-600/50">
                  <TruckIcon class="w-3.5 h-3.5" />
                  {{ trx.vehicle }}
                </span>
              </td>
              <td class="px-8 py-5 text-gray-500 dark:text-gray-400">{{ trx.date }}</td>
              <td class="px-8 py-5 font-bold text-gray-900 dark:text-white">{{ trx.amount }}</td>
              <td class="px-8 py-5">
                <span :class="['px-3 py-1 rounded-full text-[11px] font-bold tracking-wide uppercase shadow-sm', getStatusColor(trx.status)]">
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

<style scoped>
@keyframes fade-in {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
  animation: fade-in 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

/* Base stagger effect for children in stats grid */
.grid > div {
  animation: fade-in 0.6s cubic-bezier(0.16, 1, 0.3, 1) both;
}
</style>
