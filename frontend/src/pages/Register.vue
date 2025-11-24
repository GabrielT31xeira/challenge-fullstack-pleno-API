<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100 dark:bg-gray-900">
    <div class="bg-white dark:bg-gray-800 p-8 rounded-xl w-full max-w-md shadow-lg">
      <h1 class="text-2xl font-bold mb-6 text-center text-white">Criar Conta</h1>

      <form @submit.prevent="register">
        <!-- Nome -->
        <div class="mb-4">
          <label class="block mb-1 font-semibold text-white">Nome</label>
          <input
              v-model="form.name"
              type="text"
              class="text-white w-full p-2 rounded border dark:bg-gray-700"
          />
        </div>

        <!-- Email -->
        <div class="mb-4">
          <label class="block mb-1 font-semibold text-white">Email</label>
          <input
              v-model="form.email"
              type="email"
              class="text-white w-full p-2 rounded border dark:bg-gray-700"
          />
        </div>

        <!-- Senha -->
        <div class="mb-6">
          <label class="block mb-1 font-semibold text-white">Senha</label>
          <input
              v-model="form.password"
              type="password"
              class="text-white w-full p-2 rounded border dark:bg-gray-700"
          />
        </div>

        <!-- Botão -->
        <button
            type="submit"
            class="w-full bg-green-600 hover:bg-green-700 text-white p-3 rounded font-bold"
        >
          Registrar
        </button>
      </form>

      <p class="mt-4 text-center text-sm text-white">
        Já tem conta?
        <router-link to="/login" class="text-blue-600">Entrar</router-link>
      </p>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import {registerUser} from "../api/auth";
import {toastError, toastSuccess, toastValidation} from "@/utils/toastApiHandler.ts";

const router = useRouter();

const form = ref({
  name: "",
  email: "",
  password: "",
})

const register = async () => {
  try {
    const res = await registerUser(form.value);

    if (res.success) {
      toastSuccess("Cadastro realizado!");
      router.push("/login");
      return;
    }

    toastError(res.message);
    if (res.errors) {
      toastValidation(res.errors);
    }
  } catch (error: any) {
    const apiError = error.response?.data;

    if (apiError) {
      toastError(apiError.message || "Erro ao fazer o cadastro");
      if (apiError.errors) {
        toastValidation(apiError.errors);
      }
      return;
    }

    toastError("Erro no servidor");
  }
};
</script>
