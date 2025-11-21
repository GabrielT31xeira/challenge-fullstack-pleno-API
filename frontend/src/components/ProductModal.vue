<template>
  <div
      @click="onClick"
      class="p-4 bg-gray-100 dark:bg-gray-800 rounded shadow hover:shadow-lg cursor-pointer transition"
  >
    <h2 class="font-bold">{{ name }}</h2>
    <p>{{ price }}</p>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";

export default defineComponent({
  props: {
    name: { type: String, required: true },
    price: { type: String, required: true },
    onClick: { type: Function, required: true },
  },
});
</script>
