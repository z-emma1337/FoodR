<script setup>
import { ref, watch } from 'vue'
import { X as CloseIcon, ShieldCheck, QrCode, KeyRound, Copy, Check } from 'lucide-vue-next'
import { useTwoFactorAuth } from '@/composables/useTwoFactorAuth'

const props = defineProps({ open: { type: Boolean, required: true } })
const emit = defineEmits(['close', 'enabled'])

const { qrCodeSvg, manualSetupKey, recoveryCodesList, errors, fetchSetupData, fetchRecoveryCodes, clearTwoFactorAuthData } = useTwoFactorAuth()

const step = ref('setup')
const confirmCode = ref('')
const confirmError = ref('')
const loading = ref(false)
const copied = ref(false)

function csrfToken() {
    const match = document.cookie.match(/XSRF-TOKEN=([^;]+)/)
    return match ? decodeURIComponent(match[1]) : ''
}

async function apiPost(url, body) {
    return fetch(url, {
        method: 'POST',
        headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
            'X-XSRF-TOKEN': csrfToken(),
        },
        body: body ? JSON.stringify(body) : undefined,
    })
}

async function startSetup() {
    loading.value = true
    errors.value = []
    try {
        const res = await apiPost('/user/two-factor-authentication')
        if (!res.ok) throw new Error()
        await fetchSetupData()
        step.value = 'qr'
    } catch {
        errors.value = ['Nem sikerült elindítani a beállítást. Próbáld újra.']
    } finally {
        loading.value = false
    }
}

async function confirm() {
    if (confirmCode.value.replace(/\s/g, '').length !== 6) {
        confirmError.value = 'Kérjük, adj meg egy 6 jegyű kódot.'
        return
    }
    loading.value = true
    confirmError.value = ''
    try {
        const res = await apiPost('/user/confirmed-two-factor-authentication', {
            code: confirmCode.value.replace(/\s/g, ''),
        })
        if (res.status === 422) {
            confirmError.value = 'Hibás kód. Ellenőrizd az alkalmazásban és próbáld újra.'
            return
        }
        if (!res.ok) throw new Error()
        await fetchRecoveryCodes()
        step.value = 'recovery'
        emit('enabled')
    } catch {
        confirmError.value = 'Nem sikerült megerősíteni. Próbáld újra.'
    } finally {
        loading.value = false
    }
}

async function copyKey() {
    if (!manualSetupKey.value) return
    await navigator.clipboard.writeText(manualSetupKey.value)
    copied.value = true
    setTimeout(() => (copied.value = false), 2000)
}

async function close() {
    if (step.value !== 'recovery') {
        await apiPost('/user/two-factor-cancel')
    }
    emit('close')
}

watch(
    () => props.open,
    (val) => {
        if (val) {
            step.value = 'setup'
            confirmCode.value = ''
            confirmError.value = ''
            loading.value = false
            copied.value = false
            clearTwoFactorAuthData()
            startSetup()
        }
    },
)
</script>

<template>
    <Transition name="modal-fade">
        <div
            v-if="open"
            class="fixed inset-0 z-[70] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4"
            @click.self="close"
        >
            <div
                class="relative w-full max-w-md rounded-3xl shadow-2xl border-accent-600 border-6 overflow-hidden"
            >
                <div
                    class="bg-gradient-to-br from-accent-300 via-accent-200 to-accent-300 flex flex-col max-h-[90vh]"
                >
                    <div class="flex items-center justify-between px-8 pt-8 pb-4 shrink-0">
                        <div class="flex items-center gap-2">
                            <ShieldCheck class="w-6 h-6 text-brand-600" />
                            <h2 class="text-xl font-bold text-slate-900">Kétlépéses hitelesítés</h2>
                        </div>
                        <button
                            @click="close"
                            class="w-9 h-9 rounded-full flex items-center justify-center hover:bg-accent-500/30 transition-all duration-200"
                        >
                            <CloseIcon class="w-6 h-6 text-brand-600" :stroke-width="3" />
                        </button>
                    </div>

                    <div class="overflow-y-auto px-8 pb-8 foodr-scrollbar">

                        <div v-if="loading && step === 'setup'" class="py-12 flex flex-col items-center gap-3">
                            <div class="w-10 h-10 rounded-full border-4 border-brand-600 border-t-transparent animate-spin" />
                            <p class="text-sm text-slate-600">Beállítás előkészítése...</p>
                        </div>

                        <div v-else-if="errors.length && step === 'setup'" class="py-6 space-y-4">
                            <div class="rounded-3xl bg-brand-200 px-4 py-3 text-sm text-brand-900 text-center">
                                {{ errors[0] }}
                            </div>
                            <button
                                @click="startSetup"
                                class="w-full py-3 rounded-3xl bg-brand-700 text-accent-200 hover:bg-brand-800 transition-all hover:scale-[1.02] font-medium shadow-md"
                            >
                                Újrapróbálás
                            </button>
                        </div>

                        <div v-else-if="step === 'qr'" class="space-y-5">
                            <p class="text-sm text-slate-700 leading-relaxed">
                                Nyisd meg a
                                <span class="font-semibold text-brand-700">Google Authenticator</span>
                                alkalmazást, koppints a <span class="font-semibold">+</span> gombra, majd
                                <span class="font-semibold">szkenneld be a QR kódot</span>.
                            </p>

                            <div
                                v-if="qrCodeSvg"
                                class="flex justify-center p-4 bg-white rounded-2xl shadow-inner"
                                v-html="qrCodeSvg"
                            />

                            <div class="space-y-2">
                                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                    Nem tudod beolvasni? Írd be kézzel:
                                </p>
                                <div
                                    class="flex items-center gap-2 rounded-2xl bg-accent-400/40 px-4 py-3"
                                >
                                    <KeyRound class="w-4 h-4 text-brand-600 shrink-0" />
                                    <span class="text-xs font-mono text-slate-800 flex-1 break-all select-all">{{
                                        manualSetupKey
                                    }}</span>
                                    <button
                                        @click="copyKey"
                                        class="w-8 h-8 rounded-full flex items-center justify-center hover:bg-accent-500/40 transition-all"
                                    >
                                        <Check v-if="copied" class="w-4 h-4 text-green-600" />
                                        <Copy v-else class="w-4 h-4 text-brand-600" />
                                    </button>
                                </div>
                            </div>

                            <button
                                @click="step = 'confirm'"
                                class="w-full py-3 rounded-3xl bg-brand-700 text-accent-200 hover:bg-brand-800 transition-all hover:scale-[1.02] font-medium shadow-md"
                            >
                                Beolvastam → Tovább
                            </button>
                        </div>

                        <div v-else-if="step === 'confirm'" class="space-y-5">
                            <p class="text-sm text-slate-700 leading-relaxed">
                                Add meg a <span class="font-semibold">Google Authenticator</span>ban
                                megjelenő <span class="font-semibold">6 jegyű kódot</span> a beállítás
                                megerősítéséhez.
                            </p>

                            <div class="flex justify-center">
                                <input
                                    v-model="confirmCode"
                                    type="text"
                                    inputmode="numeric"
                                    maxlength="6"
                                    placeholder="000000"
                                    @keydown.enter="confirm"
                                    class="w-48 text-center text-2xl font-mono tracking-widest rounded-2xl border-2 border-accent-500 bg-white/70 px-4 py-3 focus:outline-none focus:border-brand-600 transition-colors"
                                />
                            </div>

                            <div
                                v-if="confirmError"
                                class="rounded-3xl bg-brand-200 px-4 py-3 text-sm text-brand-900 text-center"
                            >
                                {{ confirmError }}
                            </div>

                            <div class="flex gap-3">
                                <button
                                    @click="step = 'qr'"
                                    class="flex-1 py-3 rounded-3xl border-2 border-brand-700 text-brand-700 hover:bg-brand-700/10 transition-all font-medium"
                                >
                                    Vissza
                                </button>
                                <button
                                    @click="confirm"
                                    :disabled="loading"
                                    class="flex-1 py-3 rounded-3xl bg-brand-700 text-accent-200 hover:bg-brand-800 transition-all hover:scale-[1.02] font-medium shadow-md disabled:opacity-60 disabled:cursor-not-allowed"
                                >
                                    <span v-if="loading" class="flex items-center justify-center gap-2">
                                        <span class="w-4 h-4 rounded-full border-2 border-accent-200 border-t-transparent animate-spin" />
                                        Ellenőrzés...
                                    </span>
                                    <span v-else>Megerősítés</span>
                                </button>
                            </div>
                        </div>

                        <div v-else-if="step === 'recovery'" class="space-y-5">
                            <div class="flex items-start gap-3 rounded-2xl bg-green-50 border border-green-200 px-4 py-3">
                                <ShieldCheck class="w-5 h-5 text-green-600 shrink-0 mt-0.5" />
                                <p class="text-sm text-green-800 font-medium">
                                    Kétlépéses hitelesítés sikeresen bekapcsolva!
                                </p>
                            </div>

                            <div class="space-y-2">
                                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                    Mentési kódok
                                </p>
                                <p class="text-xs text-slate-600">
                                    Mentsd el ezeket a kódokat biztonságos helyre. Ha elveszíted a telefonod,
                                    ezekkel tudsz bejelentkezni.
                                </p>
                                <div class="rounded-2xl bg-accent-400/40 p-4 grid grid-cols-2 gap-2">
                                    <span
                                        v-for="code in recoveryCodesList"
                                        :key="code"
                                        class="font-mono text-xs text-slate-800 bg-white/60 rounded-lg px-2 py-1 text-center"
                                    >
                                        {{ code }}
                                    </span>
                                </div>
                            </div>

                            <button
                                @click="close"
                                class="w-full py-3 rounded-3xl bg-brand-700 text-accent-200 hover:bg-brand-800 transition-all hover:scale-[1.02] font-medium shadow-md"
                            >
                                Kész
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </Transition>
</template>
