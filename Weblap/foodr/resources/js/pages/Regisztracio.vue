<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { Eye, EyeOff, Check } from 'lucide-vue-next'
import { ref, computed } from 'vue'

// Password visibility toggles
const showPassword = ref(false)
const showPasswordConfirm = ref(false)

// Password values
const password = ref('')
const confirmPassword = ref('')

// Password validation
const isPasswordLongEnough = computed(() => password.value.length >= 8)
const doPasswordsMatch = computed(() => password.value === confirmPassword.value && confirmPassword.value.length > 0)
</script>

<template>
  <Head title="Regisztráció" />

  <div class="flex min-h-screen items-center justify-center bg-slate-100 p-4">
    <div class="w-full max-w-md">
      <div class="rounded-2xl bg-white p-8 shadow-lg ring-1 ring-slate-200">
        <h1 class="text-2xl font-semibold text-slate-900">
          Regisztráció | FoodR
        </h1>

        <form class="mt-6 space-y-4">
          <!-- USERNAME -->
          <div>
            <label class="block text-sm font-medium text-slate-700">Felhasználónév</label>
            <input
              type="text"
              placeholder="pelda123"
              class="focus-glow mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 placeholder-slate-400 outline-none focus:border-brand-500"
            />
          </div>

          <!-- EMAIL -->
          <div>
            <label class="block text-sm font-medium text-slate-700">Email cím</label>
            <input
              type="email"
              placeholder="email@pelda.hu"
              class="focus-glow mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 placeholder-slate-400 outline-none focus:border-brand-500"
            />
          </div>

          <!-- PASSWORD -->
          <div>
            <label class="block text-sm font-medium text-slate-700">Jelszó</label>
            <div class="relative mt-1">
              <input
                v-model="password"
                :type="showPassword ? 'text' : 'password'"
                placeholder="••••••••"
                class="w-full rounded-lg border bg-white px-3 py-2 pr-10 text-slate-900 placeholder-slate-400 outline-none transition-all duration-200
                       focus:ring-2 focus:ring-brand-500/20
                       border-slate-300 focus:border-brand-500"
              />
              <button
                type="button"
                @click="showPassword = !showPassword"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-brand-500 transition"
              >
                <component :is="showPassword ? EyeOff : Eye" class="h-5 w-5" />
              </button>
            </div>

            <!-- Password rules -->
            <div class="mt-2 space-y-1 text-sm">
              <p
                class="flex items-center gap-2 transition"
                :class="isPasswordLongEnough ? 'text-emerald-600' : 'text-slate-400'"
              >
                <Check v-if="isPasswordLongEnough" class="h-4 w-4" />
                <span v-else class="inline-block h-1.5 w-1.5 rounded-full bg-slate-300"></span>
                Legalább 8 karakter
              </p>
            </div>
          </div>

          <!-- PASSWORD CONFIRM -->
          <div>
            <label class="block text-sm font-medium text-slate-700">Jelszó megerősítése</label>
            <div class="relative mt-1">
              <input
                v-model="confirmPassword"
                :type="showPasswordConfirm ? 'text' : 'password'"
                placeholder="••••••••"
                class="w-full rounded-lg border bg-white px-3 py-2 pr-10 text-slate-900 placeholder-slate-400 outline-none transition-all duration-200
                       focus:ring-2 focus:ring-brand-500/20
                       border-slate-300 focus:border-brand-500"
              />
              <button
                type="button"
                @click="showPasswordConfirm = !showPasswordConfirm"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-brand-500 transition"
              >
                <component :is="showPasswordConfirm ? EyeOff : Eye" class="h-5 w-5" />
              </button>
            </div>

            <!-- Match indicator -->
            <div class="mt-2 text-sm">
              <p
                class="flex items-center gap-2 transition"
                :class="doPasswordsMatch ? 'text-emerald-600' : 'text-slate-400'"
              >
                <Check v-if="doPasswordsMatch" class="h-4 w-4" />
                <span v-else class="inline-block h-1.5 w-1.5 rounded-full bg-slate-300"></span>
                A jelszavak egyeznek
              </p>
            </div>
          </div>

          <!-- Submit button -->
          <button
            type="submit"
            class="w-full rounded-xl bg-brand-600 py-2 font-semibold text-white transition hover:bg-brand-700 focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 focus:ring-offset-white focus:outline-none"
          >
            Regisztráció
          </button>
        </form>

        <p class="mt-6 text-center text-sm text-slate-500">
          Már van fiókod?
          <Link href="/bejelentkezes" class="cursor-pointer font-medium text-brand-600 hover:underline">
            Bejelentkezés
          </Link>
        </p>
      </div>
    </div>
  </div>
</template>
