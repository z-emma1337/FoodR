<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import FormInput from '@/components/form/FormInput.vue'
import FormButton from '@/components/form/FormButton.vue'
import { Mail, Lock, User, X as CloseIcon } from 'lucide-vue-next'
import { useLoginModal } from '@/composables/useLoginModal'

const props = defineProps({
    open: {
        type: Boolean,
        required: true
    }
})

const emit = defineEmits(['close'])

const { resolveLoginSuccess } = useLoginModal()

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
        onSuccess: () => resolveLoginSuccess(),
        onError: () => { errorMessage.value = 'Hibás email cím vagy jelszó' }
    })
}

watch(() => props.open, (val) => {
    if (!val) {
        authInput.value = ''
        password.value = ''
        showPassword.value = false
        errorMessage.value = ''
    }
})

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
            valtPlaceholder.value = 'email@pelda.hu'
        }
    }, 2000)
})

onUnmounted(() => {
    if (intervalId) clearInterval(intervalId)
})
</script>

<template>
    <Transition name="modal-fade">
        <div v-if="open" class="fixed inset-0 z-[60] flex items-center justify-center bg-black/60 backdrop-blur-sm"
            @click.self="emit('close')">
            <Transition name="modal-scale">
                <div v-if="open"
                    class="relative w-full max-w-md mx-4 rounded-3xl shadow-2xl border-accent-600 border-6 overflow-hidden">
                    <div class="bg-gradient-to-br from-accent-300 via-accent-200 to-accent-300 p-10 space-y-6">

                        <!-- Close button -->
                        <button @click="emit('close')"
                            class="absolute top-4 right-4 w-9 h-9 rounded-full flex items-center justify-center hover:bg-accent-500/30 transition-all duration-200">
                            <CloseIcon class="w-6 h-6 text-brand-600" :stroke-width="3" />
                        </button>

                        <!-- Title -->
                        <h1 class="text-3xl font-semibold text-slate-900 text-center transition-all duration-300">
                            Bejelentkezés |
                            <span class="text-accent-400 text-outline-shadow">Food</span><span
                                class="text-brand-500 text-outline-shadow">R</span>
                        </h1>

                        <!-- Form -->
                        <form @submit.prevent="submit" class="space-y-5">
                            <FormInput v-model="authInput" type="text" :icon="valtIkon" :placeholder="valtPlaceholder"
                                required />

                            <FormInput v-model="password" :type="showPassword ? 'text' : 'password'" :icon="Lock"
                                placeholder="••••••••" :show-toggle="true" :toggle-state="showPassword"
                                @toggle="showPassword = !showPassword" />

                            <div v-if="errorMessage"
                                class="rounded-3xl bg-brand-200 px-3 py-2 text-center text-sm text-brand-900">
                                {{ errorMessage }}
                            </div>

                            <FormButton :disabled="!isFormValid">
                                Bejelentkezés
                            </FormButton>
                        </form>

                        <!-- Register link -->
                        <p class="text-center text-sm text-slate-800 transition-all duration-300">
                            Nincs még fiókod?
                            <Link href="/regisztracio"
                                class="font-medium text-brand-600 hover:underline inline-block hover:scale-105 transition-all duration-200">
                                Regisztráció
                            </Link>
                        </p>

                    </div>
                </div>
            </Transition>
        </div>
    </Transition>
</template>