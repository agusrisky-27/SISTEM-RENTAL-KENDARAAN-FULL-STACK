import { defineStore } from 'pinia';
import api from '../services/api';

export const usePelangganStore = defineStore('pelanggan', {
  state: () => ({
    items: [],
    loading: false,
    error: null,
  }),
  actions: {
    async fetchPelanggan() {
      this.loading = true;
      this.error = null;
      try {
        const response = await api.get('/pelanggan');
        this.items = response.data.data || response.data || [];
      } catch (err) {
        this.error = err.response?.data?.message || 'Gagal memuat data pelanggan';
      } finally {
        this.loading = false;
      }
    },
    async createPelanggan(data) {
      try {
        const response = await api.post('/pelanggan', data);
        if (response.data.status) {
          this.items.push(response.data.data);
          return true;
        }
        return false;
      } catch (err) {
        this.error = err.response?.data?.message || 'Gagal menambah pelanggan';
        return false;
      }
    },
    async updatePelanggan(id, data) {
      try {
        const response = await api.put(`/pelanggan/${id}`, data);
        if (response.data.status) {
          const index = this.items.findIndex((item) => item.id === id);
          if (index !== -1) {
            this.items[index] = { ...this.items[index], ...data };
          }
          return true;
        }
        return false;
      } catch (err) {
        this.error = err.response?.data?.message || 'Gagal mengubah pelanggan';
        return false;
      }
    },
    async deletePelanggan(id) {
      try {
        await api.delete(`/pelanggan/${id}`);
        this.items = this.items.filter((item) => item.id !== id);
        return true;
      } catch (err) {
        this.error = err.response?.data?.message || 'Gagal menghapus pelanggan';
        return false;
      }
    }
  }
});
