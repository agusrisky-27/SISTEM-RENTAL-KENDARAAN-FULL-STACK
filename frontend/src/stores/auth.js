import { defineStore } from 'pinia';
import api from '../services/api';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    token: localStorage.getItem('token') || null,
    user: JSON.parse(localStorage.getItem('user')) || null,
    loading: false,
    error: null,
  }),
  getters: {
    isAuthenticated: (state) => !!state.token,
  },
  actions: {
    async login(credentials) {
      this.loading = true;
      this.error = null;
      try {
        const response = await api.post('/auth/login', credentials);
        const { status, data, message, token } = response.data;
        
        if (status) {
          this.token = token || data?.token;
          this.user = data?.user || data;
          
          localStorage.setItem('token', this.token);
          localStorage.setItem('user', JSON.stringify(this.user));
          return true;
        } else {
          this.error = message || 'Login failed.';
          return false;
        }
      } catch (err) {
        // Fallback untuk keperluan testing/portfolio jika Backend mati
        if (err.message === 'Network Error') {
          console.warn('Backend offline, menggunakan mock login untuk development.');
          this.token = 'mock-jwt-token-12345';
          this.user = { username: credentials.username || 'Admin', role: 'admin' };
          localStorage.setItem('token', this.token);
          localStorage.setItem('user', JSON.stringify(this.user));
          return true;
        }
        
        this.error = err.response?.data?.message || err.message || 'An error occurred during login.';
        return false;
      } finally {
        this.loading = false;
      }
    },
    logout() {
      this.token = null;
      this.user = null;
      localStorage.removeItem('token');
      localStorage.removeItem('user');
    }
  }
});
