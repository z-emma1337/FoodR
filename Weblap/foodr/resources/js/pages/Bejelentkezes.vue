<script setup>
import { Head } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3'
import { Mail, Lock } from 'lucide-vue-next'
import { Eye, EyeOff, Check } from 'lucide-vue-next'
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'


const email = ref('')
const password = ref('')
const showPassword = ref(false)
const errorMessage = ref('')

const submit = () => {
    errorMessage.value = '' // előző hibát töröljük

    router.post('/bejelentkezes', {
        email: email.value,
        jelszo: password.value
    }, {
        onSuccess: () => {
            console.log('Sikeres bejelentkezés')
        },
        onError: (errors) => {
            // Hibát kiírunk a mező alá
            errorMessage.value = 'Hibás email cím vagy jelszó'
        }
    })
}


</script>

<template>

    <Head title="Bejelentkezés" />

    <div class="flex min-h-screen items-center justify-center bg-slate-100 p-4">
        <div class="w-full max-w-md">
            <div class="rounded-2xl bg-white p-8 shadow-lg ring-1 ring-slate-200">
                <h1 class="text-2xl font-semibold text-slate-900">
                    Bejelentkezés | FoodR
                </h1>

                <form @submit.prevent="submit" class="mt-6 space-y-4">
                    <!-- EMAIL -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700">
                            Email cím
                        </label>

                        <div class="relative mt-1 group">
                            <!-- ICON -->
                            <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2
             text-slate-400 z-10
             group-focus-within:text-brand-500 transition">
                                <Mail class="h-5 w-5" />
                            </span>

                            <!-- INPUT -->
                            <input v-model="email" type="email" placeholder="email@pelda.hu" required class="focus-glow w-full rounded-lg border bg-white px-3 py-2 pl-10
             text-slate-900 placeholder-slate-400 outline-none transition
             border-slate-300 focus:border-brand-500" />
                        </div>
                    </div>

                    <!-- PASSWORD -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Jelszó</label>

                        <div class="relative mt-1 group">
                            <!-- LOCK ICON -->
                            <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2
             text-slate-400 z-10
             group-focus-within:text-red-500 transition-colors">
                                <Lock class="h-5 w-5" />
                            </span>


                            <!-- INPUT -->
                            <input v-model="password" :type="showPassword ? 'text' : 'password'" placeholder="••••••••"
                                class="w-full rounded-lg border bg-white px-3 py-2 pl-10 pr-10
             text-slate-900 placeholder-slate-400 outline-none transition-all duration-200
             focus:ring-2 focus:ring-brand-500/20 border-slate-300 focus:border-brand-500" />

                            <!-- EYE BUTTON -->
                            <button type="button" @click="showPassword = !showPassword"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-brand-500 transition cursor-pointer">
                                <component :is="showPassword ? EyeOff : Eye" class="h-5 w-5" />
                            </button>
                        </div>
                    </div>

                    <div v-if="errorMessage" class="mt-2 text-sm text-brand-500 text-center">
                        {{ errorMessage }}
                    </div>


                    <!-- BUTTON -->
                    <button type="submit"
                        class="cursor-pointer w-full rounded-xl bg-brand-600 py-2 font-semibold text-white transition hover:bg-brand-700 focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 focus:ring-offset-white focus:outline-none">
                        Bejelentkezés
                    </button>
                </form>

                <p class="mt-6 text-center text-sm text-slate-500">
                    Nincs még fiókod?
                    <Link href="/regisztracio" class="cursor-pointer font-medium text-brand-600 hover:underline">
                        Regisztráció </Link>
                </p>
            </div>
        </div>
    </div>
</template>
