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
  Menu,
  X as CloseIcon
} from 'lucide-vue-next'
import { ref, computed } from 'vue'
import Avatar from 'primevue/avatar'

const page = usePage()
const user = computed(() => page.props.auth?.user)
const likedCount = computed(() => page.props.likedCount ?? 0)
const isMobileMenuOpen = ref(false)

const LeftnavItems = ref([
  { label: 'SwipeR', url: '/', icon: Home },
  { label: 'FavoR', url: '/kedvencek', icon: Heart },
  { label: 'FeedR', url: '/felfedezes', icon: Search },
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
  isMobileMenuOpen.value = false
}

const toggleMobileMenu = () => {
  isMobileMenuOpen.value = !isMobileMenuOpen.value
}

const closeMobileMenu = () => {
  isMobileMenuOpen.value = false
}
</script>

<template>
  <div class="relative flex min-h-screen
              bg-gradient-to-br from-brand-900 via-brand-700 to-brand-800
              animate-gradient overflow-hidden">
    <!-- glow -->
    <div class="pointer-events-none absolute inset-0
                bg-gradient-to-br from-accent-500/20 via-transparent to-accent-600/20
                blur-3xl animate-gradient-slow" />

    <!-- MOBILE HEADER -->
    <header class="lg:hidden fixed top-0 left-0 right-0 z-30 p-4">
      <div class="flex items-center justify-between bg-accent-300/90 backdrop-blur-lg rounded-2xl px-4 py-3 shadow-xl">
        <h1 class="text-2xl font-bold">
          <span class="text-accent-400">Food</span><span class="text-brand-500">R</span>
        </h1>
        <button @click="toggleMobileMenu" class="p-2 rounded-xl bg-accent-400/50 hover:bg-accent-400 transition">
          <Menu v-if="!isMobileMenuOpen" class="w-6 h-6 text-slate-900" />
          <CloseIcon v-else class="w-6 h-6 text-slate-900" />
        </button>
      </div>
    </header>

    <!-- MOBILE MENU OVERLAY -->
    <Transition name="menu-fade">
      <div v-if="isMobileMenuOpen" @click="closeMobileMenu"
        class="lg:hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-40">
      </div>
    </Transition>

    <!-- MOBILE MENU PANEL -->
    <Transition name="menu-slide">
      <div v-if="isMobileMenuOpen"
        class="lg:hidden fixed top-20 right-4 left-4 z-50 max-h-[calc(100vh-6rem)] overflow-y-auto">
        <div class="rounded-3xl overflow-hidden shadow-2xl">
          <div class="bg-gradient-to-br from-accent-500/90 to-accent-600/90 p-1 backdrop-blur-xl">
            <div class="rounded-3xl bg-gradient-to-br from-accent-300 via-accent-200 to-accent-300 p-6 space-y-6">

              <!-- User Section -->
              <div class="rounded-2xl bg-accent-400/40 shadow-lg p-5 text-center">
                <div v-if="user" class="space-y-3">
                  <Avatar :image="user.avatar || '/imgs/emcyPFP.png'" shape="circle"
                    class="!w-16 !h-16 ring-2 ring-accent-600/50 shadow-md mx-auto" />
                  <p class="font-bold text-slate-900">@{{ user.username || user.email.split('@')[0] }}</p>
                  <p class="text-sm text-slate-700">{{ user.email }}</p>
                </div>
                <div v-else class="space-y-3">
                  <div
                    class="w-16 h-16 rounded-full bg-accent-500/30 flex items-center justify-center shadow-md mx-auto">
                    <User class="w-8 h-8 text-slate-700" />
                  </div>
                  <p class="font-bold text-slate-900">Nincs bejelentkezve</p>
                </div>
              </div>

              <!-- Navigation -->
              <nav class="space-y-2">
                <p class="text-xs font-semibold text-slate-700 uppercase px-4">Navigáció</p>
                <a v-for="item in LeftnavItems" :key="item.label" :href="item.url" @click="closeMobileMenu" class="flex items-center px-4 py-3 rounded-xl
                          transition hover:bg-accent-400 text-slate-900">
                  <component :is="item.icon" class="h-5 w-5 mr-3" />
                  {{ item.label }}
                </a>
              </nav>

              <!-- Settings -->
              <nav class="space-y-2">
                <p class="text-xs font-semibold text-slate-700 uppercase px-4">Beállítások</p>
                <a v-for="item in RightnavItems" :key="item.label" :href="item.url" @click="closeMobileMenu" class="flex items-center px-4 py-3 rounded-xl
                          transition hover:bg-accent-400 text-slate-900">
                  <component :is="item.icon" class="h-5 w-5 mr-3" />
                  {{ item.label }}
                </a>
              </nav>

              <!-- Auth Button -->
              <button v-if="user" @click="logout" class="w-full py-3 rounded-xl bg-brand-700 text-accent-200 
                             hover:bg-brand-800 transition-all
                             font-medium shadow-md flex items-center justify-center gap-2">
                <LogOut class="w-4 h-4" />
                Kijelentkezés
              </button>
              <button v-else @click="login" class="w-full py-3 rounded-xl bg-brand-700 text-accent-200 
                             hover:bg-brand-800 transition-all
                             font-medium shadow-md flex items-center justify-center gap-2">
                <LogIn class="w-4 h-4" />
                Bejelentkezés
              </button>

            </div>
          </div>
        </div>
      </div>
    </Transition>

    <!-- LEFT SIDEBAR (Desktop) -->
    <aside class="hidden lg:flex flex-col w-80 h-[calc(100vh-2rem)] fixed top-4 left-4 z-20">
      <div class="rounded-3xl overflow-hidden shadow-2xl h-full">
        <div class="bg-gradient-to-br from-accent-500/80 to-accent-600/80 p-1 h-full">
          <div class="h-full rounded-3xl flex flex-col
                      bg-gradient-to-br from-accent-300 via-accent-200 to-accent-300
                      animate-card-gradient backdrop-blur-xl">

            <div class="p-6 pb-4">
              <h1 class="text-6xl font-bold text-center">
                <span class="text-accent-400 text-outline-shadow">Food</span><span
                  class="text-brand-500 text-outline-shadow">R</span>
              </h1>
            </div>

            <nav class="flex-1 px-3 space-y-1 overflow-y-auto">
              <a v-for="item in LeftnavItems" :key="item.label" :href="item.url" class="group flex items-center px-4 py-3 rounded-xl
                        transition hover:bg-accent-400 hover:scale-[1.02] text-slate-900">
                <component :is="item.icon" class="h-5 w-5 mr-3" />
                <span class="flex flex-1 items-center justify-between gap-2">
                  <span>{{ item.label }}</span>
                  <span v-if="item.label === 'FavoR' && user"
                    class="px-2 py-1 rounded-full bg-brand-600 text-accent-200 text-xs font-semibold">
                    {{ likedCount }}
                  </span>
                </span>
              </a>
            </nav>

          </div>
        </div>
      </div>
    </aside>

    <!-- RIGHT SIDEBAR (Desktop) -->
    <aside class="hidden lg:flex flex-col w-80 h-[calc(100vh-2rem)] fixed top-4 right-4 z-20">
      <div class="rounded-3xl overflow-hidden shadow-2xl h-full">
        <div class="bg-gradient-to-br from-accent-500/80 to-accent-600/80 p-1 h-full">
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

            <div class="mt-auto p-4">
              <div class="rounded-2xl bg-gradient-to-br from-accent-400/40 to-accent-500/40 
                          shadow-lg p-5 backdrop-blur-sm text-center">

                <div v-if="user" class="space-y-4">
                  <Avatar :image="user.avatar || '/imgs/emcyPFP.png'" shape="circle"
                    class="!w-20 !h-20 ring-2 ring-accent-600/50 shadow-md mx-auto" />
                  <p class="font-bold text-slate-900 text-lg">@{{ user.username || user.email.split('@')[0] }}</p>
                  <p class="text-base text-slate-700 mt-1">{{ user.email }}</p>
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

    <!-- PAGE CONTENT -->
    <main class="flex-1 flex flex-col 
             pt-20 lg:pt-6 px-4 sm:px-6 md:px-10 pb-6
             lg:ml-[calc(320px+1rem)] lg:mr-[calc(320px+1rem)]
             z-10 overflow-y-auto">
      <div class="max-w-7xl mx-auto w-full">
        <slot />
      </div>
    </main>

  </div>
</template>

