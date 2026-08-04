import { ref, onMounted, watch } from 'vue';

export function useTheme() {
  const isDark = ref(false);

  const toggleTheme = () => {
    isDark.value = !isDark.value;
  };

  onMounted(() => {
    // Cek preferensi user di localStorage atau OS theme
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme) {
      isDark.value = savedTheme === 'dark';
    } else {
      isDark.value = window.matchMedia('(prefers-color-scheme: dark)').matches;
    }
    applyTheme(isDark.value);
  });

  watch(isDark, (newVal) => {
    applyTheme(newVal);
    localStorage.setItem('theme', newVal ? 'dark' : 'light');
  });

  const applyTheme = (dark) => {
    if (dark) {
      document.documentElement.classList.add('dark');
    } else {
      document.documentElement.classList.remove('dark');
    }
  };

  return {
    isDark,
    toggleTheme,
  };
}
