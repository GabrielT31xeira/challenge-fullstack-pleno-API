<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
    <Navbar />
    <div class="flex">
      <Sidebar />
      <main class="flex-1 p-6">
        <div class="flex flex-col gap-6">
          <!-- Search -->
          <SearchBar @search="handleSearch" />

          <!-- Loading -->
          <div v-if="loading" class="text-center text-gray-700 dark:text-gray-200 py-10">
            Carregando produtos...
          </div>

          <!-- Grid -->
          <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 min-h-[600px]">
            <ProductCard
                v-for="product in products"
                :key="product.id"
                :product="product"
                @click="openProductModal(product)"
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

          <!-- Modal -->
          <div
              v-if="selectedProduct"
              class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50"
              @click.self="closeProductModal"
          >
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg max-w-lg w-full">
              <h2 class="text-xl font-bold mb-2">{{ selectedProduct.name }}</h2>
              <p class="mb-2">{{ selectedProduct.description }}</p>
              <p class="text-blue-600 dark:text-blue-400 font-bold mb-2">
                R$ {{ Number(selectedProduct.price).toFixed(2) }}
              </p>
              <p class="mb-2">Categoria: {{ selectedProduct.category.name }}</p>
              <p class="mb-2">Quantidade: {{ selectedProduct.quantity }}</p>

              <div class="flex flex-wrap gap-1 mb-4">
                <span
                    v-for="(tag, index) in selectedProduct.tags"
                    :key="index"
                    class="text-xs bg-blue-200 dark:bg-blue-700 text-blue-800 dark:text-blue-200 px-2 py-0.5 rounded"
                >
                  {{ tag.name }}
                </span>
              </div>

              <button
                  v-if="auth.isAuthenticated"
                  @click="addToCart(selectedProduct)"
                  class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded mr-3"
              >
                Adicionar ao Carrinho
              </button>

              <button
                  @click="closeProductModal"
                  class="px-4 py-2 bg-red-600 dark:bg-red-700 text-white rounded hover:bg-red-700 dark:hover:bg-red-800 transition"
              >
                Fechar
              </button>
            </div>
          </div>

          <div
              v-if="showCartModal"
              class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50"
              @click.self="closeCartModal"
          >
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg max-w-md w-full">

              <h2 class="text-xl font-bold mb-4">Adicionar ao Carrinho</h2>

              <label class="block mb-4">
                <span class="text-sm font-medium">Quantidade</span>
                <input
                    v-model.number="cartQuantity"
                    type="number"
                    min="1"
                    class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:border-gray-600"
                />
              </label>

              <label class="block mb-6">
                <span class="text-sm font-medium">Escolha o Carrinho (opcional)</span>

                <select
                    v-model="selectedCartId"
                    class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:border-gray-600"
                >
                  <option value="">Criar novo carrinho automaticamente</option>

                  <option
                      v-for="cart in userCarts"
                      :key="cart.id"
                      :value="cart.id"
                  >
                    {{ cart.name }}
                  </option>

                </select>
              </label>

              <div class="flex justify-end gap-3">
                <button
                    class="px-4 py-2 bg-gray-400 rounded hover:bg-gray-500"
                    @click="closeCartModal"
                >
                  Cancelar
                </button>

                <button
                    class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700"
                    @click="confirmAddToCart"
                    :disabled="cartQuantity < 1"
                >
                  Confirmar
                </button>
              </div>

            </div>
          </div>


        </div>
      </main>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, onMounted } from "vue";
import Navbar from "../components/Navbar.vue";
import Sidebar from "../components/Sidebar.vue";
import SearchBar from "../components/SearchBar.vue";
import ProductCard from "../components/ProductCard.vue";
import Pagination from "../components/Pagination.vue";
import { fetchProducts, type Product, type PaginationMeta, type PaginationLinks } from "../api/product";
import { useAuthStore } from "@/stores/auth";
import { getAll as fetchUserCarts, addItem } from "@/api/auth/Cart";
import {toastError, toastSuccess, toastValidation} from "@/utils/toastApiHandler.ts";

export default defineComponent({
  components: { Navbar, Sidebar, SearchBar, ProductCard, Pagination },
  setup() {
    const auth = useAuthStore();
    const products = ref<Product[]>([]);
    const loading = ref(false);
    const page = ref(1);

    const meta = ref<PaginationMeta | null>(null);
    const links = ref<PaginationLinks | null>(null);

    const selectedProduct = ref<Product | null>(null);
    const currentFilters = ref<{ categoryId?: string; name?: string; price?: number }>({});

    // Carrega produtos com página e filtros
    const loadProducts = async (filters?: { categoryId?: string; name?: string; price?: number }) => {
      loading.value = true;
      try {
        const res = await fetchProducts(page.value, filters ?? currentFilters.value);
        products.value = res.data ?? [];
        meta.value = res.meta ?? null;
        links.value = res.links ?? null;
      } catch (err) {
        console.error("Erro ao carregar produtos:", err);
      } finally {
        loading.value = false;
      }
    };

    // Recebe filtros do SearchBar
    const handleSearch = (filters: { categoryId?: string; name?: string; price?: number }) => {
      currentFilters.value = filters;
      page.value = 1;
      loadProducts(filters);
    };

    // Alterar página pela Pagination
    const changePage = (newPage: number) => {
      if (newPage < 1) return;
      if (meta.value && newPage > meta.value.last_page) return;
      page.value = newPage;
      loadProducts();
      window.scrollTo({ top: 0, behavior: "smooth" });
    };

    // Modal
    const openProductModal = (product: Product) => (selectedProduct.value = product);
    const closeProductModal = () => (selectedProduct.value = null);

    // Modal de adicionar ao carrinho
    const showCartModal = ref(false);
    const cartQuantity = ref(1);
    const selectedCartId = ref("");

    const userCarts = ref<any[]>([]);
    const cartsLoading = ref(false);

    const addToCart = (product: Product) => {
      selectedProduct.value = product;
      showCartModal.value = true;
    };

    const closeCartModal = () => {
      showCartModal.value = false;
      cartQuantity.value = 1;
      selectedCartId.value = "";
    };

    const confirmAddToCart = async () => {
      if (!selectedProduct.value) return;

      const payload = {
        product_id: selectedProduct.value.id,
        quantity: cartQuantity.value,
        cart_id: selectedCartId.value || null,
      };

      try {
        const res = await addItem(payload);
        closeCartModal();

        if (res.errors) {
          toastError(res.message);
          toastValidation(res.errors);
        } else {
          toastSuccess("Produto adicionado ao carrinho!");
        }
      } catch (error: any) {
        const apiError = error.response?.data;

        if (apiError) {
          toastError(apiError.message || "Erro ao adicionar item.");
          if (apiError.errors) {
            toastValidation(apiError.errors);
          }
          return;
        }

        toastError("Erro no servidor.");

        closeCartModal();
      }
    };

    const loadUserCarts = async () => {
      if (!auth.isAuthenticated) return;
      cartsLoading.value = true;

      try {
        const res = await fetchUserCarts();

        if (res.success) {
          userCarts.value = res.data;
        } else {
          userCarts.value = [];
        }
      } catch (err) {
        console.error("Erro ao buscar carrinhos do usuário:", err);
        userCarts.value = [];
      } finally {
        cartsLoading.value = false;
      }
    };

    onMounted(() => loadProducts());

    onMounted(() => {
      if (auth.isAuthenticated) {
        loadUserCarts();
      }
    })

    return {
      userCarts,
      confirmAddToCart,
      loadUserCarts,
      cartQuantity,
      selectedProduct,
      selectedCartId,
      closeCartModal,
      showCartModal,
      addToCart,
      auth,
      products,
      loading,
      meta,
      links,
      handleSearch,
      changePage,
      openProductModal,
      closeProductModal,
    };
  },
});
</script>
