import { ref } from 'vue'
import { weatherService } from '../services/weatherService'
import { toWeatherViewModel } from '../models/weatherModel'

function errMsg(e, fallback) {
  return e?.response?.data?.error
    || e?.response?.data?.message
    || e?.message
    || fallback
}

export function useWeatherController() {
  const city = ref('')
  const loading = ref(false)
  const error = ref('')
  const ui = ref(null)

  async function searchByCity() {
    const c = city.value.trim()
    if (!c) return

    loading.value = true
    error.value = ''
    try {
      const raw = await weatherService.byCity(c)
      ui.value = toWeatherViewModel(raw, c)
    } catch (e) {
      error.value = errMsg(e, 'Erreur récupération météo')
      ui.value = null
    } finally {
      loading.value = false
    }
  }

  async function loadByFavorite(fav) {
    loading.value = true
    error.value = ''
    try {
      const raw = await weatherService.byCoords(fav.latitude, fav.longitude)
      city.value = fav.city || city.value
      ui.value = toWeatherViewModel(raw, city.value)
    } catch (e) {
      error.value = errMsg(e, 'Erreur météo (favori)')
    } finally {
      loading.value = false
    }
  }

  return { city, loading, error, ui, searchByCity, loadByFavorite }
}
