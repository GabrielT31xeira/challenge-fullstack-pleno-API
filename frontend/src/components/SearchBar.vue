<template>
  <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2 mb-4">
    <!-- Input de busca por nome -->
    <input
        v-model="searchTerm"
        type="text"
        placeholder="Buscar por nome"
        class="flex-1 p-2 rounded border border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100"
    />

    <!-- Select de categorias -->
    <select
        v-model="selectedCategory"
        class="appearance-none p-2 rounded border border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100"
    >
      <option value="">categorias</option>
      <option
          v-for="category in categories"
          :key="category.id"
          :value="category.id"
      >
        {{ category.name }}
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
import { defineComponent, ref, onMounted } from "vue";
import {type Category, fetchCategories} from "../api/category";

export default defineComponent({
  emits: ["search"],
  setup(_, { emit }) {
    const categories = ref<Category[]>([]);
    const selectedCategory = ref<string>("");
    const searchTerm = ref<string>("");
    const price = ref<number | null>(null);
    const loading = ref(false);

    const loadCategories = async () => {
      loading.value = true;
      try {
        categories.value = await fetchCategories();
      } catch (err) {
        console.error("Erro ao carregar categorias", err);
      } finally {
        loading.value = false;
      }
    };

    onMounted(loadCategories);

    const applyFilters = () => {
      emit("search", {
        name: searchTerm.value,
        categoryId: selectedCategory.value,
        price: price.value,
      });
    };

    return {
      categories,
      selectedCategory,
      searchTerm,
      price,
      applyFilters,
      loading,
    };
  },
});
</script>
