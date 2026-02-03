<script setup>
const props = defineProps({
  modelValue: { type: String, default: '' },
  loading: { type: Boolean, default: false },
})
const emit = defineEmits(['update:modelValue', 'submit'])

function onInput(e) {
  emit('update:modelValue', e.target.value)
}
</script>

<template>
  <form class="searchRow" @submit.prevent="emit('submit')">
    <input
      class="input"
      type="text"
      placeholder="Entrer votre ville"
      autocomplete="off"
      :value="props.modelValue"
      @input="onInput"
    />
    <button class="btnPrimary" type="submit" :disabled="props.loading">
      {{ props.loading ? 'Recherche…' : 'Rechercher' }}
    </button>
  </form>
</template>
