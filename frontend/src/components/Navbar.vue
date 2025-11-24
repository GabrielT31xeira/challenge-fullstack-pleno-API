<template>
  <nav class="flex justify-between items-center p-4 bg-gray-800 dark:bg-gray-900 text-white">
    <h1 class="text-xl font-bold">GTech Technological</h1>

    <div class="space-x-4">
      <!-- Se não estiver logado -->
      <template v-if="!auth.isAuthenticated">
        <button
            class="px-4 py-2 bg-blue-600 dark:bg-blue-700 rounded hover:bg-blue-700 dark:hover:bg-blue-800 transition"
            @click="loginPage"
        >
          Login
        </button>
        <button
            class="px-4 py-2 bg-green-600 dark:bg-green-700 rounded hover:bg-green-700 dark:hover:bg-green-800 transition"
            @click="registerPage"
        >
          Registrar
        </button>
      </template>

      <!-- Se estiver logado -->
      <template v-else>
        <span
            class="font-bold cursor-pointer hover:underline"
            @click="showProfile = true"
        >
          {{ auth.user?.name }}
        </span>
      </template>
    </div>
  </nav>

  <!-- Modal de perfil -->
  <div
      v-if="showProfile"
      class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50"
      @click.self="showProfile = false"
  >
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg max-w-sm w-full flex flex-col gap-3">
      <h2 class="text-xl font-bold mb-2">{{ auth.user?.name }}</h2>
      <p><strong>Email: </strong> {{ auth.user?.email }}</p>
      <p v-if="auth.user?.role === 'user'"><strong>Perfil: </strong> Usuário</p>
      <p v-if="auth.user?.role === 'admin'"><strong>Perfil: </strong> Adminstrador</p>

      <div class="flex justify-end gap-2 mt-4">
        <button
            class="px-4 py-2 bg-gray-500 dark:bg-gray-700 text-white rounded hover:bg-gray-600 dark:hover:bg-gray-600 transition"
            @click="showProfile = false"
        >
          Fechar
        </button>
        <button
            class="px-4 py-2 bg-red-600 dark:bg-red-700 text-white rounded hover:bg-red-700 dark:hover:bg-red-800 transition"
            @click="logout()"
        >
          Logout
        </button>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth";
import { logoutUser } from "../api/auth";
import {toastSuccess} from "@/utils/toastApiHandler.ts";

const router = useRouter();
const auth = useAuthStore();

const showProfile = ref(false);

function loginPage() {
  router.push("/login");
}

function registerPage() {
  router.push("/register");
}

async function logout() {
  try {
    await logoutUser();

    auth.logout();
    showProfile.value = false;
    toastSuccess("Logout realizado com sucesso!");
    router.push("/login");
  } catch (err: any) {
    console.error("Erro ao deslogar:", err);
    alert("Não foi possível deslogar. Tente novamente.");
  }
}
</script>
