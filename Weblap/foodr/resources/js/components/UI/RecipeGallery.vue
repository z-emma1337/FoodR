<script setup>
import { ref, onMounted } from 'vue'
import { ChevronLeft, ChevronRight } from 'lucide-vue-next'
import RecipeGalleryCard from './RecipeGalleryCard.vue'
import RecipeModal from './RecipeModal.vue'

const recipes = ref([])
const selectedRecipe = ref(null)
const isModalVisible = ref(false)
const scrollContainer = ref(null)
const mainScrollContainer = ref(null)

const loadRecipes = async () => {
  try {
    const res = await fetch('/recipes')
    recipes.value = await res.json()
  } catch (err) {
    console.error('Hiba a receptek betöltésekor:', err)
  }
}

onMounted(() => {
  loadRecipes()
})

const openModal = (recipe) => {
  selectedRecipe.value = recipe
  isModalVisible.value = true
}

const handleAddToFavorites = async (recipe) => {
  console.log('Recept hozzáadva a kedvencekhez:', recipe.nev)
  await loadRecipes()
}

const handleRemoveFromFavorites = async (recipe) => {
  console.log('Recept eltávolítva a kedvencekből:', recipe.nev)
  await loadRecipes()
}

// Horizontal scroll functions (mobile)
const scroll = (direction) => {
  if (!scrollContainer.value) return
  const scrollAmount = 320
  const newPosition = scrollContainer.value.scrollLeft + (direction === 'left' ? -scrollAmount : scrollAmount)
  scrollContainer.value.scrollTo({
    left: newPosition,
    behavior: 'smooth'
  })
}

const canScrollLeft = ref(false)
const canScrollRight = ref(true)

const updateScrollButtons = () => {
  if (!scrollContainer.value) return
  canScrollLeft.value = scrollContainer.value.scrollLeft > 0
  canScrollRight.value =
    scrollContainer.value.scrollLeft <
    scrollContainer.value.scrollWidth - scrollContainer.value.clientWidth - 10
}
</script>

<template>
  <div class="w-full h-full flex items-center justify-center">

    <div ref="mainScrollContainer" class="gallery-scroll-container w-full max-w-[1600px] h-[calc(100vh-6rem)]
             overflow-y-auto rounded-3xl
             bg-gradient-to-br from-accent-400/20 via-transparent to-accent-400/20
             backdrop-blur-sm
             border-2 border-accent-500/30
             shadow-2xl
             p-6 md:p-8">

      <div class="hidden md:grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6 pb-6">
        <RecipeGalleryCard v-for="recipe in recipes" :key="recipe.id" :recipe="recipe" @open-modal="openModal" />
      </div>

      <div class="md:hidden relative">

        <button v-if="canScrollLeft" @click="scroll('left')" class="absolute left-2 top-1/2 -translate-y-1/2 z-10
                 p-2 rounded-full bg-accent-400/90 hover:bg-accent-500
                 shadow-xl backdrop-blur-sm transition-all hover:scale-110">
          <ChevronLeft class="w-5 h-5 text-slate-900" />
        </button>

        <button v-if="canScrollRight" @click="scroll('right')" class="absolute right-2 top-1/2 -translate-y-1/2 z-10
                 p-2 rounded-full bg-accent-400/90 hover:bg-accent-500
                 shadow-xl backdrop-blur-sm transition-all hover:scale-110">
          <ChevronRight class="w-5 h-5 text-slate-900" />
        </button>

        <div ref="scrollContainer" @scroll="updateScrollButtons" class="flex gap-4 overflow-x-auto pb-4 snap-x snap-mandatory
                 scrollbar-hide scroll-smooth px-2">
          <div v-for="recipe in recipes" :key="recipe.id" class="flex-shrink-0 w-[280px] sm:w-[320px] snap-center">
            <RecipeGalleryCard :recipe="recipe" @open-modal="openModal" />
          </div>
        </div>

        <div class="flex justify-center gap-2 mt-4">
          <div v-for="(recipe, index) in recipes.slice(0, 10)" :key="recipe.id"
            class="w-2 h-2 rounded-full transition-all bg-accent-300">
          </div>
        </div>
      </div>

    </div>

    <RecipeModal :recipe="selectedRecipe" v-model:visible="isModalVisible" @addToFavorites="handleAddToFavorites"
      @removeFromFavorites="handleRemoveFromFavorites" />

  </div>
</template>

<style scoped>
.gallery-scroll-container::-webkit-scrollbar {
  width: 10px;
}

.gallery-scroll-container::-webkit-scrollbar-track {
  background: rgba(0, 0, 0, 0.05);
  border-radius: 10px;
  margin-right: 1rem;
}

.gallery-scroll-container::-webkit-scrollbar-thumb {
  background: rgba(0, 0, 0, 0.2);
  border-radius: 10px;
}

.gallery-scroll-container::-webkit-scrollbar-thumb:hover {
  background: rgba(0, 0, 0, 0.4);
}

.gallery-scroll-container {
  scrollbar-width: thick;
  scrollbar-color: rgba(0, 0, 0, 0.2) rgba(0, 0, 0, 0.05);
  padding-right: 1rem;
}

.gallery-scroll-container {
  scroll-behavior: smooth;
}

.scrollbar-hide {
  -ms-overflow-style: none;
  scrollbar-width: none;
}

.scrollbar-hide::-webkit-scrollbar {
  display: none;
}

.snap-x {
  scroll-snap-type: x mandatory;
}

.snap-center {
  scroll-snap-align: center;
}

.gallery-scroll-container {
  animation: containerFadeIn 0.5s ease-out;
}

@keyframes containerFadeIn {
  from {
    opacity: 0;
    transform: translateY(0px) scale(0.98);
  }

  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

@keyframes borderPulse {

  0%,
  100% {
    border-color: rgb(234, 88, 12, 0.3);
  }

  50% {
    border-color: rgb(234, 88, 12, 0.5);
  }
}

.gallery-scroll-container {
  animation: containerFadeIn 0.6s ease-out, borderPulse 4s ease-in-out infinite;
}

@media (max-width: 768px) {
  .gallery-scroll-container {
    border-radius: 1.5rem;
    padding: 1rem;
    height: calc(100vh - 10rem);
  }
}
</style>