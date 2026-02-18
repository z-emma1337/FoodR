<script setup>
import { ref, onMounted } from 'vue'
import RecipeGalleryCard from './RecipeGalleryCard.vue'
import RecipeModal from './RecipeModal.vue'

const recipes = ref([])
const selectedRecipe = ref(null)
const isModalVisible = ref(false)

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
</script>

<template>
  <div class="w-full h-full flex items-center justify-center">

    <!-- Külső wrapper: keret + lekerekítés + levágás -->
    <div class=" rounded-3xl overflow-hidden
                border-accent-600 border-6
                bg-gradient-to-br from-brand-800 via-brand-600 to-accent-700 animate-gradient backdrop-blur-sm shadow-2xl">

      <!-- Belső scroll konténer -->
      <div class="h-[calc(100vh-6rem)] max-sm:h-[calc(100vh-8rem)] overflow-y-auto scroll-smooth
                  p-6 sm:p-8 max-sm:p-4
                  foodr-scrollbar">

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
          <RecipeGalleryCard v-for="recipe in recipes" :key="recipe.id" :recipe="recipe" @open-modal="openModal" />
        </div>

      </div>
    </div>

    <RecipeModal :recipe="selectedRecipe" v-model:visible="isModalVisible"
      @addToFavorites="handleAddToFavorites" @removeFromFavorites="handleRemoveFromFavorites" />
  </div>
</template>