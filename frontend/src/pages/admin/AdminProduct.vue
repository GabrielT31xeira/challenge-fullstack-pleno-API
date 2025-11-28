<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
    <Navbar />

    <div class="flex">
      <AdminSidebar />

      <main class="flex-1 p-6">
        <p class="mx-3 my-1 text-3xl font-bold">Gerenciar Produtos</p>

        <!-- BOTÃO CRIAR PRODUTO -->
        <div class="w-full flex justify-end mb-4">
          <button
              @click="openCreateModal"
              class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded shadow transition mr-9"
          >
            Criar Produto
          </button>
        </div>

        <div class="flex flex-col gap-6">

          <!-- Search -->
          <SearchBar @search="handleSearch" class="mx-5 my-1"/>

          <!-- Loading -->
          <div v-if="loading" class="text-center text-gray-700 dark:text-gray-200 py-10">
            Carregando produtos...
          </div>

          <!-- Grid de produtos -->
          <div
              v-else
              class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 min-h-[600px]"
          >
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

          <!-- Modal de visualização -->
          <div
              v-if="selectedProduct"
              class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50"
              @click.self="closeProductModal"
          >
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
              <h2 class="text-2xl font-bold mb-4">{{ selectedProduct.name }}</h2>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <!-- Informações Básicas -->
                <div class="space-y-3">
                  <div>
                    <label class="text-sm font-semibold text-gray-600 dark:text-gray-400">Descrição:</label>
                    <p class="mt-1 text-gray-900 dark:text-gray-100">{{ selectedProduct.description || 'Nenhuma descrição' }}</p>
                  </div>

                  <div class="grid grid-cols-2 gap-3">
                    <div>
                      <label class="text-sm font-semibold text-gray-600 dark:text-gray-400">Preço:</label>
                      <p class="text-green-600 dark:text-green-400 font-bold">
                        R$ {{ Number(selectedProduct.price).toFixed(2) }}
                      </p>
                    </div>

                    <div>
                      <label class="text-sm font-semibold text-gray-600 dark:text-gray-400">Preço Custo:</label>
                      <p class="text-orange-600 dark:text-orange-400 font-bold">
                        R$ {{ Number(selectedProduct.cost_price).toFixed(2) }}
                      </p>
                    </div>
                  </div>

                  <div class="grid grid-cols-2 gap-3">
                    <div>
                      <label class="text-sm font-semibold text-gray-600 dark:text-gray-400">Quantidade:</label>
                      <p class="text-blue-600 dark:text-blue-400 font-bold">
                        {{ selectedProduct.quantity }} unidades
                      </p>
                    </div>

                    <div>
                      <label class="text-sm font-semibold text-gray-600 dark:text-gray-400">Estoque Mínimo:</label>
                      <p class="text-yellow-600 dark:text-yellow-400 font-bold">
                        {{ selectedProduct.min_quantity }} unidades
                      </p>
                    </div>
                  </div>
                </div>

                <!-- Informações Adicionais -->
                <div class="space-y-3">
                  <div>
                    <label class="text-sm font-semibold text-gray-600 dark:text-gray-400">Categoria:</label>
                    <p class="mt-1 text-gray-900 dark:text-gray-100">
                      {{ selectedProduct.category?.name || 'Sem categoria' }}
                    </p>
                  </div>

                  <div>
                    <label class="text-sm font-semibold text-gray-600 dark:text-gray-400">Status:</label>
                    <span
                        :class="[
                            'px-2 py-1 rounded text-xs font-bold',
                            selectedProduct.active
                                ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100'
                                : 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100'
                        ]"
                    >
                        {{ selectedProduct.active ? 'Ativo' : 'Inativo' }}
                    </span>
                  </div>

                  <div>
                    <label class="text-sm font-semibold text-gray-600 dark:text-gray-400">ID do Produto:</label>
                    <p class="text-sm text-gray-500 dark:text-gray-400 font-mono">
                      {{ selectedProduct.id }}
                    </p>
                  </div>

                  <!-- Margem de Lucro -->
                  <div>
                    <label class="text-sm font-semibold text-gray-600 dark:text-gray-400">Margem de Lucro:</label>
                    <p
                        :class="[
                            'font-bold',
                            (selectedProduct.price - selectedProduct.cost_price) > 0
                                ? 'text-green-600 dark:text-green-400'
                                : 'text-red-600 dark:text-red-400'
                        ]"
                    >
                      R$ {{ (selectedProduct.price - selectedProduct.cost_price).toFixed(2) }}
                      ({{ ((selectedProduct.price - selectedProduct.cost_price) / selectedProduct.cost_price * 100).toFixed(1) }}%)
                    </p>
                  </div>
                </div>
              </div>

              <!-- Tags -->
              <div class="mb-6">
                <label class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2 block">Tags:</label>
                <div class="flex flex-wrap gap-2">
                <span
                    v-for="(tag, index) in selectedProduct.tags"
                    :key="index"
                    class="px-3 py-1 bg-blue-100 dark:bg-blue-800 text-blue-800 dark:text-blue-200 rounded-full text-sm"
                >
                    {{ tag.name }}
                </span>
                  <span
                      v-if="!selectedProduct.tags || selectedProduct.tags.length === 0"
                      class="text-gray-500 dark:text-gray-400 text-sm"
                  >
                    Nenhuma tag associada
                </span>
                </div>
              </div>

              <!-- Botões de Ação -->
              <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                <button
                    @click="closeProductModal"
                    class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded transition"
                >
                  Fechar
                </button>

                <button
                    @click="askDelete(selectedProduct.id)"
                    class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded transition"
                >
                  Apagar Produto
                </button>

                <button
                    @click="openEditModal(selectedProduct)"
                    class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded transition"
                >
                  Editar Produto
                </button>
              </div>
            </div>
          </div>

          <!-- Modal CONFIRMAR delete -->
          <div
              v-if="showDeleteModal"
              class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50"
              @click.self="closeDeleteModal"
          >
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg max-w-sm w-full shadow-lg">
              <h2 class="text-xl font-bold mb-4 text-red-600">Confirmar Exclusão</h2>
              <p class="mb-6 text-gray-700 dark:text-gray-300">
                Tem certeza que deseja excluir o produto
                <span class="font-semibold">{{ productToDelete?.name }}</span>?
              </p>

              <div class="flex justify-end gap-3">
                <button
                    @click="closeDeleteModal"
                    class="px-4 py-2 bg-gray-400 text-white rounded hover:bg-gray-500 transition"
                >
                  Cancelar
                </button>

                <button
                    @click="confirmDelete"
                    class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition"
                >
                  Deletar
                </button>
              </div>
            </div>
          </div>

          <!-- Modal Criar Produto -->
          <div
              v-if="showCreateModal"
              class="fixed inset-0 bg-black bg-opacity-40 flex justify-center items-center z-50"
              @click.self="closeCreateModal"
          >
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg w-[450px] shadow-lg">

              <h2 class="text-2xl font-bold mb-4">Criar Produto</h2>

              <div class="flex flex-col gap-4">

                <div>
                  <label class="text-sm">Nome</label>
                  <input v-model="newProduct.name" class="w-full border p-2 rounded text-black" />
                </div>

                <div>
                  <label class="text-sm">Descrição</label>
                  <textarea
                      v-model="newProduct.description"
                      class="w-full border p-2 rounded text-black "
                      rows="3"
                  ></textarea>
                </div>

                <div class="grid grid-cols-2 gap-3">
                  <div>
                    <label class="text-sm">Preço</label>
                    <input v-model.number="newProduct.price" type="number" step="0.01" class="w-full border p-2 rounded text-black"/>
                  </div>

                  <div>
                    <label class="text-sm">Preço Custo</label>
                    <input v-model.number="newProduct.cost_price" type="number" step="0.01" class="w-full border p-2 rounded text-black"/>
                  </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                  <div>
                    <label class="text-sm">Quantidade</label>
                    <input v-model.number="newProduct.quantity" type="number" class="w-full border p-2 rounded text-black"/>
                  </div>

                  <div>
                    <label class="text-sm">Min. estoque</label>
                    <input v-model.number="newProduct.min_quantity" type="number" class="w-full border p-2 rounded text-black"/>
                  </div>
                </div>

                <div>
                  <label class="text-sm">Categoria</label>
                  <select
                      v-model="newProduct.category_id"
                      class="w-full border p-2 rounded bg-white text-black dark:bg-gray-700 dark:text-white"
                      style="background-color:white; color:black;"
                  >
                    <option class="text-black" value="">Selecione...</option>
                    <option v-for="c in categories" :key="c.id" :value="c.id" class="text-black">
                      {{ c.name }}
                    </option>
                  </select>
                </div>

                <div class="border rounded p-2 bg-white dark:bg-gray-800">
                  <label class="text-sm mb-1 block">Tags</label>
                  <div v-for="t in tags.data" :key="t.id" class="flex items-center mb-1">
                    <input
                        type="checkbox"
                        :value="t.id"
                        v-model="newProduct.tags"
                        class="mr-2"
                    />
                    <span class="text-black dark:text-white">{{ t.name }}</span>
                  </div>
                </div>

                <label class="flex items-center gap-2 text-sm text-white">
                  <input type="checkbox" v-model="newProduct.active" />
                  Ativo
                </label>
              </div>

              <div class="flex justify-end gap-3 mt-6">
                <button @click="closeCreateModal" class="px-3 py-1 bg-gray-400 text-white rounded">
                  Cancelar
                </button>
                <button @click="submitNewProduct" class="px-3 py-1 bg-blue-600 text-white rounded">
                  Salvar
                </button>
              </div>

            </div>
          </div>

          <!--- Modal de Editar --->
          <div
              v-if="showEditModal"
              class="fixed inset-0 bg-black bg-opacity-40 flex justify-center items-center z-50"
              @click.self="showEditModal = false"
          >
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg w-[450px] shadow-lg">

              <h2 class="text-2xl font-bold mb-4">Editar Produto</h2>

              <div class="flex flex-col gap-4">

                <div>
                  <label class="text-sm">Nome</label>
                  <input
                      v-model="editProduct.name"
                      class="w-full border p-2 rounded text-black dark:text-white bg-white dark:bg-gray-700"
                  />
                </div>

                <div>
                  <label class="text-sm">Descrição</label>
                  <textarea
                      v-model="editProduct.description"
                      class="w-full border p-2 rounded text-black dark:text-white bg-white dark:bg-gray-700"
                      rows="3"
                  ></textarea>
                </div>

                <div class="grid grid-cols-2 gap-3">
                  <div>
                    <label class="text-sm">Preço</label>
                    <input
                        v-model.number="editProduct.price"
                        type="number"
                        step="0.01"
                        class="w-full border p-2 rounded text-black dark:text-white bg-white dark:bg-gray-700"
                    />
                  </div>

                  <div>
                    <label class="text-sm">Preço Custo</label>
                    <input
                        v-model.number="editProduct.cost_price"
                        type="number"
                        step="0.01"
                        class="w-full border p-2 rounded text-black dark:text-white bg-white dark:bg-gray-700"
                    />
                  </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                  <div>
                    <label class="text-sm">Quantidade</label>
                    <input
                        v-model.number="editProduct.quantity"
                        type="number"
                        class="w-full border p-2 rounded text-black dark:text-white bg-white dark:bg-gray-700"
                    />
                  </div>

                  <div>
                    <label class="text-sm">Min. estoque</label>
                    <input
                        v-model.number="editProduct.min_quantity"
                        type="number"
                        class="w-full border p-2 rounded text-black dark:text-white bg-white dark:bg-gray-700"
                    />
                  </div>
                </div>

                <div>
                  <label class="text-sm">Categoria</label>
                  <select
                      v-model="editProduct.category_id"
                      class="w-full border p-2 rounded text-black dark:text-white bg-white dark:bg-gray-700"
                  >
                    <option value="">Selecione...</option>
                    <option v-for="c in categories" :key="c.id" :value="c.id" class="text-black">
                      {{ c.name }}
                    </option>
                  </select>
                </div>

                <div class="border rounded p-2 bg-white dark:bg-gray-800">
                  <label class="text-sm mb-1 block">Tags</label>
                  <div v-for="t in tags.data" :key="t.id" class="flex items-center mb-1">
                    <input
                        type="checkbox"
                        :value="t.id"
                        v-model="editProduct.tags"
                        class="mr-2"
                    />
                    <span class="text-black dark:text-white">{{ t.name }}</span>
                  </div>
                </div>

                <label class="flex items-center gap-2 text-sm text-black dark:text-white">
                  <input type="checkbox" v-model="editProduct.active" />
                  Ativo
                </label>
              </div>

              <div class="flex justify-end gap-3 mt-6">
                <button
                    @click="showEditModal = false"
                    class="px-4 py-2 bg-gray-400 text-white rounded hover:bg-gray-500 transition"
                >
                  Cancelar
                </button>
                <button
                    @click="saveEditedProduct"
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition"
                >
                  Salvar
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

import Navbar from "@/components/Navbar.vue";
import AdminSidebar from "@/components/admin/AdminSidebar.vue";
import SearchBar from "@/components/SearchBar.vue";
import ProductCard from "@/components/ProductCard.vue";
import Pagination from "@/components/Pagination.vue";

import {
  fetchProducts,
  type Product,
  type PaginationLinks,
  type PaginationMeta,
} from "@/api/product";

import {
  getAllTags,
  createProduct,
  updateProduct,
  removeProduct,
} from "@/api/auth/admin/Admin";

import { toastError, toastSuccess } from "@/utils/toastApiHandler";
import {fetchCategories} from "@/api/category.ts";

interface CreateProductDTO {
  name: string;
  description: string;
  price: number;
  cost_price: number;
  quantity: number;
  min_quantity: number;
  active: boolean;
  category_id: string;
  tags: string[];
}

interface UpdateProductDTO {
  id: string;
  name: string;
  description: string;
  price: number;
  cost_price: number;
  quantity: number;
  min_quantity: number;
  active: boolean;
  category_id: string;
  tags: string[];
}

export default defineComponent({
  components: {
    Navbar,
    AdminSidebar,
    SearchBar,
    ProductCard,
    Pagination,
  },

  setup() {
    const products = ref<Product[]>([]);
    const categories = ref([]);
    const tags = ref([]);

    const loading = ref(false);
    const page = ref(1);

    const selectedProduct = ref<Product | null>(null);

    const meta = ref<PaginationMeta | null>(null);
    const links = ref<PaginationLinks | null>(null);

    const currentFilters = ref({});

    /* --------------------------
     * Carregar produtos
     -------------------------- */
    const loadProducts = async (filters?: any) => {
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

    /* --------------------------
     * Carregar categorias e tags
     -------------------------- */
    const loadCategories = async () => {
      categories.value = await fetchCategories();
    };

    const loadTags = async () => {
      tags.value = await getAllTags();
    };

    /* --------------------------
     * Modal: Criar produto
     -------------------------- */
    const showCreateModal = ref(false);

    const newProduct = ref<CreateProductDTO>({
      name: "",
      description: "",
      price: 0,
      cost_price: 0,
      quantity: 0,
      min_quantity: 0,
      active: true,
      category_id: "",
      tags: [],
    });

    const openCreateModal = () => {
      showCreateModal.value = true;
    };

    const closeCreateModal = () => {
      showCreateModal.value = false;
      resetForm();
    };

    const resetForm = () => {
      newProduct.value = {
        name: "",
        description: "",
        price: 0,
        cost_price: 0,
        quantity: 0,
        min_quantity: 0,
        active: true,
        category_id: "",
        tags: [],
      };
    };

    const submitNewProduct = async () => {
      try {
        const res = await createProduct(newProduct.value);

        if (res.success) {
          toastSuccess("Produto criado com sucesso!");
          loadProducts();
          closeCreateModal();
        } else {
          toastError(res.errors.message);
        }
      } catch (err) {
        const apiError = err.response?.data;
        if (apiError) {
          toastError(apiError.message || "Erro ao criar produto");
        } else {
          toastError("Erro no servidor.");
        }
      }
    };

    /* --------------------------
     * Atualizar
     -------------------------- */
    const showEditModal = ref(false);

    const editProduct = ref<UpdateProductDTO>({
      id: "",
      name: "",
      description: "",
      price: 0,
      cost_price: 0,
      quantity: 0,
      min_quantity: 0,
      active: true,
      category_id: "",
      tags: [],
    });

    const openEditModal = (product: Product) => {
      editProduct.value = {
        id: product.id,
        name: product.name,
        description: product.description,
        price: product.price,
        cost_price: product.cost_price,
        quantity: product.quantity,
        min_quantity: product.min_quantity,
        active: product.active,
        category_id: product.category_id,
        tags: product.tags ? product.tags.map((tag: any) => tag.id) : [],
      };
      showEditModal.value = true;
    };

    const saveEditedProduct = async () => {
      try {
        await updateProduct(editProduct.value.id, editProduct.value);
        toastSuccess("Produto atualizado com sucesso!");
        showEditModal.value = false;
        await loadProducts();
      } catch (err) {
        const apiError = err.response?.data;
        if (apiError) {
          toastError(apiError.message || "Erro ao editar produto");
        } else {
          toastError("Erro no servidor.");
        }
      }
    };

    /* --------------------------
     * Delete
     -------------------------- */
    const showDeleteModal = ref(false);
    const productToDelete = ref<any>(null);

    const askDelete = (productId: string) => {
      const product = products.value.find(p => p.id === productId);
      if (product) {
        productToDelete.value = product;
        showDeleteModal.value = true;
      }
    };

    const closeDeleteModal = () => {
      showDeleteModal.value = false;
      productToDelete.value = null;
    };

    const confirmDelete = async () => {
      if (!productToDelete.value) return;

      try {
        await removeProduct(productToDelete.value.id);
        toastSuccess("Produto removido");
        loadProducts();
      } catch (e) {
        toastError("Erro ao apagar produto");
      } finally {
        showDeleteModal.value = false;
        selectedProduct.value = null;
      }
    };

    /* --------------------------
     * Search
     -------------------------- */
    const handleSearch = (filters: any) => {
      currentFilters.value = filters;
      page.value = 1;
      loadProducts(filters);
    };

    /* --------------------------
     * Paginação
     -------------------------- */
    const changePage = (newPage: number) => {
      if (meta.value && newPage <= meta.value.last_page) {
        page.value = newPage;
        loadProducts();
      }
    };

    const openProductModal = (p: Product) => {
      selectedProduct.value = p;
    };

    const closeProductModal = () => {
      selectedProduct.value = null;
    };

    /* --------------------------
     * Lifecycle
     -------------------------- */
    onMounted(() => {
      loadProducts();
      loadCategories();
      loadTags();
    });

    return {
      // states
      products,
      categories,
      tags,
      loading,
      meta,
      links,
      selectedProduct,

      // create product modal
      showCreateModal,
      newProduct,
      openCreateModal,
      closeCreateModal,
      submitNewProduct,

      // update modal
      openEditModal,
      saveEditedProduct,
      showEditModal,
      editProduct,

      // delete modal
      showDeleteModal,
      productToDelete,
      askDelete,
      closeDeleteModal,
      confirmDelete,

      // product modal
      openProductModal,
      closeProductModal,

      // filters
      handleSearch,
      changePage,
    };
  },
});
</script>