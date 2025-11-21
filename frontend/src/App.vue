<template>
  <AppLayout />
</template>

<script setup lang="ts">
import AppLayout from '@/components/Layout/AppLayout.vue'
</script>

<style>
/* Estilos globais para dark mode */
body {
  @apply bg-dark-900 text-gray-100;
}

/* Scrollbar customizada */
::-webkit-scrollbar {
  width: 8px;
}

::-webkit-scrollbar-track {
  @apply bg-dark-800;
}

::-webkit-scrollbar-thumb {
  @apply bg-dark-600 rounded-full;
}

::-webkit-scrollbar-thumb:hover {
  @apply bg-dark-500;
}
</style>