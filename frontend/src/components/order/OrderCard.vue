<template>
  <div
      class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700
           hover:shadow-lg hover:-translate-y-1 transition cursor-pointer flex flex-col justify-between"
      @click="$emit('click')"
  >
    <!-- HEADER -->
    <div class="flex justify-between items-center mb-3">
      <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
        {{ order.cart?.name ?? "Carrinho sem nome" }}
      </h2>

      <span
          class="px-2 py-1 text-xs rounded-full"
          :class="statusColor(order.status)"
      >
        {{ order.status }}
      </span>
    </div>

    <!-- ITEMS PREVIEW -->
    <div v-if="order.cart?.items?.length" class="flex items-center gap-3 mb-3">
      <div class="flex-1">
        <p class="font-medium line-clamp-1">
          {{ order.cart.items[0].product.name }}
        </p>

        <p class="text-sm text-gray-500 dark:text-gray-400">
          {{ order.cart.total_quantity }} item(s)
        </p>
      </div>
    </div>

    <!-- FOOTER -->
    <div class="mt-auto flex justify-between items-center pt-3 border-t border-gray-200 dark:border-gray-700">
      <p class="text-lg font-bold text-gray-900 dark:text-gray-100">
        R$ {{ formatCurrency(order.total) }}
      </p>

      <span class="text-xs text-gray-500 dark:text-gray-400">
        {{ formatDate(order.created_at) }}
      </span>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";

export default defineComponent({
  name: "OrderCard",
  props: {
    order: {
      type: Object,
      required: true,
    },
  },

  setup() {
    /* -------------------------------------------
     * HELPERS
     * ------------------------------------------- */

    const formatCurrency = (value: number | string) => {
      return Number(value).toFixed(2).replace(".", ",");
    };

    const formatDate = (date: string) => {
      return new Date(date).toLocaleDateString("pt-BR");
    };

    const statusColor = (status: string) => {
      switch (status) {
        case "pending":
          return "text-yellow-700 bg-yellow-200";
        case "processing":
          return "text-blue-700 bg-blue-200";
        case "shipped":
          return "text-indigo-700 bg-indigo-200";
        case "delivered":
          return "text-green-700 bg-green-200";
        case "cancelled":
          return "text-red-700 bg-red-200";
      }
    };

    return {
      formatCurrency,
      formatDate,
      statusColor,
    };
  },
});
</script>

<style scoped>
.line-clamp-1 {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
