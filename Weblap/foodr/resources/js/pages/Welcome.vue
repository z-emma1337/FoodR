<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
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
import { ref } from 'vue'
import Avatar from 'primevue/avatar'

const user = {
  name: 'Emcy',
  email: 'emcy@foodr.test',
  avatar: '/imgs/emcyPFP.png'
}

const leftItems = ref([
  { label: 'SwipeR', url: '/welcome', icon: Home },
  { label: 'Kedvencek', url: '/kedvencek', icon: Heart },
  { label: 'Felfedezés', url: '/felfedezes', icon: Search },
])

const rightItems = ref([
  { label: 'Felhasználói fiók beállítások', url: '/felhasznaloi-fiok-beallitasok', icon: User },
  { label: 'Profil szerkesztése', url: '/profil-szerkesztes', icon: Settings },
  { label: 'Fiók biztonság', url: '/fiok-biztonsag', icon: Shield },
  { label: 'Adatvédelmi beállítások', url: '/adatvedelmi-beallitasok', icon: Lock },
  { label: 'Segítség / Support', url: '/support', icon: HelpCircle },
])

const logout = () => {
  router.post('/logout')
}
</script>

<template>
  <Head title="FoodR – Főoldal" />

  <div
    class="relative flex min-h-screen overflow-hidden
           bg-gradient-to-br from-brand-900 via-brand-700 to-brand-800
           animate-gradient"
  >
    <!-- glow overlay -->
    <div
      class="pointer-events-none absolute inset-0
             bg-gradient-to-br from-accent-500 via-transparent to-accent-600
             blur-3xl animate-gradient-slow"
    ></div>

    <!-- LEFT SIDEBAR -->
    <aside
      class="hidden md:flex md:w-64 flex-col
             bg-accent-300 backdrop-blur-xl
             shadow-2xl shadow-brand-900
             border-r border-accent-500 z-10"
    >
      <div class="p-6 pb-4">
        <h2 class="text-2xl font-bold text-slate-900">
          <span class="text-accent-400 text-outline-shadow">Food</span>
          <span class="text-brand-500 text-outline-shadow">R</span>
        </h2>
      </div>

      <nav class="flex-1 px-3 space-y-1">
        <a
          v-for="item in leftItems"
          :key="item.label"
          :href="item.url"
          class="group flex items-center px-4 py-3 rounded-xl
                 text-slate-800
                 transition-all duration-200
                 hover:bg-accent-400 hover:text-slate-900
                 hover:scale-[1.02]"
        >
          <component
            :is="item.icon"
            class="h-5 w-5 mr-3 text-slate-700
                   transition-all duration-200
                   group-hover:text-brand-700 group-hover:scale-110"
          />
          <span class="font-medium">{{ item.label }}</span>
        </a>
      </nav>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="flex-1 flex flex-col p-6 md:p-10 z-10">
      <div class="max-w-5xl mx-auto w-full">
        <!-- HERO CARD -->
        <div class="rounded-3xl overflow-hidden shadow-2xl">
          <div class="bg-gradient-to-br from-accent-500/70 to-accent-600/70 p-1">
            <div class="rounded-3xl bg-accent-300 p-8 md:p-12">

              <h1 class="text-3xl font-semibold text-slate-900 mb-4">
                Üdv újra,
                <span class="text-brand-700">{{ user.name }}</span> 👋
              </h1>

              <p class="text-slate-700 max-w-xl">
                Fedezz fel új recepteket, húzz jobbra vagy balra,
                és építsd fel a saját FoodR ízlésvilágodat.
              </p>

            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- RIGHT SIDEBAR -->
    <aside
      class="hidden lg:flex lg:w-80 flex-col
             bg-accent-300 backdrop-blur-xl
             shadow-2xl shadow-brand-900/30
             border-l border-accent-500/30 z-10"
    >
      <div class="p-6 pb-4">
        <h3 class="text-xl font-semibold text-slate-900">
          Felhasználói fiók
        </h3>
      </div>

      <nav class="flex-1 px-3 space-y-1">
        <a
          v-for="item in rightItems"
          :key="item.label"
          :href="item.url"
          class="group flex items-center px-4 py-3 rounded-xl
                 text-slate-800
                 transition-all duration-200
                 hover:bg-accent-400 hover:text-slate-900
                 hover:scale-[1.02]"
        >
          <component
            :is="item.icon"
            class="h-5 w-5 mr-3 text-slate-700
                   transition-all duration-200
                   group-hover:text-brand-700 group-hover:scale-110"
          />
          <span class="font-medium">{{ item.label }}</span>
        </a>
      </nav>

      <!-- USER CARD -->
      <div class="p-5 mt-auto border-t border-accent-400
                  bg-accent-300 backdrop-blur-xl rounded-t-3xl">
        <div class="flex items-center gap-3 mb-4 p-3 rounded-2xl bg-accent-200">
          <Avatar :image="user.avatar" shape="circle" class="!w-12 !h-12" />
          <div>
            <div class="font-semibold text-slate-900">{{ user.name }}</div>
            <div class="text-sm text-slate-600">{{ user.email }}</div>
          </div>
        </div>

        <button
          @click="logout"
          class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-2xl
                 bg-brand-700 text-accent-200 font-medium
                 shadow-xl shadow-brand-800/40
                 transition-all
                 hover:bg-brand-800 hover:scale-[1.02]
                 focus:outline-none focus:ring-4 focus:ring-brand-500"
        >
          <LogOut class="h-5 w-5" />
          Kijelentkezés
        </button>
      </div>
    </aside>

    <!-- MOBILE NAV -->
    <div
      class="md:hidden fixed bottom-0 left-0 right-0
             bg-accent-300 backdrop-blur-xl
             border-t border-accent-500/30 z-50"
    >
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

