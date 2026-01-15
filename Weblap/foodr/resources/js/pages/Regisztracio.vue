<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { Eye, EyeOff, Check, User, Mail, Lock, X } from 'lucide-vue-next'
import { ref, computed, watch } from 'vue'
import axios from 'axios'
import { router } from '@inertiajs/vue3'

// EMAIL
const email = ref('')

// USERNAME
const username = ref('')
const isUsernameAvailable = ref(null)
const checkingUsername = ref(false)
const usernameTouched = ref(false)
const isUsernameLongEnough = computed(() => username.value.length >= 4)

const hasOnlyValidChars = computed(() =>
  /^[a-zA-Z0-9._-]+$/.test(username.value)
)

const isUsernameFormatValid = computed(() =>
  isUsernameLongEnough.value && hasOnlyValidChars.value
)

// USERNAME CLEAR FUNCTION
const clearUsername = () => {
  username.value = ''
  isUsernameAvailable.value = null
  usernameTouched.value = false
}

// PASSWORDS
const password = ref('')
const confirmPassword = ref('')
const showPassword = ref(false)
const showPasswordConfirm = ref(false)

// PASSWORD VALIDATION
const isPasswordLongEnough = computed(() => password.value.length >= 8)
const hasNumber = computed(() => /\d/.test(password.value))

// OPTIONAL (csak strength jelzéshez)
const hasUppercase = computed(() => /[A-Z]/.test(password.value))
const hasSymbol = computed(() => /[^a-zA-Z0-9]/.test(password.value))

const isPasswordValid = computed(() =>
  isPasswordLongEnough.value &&
  hasUppercase.value &&
  hasNumber.value && hasSymbol.value
)

const doPasswordsMatch = computed(() =>
  password.value === confirmPassword.value && confirmPassword.value.length > 0
)

// USERNAME CHECK
watch(username, async (val) => {
  usernameTouched.value = true

  // ha formailag nem jó, ne kérdezzen backendet
  if (!isUsernameFormatValid.value) {
    isUsernameAvailable.value = null
    return
  }

  checkingUsername.value = true

  try {
    const response = await axios.get('/check-username', {
      params: { username: val }
    })
    isUsernameAvailable.value = response.data.available
  } catch (e) {
    isUsernameAvailable.value = false
  } finally {
    checkingUsername.value = false
  }
})

// SUBMIT
const submit = () => {
  router.post('/regisztracio', {
    nev: username.value,
    email: email.value,
    jelszo: password.value,
    jelszo_confirmation: confirmPassword.value,
    allergen_id: 1,
  }, {
    onSuccess: () => console.log('Sikeres regisztráció'),
    onError: (errors) => console.error(errors)
  })
}
</script>

<template>

  <Head title="Regisztráció" />

  <!-- PAGE -->
  <div class="relative flex min-h-screen items-center justify-center p-4 overflow-hidden
           bg-gradient-to-br from-brand-900 via-brand-700 to-brand-800
           animate-gradient">
    <!-- subtle glow overlay -->
    <div class="pointer-events-none absolute inset-0
             bg-gradient-to-br from-accent-500/20 via-transparent to-accent-600/20
             blur-3xl animate-gradient-slow"></div>

    <div class="relative w-full max-w-md">
      <!-- ACCENT FRAME -->
      <div class="rounded-3xl overflow-hidden shadow-2xl">
        <div class="bg-gradient-to-br from-accent-500/80 to-accent-600/80 p-1">
          <!-- CARD -->
          <div class="rounded-3xl bg-accent-300 p-8">
            <h1 class="text-2xl font-semibold text-slate-900">
              Regisztráció
              <span class="text-brand-700">| FoodR</span>
            </h1>

            <form class="mt-6 space-y-5" @submit.prevent="submit">
              <!-- USERNAME -->
              <div>
                <label class="block text-sm font-medium text-slate-800">Felhasználónév</label>
                <div class="relative mt-1 group">
                  <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2
                           text-slate-700 transition group-focus-within:text-accent-800">
                    <User class="h-5 w-5" />
                  </span>
                  <input v-model="username" type="text" placeholder="felhasznalonev" class="w-full rounded-xl border border-accent-400/60
                       bg-accent-200 px-3 py-2 pl-10 pr-10
                       text-slate-900 placeholder-slate-700 outline-none
                       transition-all duration-300
                       focus:bg-accent-200
                       focus:border-accent-600
                       focus:ring-4 focus:ring-accent-600/30" />
                  <button v-if="username.length > 0" type="button" @click="clearUsername"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-600 hover:text-brand-600 transition cursor-pointer">
                    <X class="h-5 w-5" />
                  </button>
                </div>
                <!-- VALIDATION -->
                <div v-if="usernameTouched" class="mt-2 text-sm space-y-1">
                  <p class="flex items-center gap-2 transition"
                    :class="username.length >= 4 ? 'text-emerald-700' : 'text-slate-800'">
                    <Check v-if="username.length >= 4" class="h-4 w-4" />
                    <span v-else class="inline-block h-1.5 w-1.5 rounded-full bg-slate-900"></span>
                    Legalább 4 karakter
                  </p>
                  <p v-if="username.length >= 4" class="flex items-center gap-2 transition" :class="checkingUsername ? 'text-slate-800'
                    : isUsernameAvailable ? 'text-emerald-700' : 'text-brand-600'">
                    <span v-if="checkingUsername">Ellenőrzés…</span>
                    <template v-else>
                      <Check v-if="isUsernameAvailable" class="h-4 w-4" />
                      <span v-else class="inline-block h-1.5 w-1.5 rounded-full bg-brand-600"></span>
                      {{ isUsernameAvailable ? 'Felhasználónév elérhető' : 'A felhasználónév nem elérhető' }}
                    </template>
                  </p>
                </div>
              </div>

              <!-- EMAIL -->
              <div>
                <label class="block text-sm font-medium text-slate-800">Email cím</label>
                <div class="relative mt-1 group">
                  <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2
                           text-slate-700 transition group-focus-within:text-accent-800">
                    <Mail class="h-5 w-5" />
                  </span>
                  <input v-model="email" type="email" placeholder="email@pelda.hu" required class="w-full rounded-xl border border-accent-400/60
                       bg-accent-200 px-3 py-2 pl-10 pr-10
                       text-slate-900 placeholder-slate-700 outline-none
                       transition-all duration-300
                       focus:bg-accent-200
                       focus:border-accent-600
                       focus:ring-4 focus:ring-accent-600/30" />
                </div>
              </div>

              <!-- PASSWORD -->
              <div>
                <label class="block text-sm font-medium text-slate-800">Jelszó</label>
                <div class="relative mt-1 group">
                  <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2
                           text-slate-700 transition group-focus-within:text-accent-800">
                    <Lock class="h-5 w-5" />
                  </span>
                  <input v-model="password" :type="showPassword ? 'text' : 'password'" placeholder="••••••••" class="w-full rounded-xl border border-accent-400/60
                       bg-accent-200 px-3 py-2 pl-10 pr-10
                       text-slate-900 placeholder-slate-700 outline-none
                       transition-all duration-300
                       focus:bg-accent-200
                       focus:border-accent-600
                       focus:ring-4 focus:ring-accent-600/30" />
                  <button type="button" @click="showPassword = !showPassword"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-600 hover:text-accent-700 transition cursor-pointer">
                    <component :is="showPassword ? EyeOff : Eye" class="h-5 w-5" />
                  </button>
                </div>
                <div class="mt-2 space-y-1 text-sm">
                  <p class="flex items-center gap-2"
                    :class="isPasswordLongEnough ? 'text-emerald-700' : 'text-slate-800'">
                    <Check v-if="isPasswordLongEnough" class="h-4 w-4" />
                    <span v-else class="inline-block h-1.5 w-1.5 rounded-full bg-slate-900"></span>
                    Legalább 8 karakter
                  </p>

                  <p class="flex items-center gap-2" :class="hasNumber ? 'text-emerald-700' : 'text-slate-800'">
                    <Check v-if="hasNumber" class="h-4 w-4" />
                    <span v-else class="inline-block h-1.5 w-1.5 rounded-full bg-slate-900"></span>
                    Tartalmaz számot
                  </p>

                  <!-- AJÁNLOTT -->
                  <p class="flex items-center gap-2" :class="hasUppercase ? 'text-emerald-700' : 'text-slate-800'">
                    <Check v-if="hasUppercase" class="h-4 w-4 text-emerald-700" />
                    <span v-else class="inline-block h-1.5 w-1.5 rounded-full bg-slate-900"></span>
                    Tartalmaz nagybetűt
                  </p>
                  <p class="flex items-center gap-2" :class="hasSymbol ? 'text-emerald-700' : 'text-slate-800'">
                    <Check v-if="hasSymbol" class="h-4 w-4 text-emerald-700" />
                    <span v-else class="inline-block h-1.5 w-1.5 rounded-full bg-slate-900"></span>
                    Tartalmaz speciális karaktert
                  </p>
                </div>
              </div>

              <!-- CONFIRM PASSWORD -->
              <div>
                <label class="block text-sm font-medium text-slate-800">Jelszó megerősítése</label>
                <div class="relative mt-1 group">
                  <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2
                           text-slate-700 transition group-focus-within:text-accent-800">
                    <Lock class="h-5 w-5" />
                  </span>
                  <input v-model="confirmPassword" :type="showPasswordConfirm ? 'text' : 'password'"
                    placeholder="••••••••" class="w-full rounded-xl border border-accent-400/60
                       bg-accent-200 px-3 py-2 pl-10 pr-10
                       text-slate-900 placeholder-slate-700 outline-none
                       transition-all duration-300
                       focus:bg-accent-200
                       focus:border-accent-600
                       focus:ring-4 focus:ring-accent-600/30" />
                  <button type="button" @click="showPasswordConfirm = !showPasswordConfirm"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-600 hover:text-accent-700 transition cursor-pointer">
                    <component :is="showPasswordConfirm ? EyeOff : Eye" class="h-5 w-5" />
                  </button>
                </div>
                <div class="mt-2 text-sm">
                  <p class="flex items-center gap-2 transition"
                    :class="doPasswordsMatch ? 'text-emerald-700' : 'text-slate-800 '">
                    <Check v-if="doPasswordsMatch" class="h-4 w-4" />
                    <span v-else class="inline-block h-1.5 w-1.5 rounded-full bg-slate-900"></span>
                    A jelszavak egyeznek
                  </p>
                </div>
              </div>

              <button type="submit" class="w-full rounded-2xl bg-brand-700 py-2.5 font-semibold text-accent-200
         shadow-xl shadow-brand-800/40 transition-all
         hover:bg-brand-800 hover:scale-[1.02]
         focus:outline-none focus:ring-4 focus:ring-brand-500/40
         disabled:bg-brand-400 disabled:text-accent-200 disabled:cursor-not-allowed cursor-pointer" :disabled="checkingUsername ||
          !isUsernameValid ||
          !isPasswordValid ||
          !doPasswordsMatch
          ">
                Regisztráció
              </button>


            </form>

            <p class="mt-6 text-center text-sm text-slate-800">
              Már van fiókod?
              <Link href="/bejelentkezes" class="font-medium text-brand-600 hover:underline">
                Bejelentkezés
              </Link>
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
