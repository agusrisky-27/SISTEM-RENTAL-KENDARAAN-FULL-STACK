import { createRouter, createWebHistory } from 'vue-router';
import DashboardView from '../views/DashboardView.vue';
import KendaraanView from '../views/KendaraanView.vue';
import PelangganView from '../views/PelangganView.vue';
import UsersView from '../views/UsersView.vue';
import TransaksiView from '../views/TransaksiView.vue';
import PengembalianView from '../views/PengembalianView.vue';
import PembayaranView from '../views/PembayaranView.vue';
import LoginView from '../views/LoginView.vue';

const routes = [
  { path: '/', redirect: '/dashboard' },
  { path: '/login', name: 'Login', component: LoginView },
  { path: '/dashboard', name: 'Dashboard', component: DashboardView, meta: { requiresAuth: true } },
  { path: '/kendaraan', name: 'Kendaraan', component: KendaraanView, meta: { requiresAuth: true } },
  { path: '/pelanggan', name: 'Pelanggan', component: PelangganView, meta: { requiresAuth: true } },
  { path: '/users', name: 'Users', component: UsersView, meta: { requiresAuth: true } },
  { path: '/transaksi', name: 'Transaksi', component: TransaksiView, meta: { requiresAuth: true } },
  { path: '/pengembalian', name: 'Pengembalian', component: PengembalianView, meta: { requiresAuth: true } },
  { path: '/pembayaran', name: 'Pembayaran', component: PembayaranView, meta: { requiresAuth: true } },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token');
  if (to.meta.requiresAuth && !token) {
    next('/login');
  } else if (to.path === '/login' && token) {
    next('/dashboard');
  } else {
    next();
  }
});

export default router;
