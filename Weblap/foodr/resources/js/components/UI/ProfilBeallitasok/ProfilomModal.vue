<script setup>
import { computed } from "vue";
import { usePage } from "@inertiajs/vue3";
import {
    X as CloseIcon,
    User,
    Mail,
    Heart,
    CalendarDays,
    ShieldCheck,
    ShieldAlert,
} from "lucide-vue-next";

defineProps({ open: { type: Boolean, required: true } });
const emit = defineEmits(["close"]);

const page = usePage();
const user = computed(() => page.props.auth?.user);
const likedCount = computed(() => page.props.likedCount ?? 0);
const isVerified = computed(() => !!user.value?.email_verified_at);
</script>

<template>
    <Transition name="modal-fade">
        <div v-if="open" class="fixed inset-0 z-[60] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4"
            @click.self="emit('close')">
            <div
                class="relative w-full max-w-lg rounded-3xl shadow-2xl border-accent-600 border-6 overflow-hidden foodr-scrollbar">
                <div class="bg-gradient-to-br from-accent-300 via-accent-200 to-accent-300 flex flex-col max-h-[85vh]">
                    <div class="flex items-center justify-between px-8 pt-8 pb-4 shrink-0">
                        <h2 class="text-2xl font-bold text-slate-900">Profilom</h2>
                        <button @click="emit('close')"
                            class="w-9 h-9 rounded-full flex items-center justify-center hover:bg-accent-500/30 transition-all duration-200">
                            <CloseIcon class="w-6 h-6 text-brand-600" :stroke-width="3" />
                        </button>
                    </div>

                    <div class="overflow-y-auto px-8 pb-8 space-y-6">
                        <div class="flex flex-col items-center gap-4 py-4">
                            <div
                                class="w-24 h-24 rounded-full bg-accent-500/30 flex items-center justify-center shadow-lg border-4 border-accent-400/50">
                                <User class="w-12 h-12 text-brand-600" />
                            </div>
                            <div class="text-center">
                                <p class="text-2xl font-bold text-slate-900">
                                    {{ user?.nev }}
                                </p>
                                <p class="text-sm text-slate-600 mt-1">
                                    FoodR felhasználó
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div
                                class="rounded-2xl bg-gradient-to-br from-accent-400/40 to-accent-500/40 p-4 text-center shadow-sm">
                                <Heart class="w-6 h-6 text-brand-600 mx-auto mb-1" />
                                <p class="text-2xl font-bold text-slate-900">
                                    {{ likedCount }}
                                </p>
                                <p class="text-xs text-slate-600 font-medium">
                                    Kedvenc recept
                                </p>
                            </div>
                            <div class="rounded-2xl bg-gradient-to-br from-accent-400/40 to-accent-500/40 p-4 text-center shadow-sm">
                                <ShieldCheck v-if="isVerified" class="w-6 h-6 text-brand-600 mx-auto mb-1" />
                                <ShieldAlert v-else class="w-6 h-6 text-brand-600 mx-auto mb-1" />
                                <p class="text-2xl font-bold text-slate-900">
                                    {{ isVerified ? "Aktív" : "Inaktív" }}
                                </p>
                                <p class="text-xs text-slate-600 font-medium">
                                    Fiók státusz
                                </p>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider px-1">
                                Fiók adatok
                            </p>

                            <div
                                class="rounded-2xl bg-gradient-to-br from-accent-400/40 to-accent-500/40 p-4 flex items-center gap-4 shadow-sm">
                                <div
                                    class="w-10 h-10 rounded-full bg-accent-500/30 flex items-center justify-center shrink-0">
                                    <User class="w-5 h-5 text-brand-600" />
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs text-slate-500 font-medium">
                                        Felhasználónév
                                    </p>
                                    <p class="text-slate-900 font-semibold truncate">
                                        {{ user?.nev }}
                                    </p>
                                </div>
                            </div>

                            <div
                                class="rounded-2xl bg-gradient-to-br from-accent-400/40 to-accent-500/40 p-4 flex items-center gap-4 shadow-sm">
                                <div
                                    class="w-10 h-10 rounded-full bg-accent-500/30 flex items-center justify-center shrink-0">
                                    <Mail class="w-5 h-5 text-brand-600" />
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs text-slate-500 font-medium">
                                        Email cím
                                    </p>
                                    <p class="text-slate-900 font-semibold truncate">
                                        {{ user?.email }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-2xl bg-brand-700/10 border border-brand-700/20 p-5 space-y-2">
                            <p class="text-sm font-semibold text-brand-700">
                                Hamarosan elérhető
                            </p>
                            <p class="text-xs text-slate-600">
                                Profilkép feltöltése, felhasználónév módosítása, és egyéb
                                személyes beállítások hamarosan elérhetők lesznek.
                            </p>
                        </div>

                        <button @click="emit('close')"
                            class="w-full py-3 rounded-3xl bg-brand-700 text-accent-200 hover:bg-brand-800 transition-all hover:scale-[1.02] font-medium shadow-md">
                            Bezárás
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </Transition>
</template>