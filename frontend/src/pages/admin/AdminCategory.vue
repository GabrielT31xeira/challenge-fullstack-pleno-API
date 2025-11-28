<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
    <Navbar />

    <div class="flex">
      <AdminSidebar />

      <main class="flex-1 p-6">
        <p class="mx-3 my-1 text-3xl font-bold">Gerenciar Categorias</p>

        <!-- BOTÃO CRIAR CATEGORIA -->
        <div class="w-full flex justify-end mb-4">
          <button
              @click="openCreateModal"
              class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded shadow transition mr-9"
          >
            Criar Categoria
          </button>
        </div>

        <div class="flex flex-col gap-6">

          <!-- Search -->
          <SearchBar @search="handleSearch" class="mx-5 my-1"/>

          <!-- Loading -->
          <div v-if="loading" class="text-center text-gray-700 dark:text-gray-200 py-10">
            Carregando categorias...
          </div>

          <!-- Grid de categorias -->
          <div
              v-else
              class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 min-h-[600px]"
          >
            <CategoryCard
                v-for="category in categories"
                :key="category.id"
                :category="category"
                @click="openCategoryModal(category)"
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
              v-if="selectedCategory"
              class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50"
              @click.self="closeCategoryModal"
          >
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
              <h2 class="text-2xl font-bold mb-4">{{ selectedCategory.name }}</h2>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <!-- Informações Básicas -->
                <div class="space-y-3">
                  <div>
                    <label class="text-sm font-semibold text-gray-600 dark:text-gray-400">Descrição:</label>
                    <p class="mt-1 text-gray-900 dark:text-gray-100">{{ selectedCategory.description || 'Nenhuma descrição' }}</p>
                  </div>

                  <div>
                    <label class="text-sm font-semibold text-gray-600 dark:text-gray-400">Slug:</label>
                    <p class="mt-1 text-gray-900 dark:text-gray-100 font-mono">{{ selectedCategory.slug }}</p>
                  </div>
                </div>

                <!-- Informações Adicionais -->
                <div class="space-y-3">
                  <div>
                    <label class="text-sm font-semibold text-gray-600 dark:text-gray-400">Categoria Pai:</label>
                    <p class="mt-1 text-gray-900 dark:text-gray-100">
                      {{ selectedCategory.parent?.name || 'Nenhuma (Categoria Raiz)' }}
                    </p>
                  </div>

                  <div>
                    <label class="text-sm font-semibold text-gray-600 dark:text-gray-400">Status:</label>
                    <span
                        :class="[
                            'px-2 py-1 rounded text-xs font-bold',
                            selectedCategory.active
                                ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100'
                                : 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100'
                        ]"
                    >
                        {{ selectedCategory.active ? 'Ativa' : 'Inativa' }}
                    </span>
                  </div>

                  <div>
                    <label class="text-sm font-semibold text-gray-600 dark:text-gray-400">ID da Categoria:</label>
                    <p class="text-sm text-gray-500 dark:text-gray-400 font-mono">
                      {{ selectedCategory.id }}
                    </p>
                  </div>

                  <div>
                    <label class="text-sm font-semibold text-gray-600 dark:text-gray-400">Subcategorias:</label>
                    <p class="text-blue-600 dark:text-blue-400 font-bold">
                      {{ selectedCategory.children?.length || 0 }} subcategorias
                    </p>
                  </div>
                </div>
              </div>

              <!-- Subcategorias -->
              <div class="mb-6">
                <label class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2 block">Subcategorias:</label>
                <div class="flex flex-wrap gap-2">
                  <span
                      v-for="(child, index) in selectedCategory.children"
                      :key="index"
                      class="px-3 py-1 bg-blue-100 dark:bg-blue-800 text-blue-800 dark:text-blue-200 rounded-full text-sm"
                  >
                    {{ child.name }}
                  </span>
                  <span
                      v-if="!selectedCategory.children || selectedCategory.children.length === 0"
                      class="text-gray-500 dark:text-gray-400 text-sm"
                  >
                    Nenhuma subcategoria
                  </span>
                </div>
              </div>

              <!-- Botões de Ação -->
              <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                <button
                    @click="closeCategoryModal"
                    class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded transition"
                >
                  Fechar
                </button>

                <button
                    @click="askDelete(selectedCategory.id)"
                    class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded transition"
                >
                  Apagar Categoria
                </button>

                <button
                    @click="openEditModal(selectedCategory)"
                    class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded transition"
                >
                  Editar Categoria
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
                Tem certeza que deseja excluir a categoria
                <span class="font-semibold">{{ categoryToDelete?.name }}</span>?
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

          <!-- Modal Criar Categoria -->
          <div
              v-if="showCreateModal"
              class="fixed inset-0 bg-black bg-opacity-40 flex justify-center items-center z-50"
              @click.self="closeCreateModal"
          >
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg w-[450px] shadow-lg">

              <h2 class="text-2xl font-bold mb-4">Criar Categoria</h2>

              <div class="flex flex-col gap-4">

                <div>
                  <label class="text-sm">Nome</label>
                  <input v-model="newCategory.name" class="w-full border p-2 rounded text-black" />
                </div>

                <div>
                  <label class="text-sm">Slug</label>
                  <input v-model="newCategory.slug" class="w-full border p-2 rounded text-black" />
                </div>

                <div>
                  <label class="text-sm">Descrição</label>
                  <textarea
                      v-model="newCategory.description"
                      class="w-full border p-2 rounded text-black"
                      rows="3"
                  ></textarea>
                </div>

                <div>
                  <label class="text-sm">Categoria Pai</label>
                  <select
                      v-model="newCategory.parent_id"
                      class="w-full border p-2 rounded bg-white text-black dark:bg-gray-700 dark:text-white"
                  >
                    <option class="text-black" :value="null">Nenhuma (Categoria Raiz)</option>
                    <option v-for="category in categories" :key="category.id" :value="category.id" class="text-black">
                      {{ category.name }}
                    </option>
                  </select>
                </div>

                <label class="flex items-center gap-2 text-sm text-white">
                  <input type="checkbox" v-model="newCategory.active" />
                  Ativa
                </label>
              </div>

              <div class="flex justify-end gap-3 mt-6">
                <button @click="closeCreateModal" class="px-3 py-1 bg-gray-400 text-white rounded">
                  Cancelar
                </button>
                <button @click="submitNewCategory" class="px-3 py-1 bg-blue-600 text-white rounded">
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

              <h2 class="text-2xl font-bold mb-4">Editar Categoria</h2>

              <div class="flex flex-col gap-4">

                <div>
                  <label class="text-sm">Nome</label>
                  <input
                      v-model="editCategory.name"
                      class="w-full border p-2 rounded text-black dark:text-white bg-white dark:bg-gray-700"
                  />
                </div>

                <div>
                  <label class="text-sm">Slug</label>
                  <input
                      v-model="editCategory.slug"
                      class="w-full border p-2 rounded text-black dark:text-white bg-white dark:bg-gray-700"
                  />
                </div>

                <div>
                  <label class="text-sm">Descrição</label>
                  <textarea
                      v-model="editCategory.description"
                      class="w-full border p-2 rounded text-black dark:text-white bg-white dark:bg-gray-700"
                      rows="3"
                  ></textarea>
                </div>

                <div>
                  <label class="text-sm">Categoria Pai</label>
                  <select
                      v-model="editCategory.parent_id"
                      class="w-full border p-2 rounded text-black dark:text-white bg-white dark:bg-gray-700"
                  >
                    <option :value="null">Nenhuma (Categoria Raiz)</option>
                    <option
                        v-for="category in categories.filter(c => c.id !== editCategory.id)"
                        :key="category.id"
                        :value="category.id"
                    >
                      {{ category.name }}
                    </option>
                  </select>
                </div>

                <label class="flex items-center gap-2 text-sm text-black dark:text-white">
                  <input type="checkbox" v-model="editCategory.active" />
                  Ativa
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
                    @click="saveEditedCategory"
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
import SearchBar from "@/components/global/SearchBar.vue";
import CategoryCard from "@/components/category/CategoryCard.vue";
import Pagination from "@/components/Pagination.vue";

import {
  fetchCategory, createCategory,
  updateCategory,
  removeCategory,
  type Category, type PaginationMeta, type PaginationLinks,
} from "@/api/auth/admin/Admin";

import { toastError, toastSuccess } from "@/utils/toastApiHandler";

interface CreateCategoryDTO {
  name: string;
  slug: string;
  description: string;
  active: boolean;
  parent_id: string | null;
}

interface UpdateCategoryDTO {
  id: string;
  name: string;
  slug: string;
  description: string;
  active: boolean;
  parent_id: string | null;
}

export default defineComponent({
  components: {
    Navbar,
    AdminSidebar,
    SearchBar,
    CategoryCard,
    Pagination,
  },

  setup() {
    const categories = ref<Category[]>([]);

    const loading = ref(false);
    const page = ref(1);

    const selectedCategory = ref<Category | null>(null);

    const meta = ref<PaginationMeta | null>(null);
    const links = ref<PaginationLinks | null>(null);

    const currentFilters = ref({});

    /* --------------------------
     * Carregar categorias
     -------------------------- */
    const loadCategories = async (filters?: any) => {
      loading.value = true;
      try {
        const res = await fetchCategory(page.value, filters ?? currentFilters.value);
        categories.value = res.data ?? [];
        meta.value = res.meta ?? null;
        links.value = res.links ?? null;
      } catch (err) {
        console.error("Erro ao carregar categorias:", err);
      } finally {
        loading.value = false;
      }
    };

    /* --------------------------
     * Modal: Criar categoria
     -------------------------- */
    const showCreateModal = ref(false);

    const newCategory = ref<CreateCategoryDTO>({
      name: "",
      slug: "",
      description: "",
      active: true,
      parent_id: null,
    });

    const openCreateModal = () => {
      showCreateModal.value = true;
    };

    const closeCreateModal = () => {
      showCreateModal.value = false;
      resetForm();
    };

    const resetForm = () => {
      newCategory.value = {
        name: "",
        slug: "",
        description: "",
        active: true,
        parent_id: null,
      };
    };

    const submitNewCategory = async () => {
      try {
        const res = await createCategory(newCategory.value);

        if (res.success) {
          toastSuccess("Categoria criada com sucesso!");
          loadCategories();
          closeCreateModal();
        } else {
          toastError(res.errors.message);
        }
      } catch (err) {
        const apiError = err.response?.data;
        if (apiError) {
          toastError(apiError.message || "Erro ao criar categoria");
        } else {
          toastError("Erro no servidor.");
        }
      }
    };

    /* --------------------------
     * Atualizar
     -------------------------- */
    const showEditModal = ref(false);

    const editCategory = ref<UpdateCategoryDTO>({
      id: "",
      name: "",
      slug: "",
      description: "",
      active: true,
      parent_id: null,
    });

    const openEditModal = (category: Category) => {
      editCategory.value = {
        id: category.id,
        name: category.name,
        slug: category.slug,
        description: category.description,
        active: Boolean(category.active),
        parent_id: category.parent_id,
      };
      showEditModal.value = true;
    };

    const saveEditedCategory = async () => {
      try {
        await updateCategory(editCategory.value,editCategory.value.id);
        toastSuccess("Categoria atualizada com sucesso!");
        showEditModal.value = false;
        selectedCategory.value = null;
        await loadCategories();
      } catch (err) {
        const apiError = err.response?.data;
        if (apiError) {
          toastError(apiError.message || "Erro ao editar categoria");
        } else {
          toastError("Erro no servidor.");
        }
      }
    };

    /* --------------------------
     * Delete
     -------------------------- */
    const showDeleteModal = ref(false);
    const categoryToDelete = ref<Category | null>(null);

    const askDelete = (categoryId: string) => {
      const category = categories.value.find(c => c.id === categoryId);
      if (category) {
        categoryToDelete.value = category;
        showDeleteModal.value = true;
      }
    };

    const closeDeleteModal = () => {
      showDeleteModal.value = false;
      categoryToDelete.value = null;
    };

    const confirmDelete = async () => {
      if (!categoryToDelete.value) return;

      try {
        await removeCategory(categoryToDelete.value.id);
        toastSuccess("Categoria removida");
        loadCategories();
      } catch (e) {
        toastError("Erro ao apagar categoria");
      } finally {
        showDeleteModal.value = false;
        selectedCategory.value = null;
      }
    };

    /* --------------------------
     * Search
     -------------------------- */
    const handleSearch = (filters: any) => {
      currentFilters.value = filters;
      page.value = 1;
      loadCategories(filters);
    };

    /* --------------------------
     * Paginação
     -------------------------- */
    const changePage = (newPage: number) => {
      if (meta.value && newPage <= meta.value.last_page) {
        page.value = newPage;
        loadCategories();
      }
    };

    const openCategoryModal = (category: Category) => {
      selectedCategory.value = category;
    };

    const closeCategoryModal = () => {
      selectedCategory.value = null;
    };

    /* --------------------------
     * Lifecycle
     -------------------------- */
    onMounted(() => {
      loadCategories();
    });

    return {
      // states
      categories,
      loading,
      meta,
      links,
      selectedCategory,

      // create category modal
      showCreateModal,
      newCategory,
      openCreateModal,
      closeCreateModal,
      submitNewCategory,

      // update modal
      openEditModal,
      saveEditedCategory,
      showEditModal,
      editCategory,

      // delete modal
      showDeleteModal,
      categoryToDelete,
      askDelete,
      closeDeleteModal,
      confirmDelete,

      // category modal
      openCategoryModal,
      closeCategoryModal,

      // filters
      handleSearch,
      changePage,
    };
  },
});
</script>