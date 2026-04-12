<script setup>
import { Clock, Users, ChefHat, X as CloseIcon, Upload, Trash2, Pen } from 'lucide-vue-next'
import { computed, ref, watch, onMounted } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
 
const props = defineProps({
    recipe: Object,
    visible: Boolean,
    open: { type: Boolean, required: true },
    blur: { type: Boolean, default: true }
})
 
const emit = defineEmits(['close'])
const receptNev = ref(props.recipe.nev);
const receptIdo = ref(props.recipe.ido);
const receptAdag = ref(props.recipe.adag);
const receptHozzavalok = ref(props.recipe.hozzavalok.map(h => ({ ...h, adag: Number(h.adag) })));
const receptLeirasok = ref(
    props.recipe.leiras
        .split(/\d+\.\s/)
        .filter(l => l.trim().length > 0)
        .map(l => ({ leiras: l.trim() }))
);
const letrehozhato = ref(false);
const Kep = ref(null);
const kepPreview = ref(props.recipe.kep_url);
const hozzavalok = ref([]);
 
const getOsszesHozzavalo = async () => {
    try {
        const res = await fetch('/osszes-alapanyag')
        const data = await res.json()
        hozzavalok.value = data.map(h => h[0])
    } catch (err) {
        console.error(err)
    }
}
onMounted(() => {
    getOsszesHozzavalo()
})
 
const addHozzavalo = () => {
    if (receptHozzavalok.value.at(-1).nev && receptHozzavalok.value.at(-1).adag) {
        receptHozzavalok.value.push({ nev: '', adag: null })
    }
}
 
const torolHozzavalo = (index) => {
    receptHozzavalok.value.splice(index, 1)
}
 
const addLeiras = () => {
    if (receptLeirasok.value.at(-1).leiras) {
        receptLeirasok.value.push({ leiras: '' })
    }
}
 
const torolLeiras = (index) => {
    receptLeirasok.value.splice(index, 1)
}
 
const Szerkesztes = () => {
    const formData = new FormData()
 
    formData.append('receptNev', receptNev.value)
    formData.append('receptIdo', receptIdo.value)
    formData.append('receptAdag', receptAdag.value)
 
    receptHozzavalok.value.forEach((h, i) => {
        formData.append(`receptHozzavalok[${i}][nev]`, h.nev)
        formData.append(`receptHozzavalok[${i}][adag]`, h.adag)
    })
 
    receptLeirasok.value.forEach((l, i) => {
        formData.append(`receptLeirasok[${i}]`, l.leiras)
    })
 
    if (Kep.value) {
        formData.append('kep', Kep.value)
    } else {
        formData.append('kep_url', props.recipe.kep_url)
    }
 
    router.post(`/receptSzerkesztese/${props.recipe.id}`, formData, {
        forceFormData: true,
        preserveScroll: false,
        onSuccess: () => {
            emit('close')
            window.location.reload()
        },
        onError: (errors) => {
            console.error(errors)
        }
    })
}
 
watch([receptNev, receptIdo, receptAdag, receptHozzavalok, receptLeirasok, Kep], () => {
    const kepOk = kepPreview.value !== null;
    if (!kepOk || receptNev.value.length <= 1 || receptIdo.value <= 0 || receptAdag.value <= 0 ||
        receptHozzavalok.value.some(h => h.nev.length <= 1 || h.adag <= 0) ||
        receptLeirasok.value.some(l => l.leiras.length <= 1)) {
        letrehozhato.value = false;
    } else {
        letrehozhato.value = true;
    }
}, { deep: true, immediate: true })
 
const KepFeltoltes = (FeltoltottKep) => {
    Kep.value = FeltoltottKep.target.files[0]
    kepPreview.value = URL.createObjectURL(Kep.value)
}
 
const activeHozzavaloIndex = ref(null)
const hozzavaloKeres = ref('')
 
const szurtHozzavalok = computed(() => {
    const input = hozzavaloKeres.value.toLowerCase()
    if (!input) return hozzavalok.value.slice(0, 8)
    return hozzavalok.value.filter(h => h.toLowerCase().includes(input)).slice(0, 8)
})
 
</script>
 
<template>
    <Transition name="modal-fade">
        <div v-if="props.open" :class="props.blur ? 'bg-black/60 backdrop-blur-sm' : ''"
            class="fixed inset-0 z-[100] flex items-center justify-center p-4"
            @click.self="emit('close')">
            <div
                class="relative w-full max-w-lg rounded-3xl shadow-2xl border-accent-600 border-6 overflow-hidden foodr-scrollbar">
                <div class="bg-gradient-to-br from-accent-300 via-accent-200 to-accent-300 flex flex-col max-h-[85vh]">
 
                    <div class="flex items-center justify-between px-8 pt-8 pb-4 shrink-0">
                        <h2 class="text-2xl font-bold text-slate-900">
                            Recept szerkesztése
                        </h2>
                        <button @click="emit('close')"
                            class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-200">
                            <CloseIcon class="w-10 h-10 text-brand-600" :stroke-width="3" />
                        </button>
                    </div>
 
                    <div class="overflow-y-auto px-8 pb-8 space-y-1 text-slate-900">
 
                        <label class="block">Recept elnevezése</label>
                        <div class="relative flex items-center w-full mb-5 rounded-base">
                            <input v-model="receptNev" type="text"
                                :class="receptNev.length <= 1 ? 'border-red-500 shadow-red-400 shadow-lg' : 'border-accent-600'"
                                class="h-10 pl-5 bg-accent-400/60 border-3 focus:outline-none focus:ring-2 focus:ring-accent-400 focus:border-transparent transition-all shadow-md w-full rounded-3xl mx-2 py-2.5" />
                        </div>
 
                        <label class="block">Hány perc elkészíteni ezt a receptet?</label>
                        <div class="relative flex items-center w-full rounded-base mb-5">
                            <button type="button" @click="receptIdo = receptIdo - 1"
                                class="text-body w-10 button-brand rounded-full font-bold flex items-center justify-center pb-0.5">
                                -
                            </button>
                            <input type="number" placeholder="40" v-model.number="receptIdo" min="1" max="400"
                                :class="receptIdo <= 0 ? 'border-red-500 shadow-red-400 shadow-md' : 'border-accent-600'"
                                class="h-10 bg-accent-400/60 border-3 focus:outline-none focus:ring-2 focus:ring-accent-400 focus:border-transparent transition-all shadow-md w-15 rounded-3xl mx-2 text-center py-2.5"
                                required />
                            <button type="button" @click="receptIdo++"
                                class="text-body button-brand w-10 rounded-full font-bold flex items-center justify-center pb-0.5">
                                +
                            </button>
                            <label class="pl-2">perc</label>
                        </div>
 
                        <label class="block">Hány adag készül el az ételből?</label>
                        <div class="relative flex items-center w-full mb-5 rounded-base">
                            <button type="button" @click="receptAdag = receptAdag - 1"
                                class="text-body button-brand w-10 rounded-full font-bold flex items-center justify-center pb-0.5">
                                -
                            </button>
                            <input type="number" placeholder="4" v-model.number="receptAdag" min="1" max="100"
                                :class="receptAdag <= 0 ? 'border-red-500 shadow-red-400 shadow-md' : 'border-accent-600'"
                                class="h-10 bg-accent-400/60 border-3 focus:outline-none focus:ring-2 focus:ring-accent-400 focus:border-transparent transition-all shadow-md w-15 rounded-3xl mx-2 text-center py-2.5"
                                required />
                            <button type="button" @click="receptAdag++"
                                class="text-body button-brand w-10 rounded-full font-bold flex items-center justify-center pb-0.5">
                                +
                            </button>
                            <label class="pl-2">adag</label>
                        </div>
 
                        <label class="block">Hozzávalók hozzáadása</label>
                        <div class="relative flex flex-col w-full mb-5 rounded-base">
                            <div v-for="(hozzavalo, index) in receptHozzavalok" :key="index"
                                class="flex items-center mb-3 space-x-2">
 
                                <!-- Dropdown trigger -->
                                <div class="relative w-full">
                                    <button type="button"
                                        @click="activeHozzavaloIndex = activeHozzavaloIndex === index ? null : index"
                                        :class="hozzavalo.nev.length <= 1 ? 'border-red-500' : 'border-accent-600'"
                                        class="h-10 w-full pl-4 pr-8 bg-accent-400/60 border-3 rounded-3xl
                                               text-left text-brand-700 flex items-center justify-between
                                               focus:outline-none shadow-md">
                                        <span :class="!hozzavalo.nev ? 'text-slate-700/50' : 'text-brand-700'">
                                            {{ hozzavalo.nev || 'Válassz hozzávalót...' }}
                                        </span>
                                    </button>
 
                                    <!-- Dropdown panel -->
                                    <div v-if="activeHozzavaloIndex === index" class="absolute z-50 top-full left-0 mt-1 w-full bg-accent-200
                                               border-3 border-accent-600 rounded-2xl shadow-xl overflow-hidden">
                                        <div class="p-2 sticky top-0 bg-accent-200">
                                            <input v-model="hozzavaloKeres" type="text" placeholder="Keresés..." class="w-full px-3 py-1.5 text-sm bg-accent-400/60 border-2 border-accent-600
                                                   rounded-xl text-brand-700
                                                   focus:outline-none focus:ring-2 focus:ring-accent-400" />
                                        </div>
                                        <div class="max-h-40 overflow-y-auto foodr-scrollbar pb-1 px-1">
                                            <button v-for="h in szurtHozzavalok" :key="h" type="button"
                                                @mousedown.prevent="hozzavalo.nev = h; activeHozzavaloIndex = null; hozzavaloKeres = ''"
                                                :class="hozzavalo.nev === h ? 'bg-brand-700 text-accent-200' : 'text-brand-700 hover:bg-brand-700 hover:text-accent-200'"
                                                class="w-full text-left px-4 py-2 text-sm rounded-xl transition-colors">
                                                {{ h }}
                                            </button>
                                            <p v-if="szurtHozzavalok.length === 0"
                                                class="text-center text-sm text-brand-500 py-2">
                                                Nincs ilyen hozzávaló
                                            </p>
                                        </div>
                                    </div>
                                </div>
 
                                <input v-model.number="hozzavalo.adag" required type="number" placeholder="500" value=""
                                    :class="hozzavalo.adag <= 0 ? 'border-red-500 shadow-red-400 shadow-md' : 'border-accent-600'"
                                    class="h-10 bg-accent-400/60 border-3 text-center focus:outline-none focus:ring-2 focus:ring-accent-400 focus:border-transparent transition-all shadow-md w-15 rounded-3xl mx-1 py-2.5" />
                                <label>g</label>
                                <button v-if="index > 0" @click="torolHozzavalo(index)"
                                    class="delete-btn w-10 h-10 p-2 rounded-full bg-brand-700 text-accent-200 shadow-md transition-all duration-200 flex items-center justify-center hover:bg-red-600">
                                    <Trash2 class="trash-icon w-6 h-6" />
                                </button>
                                <button v-else @click="hozzavalo.adag = null, hozzavalo.nev = ''"
                                    class="delete-btn w-10 h-10 p-2 rounded-full bg-brand-700 text-accent-200 shadow-md transition-all duration-200 flex items-center justify-center hover:bg-red-600">
                                    <Trash2 class="trash-icon w-6 h-6" />
                                </button>
                            </div>
                            <div class="w-full">
                                <button @click="addHozzavalo()"
                                    class="w-full p-2 rounded-full font-bold shadow-md
                                           flex items-center justify-center button-brand">
                                    + Hozzávaló
                                </button>
                            </div>
                        </div>
 
                        <label class="block">Leírás hozzáadása lépésenként</label>
                        <div class="relative flex flex-col w-full mb-5 rounded-base items-center">
                            <div v-for="(leiras, index) in receptLeirasok" :key="index"
                                class="flex items-center mb-3 space-x-2 w-full">
                                <p class="text-lg font-bold text-brand-600">{{ index + 1 }}.</p>
                                <input v-model="leiras.leiras" type="text" required=""
                                    placeholder="Főzze meg a tésztát."
                                    :class="leiras.leiras.length <= 1 ? 'border-red-500 shadow-red-400 shadow-md' : 'border-accent-600'"
                                    class="h-10 pl-5 bg-accent-400/60 border-3 focus:outline-none focus:ring-2 focus:ring-accent-400 focus:border-transparent transition-all shadow-md w-full rounded-3xl mx-1 py-2.5" />
                                <button v-if="index > 0" @click="torolLeiras(index)"
                                    class="delete-btn w-10 h-10 p-2 rounded-full bg-brand-700 text-accent-200 shadow-md transition-all duration-200 flex items-center justify-center hover:bg-red-600">
                                    <Trash2 class="trash-icon w-6 h-6" />
                                </button>
                                <button v-else @click="leiras.leiras = ''"
                                    class="delete-btn w-10 h-10 p-2 rounded-full bg-brand-700 text-accent-200 shadow-md transition-all duration-200 flex items-center justify-center hover:bg-red-600">
                                    <Trash2 class="trash-icon w-6 h-6" />
                                </button>
                            </div>
                            <div class="w-full">
                                <button @click="addLeiras()"
                                    class="w-full p-2 rounded-full font-bold shadow-md
                                           flex items-center justify-center button-brand">
                                    + Lépés
                                </button>
                            </div>
                        </div>
 
                        <label class="block">Fénykép feltöltése</label>
                        <div class="relative flex flex-col w-full mb-5 rounded-base items-center">
                            <div class="flex items-center justify-center w-full">
                                <label for="dropzone-file"
                                    :style="kepPreview ? { backgroundImage: `url(${kepPreview})` } : {}"
                                    :class="kepPreview == null ? 'border-red-500 shadow-red-400 shadow-lg' : 'border-accent-600'"
                                    class="group relative flex flex-col items-center justify-center w-full h-64 border-3 rounded-3xl bg-accent-400/60 shadow-md bg-cover bg-center bg-no-repeat overflow-hidden">
 
                                    <div v-if="kepPreview == null"
                                        class="flex flex-col items-center justify-center text-body pt-5 pb-6">
                                        <Upload class="w-10 h-10" />
                                        <p class="mb-2 text-md">
                                            <span class="font-semibold">Kattints a feltöltéshez</span>
                                            vagy húzd ide a fényképet.
                                        </p>
                                    </div>
 
                                    <div v-if="kepPreview"
                                        class="absolute inset-0 flex items-center justify-center bg-black/50 opacity-0 group-hover:opacity-100 transition-all duration-200">
                                        <Pen class="w-15 h-15 text-accent-300" />
                                    </div>
 
                                    <input id="dropzone-file" type="file" accept="image/jpeg,image/png,image/webp"
                                        @change="KepFeltoltes" class="hidden" />
                                </label>
                            </div>
                        </div>
                    </div>
 
                    <div class="flex items-center justify-between px-8 pt-8 pb-4 shrink-0">
                        <button @click="Szerkesztes()" :disabled="!letrehozhato"
                            class="w-full p-2 py-4 rounded-full font-bold text-lg shadow-md
                                   flex items-center justify-center gap-2 button-brand
                                   disabled:opacity-50 disabled:transition-none disabled:hover:scale-100 disabled:hover:bg-brand-700">
                            Mentés
                        </button>
                    </div>
 
                </div>
            </div>
        </div>
    </Transition>
</template>