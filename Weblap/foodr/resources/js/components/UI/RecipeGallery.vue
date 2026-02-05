<script setup>
import { ref, onMounted } from 'vue'
import { ChevronLeft, ChevronRight } from 'lucide-vue-next'
import RecipeGalleryCard from './RecipeGalleryCard.vue'
import RecipeModal from './RecipeModal.vue'

const recipes = ref([])
const selectedRecipe = ref(null)
const isModalVisible = ref(false)
const scrollContainer = ref(null)

onMounted(async () => {
  try {
    const res = await fetch('/recipes')
    recipes.value = await res.json()
  } catch (err) {
    console.error('Hiba a receptek betöltésekor:', err)
  }
})

const openModal = (recipe) => {
  selectedRecipe.value = recipe
  isModalVisible.value = true
}

// Horizontal scroll functions
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
  <div class="space-y-6">
    
    <!-- Desktop Grid View -->
    <div class="hidden md:grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6">
      <RecipeGalleryCard 
        v-for="recipe in recipes" 
        :key="recipe.id"
        :recipe="recipe"
        @open-modal="openModal"
      />
    </div>

    <!-- Mobile Horizontal Slider -->
    <div class="md:hidden relative">
      
      <!-- Scroll Buttons -->
      <button v-if="canScrollLeft"
              @click="scroll('left')"
              class="absolute left-2 top-1/2 -translate-y-1/2 z-10
                     p-2 rounded-full bg-accent-400/90 hover:bg-accent-500
                     shadow-xl backdrop-blur-sm transition-all hover:scale-110">
        <ChevronLeft class="w-5 h-5 text-slate-900" />
      </button>

      <button v-if="canScrollRight"
              @click="scroll('right')"
              class="absolute right-2 top-1/2 -translate-y-1/2 z-10
                     p-2 rounded-full bg-accent-400/90 hover:bg-accent-500
                     shadow-xl backdrop-blur-sm transition-all hover:scale-110">
        <ChevronRight class="w-5 h-5 text-slate-900" />
      </button>

      <!-- Scrollable Container -->
      <div ref="scrollContainer"
           @scroll="updateScrollButtons"
           class="flex gap-4 overflow-x-auto pb-4 snap-x snap-mandatory
                  scrollbar-hide scroll-smooth px-2">
        <div v-for="recipe in recipes" 
             :key="recipe.id"
             class="flex-shrink-0 w-[280px] sm:w-[320px] snap-center">
          <RecipeGalleryCard 
            :recipe="recipe"
            @open-modal="openModal"
          />
        </div>
      </div>

      <!-- Scroll Indicators -->
      <div class="flex justify-center gap-2 mt-4">
        <div v-for="(recipe, index) in recipes.slice(0, 10)" 
             :key="recipe.id"
             class="w-2 h-2 rounded-full transition-all bg-accent-300">
        </div>
      </div>
    </div>

    <!-- Recipe Modal (PrimeVue Dialog) -->
    <RecipeModal 
      :recipe="selectedRecipe"
      v-model:visible="isModalVisible"
    />

  </div>
</template>

<style scoped>
/* Hide scrollbar but keep functionality */
.scrollbar-hide {
  -ms-overflow-style: none;
  scrollbar-width: none;
}

.scrollbar-hide::-webkit-scrollbar {
  display: none;
}

/* Smooth snap scrolling */
.snap-x {
  scroll-snap-type: x mandatory;
}

.snap-center {
  scroll-snap-align: center;
}
</style>