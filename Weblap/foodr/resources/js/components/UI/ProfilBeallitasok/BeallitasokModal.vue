<script setup>
import { ref, computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import ToggleSwitch from 'primevue/toggleswitch';
import {
    X as CloseIcon, Settings, Bell, Shield, Eye, EyeOff,
    Palette, Globe, Trash2, LogOut
} from 'lucide-vue-next'

defineProps({ open: { type: Boolean, required: true } })
const emit = defineEmits(['close'])

const page = usePage()
const user = computed(() => page.props.auth?.user)

</script>

<template>
    <Transition name="modal-fade">
        <div v-if="open" class="fixed inset-0 z-[60] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4"
            @click.self="emit('close')">
            <div class="relative w-full max-w-lg rounded-3xl shadow-2xl border-accent-600 border-6 overflow-hidden">
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

                                <div class="flex items-center justify-between p-4">
                                    <div class="flex items-center gap-3">
                                        <Bell class="w-5 h-5 text-brand-600 shrink-0" />
                                        <div>
                                            <p class="text-sm font-semibold text-slate-900">valamit talaljunk ki ide</p>
                                            <p class="text-xs text-slate-500">es ide is, el van baszva a toggle cucc, meg a szine</p>
                                        </div>
                                    </div>

                                    <!-- itt van a gomb, nem tudom meg hogyan kell modify olni a szinet rendesen-->
                                    <ToggleSwitch v-model="checked" />
                                </div>

                                <div class="flex items-center justify-between p-4">
                                    <div class="flex items-center gap-3">
                                        <Globe class="w-5 h-5 text-brand-600 shrink-0" />
                                        <div>
                                            <p class="text-sm font-semibold text-slate-900">Same itt is kene valami</p>
                                            <p class="text-xs text-slate-500">valami ertesites cucc</p>
                                        </div>
                                    </div>
                                    <ToggleSwitch v-model="checked" />
                                </div>

                            </div>
                        </div>

                        <div class="space-y-3">
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider px-1">Adatvédelem
                            </p>

                            <div
                                class="rounded-2xl bg-gradient-to-br from-accent-400/40 to-accent-500/40 divide-y divide-accent-400/30 shadow-sm overflow-hidden">

                                <div class="flex items-center justify-between p-4">
                                    <div class="flex items-center gap-3">
                                        <Shield class="w-5 h-5 text-brand-600 shrink-0" />
                                        <div>
                                            <p class="text-sm font-semibold text-slate-900">Privát profil</p>
                                            <p class="text-xs text-slate-500">Csak te látod az adataidat</p>
                                        </div>
                                    </div>
                                    <ToggleSwitch v-model="checked" />
                                </div>

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
                                    class="w-full flex items-center gap-3 p-4 text-left hover:bg-red-100 transition-colors">
                                    <Trash2 class="w-5 h-5 text-red-500 shrink-0" />
                                    <div>
                                        <p class="text-sm font-semibold text-red-700">Fiók törlése</p>
                                        <p class="text-xs text-red-400">Ez a művelet visszavonhatatlan</p>
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
</template>