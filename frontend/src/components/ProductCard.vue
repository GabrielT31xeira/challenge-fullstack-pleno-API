<template>
  <div
      class="bg-gray-100 dark:bg-gray-800 rounded-lg p-4 shadow hover:shadow-lg cursor-pointer transition"
      @click="$emit('click', product)"
  >
    <!-- Nome do produto -->
    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">
      {{ product.name }}
    </h2>

    <!-- Categoria -->
    <p class="text-sm text-gray-600 dark:text-gray-300 mb-2">
      Categoria: {{ product.category?.name ?? 'Sem categoria' }}
    </p>

    <!-- Preço -->
    <p class="text-blue-600 dark:text-blue-400 font-bold mb-2">
      R$ {{ Number(product.price).toFixed(2) }}
    </p>

    <!-- Tags -->
    <div class="flex flex-wrap gap-1">
      <span
          v-for="(tag, index) in product.tags"
          :key="index"
          class="text-xs bg-blue-200 dark:bg-blue-700 text-blue-800 dark:text-blue-200 px-2 py-0.5 rounded"
      >
        {{ tag.name }}
      </span>
    </div>
  </div>
</template>

<script lang="ts">
import {
  defineComponent, type PropType,

} from "vue";
import { type Product } from "../api/product";

export default defineComponent({
  name: "ProductCard",
  props: {
    product: {
      type: Object as PropType<Product>,
      required: true,
    },
  },
});
</script>
