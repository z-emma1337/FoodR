<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import FormInput from '@/components/form/FormInput.vue'
import FormButton from '@/components/form/FormButton.vue'
import RegisztracioModal from '@/components/UI/RegisztracioModal.vue'
import { Mail, Lock, User, X as CloseIcon } from 'lucide-vue-next'
import { useLoginModal } from '@/composables/useLoginModal'

const props = defineProps({ open: { type: Boolean, required: true } })
const emit = defineEmits(['close'])

const { resolveLoginSuccess } = useLoginModal()

const visibleFace = ref('login')
const rotateY = ref(0)
const isFlipping = ref(false)

const flipTo = (face) => {
    if (isFlipping.value || visibleFace.value === face) return
    isFlipping.value = true

    rotateY.value = 90

    setTimeout(() => {
        visibleFace.value = face

        rotateY.value = -90
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                rotateY.value = 0
                setTimeout(() => { isFlipping.value = false }, 350)
            })
        })
    }, 350)
}

const openRegisztracio = () => flipTo('register')
const closeRegisztracio = () => flipTo('login')

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
        visibleFace.value = 'login'
        rotateY.value = 0
        isFlipping.value = false
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
        <div v-if="open" class="fixed inset-0 z-[60] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4"
            @click.self="emit('close')">
            <div class="w-full max-w-md" :style="{
                transform: `rotateY(${rotateY}deg)`,
                transition: 'transform 0.35s ease-in-out',
            }">

                <div v-if="visibleFace === 'login'"
                    class="w-full rounded-3xl shadow-2xl border-accent-600 border-6 overflow-hidden">
                    <div class="bg-gradient-to-br from-accent-300 via-accent-200 to-accent-300 p-10 space-y-6">

                        <button @click="emit('close')"
                            class="absolute top-4 right-4 w-9 h-9 rounded-full flex items-center justify-center hover:bg-accent-500/30 transition-all duration-200">
                            <CloseIcon class="w-6 h-6 text-brand-600" :stroke-width="3" />
                        </button>

                        <h1 class="text-3xl font-semibold text-slate-900 text-center">
                            Bejelentkezés |
                            <span class="text-accent-400 text-outline-shadow">Food</span><span
                                class="text-brand-500 text-outline-shadow">R</span>
                        </h1>

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

                        <p class="text-center text-sm text-slate-800">
                            Nincs még fiókod?
                            <button type="button" @click="openRegisztracio"
                                class="font-medium text-brand-600 hover:underline inline-block hover:scale-105 transition-all duration-200">
                                Regisztráció
                            </button>
                        </p>

                    </div>
                </div>

                <div v-if="visibleFace === 'register'"
                    class="w-full rounded-3xl shadow-2xl border-accent-600 border-6 overflow-hidden">
                    <RegisztracioModal :open="visibleFace === 'register'" @close="closeRegisztracio"
                        @switch-to-login="closeRegisztracio" />
                </div>

            </div>
        </div>
    </Transition>
</template>