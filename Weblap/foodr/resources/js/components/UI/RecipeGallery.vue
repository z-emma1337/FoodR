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

    <!-- Mobile -->
    
<div class="flex flex-col items-center">
  <div v-for="recipe in recipes" :key="recipe.id" 
       class="w-[83vw] max-w-[350px] mb-4">
    <RecipeGalleryCard :recipe="recipe" @open-modal="openModal" />
  </div>
</div>
      </div>



    <!-- Recipe Modal (PrimeVue Dialog) -->
    <RecipeModal 
      :recipe="selectedRecipe"
      v-model:visible="isModalVisible"
    />


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