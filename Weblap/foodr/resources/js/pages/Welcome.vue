<script setup>
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import {
  User,
  Settings,
  Shield,
  Lock,
  HelpCircle,
  LogOut,
  Heart,
  Search,
  Home
} from 'lucide-vue-next'
import { ref, computed } from 'vue'
import Avatar from 'primevue/avatar'

// Inertia page props-ból user lekérése
const page = usePage()
const user = computed(() => page.props.auth?.user)

// DEBUG - nézd meg mit kapsz
console.log('Page props:', page.props)
console.log('Auth:', page.props.auth)
console.log('User:', user.value)

const rightItems = ref([
  { label: 'SwipeR', url: '/welcome', icon: Home },
  { label: 'Kedvencek', url: '/kedvencek', icon: Heart },
  { label: 'Felfedezés', url: '/felfedezes', icon: Search },
])

const logout = () => {
  router.post('/logout')
}
</script>

<template>

  <Head title="Főoldal" />

  <div class="relative flex min-h-screen overflow-hidden
           bg-gradient-to-br from-brand-900 via-brand-700 to-brand-800
           animate-gradient">
    <!-- glow overlay -->
    <div class="pointer-events-none absolute inset-0
             bg-gradient-to-br from-accent-500 via-transparent to-accent-600
             blur-3xl animate-gradient-slow"></div>

  

    <!-- MAIN CONTENT -->
    <main class="flex-1 flex flex-col p-6 md:p-10 z-10">
      <div class="max-w-5xl mx-auto w-full">


      </div>
    </main>
    
    <!-- RIGHT SIDEBAR -->
    <aside class="hidden lg:flex lg:w-80 flex-col
                  bg-accent-300/95 backdrop-blur-xl
                  m-4 rounded-3xl
                  shadow-2xl shadow-brand-900/40
                  border border-accent-500/20 z-10
                  overflow-hidden">

      <div class="p-6 pb-4">
        <h1 class="text-6xl font-bold text-slate-900 text-center">
          <span class="text-accent-400 text-outline-shadow">Food</span>
          <span class="text-brand-500 text-outline-shadow">R</span>
        </h1>
      </div>

      <nav class="flex-1 px-3 space-y-1">
        <a v-for="item in rightItems" :key="item.label" :href="item.url" 
           class="group flex items-center px-4 py-3 rounded-xl
                  text-slate-800
                  transition-all duration-200
                  hover:bg-accent-400 hover:text-slate-900
                  hover:scale-[1.02]">
          <component :is="item.icon" 
                     class="h-5 w-5 mr-3 text-slate-700
                            transition-all duration-200
                            group-hover:text-brand-700 group-hover:scale-110" />
          <span class="font-medium">{{ item.label }}</span>
        </a>
      </nav>

      <!-- USER CARD -->
      <div class="p-5 mt-auto border-t border-accent-400/30">
        <div v-if="user" class="flex items-center gap-3 mb-4 p-3 rounded-2xl bg-accent-200/50">
          <Avatar 
            :image="user.avatar || '/imgs/emcyPFP.png'"
            shape="circle" 
            class="!w-12 !h-12" 
          />
          <div>
            <div class="font-semibold text-slate-900">{{ user.name }}</div>
            <div class="text-sm text-slate-600">{{ user.email }}</div>
          </div>
        </div>
        
        <!-- Ha nincs user, debug info -->
        <div v-else class="text-slate-800 text-sm p-3">
          Nincs bejelentkezett user
        </div>

        <button @click="logout" 
                class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-2xl
                       bg-brand-700 text-accent-200 font-medium
                       shadow-xl shadow-brand-800/40
                       transition-all
                       hover:bg-brand-800 hover:scale-[1.02]
                       focus:outline-none focus:ring-4 focus:ring-brand-500">
          <LogOut class="h-5 w-5" />
          Kijelentkezés
        </button>
      </div>
    </aside>

    <!-- MOBILE NAV -->
    <div class="md:hidden fixed bottom-0 left-0 right-0
             bg-accent-300 backdrop-blur-xl
             border-t border-accent-500/30 z-50">
      <div class="flex justify-around py-3">
        <a href="/welcome" class="flex flex-col items-center text-slate-700 hover:text-brand-700">
          <Home class="h-6 w-6" />
          <span class="text-xs mt-1">SwipeR</span>
        </a>
        <a href="/kedvencek" class="flex flex-col items-center text-slate-700 hover:text-brand-700">
          <Heart class="h-6 w-6" />
          <span class="text-xs mt-1">Kedvencek</span>
        </a>
      </div>
    </div>
  </div>
</template>