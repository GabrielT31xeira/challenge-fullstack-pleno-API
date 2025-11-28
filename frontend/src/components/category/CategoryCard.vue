<template>
  <div
      class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-200 dark:border-gray-700
           hover:shadow-xl transition-transform hover:-translate-y-1 cursor-pointer"
      @click="$emit('click')"
  >
    <div class="flex items-start justify-between mb-3">
      <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 line-clamp-1">
        {{ category.name }}
      </h2>
      <span
          class="flex items-center gap-1 text-xs px-2 py-1 rounded-full"
          :class="category.active ?
          'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' :
          'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'"
      >
        <span class="w-1.5 h-1.5 rounded-full" :class="category.active ? 'bg-green-500' : 'bg-red-500'"></span>
        {{ category.active ? 'Ativa' : 'Inativa' }}
      </span>
    </div>

    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 line-clamp-2">
      {{ category.description || 'Sem descrição' }}
    </p>

    <div class="text-sm text-gray-700 dark:text-gray-300 space-y-1">
      <p><span class="font-medium">Slug:</span> {{ category.slug }}</p>
      <p><span class="font-medium">Subcategorias:</span> {{ category.children.length }}</p>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, type PropType } from "vue";

export interface Category {
  id: string;
  name: string;
  slug: string;
  description: string | null;
  active: number;
  children: Category[];
}

export default defineComponent({
  name: "CategoryCard",
  props: {
    category: {
      type: Object as PropType<Category>,
      required: true,
    },
  },
  emits: ['click'],
});
</script>