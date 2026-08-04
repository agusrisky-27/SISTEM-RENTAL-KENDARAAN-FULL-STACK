import { createRouter, createWebHistory } from 'vue-router';
import DashboardView from '../views/DashboardView.vue';
import KendaraanView from '../views/KendaraanView.vue';
import PelangganView from '../views/PelangganView.vue';
import UsersView from '../views/UsersView.vue';
import TransaksiView from '../views/TransaksiView.vue';
import PengembalianView from '../views/PengembalianView.vue';
import PembayaranView from '../views/PembayaranView.vue';

const routes = [
  { path: '/', redirect: '/dashboard' },
  { path: '/dashboard', name: 'Dashboard', component: DashboardView },
  { path: '/kendaraan', name: 'Kendaraan', component: KendaraanView },
  { path: '/pelanggan', name: 'Pelanggan', component: PelangganView },
  { path: '/users', name: 'Users', component: UsersView },
  { path: '/transaksi', name: 'Transaksi', component: TransaksiView },
  { path: '/pengembalian', name: 'Pengembalian', component: PengembalianView },
  { path: '/pembayaran', name: 'Pembayaran', component: PembayaranView },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
