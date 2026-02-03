<script setup>
defineProps({
  open: { type: Boolean, default: false },
  loading: { type: Boolean, default: false },
  count: { type: Number, default: 0 },
  items: { type: Array, default: () => [] },
})

const emit = defineEmits(['close', 'refresh', 'view', 'remove'])
</script>

<template>
  <aside class="drawer" :class="{ open }">
    <div class="drawerHeader">
      <div>
        <div class="drawerTitle">Favoris</div>
        <div class="drawerSub">{{ count }} ville(s)</div>
      </div>

      <div style="display:flex; gap:8px;">
        <button class="miniBtn" type="button" @click="emit('refresh')">
          ↻
        </button>
        <button class="miniBtn" type="button" @click="emit('close')">
          ✕
        </button>
      </div>
    </div>

    <div v-if="loading" style="margin-top:12px; opacity:0.85;">Chargement…</div>

    <ul v-else class="favList">
      <li v-for="f in items" :key="f.id" class="favItem">
        <div style="display:flex; flex-direction:column; gap:2px;">
          <span style="font-weight:800;">{{ f.city }}</span>
          <small style="opacity:0.8;">{{ f.latitude }}, {{ f.longitude }}</small>
        </div>

        <div style="display:flex; gap:8px;">
          <button class="miniBtn" type="button" @click="emit('view', f)">Voir</button>
          <button class="miniBtn" type="button" @click="emit('remove', f.id)">Supprimer</button>
        </div>
      </li>
    </ul>
  </aside>
</template>
