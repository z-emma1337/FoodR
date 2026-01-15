<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { User, Settings, Shield, Lock, HelpCircle, LogOut, Heart, Search, Home } from 'lucide-vue-next'
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import Avatar from 'primevue/avatar'   // keeping PrimeVue Avatar for now – or replace with img

// You can remove PrimeVue dependencies later if you want full custom styling

const user = {
    name: 'Emcy',
    email: 'emcy@foodr.test',
    avatar: '/imgs/emcyPFP.png'
}

const leftItems = ref([
    { label: 'SwipeR', url: '/welcome', icon: Home, badge: null },
    { label: 'Kedvencek', url: '/kedvencek', icon: Heart, badge: null },
    { label: 'Felfedezés', url: '/felfedezes', icon: Search, badge: null },
])

const rightItems = ref([
    { label: 'Felhasználói fiók beállítások', url: '/felhasznaloi-fiok-beallitasok', icon: User },
    { label: 'Profil szerkesztése', url: '/profil-szerkesztes', icon: Settings },
    { label: 'Fiók biztonság', url: '/fiok-biztonsag', icon: Shield },
    { label: 'Adatvédelmi beállítások', url: '/adatvedelmi-beallitasok', icon: Lock },
    { label: 'Segítség / Support', url: '/support', icon: HelpCircle },
])

const logout = () => {
    router.post('/logout', {}, {
        onSuccess: () => console.log('Sikeres kijelentkezés'),
        onError: (errors) => console.error(errors)
    })
}
</script>

<template>

    <Head title="FoodR – Főoldal" />

    <div class="relative flex min-h-screen overflow-hidden
              bg-gradient-to-br from-brand-900 via-brand-700 to-brand-800
              animate-gradient">

        <!-- subtle glow overlay -->
        <div class="pointer-events-none absolute inset-0
                bg-gradient-to-br from-accent-500/20 via-transparent to-accent-600/20
                blur-3xl animate-gradient-slow"></div>

        <!-- LEFT SIDEBAR -->
        <div class="hidden md:flex md:w-64 flex-col border-r border-accent-600/30 bg-accent-300 backdrop-blur-sm">
            <div class="p-6 pb-2">
                <h2 class="text-2xl font-bold text-slate-900">
                    Food<span class="text-brand-700">R</span>
                </h2>
            </div>

            <nav class="flex-1 px-3 py-4 space-y-1">
                <a v-for="item in leftItems" :key="item.label" :href="item.url"
                    class="group flex items-center px-4 py-3 rounded-xl text-slate-800 hover:bg-accent-400/40 hover:text-slate-900 transition-all duration-200">
                    <component :is="item.icon" class="h-5 w-5 mr-3 text-slate-700 group-hover:text-brand-700" />
                    <span class="font-medium">{{ item.label }}</span>

                    <span v-if="item.badge"
                        class="ml-auto bg-brand-600 text-accent-100 text-xs font-bold px-2.5 py-1 rounded-full">
                        {{ item.badge }}
                    </span>
                </a>
            </nav>
        </div>

        <!-- MAIN CONTENT -->
        <div class="flex-1 flex flex-col p-6 md:p-10">
            <div class="max-w-5xl mx-auto w-full">


            </div>
        </div>

        <!-- RIGHT SIDEBAR (User menu) -->
        <div class="hidden lg:flex lg:w-80 flex-col border-l border-accent-600/30 bg-accent-300 backdrop-blur-sm">
            <div class="p-6 pb-2">
                <h3 class="text-xl font-semibold text-slate-900">Felhasználói fiók</h3>
            </div>

            <nav class="flex-1 px-3 py-4 space-y-1">
                <a v-for="item in rightItems" :key="item.label" :href="item.url"
                    class="group flex items-center px-4 py-3 rounded-xl text-slate-800 hover:bg-accent-400/40 hover:text-slate-900 transition-all duration-200">
                    <component :is="item.icon" class="h-5 w-5 mr-3 text-slate-700 group-hover:text-brand-700" />
                    <span class="font-medium">{{ item.label }}</span>
                </a>
            </nav>

            <!-- Bottom user section -->
            <div class="p-5 border-t border-accent-500/30 mt-auto">
                <div class="flex items-center gap-3 mb-4">
                    <Avatar :image="user.avatar" shape="circle" class="!w-12 !h-12" />
                    <div>
                        <div class="font-semibold text-slate-900">{{ user.name }}</div>
                        <div class="text-sm text-slate-600">{{ user.email }}</div>
                    </div>
                </div>

                <button @click="logout" class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-2xl
                 bg-brand-700 text-accent-200 font-medium
                 hover:bg-brand-800 hover:scale-[1.02] transition-all duration-200
                 focus:outline-none focus:ring-4 focus:ring-brand-500/40">
                    <LogOut class="h-5 w-5" />
                    Kijelentkezés
                </button>
            </div>
        </div>

        <!-- Mobile bottom bar / fallback – can be extended later -->
        <div
            class="md:hidden fixed bottom-0 left-0 right-0 bg-accent-300 backdrop-blur-lg border-t border-accent-500/30 z-50">
            <div class="flex justify-around py-3">
                <a href="/welcome" class="flex flex-col items-center text-slate-700 hover:text-brand-700">
                    <Home class="h-6 w-6" />
                    <span class="text-xs mt-1">SwipeR</span>
                </a>
                <a href="/kedvencek" class="flex flex-col items-center text-slate-700 hover:text-brand-700">
                    <Heart class="h-6 w-6" />
                    <span class="text-xs mt-1">Kedvencek</span>
                </a>
                <!-- more mobile nav items... -->
            </div>
        </div>
    </div>
</template>

<style scoped>
.animate-gradient {
    animation: gradient 18s ease infinite;
}

@keyframes gradient {
    0% {
        background-position: 0% 50%;
    }

    50% {
        background-position: 100% 50%;
    }

    100% {
        background-position: 0% 50%;
    }
}

.animate-gradient-slow {
    animation: gradient 35s ease infinite;
}
</style>