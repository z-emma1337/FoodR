<script setup>
import { Clock, ShoppingBasket, Users, Heart, HeartCrack, MessageCircleMore } from 'lucide-vue-next'
import { ref, computed, watch, onMounted } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import RecipeModal from './RecipeModal.vue'

const props = defineProps({
  recipe: Object,
  visible: Boolean
})

const emit = defineEmits(['update:visible', 'addToFavorites', 'removeFromFavorites', 'openModal'])

const isLiked = ref(false)
const isCheckingLiked = ref(false)
const page = usePage()

const dialogVisible = computed({
  get: () => props.visible,
  set: (value) => emit('update:visible', value)
})

onMounted(() => {
  if (props.recipe) checkIfLiked()
})

watch(() => props.visible, async (newVal) => {
  if (newVal && props.recipe) {
    await checkIfLiked()
  }
})

const checkIfLiked = async () => {
  if (props.recipe.liked == 1) {
    isLiked.value = true

  }
  else {
    isLiked.value = false
  }
}

const formatTime = (minutes) => {
  if (minutes < 60) return `${minutes} perc`
  const h = Math.floor(minutes / 60)
  const m = minutes % 60
  return m ? `${h}ó ${m}p` : `${h} óra`
}

const getAllergenColor = (allergen) => {
  const colors = {
    'Hús': 'bg-red-500/30 border-red-400/50 text-green-100',
    'Glutén': 'bg-amber-500/30 border-amber-400/50 text-amber-100',
    'Tojás': 'bg-yellow-500/30 border-yellow-400/50 text-yellow-100',
    'Tej': 'bg-blue-500/30 border-blue-400/50 text-blue-100',
    'Dió': 'bg-orange-500/30 border-orange-400/50 text-orange-100',
    'Földimogyoró': 'bg-red-500/30 border-red-400/50 text-red-100',
  }
  return colors[allergen] || 'bg-white/20 border-white/30 text-white'
}

const handleAddToFavorites = () => {
  if (!props.recipe) return

  if (!page.props.auth?.user) {
    router.visit('/bejelentkezes')
    return
  }

  router.post('/interakcio/like', { recept_id: props.recipe.id }, {
    preserveScroll: true,
    preserveState: true,
    onSuccess: () => {
      isLiked.value = true
      emit('addToFavorites', props.recipe)
    },
    onError: (errors) => {
      console.error(errors)
    }
  })
}

const handleRemoveFromFavorites = async () => {
  if (!props.recipe) return

  if (!page.props.auth?.user) {
    router.visit('/bejelentkezes')
    return
  }

  router.post('/interakcio/unlike', { recept_id: props.recipe.id }, {
    preserveScroll: true,
    preserveState: true,
    onSuccess: () => {
      isLiked.value = false
      emit('removed', props.recipe.id)
    },
    onError: (errors) => {
      console.error(errors)
      alert(errors.recept_id ? errors.recept_id : 'Ez a recept már a kedvenceid között van!')
    }
  })
}


const recipeModalOpen = ref(false)
function OpenRecipeModal() {
  recipeModalOpen.value = true;
}

function CloseRecipeModal() {
  recipeModalOpen.value = false;
}
</script>

<template>
  <RecipeModal :open="recipeModalOpen" :recipe="recipe" @close="CloseRecipeModal" />
  <div class="rounded-2xl overflow-hidden 
              bg-gradient-to-br from-accent-300 via-accent-200 to-accent-300
              shadow-lg hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 
              border-accent-600 border-[3px] flex flex-col group w-full h-full">

    <div @click="OpenRecipeModal"
      class="cursor-pointer relative w-full aspect-[4/3] sm:aspect-[3/4] flex-shrink-0 overflow-hidden">
      <img :src="recipe.kep_url" :alt="recipe.nev" class="absolute inset-0 w-full h-full object-cover 
                  transition-transform duration-500 ease-out 
                  group-hover:scale-110 origin-center" />
      <div
        class="absolute inset-0 bg-gradient-to-b from-slate-900/70 via-transparent to-slate-900/70 transition-opacity duration-300 group-hover:opacity-80" />

      <div v-if="recipe.allergenek?.length" class="absolute top-3 left-3 right-3 flex flex-wrap gap-1.5 z-10">
        <span v-for="allergen in recipe.allergenek.slice(0, 4)" :key="allergen"
          :class="['text-xs font-bold px-2.5 py-1 rounded-full shadow-lg backdrop-blur-sm', getAllergenColor(allergen)]">
          {{ allergen }}
        </span>
        <span v-if="recipe.allergenek.length > 4"
          class="text-xs font-bold px-2.5 py-1 rounded-full shadow-lg backdrop-blur-sm bg-slate-700/90 text-white">
          +{{ recipe.allergenek.length - 4 }}
        </span>

      </div>

      <div class="absolute bottom-0 left-0 right-0 z-10 p-4 flex items-end justify-between gap-3
            bg-gradient-to-t from-black/80 via-black/40 to-transparent">

        <h3 class="text-base sm:text-lg font-bold text-white drop-shadow-lg line-clamp-2 leading-snug flex-1">
          {{ recipe.nev }}
        </h3>

        <span class="flex items-center gap-1 rounded-full
               text-white text-sm font-semibold shadow-lg">
          <Heart class="w-6 h-6 text-brand-600 fill-brand-400" />
          {{ recipe.likedb }}
        </span>
                <span class="flex items-center gap-1 rounded-full
               text-white text-sm font-semibold shadow-lg">
          <MessageCircleMore class="w-6 h-6 text-slate-600 fill-white" />
          {{ recipe.kommentdb }}
        </span>

      </div>
    </div>

    <div class="p-3 sm:p-4 flex flex-col min-h-[140px] justify-between">
      <div class="flex gap-2 text-xs sm:text-sm flex-wrap items-center justify-center mb-4">
        <div class="flex items-center gap-1.5 bg-slate-700/20 rounded-full px-2.5 py-1">
          <Clock class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-slate-700" />
          <span class="text-slate-700 font-semibold">{{ formatTime(recipe.ido) }}</span>
        </div>
        <div class="flex items-center gap-1.5 bg-slate-700/20 rounded-full px-2.5 py-1">
          <Users class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-slate-700" />
          <span class="text-slate-700 font-semibold">{{ recipe.adag }} adag</span>
        </div>
        <div class="flex items-center gap-1.5 bg-slate-700/20 rounded-full px-2.5 py-1">
          <ShoppingBasket class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-slate-700" />
          <span class="text-slate-700 font-semibold">{{ recipe.hozzavalok.length }} hozzávaló</span>
        </div>
      </div>

      <div class="flex gap-2 mt-auto">
<button @click.stop="OpenRecipeModal"
  class="flex-1 py-2.5 sm:py-3 rounded-3xl button-brand hover:!scale-[1.02] font-medium shadow-md flex items-center justify-center gap-2">
  Részletek
</button>

        <button v-if="isLiked" @click.stop="handleRemoveFromFavorites"
          class="w-12 h-12 p-2 rounded-full button-brand shadow-md flex items-center justify-center">
          <HeartCrack class="w-7 h-7" />
        </button>
        <button v-else @click.stop="handleAddToFavorites"
          class="w-12 h-12 p-2 rounded-full button-brand shadow-md transition-all duration-200 flex items-center justify-center">
          <Heart class="w-7 h-7" />
        </button>
      </div>
    </div>
  </div>
</template>