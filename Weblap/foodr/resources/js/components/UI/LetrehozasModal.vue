<script setup>
import { Clock, Users, ChefHat, X as CloseIcon, Heart, HeartCrack, Check, Trash2 } from 'lucide-vue-next'
import { computed, ref, watch, onMounted } from 'vue'
import { router, usePage } from '@inertiajs/vue3'

const props = defineProps({
    visible: Boolean,
    open: { type: Boolean, required: true }
})

const emit = defineEmits(['close'])
const receptNev = ref();
const receptIdo = ref();
const receptAdag = ref();
const receptHozzavalok = ref([{ nev: '', adag: null }]);
const receptHozzavaloNev = ref();
const receptHozzavaloAdag = ref();
const receptLeirasok = ref([{ leiras: '' }])
const receptLeiras = ref();

const nevHiba = ref(false);
const idoHiba = ref(false);
const adagHiba = ref(false);
const hozzavaloHiba= ref(false);
const hozzavaloadagHiba= ref(false);
const leirasHiba= ref(false);

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
    nevHiba.value = !receptNev.value
    idoHiba.value = !receptIdo.value
    adagHiba.value = !receptAdag.value
    hozzavaloHiba.value = !receptHozzavalok.value[0].nev
    hozzavaloadagHiba.value = !receptHozzavalok.value[0].adag 
    leirasHiba.value = !receptLeirasok.value[0].leiras  
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
                                :class="nevHiba ? 'border-red-500 shadow-red-400 shadow-lg' : 'border-accent-600'"
                                class="h-10 pl-5 bg-accent-400/60 border-3 focus:outline-none focus:ring-2 focus:ring-accent-400 focus:border-transparent transition-all shadow-md w-full rounded-3xl mx-2 py-2.5" />

                        </div>
                        <label class="block">Hány perc elkészíteni ezt a receptet?</label>
                        <div class="relative flex items-center w-full rounded-base mb-5">
                            <button type="button" @click="receptIdo = receptIdo - 1"
                                class="text-body bg-brand-600 w-10 text-accent-200 rounded-full font-bold flex items-center justify-center pb-0.5">
                                -
                            </button>
                            <input type="number" placeholder="40" v-model.number="receptIdo" min="1" max="400"
                                :class="idoHiba ? 'border-red-500 shadow-red-400 shadow-md' : 'border-accent-600'"
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
                                :class="adagHiba ? 'border-red-500 shadow-red-400 shadow-md' : 'border-accent-600'"
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
                                :class="hozzavaloHiba ? 'border-red-500 shadow-red-400 shadow-md' : 'border-accent-600'"
                                    class="h-10 pl-5 bg-accent-400/60 border-3 focus:outline-none focus:ring-2 focus:ring-accent-400 focus:border-transparent transition-all shadow-md w-80 rounded-3xl mx-1 py-2.5" />
                                <input v-model.number="hozzavalo.adag" required type="number" placeholder="500" value=""
                                :class="hozzavaloadagHiba ? 'border-red-500 shadow-red-400 shadow-md' : 'border-accent-600'"
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
                                    :class="leirasHiba ? 'border-red-500 shadow-red-400 shadow-md' : 'border-accent-600'"
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



                    </div>

                    <div class="flex items-center justify-between px-8 pt-8 pb-4 shrink-0">
                        <button @click="Letrehozas()"
                            class="w-full p-2 py-4 rounded-full bg-brand-700 hover:bg-brand-800 text-accent-200 font-bold text-lg shadow-md
                        transition-all hover:scale-[1.1] flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                            Létrehozás
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </Transition>
</template>