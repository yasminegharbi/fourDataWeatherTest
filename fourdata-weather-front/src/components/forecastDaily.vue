<script setup>
defineProps({ daily: Array })
</script>

<template>
  <div class="forecast">
    <div class="forecastTitle">Prévisions</div>

    <div v-if="daily?.length" class="forecastGrid">
      <div v-for="d in daily" :key="d.date" class="forecastItem">
        <div class="fDay">{{ d.label }}</div>
        <div class="fTemp"><span class="min">{{ d.min }}°</span><span class="max">{{ d.max }}°</span></div>
        <small class="opacity-75">{{ d.precipitation }} mm</small>
      </div>
    </div>

  </div>
</template>
