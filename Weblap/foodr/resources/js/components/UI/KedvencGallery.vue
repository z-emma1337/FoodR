<script setup>
import { ref, onMounted, computed, watch, onBeforeUnmount } from 'vue'
import RecipeModal from './RecipeModal.vue'
import KedvencGalleryCard from './KedvencGalleryCard.vue'
import { X, Filter, HeartCrack, ArrowDownUp } from 'lucide-vue-next'

const recipes = ref([])
const selectedRecipe = ref(null)
const isModalVisible = ref(false)
const searchInput = ref('')
const allergenek = ref(['nev', 'id']);
const selectedAllergens = ref([])

const loadRecipes = async () => {
  try {
    const res = await fetch('/recipes')
    recipes.value = await res.json()
  } catch (err) {
    console.error('Hiba a receptek betöltésekor:', err)
  }
}


const minHozzavalok = computed(() => recipes.value.length ? Math.min(...recipes.value.map(r => r.hozzavalok.length)) : 2)
const maxHozzavalok = computed(() => recipes.value.length ? Math.max(...recipes.value.map(r => r.hozzavalok.length)) : 10)
const minIdo = computed(() => recipes.value.length ? Math.min(...recipes.value.map(r => r.ido)) : 2)
const maxIdo = computed(() => recipes.value.length ? Math.max(...recipes.value.map(r => r.ido)) : 10)

onMounted(() => {
  loadRecipes()
  loadAllergens()
  const handleClickOutside = (event) => {
    if (isDropdownOpen.value &&
      filterButtonRef.value &&
      filterDropdownRef.value &&
      !filterButtonRef.value.contains(event.target) &&
      !filterDropdownRef.value.contains(event.target)) {
      isDropdownOpen.value = false
    }
    if (isRendezesDropdownOpen.value &&
      rendezesButtonRef.value &&
      rendezesDropdownRef.value &&
      !rendezesButtonRef.value.contains(event.target) &&
      !rendezesDropdownRef.value.contains(event.target)) {
      isRendezesDropdownOpen.value = false
    }
  }
  document.addEventListener('click', handleClickOutside)
  onBeforeUnmount(() => document.removeEventListener('click', handleClickOutside))
})

const openModal = (recipe) => {
  selectedRecipe.value = recipe
  isModalVisible.value = true
}

const handleAddToFavorites = (recipe) => {
  const idx = recipes.value.findIndex(r => r.id === recipe.id)
  if (idx >= 0) recipes.value[idx].liked = 1
}

const handleRemoveFromFavorites = (recipe) => {
  const idx = recipes.value.findIndex(r => r.id === recipe.id)
  if (idx >= 0) recipes.value[idx].liked = 0
}

const loadAllergens = async () => {
  try {
    const res = await fetch('/allergenek')
    allergenek.value = await res.json()
    allergenek.value = allergenek.value.map(a => a.nev)
  } catch (err) {
    console.error('Hiba az allergének betöltésekor:', err)
  }
}

const searchedRecipes = computed(() => {
  const input = searchInput.value.trim().toLowerCase()
  if (!input) return recipes.value.filter(r => r.liked == 1)
  const seen = new Set()
  const result = []
  for (const recipe of recipes.value) {
    if (recipe.liked != 1 || seen.has(recipe.id)) continue
    if (
      recipe.nev.toLowerCase().includes(input) ||
      recipe.leiras.toLowerCase().includes(input) ||
      recipe.hozzavalok.some(h => h.nev.toLowerCase().includes(input)) ||
      recipe.allergenek.some(a => a.toLowerCase().includes(input))
    ) {
      seen.add(recipe.id)
      result.push(recipe)
    }
  }
  return result
})

const selectedAllergenSet = computed(() => new Set(selectedAllergens.value))

const filteredRecipes = computed(() => {
  const allergenSet = selectedAllergenSet.value
  const result = []
  for (const recipe of recipes.value) {
    if (recipe.liked != 1) continue
    if (recipe.allergenek.some(a => !allergenSet.has(a))) continue
    if (recipe.hozzavalok.length > inputHozzavalok.value) continue
    if (recipe.ido > inputIdo.value) continue
    result.push(recipe)
  }
  return result
})


const rendezesiSzempontok = ['Név (A-Z)', 'Idő (növekvő)', 'Idő (csökkenő)', 'Hozzávalók (növekvő)', 'Hozzávalók (csökkenő)', 'Kedvencek', 'Kommentek']
const selectedRendezes = ref('Név (A-Z)')

const isDropdownOpen = ref(false)
const isRendezesDropdownOpen = ref(false)

const toggleRendezesDropdown = () => {
  isRendezesDropdownOpen.value = !isRendezesDropdownOpen.value
  if (isDropdownOpen.value) isDropdownOpen.value = false
}

const toggleDropdown = () => {
  isDropdownOpen.value = !isDropdownOpen.value
  if (isRendezesDropdownOpen.value) isRendezesDropdownOpen.value = false
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
  let result = searchInput.value.trim() !== '' ? searchedRecipes.value : filteredRecipes.value
  return [...result].sort((a, b) => {
    switch (selectedRendezes.value) {
      case 'Név (A-Z)': return a.nev.localeCompare(b.nev)
      case 'Idő (növekvő)': return a.ido - b.ido
      case 'Idő (csökkenő)': return b.ido - a.ido
      case 'Hozzávalók (növekvő)': return a.hozzavalok.length - b.hozzavalok.length
      case 'Hozzávalók (csökkenő)': return b.hozzavalok.length - a.hozzavalok.length
      case 'Kedvencek': return b.likedb - a.likedb
      case 'Kommentek': return b.kommentdb - a.kommentdb
      default: return 0
    }
  })
})

const reset = () => {
  searchInput.value = ''
  inputHozzavalok.value = recipes.value.reduce((max, recipe) => Math.max(max, recipe.hozzavalok.length), 0)
  inputIdo.value = recipes.value.reduce((max, recipe) => Math.max(max, recipe.ido), 0)
  selectedAllergens.value = [...allergenek.value]
}

const filterButtonRef = ref(null)
const rendezesButtonRef = ref(null)
const filterDropdownRef = ref(null)
const rendezesDropdownRef = ref(null)


</script>

<template>
  <div class="w-full h-full flex items-center justify-center">

    <!-- Külső keret -->
    <div
      class="rounded-3xl overflow-hidden border-4 border-accent-600 bg-gradient-to-br from-brand-800 via-brand-600 to-accent-700 animate-gradient backdrop-blur-sm shadow-2xl w-full h-full mx-3 lg:mx-12">

      <!-- Belső konténer -->
      <div class="h-full overflow-y-auto scroll-smooth p-3 pb-10 lg:pb-3 foodr-scrollbar">

        <div v-if="recipesToShow.length == 0 && !searchInput"
          class="relative h-full flex flex-col items-center justify-center gap-8 py-8">
          <div class="text-center space-y-4 animate-fade-in">
            <div class="w-24 h-24 mx-auto rounded-full bg-accent-400/30 flex items-center justify-center">
              <HeartCrack class="w-12 h-12 text-accent-600" />
            </div>
            <h2 class="text-3xl font-bold text-accent-200">💔 Nincs még kedvenc recepted 💔</h2>
            <p class="text-accent-300">Likeolj recepteket a SwipeR-ben és a FeedR-ben!</p>
          </div>
        </div>

        <div v-else class="w-full mb-3 relative sticky top-0 z-50 drop-shadow-[0_0_50px_theme(colors.brand.800)]">
          <input title="Keresés" v-model="searchInput" class="w-full bg-accent-200 text-brand-700 placeholder:text-brand-700 text-sm
           border-4 border-accent-600 rounded-3xl py-2 pl-12 pr-12
           focus:outline-none focus:ring-2 focus:ring-accent-400
           focus:border-transparent transition-all shadow-md" placeholder="Receptek keresése..." />


          <button title="Szűrés" ref="filterButtonRef" @click="toggleDropdown" class="absolute left-3 top-1/2 -translate-y-1/2
         bg-accent-200 rounded-full p-1.5
         text-brand-700 hover:text-brand-800 transition-all transform
         hover:scale-110">
            <Filter class="w-5 h-5" stroke-width="3" />
          </button>

          <transition name="dropdown">
            <div ref="filterDropdownRef" v-show="isDropdownOpen"
              class="z-10 absolute bg-accent-200 w-44 rounded-3xl border-4 border-accent-600 top-full left-0 mt-2 shadow-lg">
              <ul class="p-2 text-sm text-body font-medium text-brand-700 text-center">
                <li>
                  <h5>Hozzávalók</h5>
                  <h4>{{ inputHozzavalok }} db</h4>
                  <input v-model.number="inputHozzavalok" type="range" :min="minHozzavalok" :max="maxHozzavalok"
                    step="1" class="w-full mt-1 rounded border border-brand-300 accent-brand-600" />
                </li>
                <li>
                  <h5>Idő</h5>
                  <h4>{{ inputIdo }} perc</h4>
                  <input v-model.number="inputIdo" type="range" :min="minIdo" :max="maxIdo" step="1"
                    class="w-full mt-1 rounded border border-brand-300 accent-brand-600" />
                </li>
                <p class="text-xs text-slate-500 font-medium">
                  Mit tartalmazhat a recept?
                </p>
                <li v-for="allergen in allergenek" :key="allergen">
                  <label
                    class="inline-flex items-center w-full p-2 hover:bg-neutral-tertiary-medium hover:text-heading rounded">
                    <input type="checkbox" v-model="selectedAllergens" :value="allergen"
                      class="mr-2 accent-brand-600 w-5 h-5" />
                    {{ allergen }}
                  </label>
                </li>
                <li>
                  <button @click="reset()" class="w-full mt-3 py-2 rounded-3xl button-brand">
                    Alaphelyzet
                  </button>
                </li>
              </ul>
            </div>
          </transition>

          <transition name="dropdown">
            <div ref="rendezesDropdownRef" v-show="isRendezesDropdownOpen"
              class="z-10 absolute bg-accent-200 w-44 rounded-3xl border-4 border-accent-600 top-full right-0 mt-2 shadow-lg">
              <ul class="p-2 text-sm font-medium text-brand-700 text-center">
                <li class="mb-1 font-bold text-base ">Rendezés</li>
                <li v-for="szempont in rendezesiSzempontok" :key="szempont">
                  <button @click="selectedRendezes = szempont; isRendezesDropdownOpen = false"
                    class="w-full px-3 py-2 rounded-2xl transition-colors" :class="selectedRendezes === szempont
                      ? 'bg-brand-700 text-accent-200'
                      : 'hover:bg-accent-300'">
                    {{ szempont }}
                  </button>
                </li>
              </ul>
            </div>
          </transition>

          <button v-if="searchInput" @click="searchInput = ''" class="absolute right-12 top-1/2 -translate-y-1/2
           text-brand-700 hover:text-brand-800 transition-colors">
            <X class="w-5 h-5" stroke-width="3" />
          </button>
          <button title="Rendezés" ref="rendezesButtonRef" @click="toggleRendezesDropdown" class="absolute right-3 top-1/2 -translate-y-1/2
         bg-accent-200 rounded-full p-1.5
         text-brand-700 hover:text-brand-800 transition-all transform
         hover:scale-110">
            <ArrowDownUp class="w-5 h-5" stroke-width="3" />
          </button>
        </div>


        <div class="grid xs:grid-cols-2  sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-1 xl:grid-cols-3 gap-4">
          <KedvencGalleryCard v-for="recipe in recipesToShow" :key="recipe.id" :recipe="recipe" @open-modal="openModal"
            @removed="recipes = recipes.filter(r => r.id !== $event)" />
        </div>

      </div>
    </div>

    <RecipeModal :recipe="selectedRecipe" @addToFavorites="handleAddToFavorites"
      @removeFromFavorites="handleRemoveFromFavorites" />
  </div>
</template>
<style scoped>
.dropdown-enter-active,
.dropdown-leave-active {
  transition: opacity 0.3s ease, transform 0.3s ease;
}

.dropdown-enter-from,
.dropdown-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>