<template>
  <div class="flex justify-center items-center space-x-2 mt-4">
    <button
        :disabled="!prev"
        @click="$emit('change-page', currentPage - 1)"
        class="px-3 py-1 rounded bg-gray-300 dark:bg-gray-700 text-gray-800 dark:text-gray-100 disabled:opacity-50"
    >
      Anterior
    </button>

    <span class="px-3 py-1 bg-gray-200 dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded">
      Página {{ currentPage }} de {{ lastPage }}
    </span>

    <button
        :disabled="!next"
        @click="$emit('change-page', currentPage + 1)"
        class="px-3 py-1 rounded bg-gray-300 dark:bg-gray-700 text-gray-800 dark:text-gray-100 disabled:opacity-50"
    >
      Próxima
    </button>
  </div>
</template>

<script lang="ts">
import { defineComponent, type PropType } from "vue";

export default defineComponent({
  name: "Pagination",
  props: {
    currentPage: { type: Number, required: true },
    lastPage: { type: Number, required: true },
    prev: { type: String, required: false },
    next: { type: String, required: false },
  },
});
</script>
