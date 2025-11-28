<template>
  <aside class="w-64 p-4 bg-gray-900 dark:bg-gray-800 text-white h-screen">
    <ul class="flex flex-col gap-2">
      <router-link
          to="/admin/product"
          class="block p-4 rounded hover:bg-gray-700 dark:hover:bg-gray-600 cursor-pointer transition"
      >
        Produtos
      </router-link>

      <router-link
          to="/admin/category"
          v-if="auth.isAuthenticated"
          class="block p-4 rounded hover:bg-gray-700 dark:hover:bg-gray-600 cursor-pointer transition mt-2"
      >
        Categoria
      </router-link>

      <router-link
          to="/auth/orders"
          v-if="auth.isAuthenticated"
          class="block p-4 rounded hover:bg-gray-700 dark:hover:bg-gray-600 cursor-pointer transition mt-2"
      >
        Pedidos
      </router-link>

      <router-link
          to="/auth/orders"
          v-if="auth.isAuthenticated"
          class="block p-4 rounded hover:bg-gray-700 dark:hover:bg-gray-600 cursor-pointer transition mt-2"
      >
        Baixo Estoque
      </router-link>
    </ul>
  </aside>
</template>

<script setup lang="ts">
import { useAuthStore } from "@/stores/auth";
const auth = useAuthStore();
</script>
