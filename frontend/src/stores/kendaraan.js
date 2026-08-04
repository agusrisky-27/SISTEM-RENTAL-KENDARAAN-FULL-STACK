import { defineStore } from 'pinia';
import api from '../services/api';

export const useKendaraanStore = defineStore('kendaraan', {
  state: () => ({
    items: [],
    loading: false,
    error: null,
  }),
  actions: {
    async fetchKendaraan() {
      this.loading = true;
      this.error = null;
      try {
        const response = await api.get('/kendaraan');
        this.items = response.data.data || [];
      } catch (err) {
        this.error = err.response?.data?.message || err.message || 'Gagal memuat data kendaraan';
        // Mock data fallback for development if API is offline
        this.items = [
          { id: 1, nomor_polisi: 'B 1234 ABC', merk: 'Toyota', tipe: 'Avanza', tahun: 2022, harga_sewa: 400000, status: 'Tersedia', foto: null },
          { id: 2, nomor_polisi: 'D 5678 DEF', merk: 'Honda', tipe: 'Brio', tahun: 2021, harga_sewa: 300000, status: 'Disewa', foto: null },
          { id: 3, nomor_polisi: 'L 9012 GHI', merk: 'Mitsubishi', tipe: 'Xpander', tahun: 2023, harga_sewa: 500000, status: 'Tersedia', foto: null },
        ];
      } finally {
        this.loading = false;
      }
    },
    async createKendaraan(data) {
      try {
        const response = await api.post('/kendaraan', data);
        if (response.data.status) {
          this.items.push(response.data.data);
          return true;
        }
        return false;
      } catch (err) {
        this.error = err.response?.data?.message || 'Gagal menambah kendaraan';
        // Mock update for development
        this.items.push({ id: Date.now(), ...data });
        return true;
      }
    },
    async updateKendaraan(id, data) {
      try {
        const response = await api.put(`/kendaraan/${id}`, data);
        if (response.data.status) {
          const index = this.items.findIndex((item) => item.id === id);
          if (index !== -1) {
            this.items[index] = { ...this.items[index], ...data };
          }
          return true;
        }
        return false;
      } catch (err) {
        this.error = err.response?.data?.message || 'Gagal mengubah kendaraan';
        // Mock update for development
        const index = this.items.findIndex((item) => item.id === id);
        if (index !== -1) {
          this.items[index] = { ...this.items[index], ...data };
        }
        return true;
      }
    },
    async deleteKendaraan(id) {
      try {
        await api.delete(`/kendaraan/${id}`);
        this.items = this.items.filter((item) => item.id !== id);
        return true;
      } catch (err) {
        this.error = err.response?.data?.message || 'Gagal menghapus kendaraan';
        // Mock update for development
        this.items = this.items.filter((item) => item.id !== id);
        return true;
      }
    },
  },
});
