<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { Eye, EyeOff, Check } from 'lucide-vue-next'
import { ref, computed, watch } from 'vue'
import axios from 'axios'

// Username
const username = ref('')
const isUsernameAvailable = ref(null)
const checkingUsername = ref(false)
const usernameTouched = ref(false)

// Password
const password = ref('')
const confirmPassword = ref('')
const showPassword = ref(false)
const showPasswordConfirm = ref(false)

// Password validation
const isPasswordLongEnough = computed(() => password.value.length >= 8)
const doPasswordsMatch = computed(
  () => password.value === confirmPassword.value && confirmPassword.value.length > 0
)

// Username availability check (min. 4 karakter)
watch(username, async (val) => {
  usernameTouched.value = true

  if (val.length < 4) {
    isUsernameAvailable.value = null // null = nincs ellenőrizve
    return
  }

  checkingUsername.value = true

  try {
    const response = await axios.get('/check-username', { params: { username: val } })
    isUsernameAvailable.value = response.data.available
  } catch (e) {
    isUsernameAvailable.value = false
  } finally {
    checkingUsername.value = false
  }
})
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
            <label class="block text-sm font-medium text-slate-700">
              Felhasználónév
            </label>

            <input v-model="username" type="text" placeholder="pelda123" class="focus-glow mt-1 w-full rounded-lg border bg-white px-3 py-2
           text-slate-900 placeholder-slate-400 outline-none transition
           border-slate-300 focus:border-brand-500" />

            <div v-if="usernameTouched" class="mt-2 text-sm space-y-1">
              <p class="flex items-center gap-2 transition"
                :class="username.length >= 4 ? 'text-emerald-600' : 'text-slate-400'">
                <Check v-if="username.length >= 4" class="h-4 w-4" />
                <span v-else class="inline-block h-1.5 w-1.5 rounded-full bg-slate-300"></span>
                Legalább 4 karakter
              </p>

              <p v-if="username.length >= 4" class="flex items-center gap-2 transition" :class="checkingUsername
                ? 'text-slate-400'
                : isUsernameAvailable
                  ? 'text-emerald-600'
                  : 'text-red-500'
                ">
                <span v-if="checkingUsername">Ellenőrzés…</span>

                <template v-else>
                  <Check v-if="isUsernameAvailable" class="h-4 w-4" />
                  <span v-else class="inline-block h-1.5 w-1.5 rounded-full bg-red-400"></span>
                  {{ isUsernameAvailable ? 'Felhasználónév elérhető' : 'Már foglalt' }}
                </template>
              </p>
            </div>
          </div>
          <!-- EMAIL -->
          <div>
            <label class="block text-sm font-medium text-slate-700">
              Email cím
            </label>
            <input type="email" placeholder="email@pelda.hu"
              class="focus-glow mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 placeholder-slate-400 outline-none focus:border-brand-500" />
          </div>

          <!-- PASSWORD -->
          <div>
            <label class="block text-sm font-medium text-slate-700">Jelszó</label>
            <div class="relative mt-1">
              <input v-model="password" :type="showPassword ? 'text' : 'password'" placeholder="••••••••" class="w-full rounded-lg border bg-white px-3 py-2 pr-10 text-slate-900 placeholder-slate-400 outline-none transition-all duration-200
                       focus:ring-2 focus:ring-brand-500/20 border-slate-300 focus:border-brand-500" />
              <button type="button" @click="showPassword = !showPassword"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-brand-500 transition">
                <component :is="showPassword ? EyeOff : Eye" class="h-5 w-5" />
              </button>
            </div>
            <div class="mt-2 space-y-1 text-sm">
              <p class="flex items-center gap-2 transition"
                :class="isPasswordLongEnough ? 'text-emerald-600' : 'text-slate-400'">
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
              <input v-model="confirmPassword" :type="showPasswordConfirm ? 'text' : 'password'" placeholder="••••••••"
                class="w-full rounded-lg border bg-white px-3 py-2 pr-10 text-slate-900 placeholder-slate-400 outline-none transition-all duration-200
                       focus:ring-2 focus:ring-brand-500/20 border-slate-300 focus:border-brand-500" />
              <button type="button" @click="showPasswordConfirm = !showPasswordConfirm"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-brand-500 transition">
                <component :is="showPasswordConfirm ? EyeOff : Eye" class="h-5 w-5" />
              </button>
            </div>
            <div class="mt-2 text-sm">
              <p class="flex items-center gap-2 transition"
                :class="doPasswordsMatch ? 'text-emerald-600' : 'text-slate-400'">
                <Check v-if="doPasswordsMatch" class="h-4 w-4" />
                <span v-else class="inline-block h-1.5 w-1.5 rounded-full bg-slate-300"></span>
                A jelszavak egyeznek
              </p>
            </div>
          </div>
<button
  type="submit"
  class="w-full rounded-xl bg-brand-600 py-2 font-semibold text-white
         transition hover:bg-brand-700 focus:ring-2 focus:ring-brand-500
         focus:ring-offset-2 focus:ring-offset-white focus:outline-none"
  :disabled="checkingUsername ||
             username.length < 4 ||
             isUsernameAvailable !== true ||
             !isPasswordLongEnough ||
             !doPasswordsMatch">
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
