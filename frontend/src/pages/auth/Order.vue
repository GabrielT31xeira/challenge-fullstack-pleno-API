<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
    <Navbar />

    <div class="flex">
      <Sidebar />

      <main class="flex-1 p-6">
        <!-- LISTAGEM DE ORDERS -->
        <div class="flex flex-col gap-6">

          <SearchBar @search="handleSearch" />

          <div v-if="loading" class="text-center py-10">Carregando carrinhos...</div>

          <div
              v-else
              class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 min-h-[500px]"
          >
            <OrderCard
                v-for="order in orders"
                :key="order.id"
                :order="order"
                @click="openCartModal(order.id)"
                class="max-h-[160px]"
            />
          </div>

          <Pagination
              v-if="meta"
              :current-page="meta.current_page"
              :last-page="meta.last_page"
              :prev="links?.prev"
              :next="links?.next"
              @change-page="changePage"
          />

        </div>
      </main>
    </div>
  </div>
</template>
<script lang="ts">
import Sidebar from "@/components/Sidebar.vue";
import Navbar from "@/components/Navbar.vue";
import SearchBar from "@/components/global/SearchBar.vue";
import Pagination from "@/components/Pagination.vue";
import OrderCard from "@/components/order/OrderCard.vue";
import {defineComponent, onMounted, ref} from "vue";
import {fetchOrders, type OrderResponse} from "@/api/auth/Order.ts";
import type {PaginationMeta, PaginationLinks} from "@/api/auth/Order.ts";
import {toastError} from "@/utils/toastApiHandler.ts";

export default defineComponent({
  components: { Sidebar, Navbar, Pagination, SearchBar, OrderCard },

  setup() {
    /* ----------------------------------------------------
     * STATE PRINCIPAL
     * ---------------------------------------------------- */
    const page = ref(1);
    const meta = ref<PaginationMeta | null>(null);
    const links = ref<PaginationLinks | null>(null);
    const currentFilters = ref<{ search?: string }>({});
    const orders = ref<OrderResponse[]>([])

    /* ----------------------------------------------------
     * FETCH ALL ORDERS
     * ---------------------------------------------------- */
    const fetchOrder = async (
        p: number = page.value,
        filters: { search?: string } = currentFilters.value
    ) => {
      loading.value = true;

      try {
        const payload = await fetchOrders(p, filters);
        orders.value = payload.data ?? [];
        meta.value = payload.meta ?? null;
        links.value = payload.links ?? null;
      } catch (err: any) {
        console.error("Erro ao carregar carrinhos:", err);
        toastError("Erro ao carregar carrinhos.");
      } finally {
        loading.value = false;
      }
    };

    /* ----------------------------------------------------
     * STATE PRINCIPAL
     * ---------------------------------------------------- */
    const loading = ref(false);

    /* ----------------------------------------------------
     * SEARCH & PAGINATION
     * ---------------------------------------------------- */
    const handleSearch = (filters: { search?: string }) => {
      currentFilters.value = filters;
      page.value = 1;
      fetchOrder(1, filters);
    };

    const changePage = (newPage: number) => {
      if (newPage < 1) return;
      if (meta.value && newPage > meta.value.last_page) return;

      page.value = newPage;
      fetchOrder(newPage);
      window.scrollTo({ top: 0, behavior: "smooth" });
    };

    /* ----------------------------------------------------
     * INIT
     * ---------------------------------------------------- */
    onMounted(() => fetchOrder());

    return {
      /* Lists */
      orders, loading, page, meta, links, currentFilters,

      /* Filters */
      handleSearch,
      changePage
    };
  },
});
</script>