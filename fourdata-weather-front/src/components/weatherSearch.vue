<script setup lang="ts">
import { ref } from 'vue'
import { getWeatherByCity, createFavorite } from '@/services/api'
import type { WeatherResponse } from '@/types/weather'

const city = ref('')
const loading = ref(false)
const error = ref<string | null>(null)
const data = ref<WeatherResponse | null>(null)

async function search() {
  error.value = null
  data.value = null
  loading.value = true

  try {
    const c = city.value.trim()
    if (!c) {
      error.value = 'Veuillez saisir une ville.'
      return
    }
    data.value = await getWeatherByCity(c)
  } catch (e: any) {
    error.value = e?.response?.data?.message ?? 'Erreur lors de la récupération météo.'
  } finally {
    loading.value = false
  }
}

async function save() {
  if (!data.value) return
  error.value = null

  try {
    await createFavorite({
      city: data.value.city,
      lat: data.value.lat,
      lon: data.value.lon,
    })
  } catch (e: any) {
    error.value = e?.response?.data?.message ?? 'Erreur lors de la sauvegarde.'
  }
}
</script>

<template>
  <div>
    <form @submit.prevent="search" style="display:flex; gap:8px;">
      <input v-model="city" placeholder="Paris" />
      <button type="submit" :disabled="loading">Rechercher</button>
    </form>

    <p v-if="loading">Chargement…</p>
    <p v-if="error" style="color:red;">{{ error }}</p>

    <div v-if="data">
      <h3>{{ data.city }}</h3>
      <p>Température: {{ data.current.temperature }}°C</p>
      <p>Vent: {{ data.current.windSpeed }} km/h</p>

      <button @click="save">Sauvegarder</button>
    </div>
  </div>
</template>
