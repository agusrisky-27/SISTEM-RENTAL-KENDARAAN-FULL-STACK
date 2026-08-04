<script setup>
import { ref, onMounted } from 'vue'

const vehicles = ref(12)
const activeTransactions = ref(4)
const revenue = ref(8400000)
const customers = ref(27)

const formatRp = (n) => 'Rp ' + Number(n).toLocaleString('id-ID')
</script>

<template>
  <div class="page active">
    <div class="stats-grid">
      <div class="stat-card blue">
        <div class="sc-top">
          <div><div class="sc-label">Total Kendaraan</div></div>
          <div class="sc-icon ic-blue"><i class="fa-solid fa-car-side"></i></div>
        </div>
        <div class="sc-value">{{ vehicles }}</div>
        <div class="sc-sub">8 tersedia · 3 disewa · 1 servis</div>
        <div class="sc-change up"><i class="fa-solid fa-arrow-trend-up"></i> +2 unit bulan ini</div>
      </div>
      
      <div class="stat-card purple">
        <div class="sc-top">
          <div><div class="sc-label">Transaksi Aktif</div></div>
          <div class="sc-icon ic-purple"><i class="fa-solid fa-file-contract"></i></div>
        </div>
        <div class="sc-value">{{ activeTransactions }}</div>
        <div class="sc-sub">3 berjalan · 1 terlambat</div>
        <div class="sc-change down"><i class="fa-solid fa-triangle-exclamation"></i> 1 terlambat kembali</div>
      </div>

      <div class="stat-card green">
        <div class="sc-top">
          <div><div class="sc-label">Pendapatan Bulan Ini</div></div>
          <div class="sc-icon ic-green"><i class="fa-solid fa-sack-dollar"></i></div>
        </div>
        <div class="sc-value" style="font-size:20px">{{ formatRp(revenue) }}</div>
        <div class="sc-sub">dari 6 transaksi lunas</div>
        <div class="sc-change up"><i class="fa-solid fa-arrow-trend-up"></i> +14% vs bulan lalu</div>
      </div>

      <div class="stat-card amber">
        <div class="sc-top">
          <div><div class="sc-label">Total Pelanggan</div></div>
          <div class="sc-icon ic-amber"><i class="fa-solid fa-user-group"></i></div>
        </div>
        <div class="sc-value">{{ customers }}</div>
        <div class="sc-sub">5 pelanggan baru bulan ini</div>
        <div class="sc-change up"><i class="fa-solid fa-arrow-trend-up"></i> +18% growth</div>
      </div>
    </div>
  </div>
</template>
