<template>
  <aside class="w-64 p-4 bg-gray-900 dark:bg-gray-800 text-white h-screen">
    <ul class="flex flex-col gap-2">
      <router-link
          to="/"
          class="block p-4 rounded hover:bg-gray-700 dark:hover:bg-gray-600 cursor-pointer transition"
      >
        Produtos em destaque
      </router-link>

      <router-link
          to="/auth/carts"
          v-if="auth.isAuthenticated"
          class="block p-4 rounded hover:bg-gray-700 dark:hover:bg-gray-600 cursor-pointer transition mt-2"
      >
        Meus Carrinhos
      </router-link>
    </ul>
  </aside>
</template>

<script setup lang="ts">
import { useAuthStore } from "@/stores/auth";
const auth = useAuthStore();
</script>
