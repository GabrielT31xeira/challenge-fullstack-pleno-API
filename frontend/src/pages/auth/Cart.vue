<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
    <Navbar />
    <div class="flex">
      <Sidebar />
      <main class="flex-1 p-6">
        <div class="w-full flex justify-end mb-4">
          <button
              @click="openModal = true"
              class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded shadow transition"
          >
            Criar Carrinho
          </button>
        </div>

        <div
            v-if="openModal"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
        >
          <div class="bg-white dark:bg-gray-800 p-6 rounded shadow-lg w-80 relative">

            <h2 class="text-lg font-bold mb-4 text-gray-900 dark:text-white">
              Criar novo carrinho
            </h2>

            <input
                v-model="form.name"
                type="text"
                placeholder="Nome do carrinho"
                class="w-full p-2 border rounded mb-4 bg-gray-100 dark:bg-gray-700 dark:text-white"
            />

            <div class="flex justify-between">
              <button
                  @click="openModal = false"
                  class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded"
              >
                Cancelar
              </button>

              <button
                  @click="submitCart"
                  class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded"
              >
                Criar
              </button>
            </div>
          </div>
        </div>

        <!-- Modal de Visualização do Carrinho -->
        <div
            v-if="selectedCart"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
        >
          <div class="bg-white dark:bg-gray-800 p-6 rounded shadow-lg w-96 max-h-[90vh] overflow-y-auto relative">

            <!-- Título -->
            <h2 class="text-xl font-bold mb-4 text-gray-900 dark:text-white">
              Detalhes do Carrinho
            </h2>

            <!-- Dados principais -->
            <p class="text-gray-700 dark:text-gray-200 mb-2">
              <strong>Nome:</strong> {{ selectedCart.name }}
            </p>

            <p class="text-gray-700 dark:text-gray-200 mb-2">
              <strong>Criado em:</strong> {{ selectedCart.created_at }}
            </p>

            <p class="text-gray-700 dark:text-gray-200 mb-4">
              <strong>Atualizado em:</strong> {{ selectedCart.updated_at }}
            </p>

            <!-- Lista de Itens -->
            <div v-if="selectedCart.items?.length">
              <h3 class="text-lg font-semibold mb-2 text-gray-900 dark:text-white">
                Itens do Carrinho
              </h3>

              <ul>
                <li
                    v-for="item in selectedCart.items"
                    :key="item.id"
                    class="mb-4 p-3 rounded border border-gray-300 dark:border-gray-600"
                >
                  <button
                      @click="removeItem(selectedCart.id, item.product_id)"
                      class="absolute right-10 text-red-500 hover:text-red-700 transition"
                      title="Remover item"
                  >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="w-5 h-5"
                    >
                      <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                  </button>
                  <p class="text-gray-900 dark:text-white font-medium">
                    {{ item.product?.name }}
                  </p>

                  <p class="text-gray-700 dark:text-gray-200">
                    <strong>Quantidade:</strong> {{ item.quantity }}
                  </p>

                  <p class="text-gray-700 dark:text-gray-200">
                    <strong>Preço unitário:</strong>
                    R$ {{ Number(item.product?.price).toFixed(2) }}
                  </p>

                  <p class="text-gray-700 dark:text-gray-200">
                    <strong>Total do item:</strong>
                    R$ {{ Number(item.total_price).toFixed(2) }}
                  </p>
                </li>
              </ul>
            </div>

            <!-- Totais -->
            <div class="mt-4 border-t pt-4 border-gray-300 dark:border-gray-700">
              <p class="text-gray-800 dark:text-gray-100">
                <strong>Total de itens:</strong> {{ selectedCart.items_count }}
              </p>

              <p class="text-gray-800 dark:text-gray-100">
                <strong>Total de unidades:</strong> {{ selectedCart.total_quantity }}
              </p>

              <p class="text-gray-900 dark:text-white text-lg font-bold mt-2">
                Total do Carrinho: R$ {{ Number(selectedCart.total).toFixed(2) }}
              </p>
            </div>

            <!-- Botão fechar -->
            <div class="flex justify-end mt-6">
              <button
                  @click="closeCartModal"
                  class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded"
              >
                Fechar
              </button>
            </div>

          </div>
        </div>


        <div class="flex flex-col gap-6">
          <!-- Search -->
          <SearchBar @search="handleSearch" />

          <!-- Loading -->
          <div v-if="loading" class="text-center text-gray-700 dark:text-gray-200 py-10">
            Carregando produtos...
          </div>

          <!-- Grid -->
          <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 min-h-[500px]">
            <CartCard
                v-for="cart in carts"
                :key="cart.id"
                :cart="cart"
                @click="openCartModal(cart.id)"
                class="max-h-[150px]"
            />
          </div>

          <!-- Paginação -->
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

import {defineComponent, onMounted, ref} from "vue";
import { toastError, toastSuccess, toastValidation } from "@/utils/toastApiHandler.ts";

import {type Cart, createCart, deleteItem, fetchCarts, getOne} from "@/api/auth/Cart.ts";

import Pagination from "@/components/Pagination.vue";
import SearchBar from "@/components/cart/SearchBar.vue";
import CartCard from "@/components/cart/CartCard.vue";
import type { PaginationLinks } from "@/api/product.ts";

export default defineComponent({
  components: { Sidebar, Navbar, Pagination, SearchBar, CartCard },
  setup() {
    const openModal = ref(false);
    const cartName = ref("");
    const loading = ref(false);
    const carts = ref<Cart[]>([]);
    const page = ref(1);
    const form = ref({
      name: "",
    });

    const currentFilters = ref<{ search?: string }>({});

    const meta = ref<any>(null);
    const links = ref<PaginationLinks | null>(null);

    function resetModal() {
      cartName.value = "";
      form.value.name = "";
      openModal.value = false;
    }

    const fetchCart = async (
        p: number = page.value,
        filters: { search?: string } = currentFilters.value
    ) => {
      loading.value = true;
      try {
        const payload = await fetchCarts(p, filters);
        carts.value = payload.data ?? [];
        meta.value = payload.meta ?? null;
        links.value = payload.links ?? null;
      } catch (err: any) {
        console.error("Erro ao carregar carrinhos:", err);
        toastError("Erro ao carregar carrinhos.");
      } finally {
        loading.value = false;
      }
    };

    const submitCart = async () => {
      if (!form.value.name?.trim()) {
        toastError("Digite um nome para o carrinho");
        return;
      }

      try {
        const res = await createCart({ name: form.value.name });

        if (res.success) {
          toastSuccess("Carrinho criado com sucesso!");
          page.value = 1;
          await fetchCart(1);
          resetModal();
          return;
        }

        toastError(res.message || "Erro ao criar o carrinho.");
        if (res.errors) toastValidation(res.errors);
        resetModal();
      } catch (error: any) {
        const apiError = error.response?.data;
        if (apiError) {
          toastError(apiError.message || "Erro ao criar o carrinho.");
          if (apiError.errors) toastValidation(apiError.errors);
        } else {
          toastError("Erro no servidor.");
        }
        resetModal();
      }
    };

    const handleSearch = (filters: { search?: string }) => {
      currentFilters.value = filters;
      page.value = 1;
      fetchCart(1, filters);
    };

    const changePage = (newPage: number) => {
      if (newPage < 1) return;
      if (meta.value && newPage > meta.value.last_page) return;

      page.value = newPage;
      fetchCart(newPage);
      window.scrollTo({ top: 0, behavior: "smooth" });
    };

    const selectedCart = ref(null);

    const openCartModal = async (cart_id: string) => {
      try {
        const res = await getOne(cart_id);

        if (res.success) {
          selectedCart.value = res.data;
          toastSuccess("Carrinho carregado!");
        } else {
          toastError("Erro ao buscar o carrinho.");
        }
      } catch (error: any) {
        const apiError = error.response?.data;

        if (apiError) {
          toastError(apiError.message || "Erro ao buscar o carrinho.");
          if (apiError.errors) toastValidation(apiError.errors);
        } else {
          toastError("Erro no servidor.");
        }

        selectedCart.value = null;
      }
    };

    const removeItem = async (cart_id: string, item_id: string) => {
      try {
        const res = await deleteItem(cart_id, item_id);

        if (res.success) {
          toastSuccess("Item removido!");
          closeCartModal()
        } else {
          toastError("Erro ao remover o item");
          closeCartModal()
        }
      } catch (error: any) {
        const apiError = error.response?.data;
        closeCartModal()
        if (apiError) {
          toastError(apiError.message || "Erro ao remover o item");
          if (apiError.errors) toastValidation(apiError.errors);
        } else {
          toastError("Erro no servidor.");
        }

        selectedCart.value = null;
      }
    };

    const closeCartModal = () => {
      selectedCart.value = null;
    };

    const formatDate = (dateStr: string) => {
      const [day, month, yearAndTime] = dateStr.split('/');
      const [year, time] = yearAndTime.split(' ');
      return new Date(`${year}-${month}-${day}T${time}`);
    };

    onMounted(() => fetchCart());

    return {
      removeItem,
      formatDate,
      openModal,
      cartName,
      loading,
      carts,
      page,
      form,
      currentFilters,
      meta,
      links,
      selectedCart,
      fetchCart,
      submitCart,
      handleSearch,
      changePage,
      openCartModal,
      closeCartModal,
    };
  },
});

</script>


