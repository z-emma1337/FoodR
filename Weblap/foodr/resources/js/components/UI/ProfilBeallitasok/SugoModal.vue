<script setup>
import { ref } from 'vue'
import { X, HelpCircle, ChevronDown, ChevronUp, Heart, Search, User, Mail } from 'lucide-vue-next'

defineProps({ open: { type: Boolean, required: true } })
const emit = defineEmits(['close'])

const openFaq = ref(null)

const faqs = [
    {
        q: 'Hogyan működik a SwipeR?',
        a: 'A SwipeR oldalon receptkártyákat látsz. Húzd jobbra a kártyát, ha tetszik a recept (like), balra ha nem (dislike). A kedvenceid a FavoR oldalon gyűlnek össze.'
    },
    {
        q: 'Kell fiók a használathoz?',
        a: 'A receptek böngészéséhez nincs szükség fiókra, de a swipe-oláshoz és a kedvencek mentéséhez be kell jelentkezned.'
    },
    {
        q: 'Hol találom a kedvenc receptjeimet?',
        a: 'A bal oldali menüben a FavoR gombra kattintva éred el az összes jobbra húzott (likeolt) receptedet.'
    },
    {
        q: 'Hogyan tudom módosítani a jelszavamat?',
        a: 'A Beállítások menüben lesz lehetőség a jelszó módosítására. Ez a funkció hamarosan elérhető.'
    },
    {
        q: 'Milyen allergiás információkat tartalmaz az app?',
        a: 'Minden receptnél feltüntetjük a főbb allergéneket, valamint jelöljük, ha egy recept vegetáriánus vagy vegán.'
    },
    {
        q: 'Hogyan tudok kijelentkezni?',
        a: 'A jobb oldali panelen, a fiókadataid alatt találod a Kijelentkezés gombot.'
    },
]

const toggleFaq = (i) => {
    openFaq.value = openFaq.value === i ? null : i
}
</script>

<template>
    <Transition name="modal-fade">
        <div v-if="open" class="fixed inset-0 z-[60] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4"
            @click.self="emit('close')">
            <div class="relative w-full max-w-lg rounded-3xl shadow-2xl border-accent-600 border-6 overflow-hidden foodr-scrollbar">
                <div class="bg-gradient-to-br from-accent-300 via-accent-200 to-accent-300 flex flex-col max-h-[85vh]">

                    <div class="flex items-center justify-between px-8 pt-8 pb-4 shrink-0">
                        <h2 class="text-2xl font-bold text-slate-900">Súgó</h2>
                        <button @click="emit('close')"
                            class="w-9 h-9 rounded-full flex items-center justify-center hover:bg-accent-500/30 transition-all duration-200">
                            <X  class="w-6 h-6 text-brand-600" :stroke-width="3" />
                        </button>
                    </div>

                    <div class="overflow-y-auto px-8 pb-8 space-y-6">

                        <div
                            class="rounded-2xl bg-gradient-to-br from-accent-400/40 to-accent-500/40 p-5 flex gap-4 items-start shadow-sm">
                            <div
                                class="w-10 h-10 rounded-full bg-accent-500/30 flex items-center justify-center shrink-0 mt-0.5">
                                <HelpCircle class="w-5 h-5 text-brand-600" />
                            </div>
                            <div>
                                <p class="font-semibold text-slate-900 text-sm">Üdvözlünk a FoodR súgójában!</p>
                                <p class="text-xs text-slate-600 mt-1">Ha kérdésed van az alkalmazással kapcsolatban,
                                    itt megtalálod a válaszokat. Ha valami hiányzik, írj nekünk!</p>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider px-1">Gyors útmutató
                            </p>
                            <div class="grid grid-cols-2 gap-3">
                                <div
                                    class="rounded-2xl bg-gradient-to-br from-accent-400/40 to-accent-500/40 p-4 flex flex-col items-center gap-2 text-center shadow-sm">
                                    <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                                        <Heart class="w-5 h-5 text-green-600" />
                                    </div>
                                    <p class="text-sm font-semibold text-slate-900">Jobbra húzás</p>
                                    <p class="text-xs text-slate-500">Like – elmenti a kedvencekhez</p>
                                </div>
                                <div
                                    class="rounded-2xl bg-gradient-to-br from-accent-400/40 to-accent-500/40 p-4 flex flex-col items-center gap-2 text-center shadow-sm">
                                    <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                                        <X class="w-5 h-5 text-red-500" v-if="false" />
                                        <span class="text-red-500 font-bold text-lg">✕</span>
                                    </div>
                                    <p class="text-sm font-semibold text-slate-900">Balra húzás</p>
                                    <p class="text-xs text-slate-500">Dislike – következő recept</p>
                                </div>
                                <div
                                    class="rounded-2xl bg-gradient-to-br from-accent-400/40 to-accent-500/40 p-4 flex flex-col items-center gap-2 text-center shadow-sm">
                                    <div
                                        class="w-10 h-10 rounded-full bg-accent-500/30 flex items-center justify-center">
                                        <Heart class="w-5 h-5 text-brand-600" />
                                    </div>
                                    <p class="text-sm font-semibold text-slate-900">FavoR</p>
                                    <p class="text-xs text-slate-500">Kedvenc receptjeid listája</p>
                                </div>
                                <div
                                    class="rounded-2xl bg-gradient-to-br from-accent-400/40 to-accent-500/40 p-4 flex flex-col items-center gap-2 text-center shadow-sm">
                                    <div
                                        class="w-10 h-10 rounded-full bg-accent-500/30 flex items-center justify-center">
                                        <Search class="w-5 h-5 text-brand-600" />
                                    </div>
                                    <p class="text-sm font-semibold text-slate-900">FeedR</p>
                                    <p class="text-xs text-slate-500">Receptek böngészése</p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider px-1">Gyakori
                                kérdések</p>
                            <div class="rounded-2xl overflow-hidden shadow-sm divide-y divide-accent-400/30">
                                <div v-for="(faq, i) in faqs" :key="i"
                                    class="bg-gradient-to-br from-accent-400/40 to-accent-500/40">
                                    <button @click="toggleFaq(i)"
                                        class="w-full flex items-center justify-between gap-3 p-4 text-left">
                                        <span class="text-sm font-semibold text-slate-900">{{ faq.q }}</span>
                                        <ChevronDown v-if="openFaq !== i" class="w-4 h-4 text-slate-500 shrink-0" />
                                        <ChevronUp v-else class="w-4 h-4 text-brand-600 shrink-0" />
                                    </button>
                                    <Transition name="faq-expand">
                                        <div v-if="openFaq === i" class="px-4 pb-4">
                                            <p class="text-sm text-slate-600">{{ faq.a }}</p>
                                        </div>
                                    </Transition>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider px-1">Kapcsolat</p>
                            <div
                                class="rounded-2xl bg-gradient-to-br from-accent-400/40 to-accent-500/40 p-4 flex items-center gap-3 shadow-sm">
                                <div
                                    class="w-10 h-10 rounded-full bg-accent-500/30 flex items-center justify-center shrink-0">
                                    <Mail class="w-5 h-5 text-brand-600" />
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">Írj nekünk</p>
                                    <p class="text-xs text-slate-500">info@foodr.hu</p>
                                </div>
                            </div>
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