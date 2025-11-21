<template>
  <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 animate-fade-in">
    <div class="bg-dark-800 rounded-2xl p-8 w-full max-w-md mx-4 border border-dark-700 shadow-2xl animate-bounce-in">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-white">Fazer Login</h2>
        <button 
          @click="$emit('close')"
          class="text-gray-400 hover:text-white transition-colors duration-200 p-2 hover:bg-dark-700 rounded-lg"
        >
          ✕
        </button>
      </div>
      
      <form class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-300 mb-2">Email</label>
          <input 
            type="email" 
            class="w-full px-4 py-3 bg-dark-700 border border-dark-600 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200"
            placeholder="seu@email.com"
          >
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-300 mb-2">Senha</label>
          <input 
            type="password" 
            class="w-full px-4 py-3 bg-dark-700 border border-dark-600 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200"
            placeholder="••••••••"
          >
        </div>
        
        <button 
          type="submit"
          class="w-full py-3 bg-gradient-to-r from-primary-600 to-primary-500 hover:from-primary-500 hover:to-primary-400 text-white rounded-lg font-semibold shadow-lg shadow-primary-600/25 transition-all duration-200 hover:shadow-xl hover:shadow-primary-500/30"
        >
          Entrar
        </button>
      </form>
      
      <div class="mt-6 text-center">
        <p class="text-gray-400">
          Não tem conta? 
          <button class="text-primary-400 hover:text-primary-300 font-medium transition-colors duration-200">
            Registrar-se
          </button>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
defineEmits<{
  close: []
}>()
</script>