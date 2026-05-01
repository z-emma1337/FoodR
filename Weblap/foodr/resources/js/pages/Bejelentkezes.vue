<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { router, Link, Head } from '@inertiajs/vue3'
import AuthLayout from '@/layouts/AuthLayout.vue'
import FormInput from '@/components/form/FormInput.vue'
import FormButton from '@/components/form/FormButton.vue'
import { Mail, Lock, User } from 'lucide-vue-next'

const authInput = ref('')
const password = ref('')
const showPassword = ref(false)
const errorMessage = ref('')

const isFormValid = computed(() => authInput.value.length > 0 && password.value.length > 0)

const submit = () => {
  errorMessage.value = ''

  router.post('/bejelentkezes', {
    authInput: authInput.value.toLowerCase(),
    jelszo: password.value
  }, {
    onError: () => errorMessage.value = 'Hibás email cím vagy jelszó'
  })
}

const valtIkon = ref(Mail)
const valtPlaceholder = ref('email@pelda.hu')


let intervalId = null

onMounted(() => {
  intervalId = setInterval(() => {
    if (valtIkon.value === Mail) {
      valtIkon.value = User
      valtPlaceholder.value = 'felhasznalonev'
    } else {
      valtIkon.value = Mail
      valtPlaceholder.value = "email@pelda.hu"
    }
  }, 2000)
})

onUnmounted(() => {
  if (intervalId) clearInterval(intervalId)
})
</script>


<template>
  <AuthLayout title="Bejelentkezés">
     <h1 class="text-2xl font-semibold text-slate-900 transition-all duration-300">
      Bejelentkezés | <span class="text-accent-400 text-outline-shadow">Food</span><span class="text-brand-500 text-outline-shadow">R</span>
    </h1>

    <form @submit.prevent="submit" class="mt-6 space-y-5">
      <FormInput
        v-model="authInput"
        type="text"
        :icon="valtIkon"
        :placeholder="valtPlaceholder"
        required
      />


      <FormInput
        v-model="password"
        :type="showPassword ? 'text' : 'password'"
        :icon="Lock"
        placeholder="••••••••"
        :show-toggle="true"
        :toggle-state="showPassword"
        @toggle="showPassword = !showPassword"
      />

      <div v-if="errorMessage" class="rounded-3xl bg-brand-200 px-3 py-2 text-center text-sm text-brand-900">
        {{ errorMessage }}
      </div>

      <FormButton :disabled="!isFormValid">
        Bejelentkezés
      </FormButton>
    </form>

    <p class="mt-6 text-center text-sm text-slate-800 transition-all duration-300">
      Nincs még fiókod?
      <Link 
        href="/regisztracio"
        class="font-medium text-brand-600 hover:underline inline-block hover:scale-105 transition-all duration-200"
      >
        Regisztráció
      </Link>
    </p>
  </AuthLayout>
</template>
