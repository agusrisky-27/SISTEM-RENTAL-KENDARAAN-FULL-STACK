import { ref } from 'vue'

export const toasts = ref([])

export const useToast = () => {
  const addToast = (msg, type = 'success') => {
    const id = Date.now()
    toasts.value.push({ id, msg, type })
    setTimeout(() => {
      removeToast(id)
    }, 3000)
  }

  const removeToast = (id) => {
    toasts.value = toasts.value.filter(t => t.id !== id)
  }

  return { toasts, addToast, removeToast }
}
