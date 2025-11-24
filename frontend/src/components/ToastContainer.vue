<template>
  <div class="fixed top-4 right-4 z-50 flex flex-col gap-3">
    <div
        v-for="toast in toasts"
        :key="toast.id"
        class="px-4 py-3 rounded shadow text-white w-72 animate-fade"
        :class="toast.type === 'success'
        ? 'bg-green-600'
        : 'bg-red-600'"
    >
      {{ toast.message }}
    </div>
  </div>
</template>

<script lang="ts" setup>
import { useToast } from "../composables/useToast";

const { toasts } = useToast();
</script>

<style>
@keyframes fade {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fade {
  animation: fade .3s ease-out;
}
</style>
