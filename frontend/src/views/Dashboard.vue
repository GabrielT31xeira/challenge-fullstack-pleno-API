<template>
  <div class="max-w-7xl mx-auto space-y-6">
    <!-- Header do Dashboard -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
      <div>
        <h1 class="text-3xl font-bold text-white mb-2">Dashboard Overview</h1>
        <p class="text-gray-400">Bem-vindo de volta! Aqui está o resumo do seu desempenho.</p>
      </div>
      <div class="flex space-x-3 mt-4 lg:mt-0">
        <button class="px-4 py-2 bg-dark-700 hover:bg-dark-600 text-gray-300 rounded-lg transition-colors duration-200">
          Exportar
        </button>
        <button class="px-4 py-2 bg-primary-600 hover:bg-primary-500 text-white rounded-lg transition-colors duration-200">
          Gerar Relatório
        </button>
      </div>
    </div>

    <!-- Grid de Métricas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <div 
        v-for="(metric, index) in metrics"
        :key="metric.title"
        class="bg-dark-800 rounded-xl p-6 border border-dark-700 shadow-lg hover:shadow-xl transition-all duration-300 animate-slide-in"
        :style="`animation-delay: ${index * 100}ms`"
      >
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-gray-400 text-sm font-medium">{{ metric.title }}</h3>
          <div class="text-2xl">{{ metric.icon }}</div>
        </div>
        <p class="text-2xl font-bold text-white mb-2">{{ metric.value }}</p>
        <div class="flex items-center">
          <span 
            :class="[
              'text-sm font-medium',
              metric.trend > 0 ? 'text-green-400' : 'text-red-400'
            ]"
          >
            {{ metric.trend > 0 ? '↗' : '↘' }} {{ Math.abs(metric.trend) }}%
          </span>
          <span class="text-gray-500 text-sm ml-2">vs último mês</span>
        </div>
      </div>
    </div>

    <!-- Gráficos e Dados -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Gráfico Principal -->
      <div class="lg:col-span-2 bg-dark-800 rounded-xl p-6 border border-dark-700">
        <h3 class="text-lg font-semibold text-white mb-6">Desempenho Mensal</h3>
        <div class="h-80 bg-dark-700 rounded-lg flex items-center justify-center">
          <p class="text-gray-400">Gráfico será implementado aqui</p>
        </div>
      </div>

      <!-- Atividades Recentes -->
      <div class="bg-dark-800 rounded-xl p-6 border border-dark-700">
        <h3 class="text-lg font-semibold text-white mb-6">Atividades Recentes</h3>
        <div class="space-y-4">
          <div 
            v-for="(activity, index) in activities"
            :key="activity.id"
            class="flex items-center space-x-3 p-3 rounded-lg bg-dark-700/50 hover:bg-dark-700 transition-colors duration-200 animate-fade-in"
            :style="`animation-delay: ${index * 50}ms`"
          >
            <div class="w-2 h-2 bg-primary-500 rounded-full"></div>
            <div class="flex-1">
              <p class="text-sm text-white">{{ activity.description }}</p>
              <p class="text-xs text-gray-500">{{ activity.time }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
interface Metric {
  title: string
  value: string
  trend: number
  icon: string
}

interface Activity {
  id: number
  description: string
  time: string
}

const metrics: Metric[] = [
  { title: 'Receita Total', value: 'R$ 42,8K', trend: 12.5, icon: '💰' },
  { title: 'Usuários Ativos', value: '1,248', trend: 8.2, icon: '👥' },
  { title: 'Taxa de Conversão', value: '4.8%', trend: -2.1, icon: '📊' },
  { title: 'Sessões', value: '12.4K', trend: 15.7, icon: '🔍' },
]

const activities: Activity[] = [
  { id: 1, description: 'Novo usuário registrado', time: '2 min atrás' },
  { id: 2, description: 'Relatório mensal gerado', time: '1 hora atrás' },
  { id: 3, description: 'Configurações atualizadas', time: '3 horas atrás' },
  { id: 4, description: 'Backup do sistema', time: '5 horas atrás' },
  { id: 5, description: 'Performance otimizada', time: '1 dia atrás' },
]
</script>