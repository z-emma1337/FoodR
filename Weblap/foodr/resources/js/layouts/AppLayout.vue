<script setup>
import { Head, router, usePage } from '@inertiajs/vue3'
import {
  LogOut,
  LogIn,
  Heart,
  Search,
  Home,
  User,
  Settings,
  HelpCircle,
} from 'lucide-vue-next'
import { ref, computed } from 'vue'
import Avatar from 'primevue/avatar'

const page = usePage()
const user = computed(() => page.props.auth?.user)
const likedCount = computed(() => page.props.likedCount ?? 0)

const LeftnavItems = ref([
  { label: 'SwipeR', url: '/welcome', icon: Home },
  { label: 'Kedvencek', url: '/kedvencek', icon: Heart },
  { label: 'Felfedezés', url: '/felfedezes', icon: Search },
])

const RightnavItems = ref([
  { label: 'Profilom', url: '/profil', icon: User },
  { label: 'Beállítások', url: '/beallitasok', icon: Settings },
  { label: 'Súgó', url: '/sugo', icon: HelpCircle },
])

const logout = () => {
  router.post('/logout')
}

const login = () => {
  router.visit('/bejelentkezes')
}
</script>

<template>
  <div class="h-screen relative flex min-h-screen overflow-hidden
              bg-gradient-to-br from-brand-900 via-brand-700 to-brand-800
              animate-gradient">

   <!-- glow -->
   <!-- glow -->
    <div class="pointer-events-none absolute inset-0
                bg-gradient-to-br from-accent-500/20 via-transparent to-accent-600/20
                blur-3xl animate-gradient-slow" />
    <!-- LEFT NAVBAR -->
    <aside class="hidden lg:flex lg:w-80 flex-col m-4 z-10">
      <!-- ACCENT FRAME -->
      <div class="rounded-3xl overflow-hidden shadow-2xl h-full">
        <div class="bg-gradient-to-br from-accent-500/80 to-accent-600/80 p-1 h-full">
          <!-- NAVBAR CARD -->
          <div class="h-full rounded-3xl flex flex-col
                      bg-gradient-to-br from-accent-300 via-accent-200 to-accent-300
                      animate-card-gradient backdrop-blur-xl">

            <div class="p-6 pb-4">
              <h1 class="text-6xl font-bold text-center">
                <span class="text-accent-400 text-outline-shadow">Food</span><span
                  class="text-brand-500 text-outline-shadow">R</span>
              </h1>
            </div>

            <nav class="flex-1 px-3 space-y-1">
              <a v-for="item in LeftnavItems" :key="item.label" :href="item.url" class="group flex items-center px-4 py-3 rounded-xl
                        transition hover:bg-accent-400 hover:scale-[1.02] text-slate-900">
                <component :is="item.icon" class="h-5 w-5 mr-3" />
                  <span class="flex flex-1 items-center justify-between gap-2">
                  <span>{{ item.label }}</span>
                  <span
                    v-if="item.label === 'Kedvencek' && user"
                    class="px-2 py-0.5 rounded-full bg-brand-600 text-accent-200 text-xs font-semibold"
                  >
                    {{ likedCount }}
                  </span>
                </span>
              </a>
            </nav>
          </div>
        </div>
      </div>
    </aside>
    <!-- PAGE CONTENT -->
    <main class="flex-1 flex flex-col p-6 md:p-10 z-10">
      <div class="max-w-5xl mx-auto w-full">
        <slot />
      </div>
    </main>

    <!-- RIGHT NAVBAR -->
    <aside class="hidden lg:flex lg:w-80 flex-col m-4 z-10">
      <!-- ACCENT FRAME -->
      <div class="rounded-3xl overflow-hidden shadow-2xl h-full">
        <div class="bg-gradient-to-br from-accent-500/80 to-accent-600/80 p-1 h-full">
          <!-- NAVBAR CARD -->
          <div class="h-full rounded-3xl flex flex-col
                      bg-gradient-to-br from-accent-300 via-accent-200 to-accent-300
                      animate-card-gradient backdrop-blur-xl">

            <div class="p-6 pb-4">
              <h2 class="text-3xl font-bold text-center">
                <span class="text-brand-500">Fiók Beállítások</span>
              </h2>
            </div>

             <nav class="flex-1 px-3 space-y-1">
               <a v-for="item in RightnavItems" :key="item.label" :href="item.url" class="group flex items-center px-4 py-3 rounded-xl
                        transition hover:bg-accent-400 hover:scale-[1.02] text-slate-900">
                <component :is="item.icon" class="h-5 w-5 mr-3" />
                {{ item.label }}
                </a>
            </nav>

            <!-- USER SECTION -->
            <div class="mt-auto p-4">
              <div class="rounded-2xl bg-gradient-to-br from-accent-400/40 to-accent-500/40 
                          shadow-lg p-5 backdrop-blur-sm">

                <!-- Logged in state -->
                <div v-if="user" class="space-y-4 text-center py-2">
                  <div class="flex justify-center">
                    <Avatar :image="user.avatar || '/imgs/emcyPFP.png'" shape="circle"
                      class="!w-20 !h-20 ring-2 ring-accent-600/50 shadow-md" />
                  </div>
                  <div>
                    <p class="font-bold text-slate-900 text-lg">@{{ user.username || user.email.split('@')[0] }}</p>
                    <p class="text-base text-slate-700 mt-1">{{ user.email }}</p>
                  </div>
                  <button @click="logout" class="w-full py-3 rounded-xl bg-brand-700 text-accent-200 
                           hover:bg-brand-800 transition-all hover:scale-[1.02]
                           font-medium shadow-md flex items-center justify-center gap-2">
                    <LogOut class="w-4 h-4" />
                    Kijelentkezés
                  </button>
                </div>
                <div v-else class="space-y-4 text-center py-2">
                  <div class="flex justify-center">
                    <div class="w-20 h-20 rounded-full bg-accent-500/30 
                                flex items-center justify-center shadow-md">
                      <User class="w-10 h-10 text-slate-700" />
                    </div>
                  </div>
                  <div>
                    <p class="font-bold text-slate-900 text-lg">Nincs bejelentkezve</p>
                    <p class="text-base text-slate-700 mt-1">Jelentkezz be a funkciók eléréséhez</p>
                  </div>
                  <button @click="login" class="w-full py-3 rounded-xl bg-brand-700 text-accent-200 
                           hover:bg-brand-800 transition-all hover:scale-[1.02]
                           font-medium shadow-md flex items-center justify-center gap-2">
                    <LogIn class="w-4 h-4" />
                    Bejelentkezés
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </aside>
  </div>
</template>