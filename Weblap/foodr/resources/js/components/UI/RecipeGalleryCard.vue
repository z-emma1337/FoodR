<script setup>
import { Clock, Users, Heart } from 'lucide-vue-next'
import { ref, watch } from 'vue'
import { router, usePage } from '@inertiajs/vue3'

const props = defineProps({
  recipe: Object
})

const emit = defineEmits(['openModal'])

const isHovering = ref(false)
const isLiked = ref(false)
const isCheckingLiked = ref(false)
const page = usePage()

const formatTime = (minutes) => {
  if (minutes < 60) return `${minutes} perc`
  const h = Math.floor(minutes / 60)
  const m = minutes % 60
  return m ? `${h}ó ${m}p` : `${h} óra`
}

const getAllergenColor = (allergen) => {
  const colors = {
    'Vegán': 'bg-green-500/30 border-green-400/50 text-green-100',
    'Vegetáriánus': 'bg-lime-500/30 border-lime-400/50 text-lime-100',
    'Glutén': 'bg-amber-500/30 border-amber-400/50 text-amber-100',
    'Tojás': 'bg-yellow-500/30 border-yellow-400/50 text-yellow-100',
    'Tej': 'bg-blue-500/30 border-blue-400/50 text-blue-100',
    'Dió': 'bg-orange-500/30 border-orange-400/50 text-orange-100',
    'Földimogyoró': 'bg-red-500/30 border-red-400/50 text-red-100',
  }

  return colors[allergen] || 'bg-white/20 border-white/30 text-white'
}

const openModal = () => {
  emit('openModal', props.recipe)
}

// Check if recipe is liked when component mounts or recipe changes
watch(() => props.recipe, async (newRecipe) => {
  if (newRecipe) {
    await checkIfLiked()
  }
}, { immediate: true })

const checkIfLiked = async () => {
  if (!props.recipe) return

  isCheckingLiked.value = true
  try {
    const response = await fetch(`/api/kedvencek/check/${props.recipe.id}`, {
      headers: {
        'Accept': 'application/json',
      },
      credentials: 'include'
    })

    if (response.ok) {
      const data = await response.json()
      isLiked.value = data.is_favorite || false
    }
  } catch (error) {
    console.error('Hiba a kedvenc ellenőrzésekor:', error)
    isLiked.value = false
  } finally {
    isCheckingLiked.value = false
  }
}

const handleAddToFavorites = (event) => {
  event.stopPropagation() // Prevent modal from opening
  
  if (!props.recipe) return

  if (!page.props.auth?.user) {
    router.visit('/bejelentkezes')
    return
  }

  router.post('/api/kedvencek', {
    recept_id: props.recipe.id
  }, {
    preserveScroll: true,
    preserveState: true,
    onSuccess: () => {
      console.log('Recept hozzáadva a kedvencekhez!')
      isLiked.value = true
    },
    onError: (errors) => {
      console.error('Hiba:', errors)
      if (errors.recept_id) {
        alert(errors.recept_id)
      } else {
        alert('Ez a recept már a kedvenceid között van!')
      }
    }
  })
}

const handleRemoveFromFavorites = (event) => {
  event.stopPropagation() // Prevent modal from opening
  
  if (!props.recipe) return

  router.delete(`/api/kedvencek/${props.recipe.id}`, {
    preserveScroll: true,
    preserveState: true,
    onSuccess: () => {
      console.log('Recept eltávolítva a kedvencekből')
      isLiked.value = false
    },
    onError: (errors) => {
      console.error('Hiba:', errors)
      alert('Hiba történt!')
    }
  })
}
</script>

<template>
  <div @click="openModal" class="rounded-2xl overflow-hidden cursor-pointer
              bg-gradient-to-br from-accent-300 via-accent-200 to-accent-300
              shadow-lg hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 border-accent-600 border-3">

    <div class="relative aspect-[4/3] sm:aspect-[3/4]">
      <img :src="recipe.kep_url" :alt="recipe.nev" class="w-full h-full object-cover" />
      <div class="absolute inset-0 bg-gradient-to-b
                  from-slate-900/70 via-transparent to-slate-900/70" />

      <div v-if="recipe.allergenek && recipe.allergenek.length > 0"
        class="absolute top-3 left-3 right-3 flex flex-wrap gap-1.5 ">
        <span v-for="allergen in recipe.allergenek.slice(0, 4)" :key="allergen" :class="[
          'text-xs font-bold px-2.5 py-1 rounded-full shadow-lg backdrop-blur-sm',
          getAllergenColor(allergen)
        ]">
          {{ allergen }}
        </span>
        <span v-if="recipe.allergenek.length > 4" class="text-xs font-bold px-2.5 py-1 rounded-full shadow-lg backdrop-blur-sm
                     bg-slate-700/90 text-white">
          +{{ recipe.allergenek.length - 4 }}
        </span>
      </div>

      <div class="absolute bottom-0 left-0 right-0 p-3">
        <h3 class="text-base sm:text-lg font-bold text-white drop-shadow-lg line-clamp-2">
          {{ recipe.nev }}
        </h3>
      </div>
    </div>

    <div class="p-3 sm:p-4 space-y-2 justify-items-center">
      <div class="flex gap-2 text-xs sm:text-sm flex-wrap">
        <div class="flex items-center gap-1.5 bg-slate-700/20 rounded-full px-2.5 py-1">
          <Clock class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-slate-700" />
          <span class="text-slate-700 font-semibold">{{ formatTime(recipe.ido) }}</span>
        </div>
        <div class="flex items-center gap-1.5 bg-slate-700/20 rounded-full px-2.5 py-1">
          <Users class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-slate-700" />
          <span class="text-slate-700 font-semibold">{{ recipe.adag }} adag</span>
        </div>
      </div>

      <div class="flex gap-2 items-center w-full">
        <button @click="openModal" class="flex-1 py-2 rounded-full bg-brand-700 text-accent-200 
                                   hover:bg-brand-800 transition-all hover:scale-[1.02]
                                   font-medium shadow-md flex items-center justify-center gap-2">
          Részletek
        </button>

        <button v-if="!isLiked" @click="handleAddToFavorites" :disabled="isCheckingLiked"
          @mouseenter="isHovering = true" @mouseleave="isHovering = false" 
          class="p-3 rounded-full bg-accent-400 hover:bg-accent-400/50 
                 text-brand-700 font-bold shadow-md
                 transition-all hover:scale-[1.02]
                 flex items-center justify-center
                 disabled:opacity-50 disabled:cursor-not-allowed">
          <Heart v-if="isHovering" :stroke-width="2.5" fill="currentColor"
            class="w-5 h-5 text-brand-700 transition-all" />
          <Heart v-else :stroke-width="2.5" class="w-5 h-5" />
        </button>

        <button v-else @click="handleRemoveFromFavorites" 
          @mouseenter="isHovering = true" @mouseleave="isHovering = false" 
          class="p-3 rounded-full bg-accent-400 hover:bg-accent-400/50 
                 text-brand-700 font-bold shadow-md
                 transition-all hover:scale-[1.02]
                 flex items-center justify-center
                 disabled:opacity-50 disabled:cursor-not-allowed">
          <Heart v-if="isHovering" :stroke-width="2.5" 
            class="w-5 h-5 text-brand-700 transition-all" />
          <Heart v-else :stroke-width="2.5" fill="currentColor"
            class="w-5 h-5 text-brand-700 transition-all" />
        </button>
      </div>
    </div>

  </div>
</template>