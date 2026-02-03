<script setup>
defineProps({
  weather: { type: Object, default: null },
})
</script>

<template>
  <div v-if="weather" class="weatherCard">
    <div class="weatherTop">
      <div>
        <div class="place">{{ weather.location }}</div>
        <div class="cond">Coordonnées : {{ weather.lat }}, {{ weather.lon }}</div>
      </div>
      <div class="temp">{{ weather.temp }}°</div>
    </div>

    <div class="weatherMeta">
      <div class="metaItem">
        <div class="metaLabel">Vent</div>
        <div class="metaValue">{{ weather.wind }} km/h</div>
      </div>
      <div class="metaItem">
  <div class="metaLabel">Précipitations</div>
  <div class="metaValue">{{ weather.precipitationToday }} mm</div>
</div>

    </div>

    <div class="divider"></div>

    <slot />
  </div>
</template>
