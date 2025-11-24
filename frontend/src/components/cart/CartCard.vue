<template>
  <div
      class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-200 dark:border-gray-700
           hover:shadow-xl transition-transform hover:-translate-y-1 cursor-pointer"
      @click="$emit('click')"
  >
    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3 line-clamp-1">
      {{ cart.name }}
    </h2>

    <div class="text-sm text-gray-700 dark:text-gray-300 space-y-1 mb-4">
      <p><span class="font-medium">Itens únicos:</span> {{ cart.items_count }}</p>
      <p><span class="font-medium">Quantidade total:</span> {{ cart.total_quantity }}</p>

      <p>
        <span class="font-medium">Subtotal:</span>
        <span class="text-blue-600 dark:text-blue-400 font-bold">
          R$ {{ Number(cart.subtotal).toFixed(2) }}
        </span>
      </p>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, type PropType } from "vue";
import type {Cart} from "@/api/auth/Cart.ts";

export default defineComponent({
  name: "CartCard",
  props: {
    cart: {
      type: Object as PropType<Cart>,
      required: true,
    },
  },
});
</script>
