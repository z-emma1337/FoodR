<script setup>
import { ref, onMounted, computed } from 'vue'
import { Heart, X, ChefHat } from 'lucide-vue-next'
import AppLayout from '@/layouts/AppLayout.vue'
import RecipeCard from '@/components/UI/RecipeCard.vue'

const recipes = ref([])
const currentIndex = ref(0)
const isDragging = ref(false)
const dragStartPos = ref({ x: 0, y: 0 })
const dragOffset = ref({ x: 0, y: 0 })
const rotation = ref(0)
const isAnimating = ref(false)

const currentRecipe = computed(() => recipes.value[currentIndex.value])
const nextRecipe = computed(() => recipes.value[currentIndex.value + 1])

// Receptek betöltése
onMounted(async () => {
  try {
    const response = await fetch('/recipes')
    const data = await response.json()
    recipes.value = data
  } catch (error) {
    console.error('Hiba a receptek betöltésekor:', error)
  }

  // Globális event listenerek a smooth drag-hez
  document.addEventListener('mousemove', handleDragMove)
  document.addEventListener('mouseup', handleDragEnd)
  document.addEventListener('touchmove', handleDragMove, { passive: false })
  document.addEventListener('touchend', handleDragEnd)
})

// Drag események
const handleDragStart = (e) => {
  if (isAnimating.value) return
  
  isDragging.value = true
  const clientX = e.type.includes('mouse') ? e.clientX : e.touches[0].clientX
  const clientY = e.type.includes('mouse') ? e.clientY : e.touches[0].clientY
  
  dragStartPos.value = { x: clientX, y: clientY }
  dragOffset.value = { x: 0, y: 0 }
}

const handleDragMove = (e) => {
  if (!isDragging.value || isAnimating.value) return
  
  if (e.type.includes('touch')) {
    e.preventDefault()
  }
  
  const clientX = e.type.includes('mouse') ? e.clientX : e.touches[0].clientX
  const clientY = e.type.includes('mouse') ? e.clientY : e.touches[0].clientY
  
  const deltaX = clientX - dragStartPos.value.x
  const deltaY = clientY - dragStartPos.value.y
  
  dragOffset.value = { x: deltaX, y: deltaY }
  rotation.value = deltaX * 0.1
}

const handleDragEnd = () => {
  if (!isDragging.value || isAnimating.value) return
  
  isDragging.value = false

  const threshold = 100

  if (Math.abs(dragOffset.value.x) > threshold) {
    if (dragOffset.value.x > 0) {
      swipeRight()
    } else {
      swipeLeft()
    }
  } else {
    // Visszaugrik
    dragOffset.value = { x: 0, y: 0 }
    rotation.value = 0
  }
}

// Swipe műveletek
const swipeLeft = async () => {
  if (isAnimating.value || !currentRecipe.value) return
  isAnimating.value = true
  
  // Animáljuk ki a kártyát
  dragOffset.value = { x: -1000, y: 0 }
  rotation.value = -30
  
  // Várunk az animáció végére
  await new Promise(resolve => setTimeout(resolve, 300))
  
  // Következő kártya
  nextCard()
}

const swipeRight = async () => {
  if (isAnimating.value || !currentRecipe.value) return
  isAnimating.value = true
  
  // TODO: Mentés a kedvencekhez
  
  // Animáljuk ki a kártyát
  dragOffset.value = { x: 1000, y: 0 }
  rotation.value = 30
  
  // Várunk az animáció végére
  await new Promise(resolve => setTimeout(resolve, 300))
  
  // Következő kártya
  nextCard()
}

const nextCard = () => {
  currentIndex.value++
  dragOffset.value = { x: 0, y: 0 }
  rotation.value = 0
  isAnimating.value = false
}
</script>

<template>
  <AppLayout>
    <div class="relative h-[calc(100vh-3rem)] flex flex-col items-center justify-center gap-8 py-8">

      <!-- Nincs több recept üzenet -->
      <div v-if="currentIndex >= recipes.length" class="text-center space-y-4 animate-fade-in">
        <div class="w-24 h-24 mx-auto rounded-full bg-accent-400/30 
                    flex items-center justify-center">
          <ChefHat class="w-12 h-12 text-accent-600" />
        </div>
        <h2 class="text-3xl font-bold text-accent-200">Elfogytak a receptek! 🎉</h2>
        <p class="text-accent-300">Nézd meg a kedvenceidet, vagy gyere vissza később!</p>
      </div>

      <!-- Kártya és gombok konténer -->
      <div v-else class="flex flex-col items-center gap-6 w-full max-w-md">

        <!-- Kártya Stack -->
        <div class="relative w-full h-[600px]">

          <!-- Következő kártya (háttérben) -->
          <RecipeCard v-if="nextRecipe" :recipe="nextRecipe" :is-background="true" />

          <!-- Aktuális kártya -->
          <RecipeCard
            v-if="currentRecipe"
            :recipe="currentRecipe"
            :is-dragging="isDragging"
            :drag-offset="dragOffset"
            :rotation="rotation"
            @dragstart="handleDragStart"
          />

        </div>

        <!-- Action Buttons - KÍVÜL a kártyán -->
        <div class="flex items-center gap-6">

          <!-- Dislike Button -->
          <button @click="swipeLeft" :disabled="isAnimating" class="group relative w-16 h-16 rounded-full 
                         bg-gradient-to-br from-red-500 to-red-600
                         shadow-lg hover:shadow-xl
                         transform hover:scale-110 active:scale-95
                         transition-all duration-200
                         disabled:opacity-50 disabled:cursor-not-allowed
                         flex items-center justify-center">
            <div class="absolute inset-0 rounded-full bg-red-400/50 
                        blur-xl group-hover:blur-2xl transition-all" />
            <X class="relative w-8 h-8 text-white" />
          </button>

          <!-- Like Button -->
          <button @click="swipeRight" :disabled="isAnimating" class="group relative w-20 h-20 rounded-full 
                         bg-gradient-to-br from-green-500 to-green-600
                         shadow-lg hover:shadow-xl
                         transform hover:scale-110 active:scale-95
                         transition-all duration-200
                         disabled:opacity-50 disabled:cursor-not-allowed
                         flex items-center justify-center">
            <div class="absolute inset-0 rounded-full bg-green-400/50 
                        blur-xl group-hover:blur-2xl transition-all" />
            <Heart class="relative w-10 h-10 text-white fill-white" />
          </button>

        </div>

      </div>

    </div>
  </AppLayout>
</template>

<style scoped>
@keyframes fade-in {
  from {
    opacity: 0;
    transform: translateY(20px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in {
  animation: fade-in 0.5s ease-out;
}
</style>
