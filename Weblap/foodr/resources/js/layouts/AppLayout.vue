<script setup>
import { router, usePage } from '@inertiajs/vue3'
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
  X,
  PencilLine
} from 'lucide-vue-next'
import { ref, computed, watch } from 'vue'

import BejelentkezesModal from '@/components/UI/BejelentkezesModal.vue'
import ProfilomModal from '@/components/UI/ProfilBeallitasok/ProfilomModal.vue'
import BeallitasokModal from '@/components/UI/ProfilBeallitasok/BeallitasokModal.vue'
import SugoModal from '@/components/UI/ProfilBeallitasok/SugoModal.vue'
import { useLoginModal } from '@/composables/useLoginModal'

const page = usePage()
const user = computed(() => page.props.auth?.user)
const likedCount = computed(() => page.props.likedCount ?? 0)
const isMobileMenuOpen = ref(false)

const { loginModalOpen, openLoginModal: _openLoginModal, closeLoginModal } = useLoginModal()
const profilomOpen = ref(false)
const beallitasokOpen = ref(false)
const sugoOpen = ref(false)

const openLoginModal = () => {
  _openLoginModal()
  isMobileMenuOpen.value = false
}

const modalMap = {
  'Profilom': { open: () => { profilomOpen.value = true }, requiresAuth: true },
  'Beállítások': { open: () => { beallitasokOpen.value = true }, requiresAuth: true },
  'Súgó': { open: () => { sugoOpen.value = true }, requiresAuth: false },
}

const LeftnavItems = ref([
  { label: 'SwipeR', title: 'Receptek válogatása', url: '/', icon: Home, active: true, requiresAuth: false },
  { label: 'FavoR', title: 'Kedvelt receptek', url: '/kedvencek', icon: Heart, active: false, requiresAuth: true },
  { label: 'FeedR', title: 'Felfedezés', url: '/felfedezes', icon: Search, active: false, requiresAuth: false },
  { label: 'CreatR', title: 'Saját receptek', url: '/receptjeim', icon: PencilLine, active: false, requiresAuth: true },
])

const RightnavItems = ref([
  { label: 'Profilom', title: 'Profilom', url: '/profil', icon: User, active: false },
  { label: 'Beállítások', title: 'Beállítások', url: '/beallitasok', icon: Settings, active: false },
  { label: 'Súgó', title: 'Súgó', url: '/sugo', icon: HelpCircle, active: false },
])

const handleLeftNav = (item) => {
  if (item.requiresAuth && !user.value) {
    openLoginModal()
    return
  }
  router.visit(item.url)
}

const handleRightNav = (item) => {
  closeMobileMenu()
  const entry = modalMap[item.label]
  if (entry?.requiresAuth && !user.value) {
    openLoginModal()
    return
  }
  entry?.open()
}

const logout = () => {
  router.post('/logout')
}

const toggleMobileMenu = () => {
  isMobileMenuOpen.value = !isMobileMenuOpen.value
}

const closeMobileMenu = () => {
  isMobileMenuOpen.value = false
}

watch(() => page.url, (currentPath) => {
  LeftnavItems.value.forEach(item => { item.active = item.url === currentPath })
  RightnavItems.value.forEach(item => { item.active = item.url === currentPath })
}, { immediate: true })
</script>

<template>
  <div class="relative flex flex-col h-[100dvh] overflow-hidden
              lg:flex-row lg:h-screen
              bg-gradient-to-br from-brand-900 via-brand-700 to-brand-800 animate-gradient">

    <div class="pointer-events-none fixed inset-0 z-0
                bg-gradient-to-br from-accent-500/20 via-transparent to-accent-600/20
                blur-3xl animate-gradient-slow" />

    <BejelentkezesModal :open="loginModalOpen" @close="closeLoginModal" />
    <ProfilomModal :open="profilomOpen" @close="profilomOpen = false" />
    <BeallitasokModal :open="beallitasokOpen" @close="beallitasokOpen = false" />
    <SugoModal :open="sugoOpen" @close="sugoOpen = false" />

    <!-- asztali baloldal -->
    <aside class="hidden lg:flex flex-col shrink-0 w-[280px] h-screen p-4 z-20">
      <div class="h-full rounded-3xl overflow-hidden shadow-2xl border-accent-600 border-6">
        <div
          class="h-full flex flex-col bg-gradient-to-br from-accent-300 via-accent-200 to-accent-300 animate-card-gradient backdrop-blur-xl">

          <div class="p-6 pb-4">
            <button @click="router.visit('/')">
              <h1 class="text-6xl font-bold text-center">
                <span class="text-accent-400 text-outline-shadow">Food</span><span
                  class="text-brand-500 text-outline-shadow">R</span>
              </h1>
            </button>
          </div>

          <nav class="flex-1 px-3 space-y-1 overflow-y-auto">
            <button v-for="item in LeftnavItems" :key="item.label" @click="handleLeftNav(item)"
              :class="item.active ? 'bg-accent-500/30' : ''" :title="item.title"
              class="w-full flex items-center px-4 py-3 rounded-full transition hover:bg-accent-500/30 text-slate-900">
              <component :is="item.icon" class="h-5 w-5 mr-3 shrink-0" />
              <span class="flex flex-1 items-center justify-between gap-2">
                <span>{{ item.label }}</span>
                <span v-if="item.label === 'FavoR' && user"
                  class="px-2 py-1 rounded-full bg-brand-600 text-accent-200 text-xs font-semibold">
                  {{ likedCount }}
                </span>
              </span>
            </button>
          </nav>

        </div>
      </div>
    </aside>

    <div class="flex flex-col flex-1 min-w-0 min-h-0 h-full lg:h-screen z-10">


      <main class="flex-1 min-h-0 overflow-hidden lg:pt-0 pt-3">
        <slot />
      </main>

      <!-- mobil nav -->
      <nav class="lg:hidden shrink-0 px-3 pb-3" style="padding-bottom: max(0.75rem, env(safe-area-inset-bottom))">
        <div
          class="relative flex items-center justify-around bg-accent-300/95 backdrop-blur-lg rounded-3xl px-2 py-1 shadow-xl border-accent-600 border-3">

          <!-- FavoR -->
          <button @click="handleLeftNav(LeftnavItems[1])"
            class="flex flex-col items-center gap-1 flex-1 py-2 rounded-3xl transition-all"
            :class="LeftnavItems[1].active ? 'bg-accent-500/40' : 'active:bg-accent-400/30'">
            <div class="relative transition-transform duration-200"
              :class="LeftnavItems[1].active ? 'scale-125' : 'scale-100'">
              <Heart class="w-7 h-7 transition-colors"
                :class="LeftnavItems[1].active ? 'text-brand-600 fill-brand-500' : 'text-slate-700'" />
              <span v-if="user && likedCount > 0"
                class="absolute -top-2 -right-2.5 min-w-[16px] h-4 px-1 rounded-full bg-brand-600 text-accent-200 text-[10px] font-bold flex items-center justify-center leading-none">
                {{ likedCount }}
              </span>
            </div>
            <span class="text-sm font-semibold tracking-wide"
              :class="LeftnavItems[1].active ? 'text-brand-600' : 'text-slate-600'">FavoR</span>
          </button>

          <!-- FeedR -->
          <button @click="handleLeftNav(LeftnavItems[2])"
            class="flex flex-col items-center gap-1 flex-1 py-2 rounded-3xl transition-all"
            :class="LeftnavItems[2].active ? 'bg-accent-500/40' : 'active:bg-accent-400/30'">
            <div class="transition-transform duration-200" :class="LeftnavItems[2].active ? 'scale-125' : 'scale-100'">
              <Search class="w-7 h-7 transition-colors"
                :class="LeftnavItems[2].active ? 'text-brand-600' : 'text-slate-700'" />
            </div>
            <span class="text-sm font-semibold tracking-wide"
              :class="LeftnavItems[2].active ? 'text-brand-600' : 'text-slate-600'">FeedR</span>
          </button>

          <!-- SwipeR -->
          <div class="flex-1 flex justify-center">
            <button @click="handleLeftNav(LeftnavItems[0])"
              class="relative -top-6 w-20 h-20 rounded-full shadow-xl border-accent-600 border-6 transition-all active:scale-95
                     bg-gradient-to-br from-accent-300 via-accent-200 to-accent-300 flex flex-col items-center justify-center gap-0.5"
              :class="LeftnavItems[0].active ? 'ring-2 ring-brand-600 ring-offset-2 ring-offset-transparent' : ''">
              <Home class="w-8 h-8 transition-colors"
                :class="LeftnavItems[0].active ? 'text-brand-600' : 'text-slate-700'" />
              <span class="text-xs font-bold tracking-wide"
                :class="LeftnavItems[0].active ? 'text-brand-600' : 'text-slate-600'">SwipeR</span>
            </button>
          </div>

          <!-- CreatR -->
          <button @click="handleLeftNav(LeftnavItems[3])"
            class="flex flex-col items-center gap-1 flex-1 py-2 rounded-3xl transition-all"
            :class="LeftnavItems[3].active ? 'bg-accent-500/40' : 'active:bg-accent-400/30'">
            <div class="transition-transform duration-200" :class="LeftnavItems[3].active ? 'scale-125' : 'scale-100'">
              <PencilLine class="w-7 h-7 transition-colors"
                :class="LeftnavItems[3].active ? 'text-brand-600' : 'text-slate-700'" />
            </div>
            <span class="text-sm font-semibold tracking-wide"
              :class="LeftnavItems[3].active ? 'text-brand-600' : 'text-slate-600'">CreatR</span>
          </button>

          <!-- Fiók -->
          <button @click="toggleMobileMenu"
            class="flex flex-col items-center gap-1 flex-1 py-2 rounded-3xl transition-all active:bg-accent-400/30">
            <Menu class="w-7 h-7 text-slate-700" />
            <span class="text-sm font-semibold tracking-wide text-slate-600">Fiók</span>
          </button>

        </div>
      </nav>

    </div>

    <!-- asztsal jobb -->
    <aside class="hidden lg:flex flex-col shrink-0 w-[280px] h-screen p-4 z-20">
      <div class="h-full rounded-3xl overflow-hidden shadow-2xl border-accent-600 border-6">
        <div
          class="h-full flex flex-col bg-gradient-to-br from-accent-300 via-accent-200 to-accent-300 animate-card-gradient backdrop-blur-xl">

          <div class="p-6 pb-4">
            <h2 class="text-3xl font-bold text-center">
              <span class="text-brand-500">Fiók Beállítások</span>
            </h2>
          </div>

          <nav class="flex-1 px-3 space-y-1">
            <button v-for="item in RightnavItems" :key="item.label" @click="handleRightNav(item)"
              :class="item.active ? 'bg-accent-500/30' : ''" :title="item.title"
              class="w-full group flex items-center px-4 py-3 rounded-3xl transition hover:bg-accent-500/30 hover:scale-[1.02] text-slate-900">
              <component :is="item.icon" class="h-5 w-5 mr-3" />
              <span class="flex-1 text-left">{{ item.label }}</span>
            </button>
          </nav>

          <div class="mt-auto p-4">
            <div
              class="rounded-3xl bg-gradient-to-br from-accent-400/40 to-accent-500/40 shadow-lg p-5 backdrop-blur-sm text-center">

              <div v-if="user" class="space-y-4 text-center py-2">
                <div class="flex justify-center">
                  <div class="w-20 h-20 rounded-full overflow-hidden shadow-md">
                    <img :src="user.profilkepurl" alt="Profilkép" class="w-full h-full object-cover scale-[1.2]" />
                  </div>
                </div>
                <p class="font-bold text-slate-900 text-lg">{{ user.nev }}</p>
                <p class="text-base text-slate-700">{{ user.email }}</p>
                <button @click="logout" title="Kijelentkezés"
                  class="w-full py-3 rounded-3xl bg-brand-700 text-accent-200 hover:bg-brand-800 transition-all hover:scale-[1.02] font-medium shadow-md flex items-center justify-center gap-2">
                  <LogOut class="w-4 h-4" />
                  Kijelentkezés
                </button>
              </div>

              <div v-else class="space-y-4 text-center py-2">
                <div class="flex justify-center">
                  <div class="w-20 h-20 rounded-full bg-accent-500/30 flex items-center justify-center shadow-md">
                    <User class="w-10 h-10 text-slate-700" />
                  </div>
                </div>
                <div>
                  <p class="font-bold text-slate-900 text-lg">Nincs bejelentkezve</p>
                  <p class="text-base text-slate-700 mt-1">Jelentkezz be a funkciók eléréséhez</p>
                </div>
                <button @click="openLoginModal" title="Bejelenkezés"
                  class="w-full py-3 rounded-3xl bg-brand-700 text-accent-200 hover:bg-brand-800 transition-all hover:scale-[1.02] font-medium shadow-md flex items-center justify-center gap-2">
                  <LogIn class="w-4 h-4" />
                  Bejelentkezés
                </button>
              </div>

            </div>
          </div>

        </div>
      </div>
    </aside>

    <!-- mobil-->
    <Transition name="menu-fade">
      <div v-if="isMobileMenuOpen" @click="closeMobileMenu"
        class="lg:hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-40" />
    </Transition>

    <Transition name="menu-slide">
      <div v-if="isMobileMenuOpen" class="lg:hidden fixed inset-0 z-50 flex items-center justify-center px-3">

        <div class="relative w-full">
          <div class="max-h-[calc(100dvh-6rem)] overflow-y-auto border-accent-600 border-6 rounded-4xl">
            <div class="rounded-3xl overflow-hidden shadow-2xl">
              <div
                class="rounded-3xl bg-gradient-to-br from-accent-300 via-accent-200 to-accent-300 p-6 space-y-4 relative">

                <div class="flex items-center justify-between">
                  <button @click="router.visit('/')">
                    <h1 class="text-5xl font-bold">
                      <span class="text-accent-400 text-outline-shadow">Food</span><span
                        class="text-brand-500 text-outline-shadow">R</span>
                    </h1>
                  </button>
                  <button @click="closeMobileMenu" class="w-12 h-12 rounded-full
                         flex items-center justify-center transition-all hover:scale-110">
                    <X class="w-7 h-7 text-brand-600" :stroke-width="3" />
                  </button>
                </div>

                <div
                  class="rounded-3xl bg-gradient-to-br from-accent-400/40 to-accent-500/40 shadow-lg p-5 backdrop-blur-sm text-center">
                  <div v-if="user" class="space-y-3 py-2">
                    <div class="flex justify-center">
                      <div class="w-16 h-16 rounded-full overflow-hidden shadow-md">
                        <img :src="user.profilkepurl" alt="Profilkép" class="w-full h-full object-cover scale-[1.2]" />
                      </div>
                    </div>
                    <p class="font-bold text-slate-900 text-lg">{{ user.nev }}</p>
                    <p class="text-sm text-slate-700">{{ user.email }}</p>
                  </div>
                  <div v-else class="space-y-3 py-2">
                    <div class="flex justify-center">
                      <div class="w-16 h-16 rounded-full bg-accent-500/30 flex items-center justify-center shadow-md">
                        <User class="w-8 h-8 text-slate-700" />
                      </div>
                    </div>
                    <p class="font-bold text-slate-900 text-lg">Nincs bejelentkezve</p>
                    <p class="text-sm text-slate-700 mt-1">Jelentkezz be a funkciók eléréséhez</p>
                    <button @click="openLoginModal"
                      class="w-full py-3 rounded-3xl bg-brand-700 text-accent-200 hover:bg-brand-800 transition-all font-medium shadow-md flex items-center justify-center gap-2">
                      <LogIn class="w-4 h-4" />
                      Bejelentkezés
                    </button>
                  </div>
                </div>

                <nav class="space-y-2 pb-2">
                  <button v-for="item in RightnavItems" :key="item.label" @click="handleRightNav(item)"
                    :class="item.active ? 'bg-accent-500/30' : ''"
                    class="w-full flex items-center justify-center px-4 py-3 rounded-full transition hover:bg-accent-500/30 text-slate-900">
                    <component :is="item.icon" class="h-5 w-5 mr-3" />
                    {{ item.label }}
                  </button>

                  <button v-if="user" @click="logout"
                    class="w-full flex items-center justify-center px-4 py-3 rounded-full transition hover:bg-red-200/50 text-slate-900">
                    <LogOut class="h-5 w-5 mr-3" />
                    Kijelentkezés
                  </button>
                </nav>

              </div>
            </div>
          </div>
        </div>
      </div>
    </Transition>

  </div>
</template>