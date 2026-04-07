<script setup>
import { ref, onMounted, computed, watch, onBeforeUnmount } from 'vue'

import { X, Filter, Plus } from 'lucide-vue-next'
import LetrehozasModal from './LetrehozasModal.vue'
import ReceptjeimGalleryCard from './ReceptjeimGalleryCard.vue'


const recipes = ref([])
const selectedRecipe = ref(null)
const isModalVisible = ref(false)
const searchInput = ref('')
const allergenek = ref([])
const selectedAllergens = ref([])
const felhasznaloid = ref([])



const loadRecipes = async () => {
    try {
        const res = await fetch('/recipes')
        recipes.value = await res.json()
    } catch (err) {
        console.error(err)
    }
}

const GetUserId = async () => {

    const res = await fetch('/felhasznalo')
    felhasznaloid.value = await res.json()
    console.log("Az id:" + felhasznaloid.value.id)

}





onMounted(() => {
    loadRecipes()
    loadAllergens()
    GetUserId();
})

const loadAllergens = async () => {
    try {
        const res = await fetch('/allergenek')
        allergenek.value = await res.json()
    } catch (err) {
        console.error(err)
    }
}

const searchedRecipes = computed(() => {
    const result = []
    const input = searchInput.value.trim().toLowerCase()
    if (!input) {
        return recipes.value
    }
    for (const recipe of recipes.value) {

        if (recipe.felhasznalo_id == felhasznaloid.value.id) {


            if (recipe.nev.toLowerCase().includes(input) && !result.includes(recipe)) {
                result.push(recipe)
            }
            if (recipe.leiras.toLowerCase().includes(input) && !result.includes(recipe)) {
                result.push(recipe)
            }
            if (recipe.hozzavalok.some(hozzavalo => hozzavalo.nev.toLowerCase().includes(input)) && !result.includes(recipe)) {
                result.push(recipe)
            }
            if (recipe.allergenek.some(allergen => allergen.toLowerCase().includes(input)) && !result.includes(recipe)) {
                result.push(recipe)
            }

        }
    }

    return result
})

const filteredRecipes = computed(() => {
    const result = []
    for (const recipe of recipes.value) {

        if (recipe.felhasznalo_id == felhasznaloid.value.id) {

            if (recipe.allergenek.some(a => !selectedAllergens.value.includes(a))) {
                continue
            }

            if (recipe.hozzavalok.length > inputHozzavalok.value) {
                continue
            }
            if (recipe.ido > inputIdo.value) {
                continue
            }

            result.push(recipe)
        }
    }

    return result
})


const isDropdownOpen = ref(false)

const toggleDropdown = () => {
    isDropdownOpen.value = !isDropdownOpen.value
}

const inputHozzavalok = ref(0)
const inputIdo = ref(0)
watch(recipes, (newRecipes) => {
    inputHozzavalok.value = newRecipes.reduce((max, recipe) => Math.max(max, recipe.hozzavalok.length), 0)
    inputIdo.value = newRecipes.reduce((max, recipe) => Math.max(max, recipe.ido), 0)
    selectedAllergens.value = allergenek.value
})

watch(allergenek, (ujLista) => {
    selectedAllergens.value = [...ujLista]
})

const recipesToShow = computed(() => {

    if (searchInput.value.trim() !== '') {
        return searchedRecipes.value
    }

    return filteredRecipes.value
})

const reset = () => {
    searchInput.value = ''
    inputHozzavalok.value = recipes.value.reduce((max, recipe) => Math.max(max, recipe.hozzavalok.length), 0)
    inputIdo.value = recipes.value.reduce((max, recipe) => Math.max(max, recipe.ido), 0)
    selectedAllergens.value = [...allergenek.value]
}

const filterButtonRef = ref(null)
const dropdownMenuRef = ref(null)

onMounted(() => {
    const handleClickOutside = (event) => {
        if (isDropdownOpen.value &&
            dropdownMenuRef.value &&
            filterButtonRef.value &&
            !dropdownMenuRef.value.contains(event.target) &&
            !filterButtonRef.value.contains(event.target)) {
            isDropdownOpen.value = false
        }
    }
    document.addEventListener('click', handleClickOutside)

    onBeforeUnmount(() => {
        document.removeEventListener('click', handleClickOutside)
    })
})

const ModalOpen = ref(false)
function OpenRecipeModal() {
    ModalOpen.value = true;
}

function CloseModal() {
    ModalOpen.value = false;
}

</script>

<template>
    <div class="w-full h-full flex items-center justify-center">

        <!-- Külső keret -->
        <div
            class="rounded-3xl overflow-hidden border-4 border-accent-600 bg-gradient-to-br from-brand-800 via-brand-600 to-accent-700 animate-gradient backdrop-blur-sm shadow-2xl w-full h-full mx-12">

            <!-- Belső konténer -->
            <div :class="recipesToShow.length > 0 ? 'overflow-y-auto' : 'overflow-hidden'"
                class="h-[calc(100vh-4rem)] max-sm:h-[calc(100vh-2rem)] scroll-smooth p-3 foodr-scrollbar">

                <div v-if="recipesToShow.length == 0"
                    class=" relative h-[calc(100vh-3rem)] flex flex-col items-center justify-center gap-8 py-8">
                    <div class="text-center space-y-4 animate-fade-in">
                        <button @click="OpenRecipeModal"
                            class="w-24 h-24 mx-auto rounded-full border-6 border-accent-600 bg-accent-300 flex items-center justify-center">
                            <Plus class="w-15 h-15 text-brand-600" />
                        </button>
                        <p class="text-3xl font-bold text-accent-200">🍴 Még nincs saját recepted 🍴</p>
                        <p class="text-accent-300">A gombra kattintva hozd létre első receptedet!</p>
                    </div>
                </div>

                <div v-else
                    class="w-full mb-3 relative sticky top-0 z-50 drop-shadow-[0_0_50px_theme(colors.brand.800)]">
                    <input v-model="searchInput" class="w-full bg-accent-200 text-brand-700 placeholder:text-brand-700 text-sm
           border-4 border-accent-600 rounded-3xl py-2 pl-12 pr-12
           focus:outline-none focus:ring-2 focus:ring-accent-400
           focus:border-transparent transition-all shadow-md" placeholder="Receptek keresése..." />

                    <button ref="filterButtonRef" @click="toggleDropdown" class="absolute left-3 top-1/2 -translate-y-1/2
         bg-accent-200 rounded-full p-1.5
         text-brand-700 hover:text-brand-800 transition-all transform
         hover:scale-110">
                        <Filter class="w-5 h-5" stroke-width="3" />
                    </button>

                    <transition name="dropdown">
                        <div ref="dropdownMenuRef" v-show="isDropdownOpen"
                            class="z-10 absolute bg-accent-200 w-44 rounded-3xl border-4 border-accent-600 top-full left-0 mt-2 shadow-lg">
                            <ul class="p-2 text-sm text-body font-medium text-brand-700 text-center">
                                <li>
                                    <h5>Hozzávalók</h5>
                                    <h4>{{ inputHozzavalok }}</h4>
                                    <input v-model.number="inputHozzavalok" type="range"
                                        :min="recipes.length ? Math.min(...recipes.map(r => r.hozzavalok.length)) : 2"
                                        :max="recipes.length ? Math.max(...recipes.map(r => r.hozzavalok.length)) : 10"
                                        step="1" class="w-full mt-1 rounded border border-brand-300 accent-brand-600" />
                                </li>
                                <li>
                                    <h5>Idő</h5>
                                    <h4>{{ inputIdo }} perc</h4>
                                    <input v-model.number="inputIdo" type="range"
                                        :min="recipes.length ? Math.min(...recipes.map(r => r.ido)) : 2"
                                        :max="recipes.length ? Math.max(...recipes.map(r => r.ido)) : 10" step="1"
                                        class="w-full mt-1 rounded border border-brand-300 accent-brand-600" />
                                </li>
                                <li v-for="allergen in allergenek" :key="allergen">
                                    <label
                                        class="inline-flex items-center w-full p-2 hover:bg-neutral-tertiary-medium hover:text-heading rounded">
                                        <input type="checkbox" v-model="selectedAllergens" :value="allergen"
                                            class="mr-2 accent-brand-600 w-5 h-5" />
                                        {{ allergen }}
                                    </label>
                                </li>
                                <li>
                                    <button @click="reset()"
                                        class="w-full mt-3 bg-brand-700 text-accent-200 py-2 rounded-3xl hover:bg-brand-800 transition-colors">
                                        Alaphelyzet
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </transition>

                    <button v-if="searchInput" @click="searchInput = ''" class="absolute right-3 top-1/2 -translate-y-1/2
           text-brand-700 hover:text-brand-800 transition-colors">
                        <X class="w-5 h-5" stroke-width="3" />
                    </button>
                </div>



                <div class="grid xs:grid-cols-2 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-1 xl:grid-cols-3 gap-4">
                    <div @click="OpenRecipeModal" v-if="recipesToShow.length != 0" class="rounded-2xl overflow-hidden cursor-pointer
              bg-gradient-to-br from-accent-300 via-accent-200 to-accent-300
              shadow-lg hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 
              border-accent-600 border-[3px] flex flex-col group w-full h-full flex items-center justify-center">


                        <Plus class="w-15 h-15 text-brand-600" />
                        <p class="text-3xl font-bold text-brand-600">Új recept létrehozása</p>


                    </div>
                    <ReceptjeimGalleryCard v-for="recipe in recipesToShow" :key="recipe.id" :recipe="recipe" />
                </div>

            </div>
        </div>
        <LetrehozasModal :open="ModalOpen" @close="CloseModal" />
    </div>
</template>
