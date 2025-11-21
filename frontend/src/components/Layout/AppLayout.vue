<template>
  <div class="min-h-screen bg-dark-900 text-gray-100 transition-colors duration-300">
    <!-- Header -->
    <header class="bg-dark-800 border-b border-dark-700 shadow-lg">
      <div class="flex items-center justify-between px-6 py-4">
        <!-- Logo e Navegação Esquerda -->
        <div class="flex items-center space-x-8">
          <div class="flex items-center space-x-3">
            <div class="w-8 h-8 bg-gradient-to-br from-primary-500 to-primary-600 rounded-lg"></div>
            <h1 class="text-xl font-bold bg-gradient-to-r from-primary-400 to-primary-300 bg-clip-text text-transparent">
              DashboardPro
            </h1>
          </div>
          
          <!-- Navegação Superior -->
          <nav class="hidden md:flex space-x-6">
            <button 
              v-for="item in navigation"
              :key="item.name"
              @click="setActivePage(item.component)"
              :class="[
                'px-3 py-2 rounded-lg font-medium transition-all duration-200',
                activePage === item.component
                  ? 'bg-primary-600 text-white shadow-lg shadow-primary-600/25'
                  : 'text-gray-400 hover:text-white hover:bg-dark-700'
              ]"
            >
              <component :is="item.icon" class="w-5 h-5 inline-block mr-2" />
              {{ item.name }}
            </button>
          </nav>
        </div>

        <!-- Área de Login/Registro -->
        <div class="flex items-center space-x-4">
          <button 
            @click="showLogin = true"
            class="px-4 py-2 text-gray-300 hover:text-white transition-colors duration-200 font-medium"
          >
            Login
          </button>
          <button 
            @click="showRegister = true"
            class="px-6 py-2 bg-gradient-to-r from-primary-600 to-primary-500 hover:from-primary-500 hover:to-primary-400 text-white rounded-lg font-medium shadow-lg shadow-primary-600/25 transition-all duration-200 hover:shadow-xl hover:shadow-primary-500/30"
          >
            Registrar
          </button>
        </div>
      </div>
    </header>

    <!-- Conteúdo Principal -->
    <main class="flex-1 p-6">
      <component 
        :is="activePage" 
        class="animate-fade-in"
      />
    </main>

    <!-- Modais de Login/Registro -->
    <LoginModal v-if="showLogin" @close="showLogin = false" />
    <RegisterModal v-if="showRegister" @close="showRegister = false" />
  </div>
</template>

<script setup lang="ts">
import { ref, shallowRef } from 'vue'
import Dashboard from '@/views/Dashboard.vue'
// import Analytics from '@/views/Analytics.vue'
// import Settings from '@/views/Settings.vue'
// import Profile from '@/views/Profile.vue'
import LoginModal from '@/components/Auth/LoginModal.vue'
// import RegisterModal from '@/components/Auth/RegisterModal.vue'

// Ícones (substitua por ícones reais depois)
const ChartIcon = { template: '<div>📊</div>' }
const AnalyticsIcon = { template: '<div>📈</div>' }
const SettingsIcon = { template: '<div>⚙️</div>' }
const ProfileIcon = { template: '<div>👤</div>' }

const navigation = [
  { name: 'Dashboard', component: Dashboard, icon: ChartIcon },
//   { name: 'Analytics', component: Analytics, icon: AnalyticsIcon },
//   { name: 'Settings', component: Settings, icon: SettingsIcon },
//   { name: 'Profile', component: Profile, icon: ProfileIcon },
]

const activePage = shallowRef(Dashboard)
const showLogin = ref(false)
const showRegister = ref(false)

const setActivePage = (component: any) => {
  activePage.value = component
}
</script>