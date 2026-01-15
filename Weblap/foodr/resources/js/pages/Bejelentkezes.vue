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

  <!-- PAGE -->
  <div
    class="relative flex min-h-screen items-center justify-center p-4 overflow-hidden
           bg-gradient-to-br from-brand-900 via-brand-700 to-brand-800
           animate-gradient"
  >
    <!-- OPTIONAL subtle glow overlay -->
    <div
      class="pointer-events-none absolute inset-0
             bg-gradient-to-br from-accent-500/20 via-transparent to-accent-600/20
             blur-3xl animate-gradient-slow"
    ></div>

    <div class="relative w-full max-w-md">
       <!-- ACCENT FRAME -->
  <div class="rounded-3xl overflow-hidden shadow-2xl">
    <div class="bg-gradient-to-br from-accent-500/80 to-accent-600/80 p-1">
        <!-- CARD -->
        <div class="rounded-3xl bg-accent-300 p-8">
          <h1 class="text-2xl font-semibold text-slate-900">
            Bejelentkezés
            <span class="text-brand-700">| FoodR</span>
          </h1>

          <form @submit.prevent="submit" class="mt-6 space-y-5">
            <!-- EMAIL -->
            <div>
              <label class="block text-sm font-medium text-slate-800">
                Email cím
              </label>

              <div class="relative mt-1 group">
                <span
                  class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2
                         text-slate-700 transition
                         group-focus-within:text-accent-800"
                >
                  <Mail class="h-5 w-5" />
                </span>

                <input
                  v-model="email"
                  type="email"
                  placeholder="email@pelda.hu"
                  required
                  class="w-full rounded-xl border border-accent-400/60
                         bg-accent-200 px-3 py-2 pl-10 pr-10
                         text-slate-900 placeholder-slate-700 outline-none
                         transition-all duration-300
                         focus:bg-accent-200
                         focus:border-accent-600
                         focus:ring-4 focus:ring-accent-600/30"
                />
              </div>
            </div>

            <!-- PASSWORD -->
            <div>
              <label class="block text-sm font-medium text-slate-800">
                Jelszó
              </label>

              <div class="relative mt-1 group">
                <span
                  class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2
                         text-slate-700 transition
                         group-focus-within:text-accent-800"
                >
                  <Lock class="h-5 w-5" />
                </span>

                <input
                  v-model="password"
                  :type="showPassword ? 'text' : 'password'"
                  placeholder="••••••••"
                  class="w-full rounded-xl border border-accent-400/60
                         bg-accent-200 px-3 py-2 pl-10 pr-10
                         text-slate-900 placeholder-slate-700 outline-none
                         transition-all duration-300
                         focus:bg-accent-200
                         focus:border-accent-600
                         focus:ring-4 focus:ring-accent-600/30"
                />

                <button
                  type="button"
                  @click="showPassword = !showPassword"
                  class="absolute right-3 top-1/2 -translate-y-1/2
                         text-slate-600 hover:text-accent-700 transition cursor-pointer"
                >
                  <component :is="showPassword ? EyeOff : Eye" class="h-5 w-5" />
                </button>
              </div>
            </div>

            <!-- ERROR -->
            <div
              v-if="errorMessage"
              class="rounded-lg bg-brand-200 px-3 py-2 text-center
                     text-sm text-brand-900"
            >
              {{ errorMessage }}
            </div>

            <!-- BUTTON -->
            <button
              type="submit"
              class="w-full rounded-2xl
                     bg-brand-700 py-2.5 font-semibold text-accent-200
                     shadow-xl shadow-brand-800/40
                     transition-all
                     hover:bg-brand-800 hover:scale-[1.02]
                     focus:outline-none focus:ring-4 focus:ring-brand-500/40 cursor-pointer"
            >
              Bejelentkezés
            </button>
          </form>

          <p class="mt-6 text-center text-sm text-slate-800">
            Nincs még fiókod?
            <Link
              href="/regisztracio"
              class="font-medium text-brand-600 hover:underline"
            >
              Regisztráció
            </Link>
          </p>
        </div>
      </div>
    </div>
    </div>
  </div>
</template>
