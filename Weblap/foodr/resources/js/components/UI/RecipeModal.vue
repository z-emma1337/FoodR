<script setup>
import { Clock, Users, ChefHat, X as CloseIcon, Heart, HeartCrack, Check } from 'lucide-vue-next'
import { computed, ref, watch } from 'vue'
import { router, usePage } from '@inertiajs/vue3'

const props = defineProps({
  recipe: Object,
  visible: Boolean,
  open: { type: Boolean, required: true }
})

const emit = defineEmits(['addToFavorites', 'removeFromFavorites', 'close'])

const isLiked = ref(props.recipe?.liked ?? false)
const isCheckingLiked = ref(false)
const page = usePage()
const adagInput = ref(1);



watch(() => props.recipe, (newRecipe) => {
  if (newRecipe) {
    isLiked.value = newRecipe.liked ?? false
    adagInput.value = newRecipe.adag
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
    console.error(error)
    isLiked.value = false
  } finally {
    isCheckingLiked.value = false
  }
}

const formatTime = (minutes) => {
  if (minutes < 60) return `${minutes} perc`
  const h = Math.floor(minutes / 60)
  const m = minutes % 60
  return m ? `${h} óra ${m} perc` : `${h} óra`
}

const getAllergenColor = (allergen) => {
  const colors = {
    'Vegán': 'bg-green-500 text-white',
    'Vegetáriánus': 'bg-lime-500 text-white',
    'Glutén': 'bg-amber-500 text-white',
    'Tojás': 'bg-yellow-500 text-white',
    'Tej': 'bg-blue-500 text-white',
    'Dió': 'bg-orange-500 text-white',
    'Földimogyoró': 'bg-red-500 text-white',
  }
  return colors[allergen] || 'bg-slate-500 text-white'
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
    }
  })
}



</script>

<template>
  <Transition name="modal-fade">
    <div v-if="props.open"
      class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4"
      @click.self="emit('close')">
      <div
        class="relative w-full max-w-lg rounded-3xl shadow-2xl border-accent-600 border-6 overflow-hidden foodr-scrollbar">
        <div class="bg-gradient-to-br from-accent-300 via-accent-200 to-accent-300 flex flex-col max-h-[85vh]">

          <div class="flex items-center justify-between px-8 pt-8 pb-4 shrink-0">
            <h2 class="text-2xl font-bold text-slate-900">
              {{ recipe.nev }}
            </h2>
            <button @click="emit('close')"
              class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-200">
              <CloseIcon class="w-10 h-10 text-brand-600" :stroke-width="3" />
            </button>
          </div>

          <div class="overflow-y-auto px-8 pb-8 space-y-6">

            <div class="flex flex-col items-center gap-4 mb-2">
              <img :src="recipe.kep_url" alt="Recept kép"
                class="w-full h-64 object-cover rounded-3xl shadow-lg border-accent-600 border-6">
            </div>


            <div class="flex flex-wrap gap-4 text-sm text-slate-700 mb-0">
              <div class="flex items-center gap-2">
                <Clock :size="18" class="text-brand-600" />
                <span>{{ formatTime(recipe.ido) }}</span>
              </div>
              <div class="flex items-center gap-2">
                <Users :size="18" class="w-5 h-5 text-brand-700" />


                <div class="relative flex items-center w-20 rounded-base">
                  <button type="button" id="" @click="adagInput>1 ? adagInput-- : null"
                    class="text-body bg-brand-600 w-10 text-accent-200 rounded-full font-bold flex items-center justify-center pb-0.5">
                    -
                  </button>
                  <input type="number" id="" v-model.number="adagInput" data-input-counter aria-describedby="helper-text-explanation"
                    class="border-x-0 h-10 placeholder:text-heading text-center w-full bg-neutral-secondary-medium border-default-medium py-2.5 placeholder:text-body"
                    required />
                  <button type="button" id="" @click="adagInput<100 ? adagInput++ : null"
                    class="text-body bg-brand-600 w-10 text-accent-200 rounded-full font-bold flex items-center justify-center pb-0.5">
                    +
                  </button>
                </div>



              </div>
            </div>



            <div v-if="recipe.allergenek && recipe.allergenek.length > 0">
              <div class="flex flex-wrap gap-2 mt-1">
                <span v-for="allergen in recipe.allergenek" :key="allergen" :class="[
                  'px-4 py-2 rounded-3xl font-semibold text-sm shadow-md',
                  getAllergenColor(allergen)
                ]">
                  {{ allergen }}
                </span>
              </div>
            </div>

            <div>
              <h4 class="text-lg font-semibold mb-3 text-brand-700 pt-4">Hozzávalók</h4>

              <div class="space-y-3">
                <div v-for="(hozzavalok, index) in recipe.hozzavalok" :key="index"
                  class="flex items-start gap-3 text-slate-800">
                  <div class="w-2.5 h-2.5 mt-2 bg-brand-600 rounded-full flex-shrink-0"></div>

                  <span class="leading-relaxed">
                    {{ hozzavalok.nev }} <span>{{ Math.round(hozzavalok.adag/recipe.adag)*adagInput }}g</span>
                  </span>
                </div>
              </div>
            </div>



            <div>
              <h4 class="text-lg font-semibold mb-3 text-brand-700 pt-4">Leírás</h4>

              <div class="space-y-6">
                <div v-for="(lepes, index) in recipe.leiras
                  .split(/\d+\.\s*/)
                  .filter(x => x.trim().length > 1)" :key="index" class="flex items-center gap-2 text-slate-800">
                  <div
                    class="w-7 h-7 flex-shrink-0 bg-brand-600 text-accent-200 font-semibold rounded-full flex items-center justify-center text-sm">
                    {{ index + 1 }}
                  </div>

                  <p class="leading-relaxed">{{ lepes.trim() }}</p>
                </div>
              </div>
            </div>





          </div>

          <div class="flex items-center justify-between px-8 pt-8 pb-4 shrink-0">
            <button v-if="isLiked == 0 || isLiked == 2" @click="handleAddToFavorites" :disabled="isCheckingLiked" class="w-full p-2 rounded-full bg-brand-700 hover:bg-brand-400/50 
                 text-accent-400 font-bold shadow-md
                 transition-all hover:scale-[1.1]
                 flex items-center justify-center gap-2
                 disabled:opacity-50 disabled:cursor-not-allowed">
              <Heart :stroke-width="2.5" class="w-15 h-15 text-accent-300 pt-0.5 transition-all" />
            </button>

            <button v-else @click="handleRemoveFromFavorites" class="w-full p-2 rounded-full bg-brand-700 hover:bg-brand-400/50 
                 text-accent-400 font-bold shadow-md
                 transition-all hover:scale-[1.1]
                 flex items-center justify-center gap-2
                 disabled:opacity-50 disabled:cursor-not-allowed">
              <HeartCrack :stroke-width="2.5" class="w-15 h-15 text-accent-300 pt-0.5 transition-all" />
            </button>
          </div>

        </div>

      </div>

    </div>


  </Transition>
</template>