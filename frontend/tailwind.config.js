/** @type {import('tailwindcss').Config} */
export default {
  darkMode: 'class',
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      colors: {
        accent: {
          DEFAULT: '#4f6ef7',
          hover: '#3a54d6',
        },
        dark: {
          bg: '#0f1117',
          card: '#1a1d27',
          border: '#2a2d3e',
          text: '#e2e8f5',
          muted: '#6b7280',
        },
        light: {
          bg: '#f1f5f9',
          card: '#ffffff',
          border: '#e2e8f0',
          text: '#0f172a',
          muted: '#64748b',
        }
      },
      fontFamily: {
        sans: ['Inter', 'sans-serif'],
        heading: ['Plus Jakarta Sans', 'sans-serif'],
      },
      borderRadius: {
        card: '16px',
        btn: '10px',
      },
      boxShadow: {
        card: '0 2px 12px rgba(0,0,0,0.08)',
      }
    }
  },
  plugins: [],
}
