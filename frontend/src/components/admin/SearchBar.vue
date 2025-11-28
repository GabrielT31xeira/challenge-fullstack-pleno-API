<template>
  <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2 mb-4">
    <!-- Input de busca por nome -->
    <input
        v-model="searchTerm"
        type="text"
        placeholder="Buscar por nome"
        class="flex-1 p-2 rounded border border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100"
    />

    <!-- Select de status -->
    <select
        v-model="selectedStatus"
        class="appearance-none p-2 rounded border border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100"
    >
      <option value="">Todos os status</option>
      <option v-for="status in statusList" :key="status.value" :value="status.value">
        {{ status.label }}
      </option>
    </select>

    <!-- Input de preço -->
    <input
        v-model.number="price"
        type="text"
        placeholder="Preço máx"
        class="appearance-none p-2 rounded border border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 w-32"
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
    const selectedStatus = ref<string>("");
    const price = ref<number | null>(null);

    const statusList = [
      { value: "pending", label: "Pendente" },
      { value: "processing", label: "Processando" },
      { value: "shipped", label: "Enviado" },
      { value: "delivered", label: "Entregue" },
      { value: "cancelled", label: "Cancelado" },
    ];

    const applyFilters = () => {
      emit("search", {
        name: searchTerm.value,
        status: selectedStatus.value,
        price: price.value,
      });
    };

    return {
      searchTerm,
      selectedStatus,
      price,
      applyFilters,
      statusList,
    };
  },
});
</script>