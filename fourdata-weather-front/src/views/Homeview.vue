<script setup>
import bgUrl from '../assets/weather-bg.jpg'
import { useWeatherController } from '../controllers/weatherController'
import { useFavoritesController } from '../controllers/favoritesController'

import SearchBar from '../components/SearchBar.vue'
import WeatherCard from '../components/WeatherCard.vue'
import ForecastDaily from '../components/ForecastDaily.vue'
import FavoritesDrawer from '../components/FavoritesDrawer.vue'

const weatherCtrl = useWeatherController()
const favCtrl = useFavoritesController()

const saveFavorite = () => favCtrl.addByCity(weatherCtrl.city.value)
</script>

<template>
  <div class="page">
    <div class="bg" :style="{ backgroundImage: `url(${bgUrl})` }"></div>
    <div class="overlay"></div>

    <header class="topbar">
      <div class="brand">
        <div class="logo">🌤️</div>
        <div>
          <div class="title"> Weather</div>
        </div>
      </div>

      <button class="favBtn" type="button" @click="favCtrl.open.value = true">
        ★ <span class="badge">{{ favCtrl.count }}</span>
      </button>
    </header>

    <main class="container">
      <section class="card">
        <h2>Recherche</h2>

        <SearchBar
          v-model="weatherCtrl.city.value"
          :loading="weatherCtrl.loading.value"
          @submit="weatherCtrl.searchByCity"
        />

        <div class="row">
          <button class="miniBtn" type="button" @click="saveFavorite" :disabled="weatherCtrl.loading.value">
            Favori
          </button>
          <button class="miniBtn" type="button" @click="favCtrl.load" :disabled="favCtrl.loading.value">
            {{ favCtrl.loading.value ? '...' : 'Refresh' }}
          </button>
        </div>

        <p v-if="weatherCtrl.error.value" class="err">{{ weatherCtrl.error.value }}</p>
        <p v-if="favCtrl.error.value" class="err">{{ favCtrl.error.value }}</p>

        <WeatherCard :weather="weatherCtrl.ui.value">
          <ForecastDaily :daily="weatherCtrl.ui.value?.daily || []" />
        </WeatherCard>
      </section>

      <FavoritesDrawer
        :open="favCtrl.open.value"
        :loading="favCtrl.loading.value"
        :count="favCtrl.count"
        :items="favCtrl.items.value"
        @close="favCtrl.open.value = false"
        @refresh="favCtrl.load"
        @view="weatherCtrl.loadByFavorite"
        @remove="favCtrl.remove"
      />
    </main>
  </div>
</template>

<style scoped>
.row{margin-top:10px;display:flex;gap:10px;flex-wrap:wrap}
.err{margin-top:12px;color:rgba(255,160,160,.95)}
</style>
