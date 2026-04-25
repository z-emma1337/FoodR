<script setup>
import { ref, onMounted, computed, onUnmounted, watch, nextTick } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import { Heart, X, ChefHat, Info } from 'lucide-vue-next'
import AppLayout from '@/layouts/AppLayout.vue'
import RecipeCard from '@/components/UI/RecipeCard.vue'
import RecipeModal from '@/components/UI/RecipeModal.vue'
import { useLoginModal } from '@/composables/useLoginModal'
import { Head } from '@inertiajs/vue3'

const recipes = ref([])
const currentIndex = ref(0)
const isDragging = ref(false)
const dragStartPos = ref({ x: 0, y: 0 })
const dragOffset = ref({ x: 0, y: 0 })
const rotation = ref(0)
const isAnimating = ref(false)
const nextCardScale = ref(0.95)
const nextCardOpacity = ref(0.5)
const shouldShowCurrentCard = ref(true)
const allergen_id = ref([]);

const page = usePage()
const { openLoginModal } = useLoginModal()
const recipeModalOpen = ref(false)

const currentRecipe = computed(() => recipes.value[currentIndex.value])
const nextRecipe = computed(() => recipes.value[currentIndex.value + 1])

const saveInteraction = (type) => {
  if (!currentRecipe.value) return Promise.resolve()

  const endpoint = type === 'like' ? '/interakcio/like' : '/interakcio/dislike'

  return new Promise((resolve) => {
    router.post(endpoint, {
      recept_id: currentRecipe.value.id
    }, {
      preserveScroll: true,
      preserveState: true,
      onSuccess: () => {
        setTimeout(() => {
          router.reload({ only: ['likedCount'], preserveScroll: true, preserveState: true })
        }, 400)
        resolve()
      },
      onError: (errors) => {
        console.error(errors)
        resolve()
      }
    })
  })
}

const requireAuth = (type, completeFn) => {
  if (page.props.auth?.user) return false

  openLoginModal(async () => {
    await saveInteraction(type)
    await completeFn()
  })
  return true
}

const exitCard = async (direction) => {
  dragOffset.value = { x: direction === 'right' ? 1000 : -1000, y: 0 }
  rotation.value = direction === 'right' ? 30 : -30
  nextCardScale.value = 1
  nextCardOpacity.value = 1
  await new Promise(resolve => setTimeout(resolve, 300))
  shouldShowCurrentCard.value = false
  nextCard()
}

const swipeLeft = async () => {
  if (isAnimating.value || !currentRecipe.value) return
  isAnimating.value = true

  if (requireAuth('dislike', () => exitCard('left'))) {
    isAnimating.value = false
    dragOffset.value = { x: 0, y: 0 }
    rotation.value = 0
    return
  }

  await saveInteraction('dislike')
  await exitCard('left')
}

const swipeRight = async () => {
  if (isAnimating.value || !currentRecipe.value) return
  isAnimating.value = true

  if (requireAuth('like', () => exitCard('right'))) {
    isAnimating.value = false
    dragOffset.value = { x: 0, y: 0 }
    rotation.value = 0
    return
  }

  await saveInteraction('like')
  await exitCard('right')
}

const swipeLeftClick = async () => {
  if (isAnimating.value || !currentRecipe.value) return
  isAnimating.value = true

  if (requireAuth('dislike', () => exitCard('left'))) {
    isAnimating.value = false
    return
  }

  await saveInteraction('dislike')
  await exitCard('left')
}

const swipeRightClick = async () => {
  if (isAnimating.value || !currentRecipe.value) return
  isAnimating.value = true

  if (requireAuth('like', () => exitCard('right'))) {
    isAnimating.value = false
    return
  }

  await saveInteraction('like')
  await exitCard('right')
}

const shuffleArray = (array) => {
  const shuffled = [...array]
  for (let i = shuffled.length - 1; i > 0; i--) {
    const j = Math.floor(Math.random() * (i + 1));
    [shuffled[i], shuffled[j]] = [shuffled[j], shuffled[i]]
  }
  return shuffled
}

const receptekBetoltes = async () => {
  try {
    allergen_id.value = await (await fetch("/allergenek/felhasznalo")).json()
  } catch {
    allergen_id.value = []
  }
  const response = await fetch('/recipes')
  const data = await response.json()
  if (allergen_id.value.length === 0) {
    recipes.value = shuffleArray(data)
  } else {
    recipes.value = shuffleArray(data).filter(
      r => r.liked === 0 && !allergen_id.value.some(x => r.allergen_id.includes(x))
    )
  }


  currentIndex.value = 0
  shouldShowCurrentCard.value = true
  isAnimating.value = false
}

onMounted(async () => {
  await receptekBetoltes()

  window.addEventListener('allergenek-frissitve', receptekBetoltes)

  document.addEventListener('mousemove', handleDragMove, { passive: true })
  document.addEventListener('mouseup', handleDragEnd, { passive: true })
  document.addEventListener('touchmove', handleDragMove, { passive: true })
  document.addEventListener('touchend', handleDragEnd, { passive: true })
})
onUnmounted(() => {
  window.removeEventListener('allergenek-frissitve', receptekBetoltes)
  document.removeEventListener('mousemove', handleDragMove)
  document.removeEventListener('mouseup', handleDragEnd)
  document.removeEventListener('touchmove', handleDragMove)
  document.removeEventListener('touchend', handleDragEnd)
})

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

  const clientX = e.type.includes('mouse') ? e.clientX : e.touches[0].clientX

  // CSAK X tengely! Nincs szabad Y mozgás (nem zabálja a memóriát, nincs getBoundingClientRect hívás)
  const deltaX = clientX - dragStartPos.value.x

  dragOffset.value = { x: deltaX, y: 0 }
  rotation.value = deltaX * 0.1

  const dragProgress = Math.min(Math.abs(deltaX) / 200, 1)
  nextCardScale.value = 0.95 + dragProgress * 0.05
  nextCardOpacity.value = 0.5 + dragProgress * 0.5
}

const handleDragEnd = () => {
  if (!isDragging.value || isAnimating.value) return
  isDragging.value = false

  if (Math.abs(dragOffset.value.x) > 100) {
    if (dragOffset.value.x > 0) swipeRight()
    else swipeLeft()
  } else {
    dragOffset.value = { x: 0, y: 0 }
    rotation.value = 0
    nextCardScale.value = 0.95
    nextCardOpacity.value = 0.5
  }
}

const nextCard = () => {
  currentIndex.value++
  nextTick(() => {
    dragOffset.value = { x: 0, y: 0 }
    rotation.value = 0
    nextCardScale.value = 0.95
    nextCardOpacity.value = 0.5
    shouldShowCurrentCard.value = true
    isAnimating.value = false
    if (recipes.value[currentIndex.value + 1]) {
      const img = new Image()
      img.src = recipes.value[currentIndex.value + 1].kep_url
    }
  })
}

</script>

<template>

  <Head title="SwipeR" />
  <div class="relative min-h-screen">
    <!-- swipe hint gradient -->
    <div v-if="dragOffset.x > 50"
      class="fixed inset-0 bg-gradient-to-l from-green-500 via-green-500/50 to-transparent pointer-events-none z-0" />

    <AppLayout class="relative z-10 min-h-screen">
      <div v-if="currentIndex >= recipes.length"
        class="min-h-[calc(100vh-3rem)] flex flex-col items-center justify-center gap-8 py-8">
        <div class="text-center space-y-4 animate-fade-in">
          <div class="w-24 h-24 mx-auto rounded-full bg-accent-400/30 flex items-center justify-center">
            <ChefHat class="w-12 h-12 text-accent-600" />
          </div>
          <h2 class="text-3xl font-bold text-accent-200">🎉 Elfogytak a receptek! 🎉</h2>
          <p class="text-accent-300">Nézd meg a kedvenceidet, vagy gyere vissza később!</p>
        </div>
      </div>

      <div v-else class=" h-full flex flex-col items-center justify-center">
        <div class="flex flex-col items-center gap-2 w-full max-w-md">

          <!-- Kártya konténer -->
          <div class="relative w-[65vh] max-w-[80vw] aspect-[3/4]" @mousedown="handleDragStart"
            @touchstart="handleDragStart">
            <!-- háttér kártya -->
            <RecipeCard v-if="nextRecipe" :recipe="nextRecipe" :is-background="true" :next-card-scale="nextCardScale"
              :next-card-opacity="nextCardOpacity" class="absolute inset-0" />

            <!-- aktuális kártya -->
            <RecipeCard v-if="currentRecipe && shouldShowCurrentCard" :recipe="currentRecipe" :is-dragging="isDragging"
              :drag-offset="dragOffset" :rotation="rotation" class="absolute inset-0" />
          </div>

          <!-- Gombok – picit belelógnak a kártyába -->
          <div class="flex items-center gap-5 z-20">
            <div class="w-20 flex justify-center">
              <button @click="swipeLeftClick" :disabled="isAnimating" class="group relative w-16 h-16 rounded-full
                       bg-gradient-to-br from-red-500 to-red-600
                       shadow-xl hover:shadow-2xl
                       transform hover:scale-110 active:scale-95
                       transition-all duration-200
                       disabled:opacity-50 disabled:cursor-not-allowed
                       flex items-center justify-center">
                <div class="absolute inset-0 rounded-full bg-red-400/50 blur-xl group-hover:blur-2xl transition-all" />
                <X class="relative w-8 h-8 text-white" />
              </button>
            </div>

            <button @click="recipeModalOpen = true" :disabled="!currentRecipe" class="group relative w-14 h-14 rounded-full
                     bg-gradient-to-br from-accent-300 via-accent-200 to-accent-300
                     border-4 border-accent-600
                     shadow-xl hover:shadow-2xl
                     transform hover:scale-110 active:scale-95
                     transition-all duration-200
                     disabled:opacity-50 disabled:cursor-not-allowed
                     flex items-center justify-center">
              <div class="absolute inset-0 rounded-full bg-accent-400/30 blur-xl group-hover:blur-2xl transition-all" />
              <Info class="relative w-7 h-7 text-brand-700" />
            </button>

            <button @click="swipeRightClick" :disabled="isAnimating" class="group relative w-20 h-20 rounded-full
                     bg-gradient-to-br from-green-500 to-green-600
                     shadow-xl hover:shadow-2xl
                     transform hover:scale-110 active:scale-95
                     transition-all duration-200
                     disabled:opacity-50 disabled:cursor-not-allowed
                     flex items-center justify-center">
              <div class="absolute inset-0 rounded-full bg-green-400/50 blur-xl group-hover:blur-2xl transition-all" />
              <Heart class="relative w-10 h-10 text-white fill-white" />
            </button>
          </div>

          <RecipeModal :open="recipeModalOpen" :recipe="currentRecipe" @close="recipeModalOpen = false" />

        </div>
      </div>

    </AppLayout>
  </div>
</template>