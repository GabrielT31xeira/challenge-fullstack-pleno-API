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
                @click="openOrderModal(order.id)"
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

        <!-- ORDER MODAL -->
        <Transition name="fade">
          <div
              v-if="selectedOrder"
              class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
          >
            <div
                class="bg-white dark:bg-gray-800 w-full max-w-2xl rounded-xl shadow-xl p-6 overflow-y-auto max-h-[90vh] relative"
            >
              <!-- BOTÃO FECHAR -->
              <button
                  @click="closeOrderModal"
                  class="absolute top-3 right-3 text-gray-600 dark:text-gray-300 hover:text-black"
              >
                ✕
              </button>

              <!-- TÍTULO -->
              <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-6">
                Detalhes do Pedido
              </h2>

              <!-- INFO PRINCIPAL -->
              <div class="space-y-4">

                <p><span class="font-semibold">ID:</span> {{ selectedOrder.id }}</p>

                <p>
                  <span class="font-semibold">Status:</span>
                  {{ selectedOrder.status }}
                </p>

                <p>
                  <span class="font-semibold">Criado em:</span>
                  {{ selectedOrder.created_at }}
                </p>

                <div class="grid grid-cols-2 gap-4">

                  <div>
                    <p class="font-semibold mb-1">Endereço de Entrega:</p>
                    <ul class="list-disc ml-5 text-sm">
                      <li v-for="(line, idx) in selectedOrder.shipping_address" :key="idx">
                        {{ line }}
                      </li>
                    </ul>
                  </div>

                  <div>
                    <p class="font-semibold mb-1">Endereço de Cobrança:</p>
                    <ul class="list-disc ml-5 text-sm">
                      <li v-for="(line, idx) in selectedOrder.billing_address" :key="idx">
                        {{ line }}
                      </li>
                    </ul>
                  </div>

                </div>

                <div v-if="selectedOrder.notes">
                  <p class="font-semibold">Observações:</p>
                  <p class="text-sm">{{ selectedOrder.notes }}</p>
                </div>

              </div>

              <!-- RESUMO FINANCEIRO -->
              <div class="mt-6 border-t border-gray-300 dark:border-gray-700 pt-4">
                <h3 class="text-lg font-semibold mb-2">Resumo</h3>

                <div class="space-y-1 text-sm">
                  <p><span class="font-semibold">Subtotal:</span> R$ {{ selectedOrder.subtotal }}</p>
                  <p><span class="font-semibold">Taxas:</span> R$ {{ selectedOrder.tax }}</p>
                  <p><span class="font-semibold">Frete:</span> R$ {{ selectedOrder.shipping_cost }}</p>

                  <p class="text-lg font-semibold mt-2">
                    Total: R$ {{ selectedOrder.total }}
                  </p>
                </div>
              </div>

              <!-- LISTA DE ITENS -->
              <div class="mt-6 border-t border-gray-300 dark:border-gray-700 pt-4">
                <h3 class="text-lg font-semibold mb-4">Itens do Pedido</h3>

                <div
                    v-for="item in selectedOrder.items"
                    :key="item.id"
                    class="border border-gray-200 dark:border-gray-700 rounded-xl p-4 mb-3"
                >
                  <p class="text-lg font-semibold">{{ item.product.name }}</p>

                  <p class="text-sm">
                    Quantidade: {{ item.quantity }}
                  </p>

                  <p class="text-sm">
                    Preço unitário: R$ {{ item.product.price }}
                  </p>

                  <p class="text-sm">
                    Subtotal: R$ {{ item.subtotal }}
                  </p>

                  <p class="text-xs mt-2 text-gray-600 dark:text-gray-400">
                    Última atualização: {{ item.product.updated_at }}
                  </p>
                </div>
              </div>

              <!-- FOOTER -->
              <div class="mt-6 text-right">
                <button
                    @click="closeOrderModal"
                    class="px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 rounded-lg"
                >
                  Fechar
                </button>
              </div>
            </div>
          </div>
        </Transition>

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
import {fetchOrders, type OrderResponse, getOne} from "@/api/auth/Order.ts";
import type {PaginationMeta, PaginationLinks} from "@/api/auth/Order.ts";
import {toastError, toastValidation} from "@/utils/toastApiHandler.ts";

export default defineComponent({
  components: { Sidebar, Navbar, Pagination, SearchBar, OrderCard },

  setup() {

    /* ----------------------------------------------------
     * ORDER MODAL (VISUALIZAR)
     * ---------------------------------------------------- */

    const selectedOrder = ref<OrderResponse | null>(null)

    const openOrderModal = async (order_id:string) => {
      try {
        const res = await getOne(order_id);

        if (res.success) {
          selectedOrder.value = res.data;
        } else {
          toastError("Erro ao buscar o pedido.");
        }
      } catch (error: any) {
        const apiError = error.response?.data;

        if (apiError) {
          toastError(apiError.message || "Erro ao buscar o pedido.");
          if (apiError.errors) toastValidation(apiError.errors);
        } else {
          toastError("Erro no servidor.");
        }

        selectedOrder.value = null;
      }
    }

    const closeOrderModal = () => {
      selectedOrder.value = null;
    };

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

      /* Order modal */
      openOrderModal, closeOrderModal, selectedOrder,

      /* Filters */
      handleSearch,
      changePage
    };
  },
});
</script>