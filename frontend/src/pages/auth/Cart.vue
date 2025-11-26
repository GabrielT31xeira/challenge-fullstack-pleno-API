<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
    <Navbar />

    <div class="flex">
      <Sidebar />

      <main class="flex-1 p-6">

        <!-- BOTÃO CRIAR CARRINHO -->
        <div class="w-full flex justify-end mb-4">
          <button
              @click="openModal = true"
              class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded shadow transition"
          >
            Criar Carrinho
          </button>
        </div>

        <!-- MODAL CRIAR CARRINHO -->
        <div v-if="openModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
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

        <!-- MODAL VISUALIZAÇÃO DO CARRINHO -->
        <div
            v-if="selectedCart"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
        >
          <div class="bg-white dark:bg-gray-800 p-6 rounded shadow-lg w-96 max-h-[100vh] overflow-y-auto relative">

            <h2 class="text-xl font-bold mb-4 text-gray-900 dark:text-white">
              Detalhes do Carrinho
            </h2>

            <div class="flex justify-end mt-4">
              <button
                  @click="deleteCart(selectedCart.id)"
                  class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded"
              >
                Apagar Carrinho
              </button>
            </div>

            <!-- INFO -->
            <p class="mb-2"><strong>Nome:</strong> {{ selectedCart.name }}</p>
            <p class="mb-2"><strong>Criado em:</strong> {{ selectedCart.created_at }}</p>
            <p class="mb-4"><strong>Atualizado em:</strong> {{ selectedCart.updated_at }}</p>

            <!-- LISTA DE ITENS -->
            <div v-if="selectedCart.items?.length">
              <h3 class="text-lg font-semibold mb-2 text-gray-900 dark:text-white">
                Itens do Carrinho
              </h3>

              <ul>
                <li
                    v-for="item in selectedCart.items"
                    :key="item.id"
                    class="relative mb-4 p-3 rounded border border-gray-300 dark:border-gray-600"
                >

                  <!-- botão remover -->
                  <button
                      @click="removeItem(selectedCart.id, item.product_id)"
                      class="absolute right-3 text-red-500 hover:text-red-700 transition"
                      title="Remover item"
                  >
                    ✖
                  </button>

                  <!-- botão editar -->
                  <button
                      @click="openEditModal(item)"
                      class="absolute right-10 text-blue-500 hover:text-blue-700 transition"
                      title="Editar item"
                  >
                    ✎
                  </button>

                  <p class="font-medium">{{ item.product?.name }}</p>
                  <p><strong>Quantidade:</strong> {{ item.quantity }}</p>
                  <p><strong>Preço unitário:</strong> R$ {{ Number(item.product?.price).toFixed(2) }}</p>
                  <p><strong>Total do item:</strong> R$ {{ Number(item.total_price).toFixed(2) }}</p>
                </li>
              </ul>
            </div>

            <!-- TOTAIS -->
            <div class="mt-4 border-t pt-4 border-gray-300 dark:border-gray-700">
              <p><strong>Total de itens:</strong> {{ selectedCart.items_count }}</p>
              <p><strong>Total de unidades:</strong> {{ selectedCart.total_quantity }}</p>

              <p class="text-lg font-bold mt-2">
                Total do Carrinho: R$ {{ Number(selectedCart.total).toFixed(2) }}
              </p>
            </div>

            <div class="flex justify-end mt-6">
              <button
                  @click="closeCartModal"
                  class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded mr-4"
              >
                Fechar
              </button>
              <button
                  @click="clearCart(selectedCart.id)"
                  class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded mr-4"
              >
                Limpar carrinho
              </button>
              <button
                  @click="openOrderModal(selectedCart.id)"
                  class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded"
              >
                Fazer pedido
              </button>
            </div>

          </div>
        </div>

        <!-- MODAL EDITAR QUANTIDADE -->
        <div v-if="showEditModal"
             class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">

          <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg w-80">
            <h2 class="text-lg font-semibold mb-4">Editar quantidade</h2>

            <label class="block text-sm mb-2">Nova quantidade</label>

            <input
                type="number"
                v-model="editQuantity"
                min="1"
                class="w-full border rounded-lg p-2 mb-4 bg-gray-50 dark:bg-gray-700"
            />

            <div class="flex justify-end gap-3">
              <button
                  @click="closeEditModal"
                  class="px-4 py-2 rounded-lg bg-gray-300 dark:bg-gray-600"
              >
                Cancelar
              </button>

              <button
                  @click="confirmEdit"
                  class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white"
              >
                Salvar
              </button>
            </div>
          </div>

        </div>

        <!-- LISTAGEM DE CARRINHOS -->
        <div class="flex flex-col gap-6">

          <SearchBar @search="handleSearch" />

          <div v-if="loading" class="text-center py-10">Carregando produtos...</div>

          <div
              v-else
              class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 min-h-[500px]"
          >
            <CartCard
                v-for="cart in carts"
                :key="cart.id"
                :cart="cart"
                @click="openCartModal(cart.id)"
                class="max-h-[150px]"
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

        <!-- MODAL DE PEDIDO -->
        <div v-if="showOrderModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg w-96 relative">
            <h2 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Fazer Pedido</h2>

            <label class="block text-sm mb-1 text-gray-700 dark:text-gray-200">Endereço de entrega</label>
            <input
                type="text"
                v-model="orderForm.shipping_address"
                class="w-full border border-gray-300 dark:border-gray-600 rounded-lg p-2 mb-4 text-gray-900"
                placeholder="Rua, número, bairro, cidade - UF"
            />

            <label class="block text-sm mb-1 text-gray-700 dark:text-gray-200">Endereço de cobrança</label>
            <input
                type="text"
                v-model="orderForm.billing_address"
                class="w-full border border-gray-300 dark:border-gray-600 rounded-lg p-2 mb-4 text-gray-900"
                placeholder="Rua, número, bairro, cidade - UF"
            />

            <label class="block text-sm mb-1 text-gray-700 dark:text-gray-200">Observações</label>
            <textarea
                v-model="orderForm.notes"
                class="w-full border border-gray-300 dark:border-gray-600 rounded-lg p-2 mb-4 text-gray-900"
                placeholder="Ex: Entregar no período da tarde"
            ></textarea>

            <div class="flex justify-end gap-3">
              <button
                  @click="closeOrderModal"
                  class="px-4 py-2 rounded-lg bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-500"
              >
                Cancelar
              </button>

              <button
                  @click="submitOrder"
                  class="px-4 py-2 rounded-lg bg-green-600 hover:bg-green-700 text-white"
              >
                Enviar Pedido
              </button>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script lang="ts">
import Sidebar from "@/components/Sidebar.vue";
import Navbar from "@/components/Navbar.vue";

import { defineComponent, onMounted, ref } from "vue";
import { toastError, toastSuccess, toastValidation } from "@/utils/toastApiHandler.ts";

import {
  type Cart,
  createCart,
  deleteItem,
  fetchCarts,
  getOne,
  updateItem as update,
  clearCart as clear,
  deleteCart as remove
} from "@/api/auth/Cart.ts";

import Pagination from "@/components/Pagination.vue";
import SearchBar from "@/components/cart/SearchBar.vue";
import CartCard from "@/components/cart/CartCard.vue";
import type { PaginationLinks } from "@/api/product.ts";
import {createOrder} from "@/api/auth/Order.ts";

export default defineComponent({
  components: { Sidebar, Navbar, Pagination, SearchBar, CartCard },

  setup() {
    /* ----------------------------------------------------
     * STATE PRINCIPAL
     * ---------------------------------------------------- */
    const loading = ref(false);
    const carts = ref<Cart[]>([]);
    const page = ref(1);
    const currentFilters = ref<{ search?: string }>({});
    const meta = ref<any>(null);
    const links = ref<PaginationLinks | null>(null);

    /* ----------------------------------------------------
     * CREATE CART MODAL
     * ---------------------------------------------------- */
    const openModal = ref(false);
    const form = ref({ name: "" });

    function resetModal() {
      form.value.name = "";
      openModal.value = false;
    }

    /* ----------------------------------------------------
     * FETCH ALL CARTS
     * ---------------------------------------------------- */
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

    /* ----------------------------------------------------
     * CREATE CART
     * ---------------------------------------------------- */
    const submitCart = async () => {
      if (!form.value.name.trim()) {
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

      } catch (error: any) {
        const apiError = error.response?.data;

        if (apiError) {
          toastError(apiError.message || "Erro ao criar o carrinho.");
          if (apiError.errors) toastValidation(apiError.errors);
        } else {
          toastError("Erro no servidor.");
        }
      }

      resetModal();
    };

    /* ----------------------------------------------------
     * SEARCH & PAGINATION
     * ---------------------------------------------------- */
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

    /* ----------------------------------------------------
     * CART MODAL (VISUALIZAR)
     * ---------------------------------------------------- */
    const selectedCart = ref<Cart | null>(null);

    const openCartModal = async (cart_id: string) => {
      try {
        const res = await getOne(cart_id);

        if (res.success) {
          selectedCart.value = res.data;
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

    const closeCartModal = () => {
      selectedCart.value = null;
    };

    /* ----------------------------------------------------
     * REMOVE ITEM
     * ---------------------------------------------------- */
    const removeItem = async (cart_id: string, item_id: string) => {
      try {
        const res = await deleteItem(cart_id, item_id);

        if (res.success) {
          toastSuccess("Item removido!");
          closeCartModal();
          fetchCart();
        } else {
          toastError("Erro ao remover o item");
        }
      } catch (error: any) {
        const apiError = error.response?.data;

        if (apiError) {
          toastError(apiError.message || "Erro ao remover o item");
          if (apiError.errors) toastValidation(apiError.errors);
        } else {
          toastError("Erro no servidor.");
        }
      }

      closeCartModal();
      selectedCart.value = null;
    };

    /* ----------------------------------------------------
     * EDIT ITEM MODAL
     * ---------------------------------------------------- */
    const showEditModal = ref(false);
    const editQuantity = ref(1);
    const editingItem = ref<any>(null);

    function openEditModal(item: any) {
      editingItem.value = item;
      editQuantity.value = Number(item.quantity);
      showEditModal.value = true;
    }

    function closeEditModal() {
      editingItem.value = null;
      showEditModal.value = false;
    }

    /* ----------------------------------------------------
     * UPDATE ITEM
     * ---------------------------------------------------- */
    const updateItem = async (cart_id: string, product_id: string, quantity: number) => {
      try {
        const res = await update(cart_id, product_id, quantity);

        if (res.success) {
          toastSuccess("Item atualizado!");
          closeCartModal();
          fetchCart();
        } else {
          toastError("Erro ao atualizar o item");
        }
      } catch (error: any) {
        const apiError = error.response?.data;

        if (apiError) {
          toastError(apiError.message || "Erro ao atualizar o item");
          if (apiError.errors) toastValidation(apiError.errors);
        } else {
          toastError("Erro no servidor.");
        }
      }

      closeCartModal();
      selectedCart.value = null;
    };

    /* ----------------------------------------------------
     * CONFIRMAR EDIÇÃO
     * ---------------------------------------------------- */
    async function confirmEdit() {
      if (!editingItem.value) return;

      await updateItem(
          selectedCart.value!.id,
          editingItem.value.product_id,
          editQuantity.value
      );

      closeEditModal();
    }

    /* ----------------------------------------------------
     * LIMPAR CARRINHO
     * ---------------------------------------------------- */

    const clearCart = async (cart_id: string) => {
      try {
        const res = await clear(cart_id);

        if (res.success) {
          toastSuccess("Carrinho limpo");
          closeCartModal();
          fetchCart();
        } else {
          toastError("Erro ao limpar o carrinho");
        }
      } catch (error: any) {
        const apiError = error.response?.data;

        if (apiError) {
          toastError(apiError.message || "Erro ao limpar o carrinho");
          if (apiError.errors) toastValidation(apiError.errors);
        } else {
          toastError("Erro no servidor.");
        }
      }

      closeCartModal();
      selectedCart.value = null;
    };

    /* ----------------------------------------------------
     * LIMPAR CARRINHO
     * ---------------------------------------------------- */

    const deleteCart = async (cart_id: string) => {
      try {
        const res = await remove(cart_id);

        if (res.success) {
          toastSuccess("Carrinho apagado");
          closeCartModal();
          fetchCart();
        } else {
          toastError("Erro ao apagar o carrinho");
        }
      } catch (error: any) {
        const apiError = error.response?.data;

        if (apiError) {
          toastError(apiError.message || "Erro ao limpar o carrinho");
          if (apiError.errors) toastValidation(apiError.errors);
        } else {
          toastError("Erro no servidor.");
        }
      }

      closeCartModal();
      selectedCart.value = null;
    };

    /* ----------------------------------------------------
    * ORDER MODAL
    * ---------------------------------------------------- */
    const showOrderModal = ref(false);

    // Formulário do pedido
    const orderForm = ref({
      cart_id: "",
      shipping_address: "",
      billing_address: "",
      notes: "",
    });

    // Abre o modal e já popula o cart_id
    function openOrderModal(cart_id: string) {
      orderForm.value.cart_id = cart_id;
      showOrderModal.value = true;
    }

    // Fecha o modal e limpa o formulário
    function closeOrderModal() {
      showOrderModal.value = false;
      orderForm.value = {
        cart_id: "",
        shipping_address: "",
        billing_address: "",
        notes: "",
      };
    }

    // Função para enviar pedido (API)
    const submitOrder = async () => {
      try {
        const res = await createOrder({
          cart_id: orderForm.value.cart_id,
          shipping_address: [orderForm.value.shipping_address],
          billing_address: [orderForm.value.billing_address],
          notes: orderForm.value.notes,
        });

        if (res.success) {
          toastSuccess("Pedido criado com sucesso!");
          closeOrderModal();
          fetchCart();
        } else {
          toastError(res.errors[0]);
        }
      } catch (error: any) {
        const apiError = error.response?.data;
        if (apiError) {
          toastError(apiError.message || "Erro ao criar pedido");
        } else {
          toastError("Erro no servidor.");
        }
      }
    };

    /* ----------------------------------------------------
     * HELPERS
     * ---------------------------------------------------- */
    const formatDate = (dateStr: string) => {
      const [day, month, yearAndTime] = dateStr.split('/');
      const [year, time] = yearAndTime.split(' ');
      return new Date(`${year}-${month}-${day}T${time}`);
    };

    /* ----------------------------------------------------
     * INIT
     * ---------------------------------------------------- */
    onMounted(() => fetchCart());

    /* ----------------------------------------------------
     * EXPORTAR PARA O TEMPLATE
     * ---------------------------------------------------- */
    return {
      /* Lists */
      carts, loading, page, meta, links, currentFilters,

      /* Modal create */
      openModal, form, submitCart, resetModal,

      /* Filters */
      handleSearch, changePage,

      /* Cart view modal */
      selectedCart, openCartModal, closeCartModal,

      /* Remove item */
      removeItem,

      /* Edit item */
      showEditModal,
      editQuantity,
      openEditModal,
      closeEditModal,
      confirmEdit,

      /* Update */
      updateItem,

      /* Clear cart */
      clearCart,

      /* Delete cart */
      deleteCart,

      /* Utils */
      formatDate,

      /* Order */
      openOrderModal,
      showOrderModal,
      orderForm,
      closeOrderModal,
      submitOrder
    };
  },
});
</script>



