<script setup>
import { Clock, Users, ChefHat, X as CloseIcon, Upload, Trash2, Pen } from 'lucide-vue-next'
import { computed, ref, watch, onMounted } from 'vue'
import { router, usePage } from '@inertiajs/vue3'

const props = defineProps({
    visible: Boolean,
    open: { type: Boolean, required: true }
})

const emit = defineEmits(['close'])
const receptNev = ref('');
const receptIdo = ref();
const receptAdag = ref();
const receptHozzavalok = ref([{ nev: '', adag: null }]);
const receptLeirasok = ref([{ leiras: '' }])
const letrehozhato = ref(false);
const Kep = ref(null);
const kepPreview = ref(null);



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

const Letrehozas = () => {

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

    formData.append('kep', Kep.value)

    router.post('/receptLetrehozas', formData, {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            emit('close')
        },
        onError: (errors) => {
            console.error(errors)
        }
    })
}

watch([receptNev, receptIdo, receptAdag, receptHozzavalok, receptLeirasok], () => {
    if (Kep == null || receptNev.value.length <= 1 || receptIdo.value <= 0 || receptAdag.value <= 0 || receptHozzavalok.value.some(h => h.nev.length <= 1 || h.adag <= 0) || receptLeirasok.value.some(l => l.leiras.length <= 1)) {
        letrehozhato.value = false;
    }
    else {
        letrehozhato.value = true
    }
},
    { deep: true }
)

const KepFeltoltes = (FeltoltottKep) => {
    Kep.value = FeltoltottKep.target.files[0]
    kepPreview.value = URL.createObjectURL(Kep.value)
}

</script>

<template>
    <Transition name="modal-fade">
        <div v-if="props.open"
            class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4"
            @click.self="emit('close')">
            <div
                class="relative w-full max-w-lg rounded-3xl shadow-2xl border-accent-600 border-6 overflow-hidden foodr-scrollbar">
                <div class="bg-gradient-to-br from-accent-300 via-accent-200 to-accent-300 flex flex-col max-h-[85vh]">

                    <div class="flex items-center justify-between px-8 pt-8 pb-4 shrink-0">
                        <h2 class="text-2xl font-bold text-slate-900">
                            Recept létrehozása
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
                                class="text-body bg-brand-600 w-10 text-accent-200 rounded-full font-bold flex items-center justify-center pb-0.5">
                                -
                            </button>
                            <input type="number" placeholder="40" v-model.number="receptIdo" min="1" max="400"
                                :class="receptIdo <= 0 ? 'border-red-500 shadow-red-400 shadow-md' : 'border-accent-600'"
                                class="h-10 bg-accent-400/60 border-3 focus:outline-none focus:ring-2 focus:ring-accent-400 focus:border-transparent transition-all shadow-md w-15 rounded-3xl mx-2 text-center py-2.5"
                                required />
                            <button type="button" @click="receptIdo++"
                                class="text-body bg-brand-600 w-10 text-accent-200 rounded-full font-bold flex items-center justify-center pb-0.5">
                                +
                            </button>
                            <label class="pl-2">perc</label>
                        </div>

                        <label class="block">Hány adag készül el az ételből?</label>
                        <div class="relative flex items-center w-full mb-5 rounded-base">
                            <button type="button" @click="receptAdag = receptAdag - 1"
                                class="text-body bg-brand-600 w-10 text-accent-200 rounded-full font-bold flex items-center justify-center pb-0.5">
                                -
                            </button>
                            <input type="number" placeholder="4" v-model.number="receptAdag" min="1" max="100"
                                :class="receptAdag <= 0 ? 'border-red-500 shadow-red-400 shadow-md' : 'border-accent-600'"
                                class="h-10 bg-accent-400/60 border-3 focus:outline-none focus:ring-2 focus:ring-accent-400 focus:border-transparent transition-all shadow-md w-15 rounded-3xl mx-2 text-center py-2.5"
                                required />
                            <button type="button" @click="receptAdag++"
                                class="text-body bg-brand-600 w-10 text-accent-200 rounded-full font-bold flex items-center justify-center pb-0.5">
                                +
                            </button>
                            <label class="pl-2">adag</label>
                        </div>


                        <label class="block">Hozzávalók hozzáadása</label>
                        <div class="relative flex flex-col w-full mb-5 rounded-base items-center">
                            <div v-for="(hozzavalo, index) in receptHozzavalok" :key="index"
                                class="flex items-center mb-3 space-x-2">
                                <input v-model="hozzavalo.nev" type="text" required placeholder="Spagetti tészta"
                                    :class="hozzavalo.nev.length <= 1 ? 'border-red-500 shadow-red-400 shadow-md' : 'border-accent-600'"
                                    class="h-10 pl-5 bg-accent-400/60 border-3 focus:outline-none focus:ring-2 focus:ring-accent-400 focus:border-transparent transition-all shadow-md w-80 rounded-3xl mx-1 py-2.5" />
                                <input v-model.number="hozzavalo.adag" required type="number" placeholder="500" value=""
                                    :class="hozzavalo.adag <= 0 ? 'border-red-500 shadow-red-400 shadow-md' : 'border-accent-600'"
                                    class="h-10 bg-accent-400/60 border-3 text-center focus:outline-none focus:ring-2 focus:ring-accent-400 focus:border-transparent transition-all shadow-md w-15 rounded-3xl mx-1 py-2.5" />
                                <label>g</label>
                                <button v-if="index > 0" @click="torolHozzavalo(index)"
                                    class="delete-btn w-10 h-10 p-2 rounded-full bg-brand-700 text-accent-200 shadow-md transition-all duration-200 flex items-center justify-center hover:bg-red-600">
                                    <Trash2 class="trash-icon w-5 h-5" />
                                </button>
                                <button v-else @click="hozzavalo.adag = null, hozzavalo.nev = ''"
                                    class="delete-btn w-10 h-10 p-2 rounded-full bg-brand-700 text-accent-200 shadow-md transition-all duration-200 flex items-center justify-center hover:bg-red-600">
                                    <Trash2 class="trash-icon w-5 h-5" />
                                </button>
                            </div>
                            <div class="w-full">
                                <button @click="addHozzavalo()"
                                    class="w-full p-2 rounded-full bg-brand-700 hover:bg-brand-800 text-accent-200 font-bold shadow-md 
                            transition-all hover:scale-[1.1] flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed">
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
                                    <Trash2 class="trash-icon w-5 h-5" />
                                </button>
                                <button v-else @click="leiras.leiras = ''"
                                    class="delete-btn w-10 h-10 p-2 rounded-full bg-brand-700 text-accent-200 shadow-md transition-all duration-200 flex items-center justify-center hover:bg-red-600">
                                    <Trash2 class="trash-icon w-5 h-5" />
                                </button>
                            </div>
                            <div class="w-full">
                                <button @click="addLeiras()"
                                    class="w-full p-2 rounded-full bg-brand-700 hover:bg-brand-800 text-accent-200 font-bold shadow-md 
                            transition-all hover:scale-[1.1] flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed">
                                    + Lépés
                                </button>
                            </div>




                        </div>


                        <label class="block">Fénykép feltöltése</label>
                        <div class="relative flex flex-col w-full mb-5 rounded-base items-center">

                            <div class="flex items-center justify-center w-full">
                                <label for="dropzone-file"
                                    :style="kepPreview ? { backgroundImage: `url(${kepPreview})` } : {}"
                                    :class="Kep == null ? 'border-red-500 shadow-red-400 shadow-lg' : 'border-accent-600'"
                                    class="group relative flex flex-col items-center justify-center w-full h-64 border-3 rounded-3xl bg-accent-400/60 shadow-md bg-cover bg-center bg-no-repeat overflow-hidden">


                                    <div v-if="Kep == null"
                                        class="flex flex-col items-center justify-center text-body pt-5 pb-6">
                                        <Upload class="w-10 h-10" />
                                        <p class="mb-2 text-md">
                                            <span class="font-semibold">Kattints a feltöltéshez</span>
                                            vagy húzd ide a fényképet.
                                        </p>
                                    </div>


                                    <div v-if="Kep"
                                        class="absolute inset-0 flex items-center  justify-center bg-black/50 opacity-0 group-hover:opacity-100 transition-all duration-200">
                                        <Pen class="w-15 h-15 text-accent-300" />
                                    </div>

                                    <input id="dropzone-file" type="file" accept="image/jpeg,image/png,image/webp"
                                        @change="KepFeltoltes" class="hidden" />

                                </label>
                            </div>

                        </div>
                    </div>

                    <div class="flex items-center justify-between px-8 pt-8 pb-4 shrink-0">
                        <button @click="Letrehozas()" :disabled="!letrehozhato"
                            class="w-full p-2 py-4 rounded-full bg-brand-700 hover:bg-brand-800 text-accent-200 font-bold text-lg shadow-md
                        transition-all hover:scale-[1.1] flex items-center justify-center gap-2 disabled:opacity-50 disabled:transition-none disabled:hover:scale-100 disabled:hover:bg-brand-700">
                            Létrehozás
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </Transition>
</template>