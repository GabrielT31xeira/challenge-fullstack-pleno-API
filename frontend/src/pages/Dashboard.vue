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
          <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <ProductCard
                v-for="product in products"
                :key="product.id"
                :product="product"
                @click="openProductModal(product)"
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
                  @click="closeProductModal"
                  class="px-4 py-2 bg-red-600 dark:bg-red-700 text-white rounded hover:bg-red-700 dark:hover:bg-red-800 transition"
              >
                Fechar
              </button>
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

export default defineComponent({
  components: { Navbar, Sidebar, SearchBar, ProductCard, Pagination },

  setup() {
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
      // opcional: scroll to top do main
      window.scrollTo({ top: 0, behavior: "smooth" });
    };

    // Modal
    const openProductModal = (product: Product) => (selectedProduct.value = product);
    const closeProductModal = () => (selectedProduct.value = null);

    onMounted(() => loadProducts());

    return {
      products,
      loading,
      meta,
      links,
      handleSearch,
      changePage,
      selectedProduct,
      openProductModal,
      closeProductModal,
    };
  },
});
</script>
