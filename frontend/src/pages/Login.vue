<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100 dark:bg-gray-900">
    <div class="bg-white dark:bg-gray-800 p-8 rounded-xl w-full max-w-md shadow-lg">
      <h1 class="text-2xl font-bold mb-6 text-center text-white">Login</h1>

      <form @submit.prevent="login">
        <!-- Email -->
        <div class="mb-4">
          <label class="block mb-1 font-semibold text-white">Email</label>
          <input
              v-model="form.email"
              type="email"
              class="w-full p-2 rounded border dark:bg-gray-700 text-white"
          />
        </div>

        <!-- Senha -->
        <div class="mb-6">
          <label class="block mb-1 font-semibold text-white">Senha</label>
          <input
              v-model="form.password"
              type="password"
              class="w-full p-2 rounded border dark:bg-gray-700 text-white"
          />
        </div>

        <!-- Botão -->
        <button
            type="submit"
            class="w-full bg-blue-600 hover:bg-blue-700 text-white p-3 rounded font-bold"
        >
          Entrar
        </button>
      </form>

      <p class="mt-4 text-center text-sm text-white">
        Não tem conta?
        <router-link to="/register" class="text-blue-600">Criar conta</router-link>
      </p>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { loginUser } from "../api/auth";
import { toastSuccess, toastError, toastValidation } from "../utils/toastApiHandler";
import {ref} from "vue";
import router from "@/router";
import { useAuthStore } from "@/stores/auth";


const form = ref({
  email: "",
  password: "",
})

const auth = useAuthStore();
const login = async () => {
  try {
    const res = await loginUser(form.value);

    if (res.success) {
      auth.setAuth(res.data.user, res.data.token);
      toastSuccess("Login realizado!");

      if (res.data.user.role === "user") {
        router.push("/");
      } else {
        router.push("/admin/dashboard");
      }

      return;
    }

    toastError(res.message);
    if (res.errors) {
      toastValidation(res.errors);
    }
  } catch (error: any) {
    const apiError = error.response?.data;

    if (apiError) {
      toastError(apiError.message || "Erro ao fazer login.");
      if (apiError.errors) {
        toastValidation(apiError.errors);
      }
      return;
    }

    toastError("Erro no servidor.");
  }
};

</script>
