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
  <div class="w-full h-full flex items-center justify-center px-4 md:px-0">
    <div class="gallery-container">
      <div class="recipe-grid">
        <RecipeGalleryCard 
          v-for="recipe in recipes" 
          :key="recipe.id" 
          :recipe="recipe" 
          @open-modal="openModal" 
        />
      </div>
    </div>

    <RecipeModal 
      :recipe="selectedRecipe" 
      v-model:visible="isModalVisible" 
      @addToFavorites="handleAddToFavorites"
      @removeFromFavorites="handleRemoveFromFavorites" 
    />
  </div>
</template>

<style scoped>
.gallery-container {
  width: 100%;
  max-width: 1600px;
  height: calc(100vh - 6rem);
  overflow-y: auto;
  padding: 1.5rem;
  border-radius: 1.5rem;
  background: linear-gradient(to bottom right, rgba(251, 146, 60, 0.15), transparent, rgba(251, 146, 60, 0.15));
  backdrop-filter: blur(4px);
  border: 2px solid rgba(234, 88, 12, 0.3);
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
  animation: fadeIn 0.6s ease-out, borderGlow 4s ease-in-out infinite;
}

.recipe-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 1rem;
  padding-bottom: 1.5rem;
}

/* Scrollbar styling */
.gallery-container::-webkit-scrollbar {
  width: 8px;
}

.gallery-container::-webkit-scrollbar-track {
  background: rgba(0, 0, 0, 0.05);
  border-radius: 10px;
}

.gallery-container::-webkit-scrollbar-thumb {
  background: rgba(0, 0, 0, 0.2);
  border-radius: 10px;
}

.gallery-container::-webkit-scrollbar-thumb:hover {
  background: rgba(0, 0, 0, 0.4);
}

.gallery-container {
  scrollbar-width: thin;
  scrollbar-color: rgba(0, 0, 0, 0.2) rgba(0, 0, 0, 0.05);
  scroll-behavior: smooth;
}

/* Animations */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px) scale(0.98);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

@keyframes borderGlow {
  0%, 100% {
    border-color: rgba(234, 88, 12, 0.3);
  }
  50% {
    border-color: rgba(234, 88, 12, 0.5);
  }
}

/* Tablet */
@media (min-width: 640px) {
  .recipe-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 1.25rem;
  }
  
  .gallery-container {
    padding: 2rem;
  }
}

/* Desktop */
@media (min-width: 1024px) {
  .recipe-grid {
    grid-template-columns: repeat(3, 1fr);
    gap: 1.5rem;
  }
  
  .gallery-container {
    padding: 2rem;
    border-radius: 1.75rem;
    height: calc(100vh - 6rem);
  }
}

/* Mobile optimizations */
@media (max-width: 639px) {
  .gallery-container {
    height: calc(100vh - 8rem);
    padding: 1rem;
    border-radius: 1.25rem;
    border-width: 1.5px;
  }
  
  .recipe-grid {
    gap: 0.875rem;
  }
}
</style>