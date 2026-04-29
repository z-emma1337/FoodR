<script setup>
import { ref, computed } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import AuthLayout from '@/layouts/AuthLayout.vue'
import FormInput from '@/components/form/FormInput.vue'
import FormButton from '@/components/form/FormButton.vue'
import ValidationItem from '@/components/form/ValidationItem.vue'
import { User, Mail, Lock, Eye } from 'lucide-vue-next'
import { useUsernameCheck } from '@/composables/useUsernameCheck'
import { usePasswordValidation } from '@/composables/usePasswordValidation'
import { useEmailCheck } from '@/composables/useEmailCheck'


const email = ref('')
const username = ref('')
const password = ref('')
const confirmPassword = ref('')
const showPassword = ref(false)
const showPasswordConfirm = ref(false)


const {
  isUsernameAvailable,
  checkingUsername,
  usernameTouched,
  isUsernameLongEnough,
  isUsernameValid,
  clearUsername
} = useUsernameCheck(username)

const {
  isPasswordLongEnough,
  hasNumber,
  hasUppercase,
  hasSymbol,
  isPasswordValid
} = usePasswordValidation(password)

const {
  isEmailAvailable,
  checkingEmail,
  emailTouched,
  hasAtSymbol,
  isEmailFormatValid,
  isEmailValid,
} = useEmailCheck(email)

const doPasswordsMatch = computed(() =>
  password.value === confirmPassword.value && confirmPassword.value.length > 0
)


const isFormValid = computed(() =>
  !checkingUsername.value &&
  isUsernameValid.value &&
  !checkingEmail.value &&
  isEmailValid.value &&
  isPasswordValid.value &&
  doPasswordsMatch.value
)


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
  <AuthLayout title="Regisztráció">
    <h1 class="text-2xl font-semibold text-slate-900 transition-all duration-300">
      Regisztráció | <span class="text-accent-400 text-outline-shadow">Food</span><span class="text-brand-500 text-outline-shadow">R</span>
    </h1>

    <form class="mt-6 space-y-5" @submit.prevent="submit">

 
      <div class="transform transition-all duration-300">
        <label class="block text-sm font-medium text-slate-800 mb-1">Felhasználónév</label>
        <FormInput v-model="username" :icon="User" placeholder="felhasznalonev" :show-clear="username.length > 0"
          @clear="clearUsername" />

        <div v-if="usernameTouched" class="mt-2 space-y-1">
          <ValidationItem :valid="isUsernameLongEnough" text="Legalább 4 karakter" />
          <ValidationItem v-if="username.length >= 4" :valid="isUsernameAvailable" :loading="checkingUsername"
            :text="checkingUsername ? 'Ellenőrzés…' : isUsernameAvailable ? 'Felhasználónév elérhető' : 'A felhasználónév nem elérhető'" />
        </div>
      </div>

      <div class="transform transition-all duration-300">
        <label class="block text-sm font-medium text-slate-800 mb-1">Email cím</label>
        <FormInput v-model="email" type="email" :icon="Mail" placeholder="email@pelda.hu" />
        <div v-if="emailTouched && hasAtSymbol" class="mt-2 space-y-1">
          <ValidationItem
            v-if="isEmailFormatValid"
            :valid="isEmailAvailable"
            :loading="checkingEmail"
            :text="checkingEmail ? 'Ellenőrzés…' : isEmailAvailable ? 'Email cím elérhető' : 'Ez az email cím már foglalt'"
          />
          <ValidationItem v-else :valid="false" text="Érvénytelen email cím formátum" />
        </div>
      </div>

      <div class="transform transition-all duration-300 ">
        <label class="block text-sm font-medium text-slate-800 mb-1">Jelszó</label>
        <FormInput v-model="password" :type="showPassword ? 'text' : 'password'" :icon="Lock" placeholder="••••••••"
          :show-toggle="true" :toggle-state="showPassword" @toggle="showPassword = !showPassword" />

        <div class="mt-2 space-y-1">
          <ValidationItem :valid="isPasswordLongEnough" text="Legalább 8 karakter" />
          <ValidationItem :valid="hasNumber" text="Tartalmaz számot" />
          <ValidationItem :valid="hasUppercase" text="Tartalmaz nagybetűt" />
          <ValidationItem :valid="hasSymbol" text="Tartalmaz speciális karaktert" />
        </div>
      </div>

      <div class="transform transition-all duration-300">
        <label class="block text-sm font-medium text-slate-800 mb-1">Jelszó megerősítése</label>
        <FormInput v-model="confirmPassword" :type="showPasswordConfirm ? 'text' : 'password'" :icon="Lock"
          placeholder="••••••••" :show-toggle="true" :toggle-state="showPasswordConfirm"
          @toggle="showPasswordConfirm = !showPasswordConfirm" />

        <div class="mt-2">
          <ValidationItem :valid="doPasswordsMatch" text="A jelszavak egyeznek" />
        </div>
      </div>

      <FormButton :disabled="!isFormValid">
        Regisztráció
      </FormButton>

    </form>

    <p class="mt-6 text-center text-sm text-slate-800 transition-all duration-300">
      Már van fiókod?
      <Link href="/"
        class="font-medium text-brand-600 hover:underline transition-all duration-200inline-block">
        Bejelentkezés
      </Link>
    </p>
  </AuthLayout>
</template>