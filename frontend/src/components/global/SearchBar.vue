<template>
  <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2 mb-4">
    <!-- Input de busca por nome -->
    <input
        v-model="searchTerm"
        type="text"
        placeholder="Buscar por nome"
        class="flex-1 p-2 rounded border border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100"
    />

    <!-- Botão de pesquisa -->
    <button
        @click="applyFilters"
        class="px-4 py-2 bg-blue-600 dark:bg-blue-700 rounded hover:bg-blue-700 dark:hover:bg-blue-800 transition"
    >
      Pesquisar
    </button>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref } from "vue";

export default defineComponent({
  emits: ["search"],
  setup(_, { emit }) {
    const searchTerm = ref<string>("");
    const loading = ref(false);

    const applyFilters = () => {
      emit("search", {
        name: searchTerm.value,
      });
    };

    return {
      searchTerm,
      applyFilters,
      loading,
    };
  },
});
</script>
