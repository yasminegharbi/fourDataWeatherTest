import { computed, onMounted, ref } from 'vue'
import { favoritesService } from '../services/favoritesService'

function errMsg(e, fallback) {
  return e?.response?.data?.error
    || e?.response?.data?.message
    || e?.message
    || fallback
}

export function useFavoritesController() {
  const items = ref([])
  const open = ref(false)
  const loading = ref(false)
  const error = ref('')

  const count = computed(() => items.value.length)

  async function load() {
    loading.value = true
    error.value = ''
    try {
      items.value = await favoritesService.list()
    } catch (e) {
      error.value = errMsg(e, 'Erreur chargement favoris')
    } finally {
      loading.value = false
    }
  }

  async function addByCity(city) {
    const c = (city || '').trim()
    if (!c) {
      error.value = 'Saisis une ville avant de sauvegarder.'
      return
    }
    await favoritesService.addByCity(c)
    await load()
    open.value = true
  }

  async function remove(id) {
    try {
      await favoritesService.remove(id)
      items.value = items.value.filter(f => f.id !== id)
    } catch (e) {
      error.value = errMsg(e, 'Erreur suppression favori')
    }
  }

  onMounted(load)

  return { items, open, loading, error, count, load, addByCity, remove }
}
