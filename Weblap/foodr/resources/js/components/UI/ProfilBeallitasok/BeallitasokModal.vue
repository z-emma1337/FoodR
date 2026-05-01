<script setup>
import { ref, computed, watch } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import ToggleSwitch from 'primevue/toggleswitch';
import {
    X as CloseIcon, Settings, Bell, Shield, Eye, EyeOff,
    Palette, Globe, Trash2, LogOut
} from 'lucide-vue-next'
import KetFaktorosBeallitasModal from '@/components/UI/2faModal.vue'

defineProps({ open: { type: Boolean, required: true } })
const emit = defineEmits(['close'])

const page = usePage()
const user = computed(() => page.props.auth?.user)
const twoFactorEnabled = computed(() => user.value?.two_factor_enabled ?? false)

const twoFactorModalOpen = ref(false)
const disabling2FA = ref(false)
const disable2FAConfirm = ref(false)
const twoFactorDisplay = ref(false)
watch(twoFactorEnabled, (val) => { twoFactorDisplay.value = val }, { immediate: true })

function csrfToken() {
    const match = document.cookie.match(/XSRF-TOKEN=([^;]+)/)
    return match ? decodeURIComponent(match[1]) : ''
}

function onTwoFactorToggle() {
    if (twoFactorEnabled.value) {
        if (!disable2FAConfirm.value) {
            disable2FAConfirm.value = true
            return
        }
        disableTwoFactor()
    } else {
        twoFactorDisplay.value = true
        twoFactorModalOpen.value = true
    }
}

function on2FAModalClose() {
    twoFactorModalOpen.value = false
    twoFactorDisplay.value = twoFactorEnabled.value
}

function cancelDisable() {
    disable2FAConfirm.value = false
}

async function disableTwoFactor() {
    disabling2FA.value = true
    disable2FAConfirm.value = false
    try {
        await fetch('/user/two-factor-authentication', {
            method: 'DELETE',
            headers: {
                Accept: 'application/json',
                'X-XSRF-TOKEN': csrfToken(),
            },
        })
        router.reload({ only: ['auth'] })
    } finally {
        disabling2FA.value = false
    }
}

function onTwoFactorEnabled() {
    router.reload({ only: ['auth'] })
}

const checked = ref(false)

const confirmMode = ref(false)
const deleting = ref(false)

function handleDeleteClick() {
    if (deleting.value) return
    if (!confirmMode.value) {
        confirmMode.value = true
        return
    }
    deleting.value = true
    router.delete('/fiok-torles', {
        onFinish: () => {
            deleting.value = false
            confirmMode.value = false
        },
    })
}

function handleDeleteMouseLeave() {
    if (!deleting.value) {
        confirmMode.value = false
    }
}
</script>

<template>
    <Transition name="modal-fade">
        <div v-if="open" class="fixed inset-0 z-[60] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4"
            @click.self="emit('close')">
            <div class="relative w-full max-w-lg rounded-3xl shadow-2xl border-accent-600 border-6 overflow-hidden foodr-scrollbar">
                <div class="bg-gradient-to-br from-accent-300 via-accent-200 to-accent-300 flex flex-col max-h-[85vh]">

                    <div class="flex items-center justify-between px-8 pt-8 pb-4 shrink-0">
                        <h2 class="text-2xl font-bold text-slate-900">Beállítások</h2>
                        <button @click="emit('close')"
                            class="w-9 h-9 rounded-full flex items-center justify-center hover:bg-accent-500/30 transition-all duration-200">
                            <CloseIcon class="w-6 h-6 text-brand-600" :stroke-width="3" />
                        </button>
                    </div>

                    <div class="overflow-y-auto px-8 pb-8 space-y-6">

                        <div class="space-y-3">
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider px-1">Értesítések
                            </p>

                            <div
                                class="rounded-2xl bg-gradient-to-br from-accent-400/40 to-accent-500/40 divide-y divide-accent-400/30 shadow-sm overflow-hidden">

                                <div class="flex items-center justify-between p-4 opacity-50 pointer-events-none">
                                    <div class="flex items-center gap-3">
                                        <Bell class="w-5 h-5 text-brand-600 shrink-0" />
                                        <div>
                                            <p class="text-sm font-semibold text-slate-900">Recept interakciók</p>
                                            <p class="text-xs text-slate-500">Értesítés, ha felhasználó interaktált az egyik recepteddel</p>
                                        </div>
                                    </div>
                                    <ToggleSwitch v-model="checked" :disabled="true" />
                                </div>

                                <div class="flex items-center justify-between p-4 opacity-50 pointer-events-none">
                                    <div class="flex items-center gap-3">
                                        <Globe class="w-5 h-5 text-brand-600 shrink-0" />
                                        <div>
                                            <p class="text-sm font-semibold text-slate-900">Heti ajánlások</p>
                                            <p class="text-xs text-slate-500">Személyre szabott receptajánlások minden héten</p>
                                        </div>
                                    </div>
                                    <ToggleSwitch v-model="checked" :disabled="true" />
                                </div>

                            </div>
                        </div>

                        <div class="space-y-3">
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider px-1">Adatvédelem
                            </p>

                            <div
                                class="rounded-2xl bg-gradient-to-br from-accent-400/40 to-accent-500/40 divide-y divide-accent-400/30 shadow-sm overflow-hidden">

                                <div class="flex items-center justify-between p-4 opacity-50 pointer-events-none">
                                    <div class="flex items-center gap-3">
                                        <Shield class="w-5 h-5 text-brand-600 shrink-0" />
                                        <div>
                                            <p class="text-sm font-semibold text-slate-900">Privát profil</p>
                                            <p class="text-xs text-slate-500">Csak te látod az adataidat</p>
                                        </div>
                                    </div>
                                    <ToggleSwitch v-model="checked" :disabled="true" />
                                </div>

                                <div class="flex items-center justify-between p-4">
                                    <div class="flex items-center gap-3">
                                        <Shield class="w-5 h-5 text-brand-600 shrink-0" />
                                        <div>
                                            <p class="text-sm font-semibold text-slate-900">Kétlépéses hitelesítés</p>
                                            <p class="text-xs text-slate-500">
                                                <template v-if="twoFactorEnabled">Bekapcsolva · Google Authenticator</template>
                                                <template v-else>Védd fiókodat Google Authenticatorral</template>
                                            </p>
                                        </div>
                                    </div>
                                    <ToggleSwitch :modelValue="twoFactorDisplay" @update:modelValue="onTwoFactorToggle" :disabled="disabling2FA" />
                                </div>
                                <Transition name="modal-fade">
                                    <div v-if="disable2FAConfirm" class="px-4 pb-3">
                                        <div class="rounded-xl bg-red-50 border border-red-200 px-3 py-2 flex items-center justify-between gap-2">
                                            <p class="text-xs text-red-700 font-medium">Biztosan kikapcsolod?</p>
                                            <div class="flex gap-2">
                                                <button
                                                    @click="cancelDisable"
                                                    class="text-xs font-semibold text-slate-500 hover:text-slate-700"
                                                >
                                                    Mégse
                                                </button>
                                                <button
                                                    @click="disableTwoFactor"
                                                    :disabled="disabling2FA"
                                                    class="text-xs font-semibold text-red-600 hover:text-red-800 disabled:opacity-60"
                                                >
                                                    Kikapcsolás
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </Transition>

                            </div>
                        </div>

                        <div class="space-y-3">
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider px-1">Megjelenés</p>
                            <div
                                class="rounded-2xl bg-gradient-to-br from-accent-400/40 to-accent-500/40 p-4 flex items-center gap-3 shadow-sm">
                                <Palette class="w-5 h-5 text-brand-600 shrink-0" />
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-slate-900">Téma</p>
                                    <p class="text-xs text-slate-500">Sötét / világos mód</p>
                                </div>
                                <span
                                    class="text-xs font-medium bg-brand-700/10 text-brand-700 px-3 py-1 rounded-full border border-brand-700/20">
                                    Hamarosan
                                </span>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <p class="text-xs font-semibold text-red-500 uppercase tracking-wider px-1">Veszélyes zóna
                            </p>
                            <div
                                class="rounded-2xl bg-red-50 border border-red-200 divide-y divide-red-100 shadow-sm overflow-hidden">
                                <button
                                    @click="handleDeleteClick"
                                    @mouseleave="handleDeleteMouseLeave"
                                    :disabled="deleting"
                                    class="w-full flex items-center gap-3 p-4 text-left transition-colors disabled:opacity-60 disabled:cursor-not-allowed"
                                    :class="confirmMode ? 'bg-red-100' : 'hover:bg-red-100'">
                                    <Trash2 class="w-5 h-5 shrink-0 transition-colors duration-200"
                                        :class="confirmMode ? 'text-red-700' : 'text-red-500'" />
                                    <div>
                                        <p class="text-sm font-semibold transition-colors duration-200"
                                            :class="confirmMode ? 'text-red-800' : 'text-red-700'">
                                            <template v-if="deleting">Törlés folyamatban...</template>
                                            <template v-else-if="confirmMode">Biztosan törölni szeretnéd?</template>
                                            <template v-else>Fiók törlése</template>
                                        </p>
                                        <p class="text-xs transition-colors duration-200"
                                            :class="confirmMode && !deleting ? 'text-red-600 font-medium' : 'text-red-400'">
                                            <template v-if="confirmMode && !deleting">Kattints még egyszer a végleges törléshez</template>
                                            <template v-else>Ez a művelet visszavonhatatlan</template>
                                        </p>
                                    </div>
                                </button>
                            </div>
                        </div>

                        <div class="rounded-2xl bg-brand-700/10 border border-brand-700/20 p-4">
                            <p class="text-xs text-slate-600">A beállítások mentése és a további opciók hamarosan
                                elérhetők lesznek.</p>
                        </div>

                        <button @click="emit('close')" class="w-full py-3 rounded-3xl bg-brand-700 text-accent-200
                     hover:bg-brand-800 transition-all hover:scale-[1.02]
                     font-medium shadow-md">
                            Bezárás
                        </button>

                    </div>
                </div>
            </div>
        </div>
    </Transition>

    <KetFaktorosBeallitasModal
        :open="twoFactorModalOpen"
        @close="on2FAModalClose"
        @enabled="onTwoFactorEnabled"
    />
</template>
