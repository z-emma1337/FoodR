<script setup>
import { ref, computed } from 'vue'
import { router, Link, Head } from '@inertiajs/vue3'
import AuthLayout from '@/layouts/AuthLayout.vue'
import FormInput from '@/components/form/FormInput.vue'
import FormButton from '@/components/form/FormButton.vue'
import { Mail, Lock, Eye } from 'lucide-vue-next'

const email = ref('')
const password = ref('')
const showPassword = ref(false)
const errorMessage = ref('')

// Form validation (minimal, email + password required)
const isFormValid = computed(() => email.value.length > 0 && password.value.length > 0)

// Submit
const submit = () => {
  errorMessage.value = '' // előző hibát töröljük

  router.post('/bejelentkezes', {
    email: email.value,
    jelszo: password.value
  }, {
    onSuccess: () => console.log('Sikeres bejelentkezés'),
    onError: () => errorMessage.value = 'Hibás email cím vagy jelszó'
  })
}
</script>


<template>
  <AuthLayout title="Bejelentkezés">
     <h1 class="text-2xl font-semibold text-slate-900 transition-all duration-300">
      Bejelentkezés | <span class="text-accent-400 text-outline-shadow">Food</span><span class="text-brand-500 text-outline-shadow">R</span>
    </h1>

    <form @submit.prevent="submit" class="mt-6 space-y-5">
      <!-- EMAIL -->
      <FormInput
        v-model="email"
        type="email"
        :icon="Mail"
        placeholder="email@pelda.hu"
        required
      />

      <!-- PASSWORD -->
      <FormInput
        v-model="password"
        :type="showPassword ? 'text' : 'password'"
        :icon="Lock"
        placeholder="••••••••"
        :show-toggle="true"
        :toggle-state="showPassword"
        @toggle="showPassword = !showPassword"
      />

      <!-- ERROR MESSAGE -->
      <div v-if="errorMessage" class="rounded-lg bg-brand-200 px-3 py-2 text-center text-sm text-brand-900">
        {{ errorMessage }}
      </div>

      <!-- SUBMIT BUTTON -->
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
