<script setup>
import { computed, onMounted, ref, watch } from "vue";
import { usePage, router } from "@inertiajs/vue3";
import axios from "axios";
import {
    X as CloseIcon,
    User,
    Mail,
    Heart,
    CalendarDays,
    ShieldCheck,
    ShieldAlert,
    Pen,
    Upload,
    ReceiptPoundSterling
} from "lucide-vue-next";

defineProps({ open: { type: Boolean, required: true } });
const emit = defineEmits(["close"]);
const allergenek = ref([]);
const felhasznaloallergenek = ref([]);
const page = usePage();
const user = computed(() => page.props.auth?.user);
const likedCount = computed(() => page.props.likedCount ?? 0);
const isVerified = computed(() => !!user.value?.email_verified_at);
const pfpurl = computed(() => user.value?.profilkepurl);
const ikonpfp = ref(["chef.png", "drumstick.png", "fish.png", "pizza.png", "avatar.png"])
const isDropUpOpen = ref(false)

const toggleDropUp = () => {
    isDropUpOpen.value = !isDropUpOpen.value
}
const closeDropUp = () => {
    isDropUpOpen.value = false
}

const UpdatePfp = async (url) => {
    await axios.post('/felhasznalo', { profilkepurl: `imgs/Profilkepek/${url}` })
    router.reload({ only: ['auth'] })
}

const KepFeltoltes = async (FeltoltottKep) => {
    const formData = new FormData()
    formData.append('profilkepfajl', FeltoltottKep.target.files[0])
    await axios.post('/felhasznalo', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
    })
    router.reload({ only: ['auth'] })
}

const selectedAllergens = ref([])

const loadAllergens = async () => {
    allergenek.value = await (await fetch('/allergenek')).json();
    felhasznaloallergenek.value = await (await fetch("/allergenek/felhasznalo")).json();
    selectedAllergens.value = felhasznaloallergenek.value.map(a => a)
}

onMounted(() => {
    loadAllergens();
})

const mentesAllergenek = () => {
    router.post('/allergenek/felhasznalo', {
        allergen_id: selectedAllergens.value
    }, {
        onSuccess: () => {
            window.dispatchEvent(new CustomEvent('allergenek-frissitve'))
        }
    })
}
</script>

<template>
    <Transition name="modal-fade">
        <div v-if="open" class="fixed inset-0 z-[60] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4"
            @click.self="() => { emit('close'); closeDropUp(); }">
            <div
                class="relative w-full max-w-lg rounded-3xl shadow-2xl border-accent-600 border-6 overflow-hidden foodr-scrollbar">
                <div class="bg-gradient-to-br from-accent-300 via-accent-200 to-accent-300 flex flex-col max-h-[85vh]">
                    <div class="flex items-center justify-between px-8 pt-8 pb-4 shrink-0">
                        <h2 class="text-2xl font-bold text-slate-900">Profilom</h2>
                        <button @click="() => { closeDropUp(); emit('close'); }"
                            class="w-9 h-9 rounded-full flex items-center justify-center hover:bg-accent-500/30 transition-all duration-200">
                            <CloseIcon class="w-6 h-6 text-brand-600" :stroke-width="3" />
                        </button>
                    </div>

                    <div class="overflow-y-auto px-8 pb-8 space-y-6" @click="closeDropUp()">
                        <div class="flex flex-col items-center gap-4 py-4">
                            <Transition name="dropdown" v-show="isDropUpOpen">
                                <div @click.stop
                                    class="z-10 absolute bg-accent-300 cursor-pointer rounded-3xl border-2 border-accent-400 top-10 shadow-lg p-2 flex flex-wrap">
                                    <div v-for="url in ikonpfp" :key="url"
                                        class="w-12 h-12 rounded-full overflow-hidden shadow-md m-1">
                                        <button @click="UpdatePfp(url)">
                                            <img :src="`/imgs/Profilkepek/${url}`" alt="Profilkép"
                                                class="w-full h-full object-cover scale-[1.2]" />
                                        </button>
                                    </div>

                                    <div
                                        class="w-12 h-12 rounded-full overflow-hidden flex items-center justify-center shadow-md m-1 bg-accent-400/60">
                                        <label for="dropzone-file"
                                            class="cursor-pointer flex items-center justify-center w-full h-full">
                                            <Upload class="w-6 h-6 text-brand-600" :stroke-width="3" />
                                        </label>
                                        <input id="dropzone-file" type="file" accept="image/jpeg,image/png,image/webp"
                                            @change="KepFeltoltes" class="hidden" />
                                    </div>
                                </div>

                            </Transition>

                            <div @click.stop="toggleDropUp"
                                class="relative w-24 h-24 cursor-pointer rounded-full overflow-hidden shadow-md group">
                                <img :src="pfpurl" alt="Profilkép" class="w-full h-full object-cover scale-[1.2]" />
                                <div
                                    class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex items-center justify-center">
                                    <Pen class="w-10 h-10 text-white" />
                                </div>
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
                            <div
                                class="rounded-2xl bg-gradient-to-br from-accent-400/40 to-accent-500/40 p-4 text-center shadow-sm">
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

                        <div class="space-y-3">
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider px-1">
                                Allergének
                            </p>

                            <div
                                class="rounded-2xl bg-gradient-to-br from-accent-400/40 to-accent-500/40 p-4 flex items-center gap-4 shadow-sm">

                                <div class="min-w-0">
                                    <p class="text-xs text-slate-500 font-medium">
                                        Mire vagy allergiás?
                                    </p>

                                    <label v-for="allergen in allergenek" :key="allergen.id"
                                        class="inline-flex items-center w-full p-2 hover:bg-neutral-tertiary-medium text-slate-900 font-semibold hover:text-heading rounded">
                                        <input type="checkbox" v-model="selectedAllergens" :value="allergen.id"
                                            @change="mentesAllergenek" class="mr-2 accent-brand-600 w-5 h-5" />
                                        {{ allergen.nev }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <button @click="() => { closeDropUp(); emit('close'); }"
                            class="w-full py-3 rounded-3xl bg-brand-700 text-accent-200 hover:bg-brand-800 transition-all hover:scale-[1.02] font-medium shadow-md">
                            Bezárás
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </Transition>
</template>