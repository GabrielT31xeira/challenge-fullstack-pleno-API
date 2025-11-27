<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
    <Navbar />
    <div class="flex">
      <AdminSidebar />
      <main class="flex-1 p-6">
        <p class="mx-3 my-3 text-3xl font-bold">Dashboard do Administrador</p>
        <section class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <!-- Loading -->
          <div v-if="loading" class="col-span-1 sm:col-span-3 space-y-3">
            <div class="animate-pulse h-12 bg-gray-200 dark:bg-gray-700 rounded"></div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
              <div class="h-28 bg-gray-100 dark:bg-gray-800 rounded-lg p-4 animate-pulse"></div>
              <div class="h-28 bg-gray-100 dark:bg-gray-800 rounded-lg p-4 animate-pulse"></div>
              <div class="h-28 bg-gray-100 dark:bg-gray-800 rounded-lg p-4 animate-pulse"></div>
            </div>
          </div>

          <!-- Dashboard cards -->
          <template v-else>
            <!-- Caso não tenha resposta -->
            <div v-if="!response || Object.keys(response).length === 0" class="col-span-1 sm:col-span-3 p-6 bg-white dark:bg-gray-800 rounded shadow text-center">
              <p class="text-sm text-gray-500 dark:text-gray-400">Nenhuma informação disponível.</p>
            </div>

            <!-- Cards -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 flex flex-col justify-between">
              <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Produtos</p>
                <h3 class="text-2xl font-bold mt-2 text-gray-900 dark:text-gray-100">
                  {{ response.product_count ?? 0 }}
                </h3>
              </div>
              <div class="text-sm text-gray-400 mt-3">Total de produtos cadastrados</div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 flex flex-col justify-between">
              <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Pedidos</p>
                <h3 class="text-2xl font-bold mt-2 text-gray-900 dark:text-gray-100">
                  {{ response.orders_count ?? 0 }}
                </h3>
              </div>
              <div class="text-sm text-gray-400 mt-3">Pedidos concluidos</div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 flex flex-col justify-between">
              <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Faturamento</p>
                <h3 class="text-2xl font-bold mt-2 text-gray-900 dark:text-gray-100">
                  R$ {{ formatCurrency(response.revenue ?? 0) }}
                </h3>
              </div>
              <div class="text-sm text-gray-400 mt-3">Receita total</div>
            </div>
          </template>
        </section>
      </main>
    </div>
  </div>
</template>

<script lang="ts">

import Navbar from "@/components/Navbar.vue";
import AdminSidebar from "@/components/admin/AdminSidebar.vue";
import {dashboard} from "@/api/auth/admin/Admin.ts"
import {defineComponent, onMounted, ref} from "vue";

export default defineComponent({
  name: "DashboardOverview",
  components: {Navbar, AdminSidebar},
  setup() {
    const loading = ref(false);
    const response = ref<Record<string, any>>({});

    const loadDashboard = async () => {
      loading.value = true;
      try {
        const res = await dashboard();
        response.value = res.data ?? {};
      } catch (err) {
        console.error("Erro ao carregar dashboard:", err);
      } finally {
        loading.value = false;
      }
    };

    const formatCurrency = (value: number | string) => {
      const n = Number(value) || 0;
      return n.toFixed(2).replace(".", ",");
    };

    onMounted(() => loadDashboard());

    return {
      loading,
      response,
      formatCurrency,
    };
  },
});
</script>