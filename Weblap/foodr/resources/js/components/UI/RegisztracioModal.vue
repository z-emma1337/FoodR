<script setup>
import { ref, computed, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import { User, Mail, Lock, X as CloseIcon } from 'lucide-vue-next'
import FormInput from '@/components/form/FormInput.vue'
import FormButton from '@/components/form/FormButton.vue'
import ValidationItem from '@/components/form/ValidationItem.vue'
import { useUsernameCheck } from '@/composables/useUsernameCheck'
import { usePasswordValidation } from '@/composables/usePasswordValidation'
import { useEmailCheck } from '@/composables/useEmailCheck'

const props = defineProps({ open: { type: Boolean, required: true } })
const emit = defineEmits(['close', 'switch-to-login', 'registered'])

const email = ref('')
const username = ref('')
const password = ref('')
const confirmPassword = ref('')
const showPassword = ref(false)
const showPasswordConfirm = ref(false)
const errorMessage = ref('')
const emailError = ref('')

const {
    isUsernameAvailable,
    checkingUsername,
    usernameTouched,
    isUsernameLongEnough,
    isUsernameValid,
    clearUsername,
} = useUsernameCheck(username)

const {
    isPasswordLongEnough,
    hasNumber,
    hasUppercase,
    hasSymbol,
    isPasswordValid,
} = usePasswordValidation(password)

const {
    isEmailAvailable,
    checkingEmail,
    emailTouched,
    hasAtSymbol,
    isEmailFormatValid,
    isEmailValid,
    clearEmail,
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
    errorMessage.value = ''
    emailError.value = ''
    router.post('/regisztracio', {
        nev: username.value,
        email: email.value,
        jelszo: password.value,
        jelszo_confirmation: confirmPassword.value,
        allergen_id: 1,
    }, {
        onSuccess: () => emit('registered'),
        onError: (errors) => {
            if (errors.email) {
                emailError.value = Array.isArray(errors.email) ? errors.email[0] : errors.email
            }
            const nonEmailErrors = Object.entries(errors).filter(([k]) => k !== 'email')
            if (nonEmailErrors.length > 0) {
                const first = nonEmailErrors[0][1]
                errorMessage.value = Array.isArray(first) ? first[0] : first
            }
        }
    })
}

watch(() => props.open, (val) => {
    if (!val) {
        username.value = ''
        password.value = ''
        confirmPassword.value = ''
        showPassword.value = false
        showPasswordConfirm.value = false
        errorMessage.value = ''
        emailError.value = ''
        clearUsername()
        clearEmail()
    }
})
</script>

<template>
    <div class="relative w-full">
        <div class="bg-gradient-to-br from-accent-300 via-accent-200 to-accent-300 flex flex-col max-h-[85vh]">

            <div class="flex items-center justify-between px-10 pt-10 pb-2 shrink-0">
                <h1 class="text-3xl font-semibold text-slate-900">
                    Regisztráció |
                    <span class="text-accent-400 text-outline-shadow">Food</span><span
                        class="text-brand-500 text-outline-shadow">R</span>
                </h1>
                <button @click="emit('close')"
                    class="w-9 h-9 rounded-full flex items-center justify-center hover:bg-accent-500/30 transition-all duration-200 shrink-0 ml-3">
                    <CloseIcon class="w-6 h-6 text-brand-600" :stroke-width="3" />
                </button>
            </div>

            <div class="overflow-y-auto px-10 pb-10">
                <form class="mt-6 space-y-5" @submit.prevent="submit">

                    <div class="transform transition-all duration-300">
                        <label class="block text-sm font-medium text-slate-800 mb-1">Felhasználónév</label>
                        <FormInput v-model="username" :icon="User" placeholder="felhasznalonev"
                            :show-clear="username.length > 0" @clear="clearUsername" />
                        <div v-if="usernameTouched" class="mt-2 space-y-1">
                            <ValidationItem :valid="isUsernameLongEnough" text="Legalább 4 karakter" />
                            <ValidationItem v-if="username.length >= 4" :valid="isUsernameAvailable"
                                :loading="checkingUsername"
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
                        <div v-if="emailError && !emailTouched" class="mt-2">
                            <ValidationItem :valid="false" :text="emailError" />
                        </div>
                    </div>

                    <div class="transform transition-all duration-300">
                        <label class="block text-sm font-medium text-slate-800 mb-1">Jelszó</label>
                        <FormInput v-model="password" :type="showPassword ? 'text' : 'password'" :icon="Lock"
                            placeholder="••••••••" :show-toggle="true" :toggle-state="showPassword"
                            @toggle="showPassword = !showPassword" />
                        <div class="mt-2 space-y-1">
                            <ValidationItem :valid="isPasswordLongEnough" text="Legalább 8 karakter" />
                            <ValidationItem :valid="hasNumber" text="Tartalmaz számot" />
                            <ValidationItem :valid="hasUppercase" text="Tartalmaz nagybetűt" />
                            <ValidationItem :valid="hasSymbol" text="Tartalmaz speciális karaktert" />
                        </div>
                    </div>

                    <div class="transform transition-all duration-300">
                        <label class="block text-sm font-medium text-slate-800 mb-1">Jelszó megerősítése</label>
                        <FormInput v-model="confirmPassword" :type="showPasswordConfirm ? 'text' : 'password'"
                            :icon="Lock" placeholder="••••••••" :show-toggle="true" :toggle-state="showPasswordConfirm"
                            @toggle="showPasswordConfirm = !showPasswordConfirm" />
                        <div class="mt-2">
                            <ValidationItem :valid="doPasswordsMatch" text="A jelszavak egyeznek" />
                        </div>
                    </div>

                    <div v-if="errorMessage"
                        class="rounded-3xl bg-brand-200 px-3 py-2 text-center text-sm text-brand-900">
                        {{ errorMessage }}
                    </div>

                    <FormButton :disabled="!isFormValid">
                        Regisztráció
                    </FormButton>

                </form>

                <p class="mt-6 text-center text-sm text-slate-800 transition-all duration-300 pb-1">
                    Már van fiókod?
                    <button type="button" @click="emit('switch-to-login')"
                        class="font-medium text-brand-600 hover:underline inline-block hover:scale-105 transition-all duration-200">
                        Bejelentkezés
                    </button>
                </p>
            </div>

        </div>
    </div>
</template>