<script setup>
import { Clock, Users, ChefHat, X as CloseIcon, Heart, HeartCrack, Check } from 'lucide-vue-next'
import Dialog from 'primevue/dialog'
import { computed, ref, watch } from 'vue'
import { router, usePage } from '@inertiajs/vue3'

const props = defineProps({
  recipe: Object,
  visible: Boolean
})
const isHovering = ref(false);

const emit = defineEmits(['update:visible', 'addToFavorites', 'removeFromFavorites'])

const isLiked = ref(false)
const isCheckingLiked = ref(false)
const page = usePage()

const dialogVisible = computed({
  get: () => props.visible,
  set: (value) => emit('update:visible', value)
})

watch(() => props.visible, async (newVal) => {
  if (newVal && props.recipe) {
    await checkIfLiked()
  }
})

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
    'Tojás': 'bg-yellow-500 text-slate-800',
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

  router.post('/api/kedvencek', {
    recept_id: props.recipe.id
  }, {
    preserveScroll: true,
    preserveState: true,
    onSuccess: () => {
      console.log('Recept hozzáadva a kedvencekhez!')
      isLiked.value = true
      emit('addToFavorites', props.recipe)
      showSuccessMessage('Hozzáadva a kedvencekhez! ❤️')
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

const handleRemoveFromFavorites = () => {
  if (!props.recipe) return

  router.delete(`/api/kedvencek/${props.recipe.id}`, {
    preserveScroll: true,
    preserveState: true,
    onSuccess: () => {
      console.log('Recept eltávolítva a kedvencekből')
      isLiked.value = false
      emit('removeFromFavorites', props.recipe)
      showSuccessMessage('Eltávolítva a kedvencekből')
    },
    onError: (errors) => {
      console.error('Hiba:', errors)
      alert('Hiba történt!')
    }
  })
}


</script>

<template>
  <Dialog v-model:visible="dialogVisible" modal :dismissableMask="true" :pt="{
    root: { class: 'max-w-2xl !border-0 !shadow-none !bg-transparent' },
    header: {
      class: 'bg-gradient-to-br from-brand-900 via-brand-700 to-brand-800 animate-gradient rounded-t-3xl !border-0'
    },
    footer: {
      class: 'bg-gradient-to-br from-brand-900 via-brand-700 to-brand-800 animate-gradient rounded-b-3xl !border-0 !p-4  !justify-center'
    },
    content: {
      class: 'bg-gradient-to-br from-accent-300 via-accent-200 to-accent-300 p-6 !border-0 foodr-scrollbar'
    },
    mask: {
      class: 'backdrop-blur-md'
    },
    pcCloseButton: {
      root: {
        class: '!bg-transparent !no-press-transition !text-accent-400 hover:!text-accent-300 !shadow-none !border-0 !w-12 !h-12 [&>svg]:!w-8 [&>svg]:!h-8 [&>svg]:!stroke-[2.5]'
      }
    }
  }">
    <template #header>
      <h3 class="text-2xl font-bold text-accent-200">
        {{ recipe?.nev }}
      </h3>
    </template>

    <div v-if="recipe" class="space-y-6">



      <!-- Fő háttér (sidebar stílus) -->
      <div class="relative space-y-6 rounded-3xl p-6
                  bg-gradient-to-br from-accent-300 via-accent-200 to-accent-300
         animate-card-gradient backdrop-blur-xl shadow-xl">
        <img :src="recipe.kep_url" alt="Recept kép"
          class="w-full h-64 object-cover rounded-3xl shadow-lg border-brand-600 border-6">

        <!-- Recipe Info -->
        <div class="flex flex-wrap gap-4 text-sm text-slate-700">
          <div class="flex items-center gap-2">
            <Clock :size="18" class="text-brand-600" />
            <span>{{ formatTime(recipe.ido) }}</span>
          </div>
          <div class="flex items-center gap-2">
            <Users :size="18" class="w-5 h-5 text-brand-700" />
            <span>{{ recipe.adag }} adag</span>
          </div>
        </div>

        <!-- Allergens on new line -->
        <div v-if="recipe.allergenek && recipe.allergenek.length > 0" class="mt-3">
          <div class="flex flex-wrap gap-2">
            <span v-for="allergen in recipe.allergenek" :key="allergen" :class="[
              'px-4 py-2 rounded-2xl font-semibold text-sm shadow-md',
              getAllergenColor(allergen)
            ]">
              {{ allergen }}
            </span>
          </div>
        </div>
      </div>
    </div>

   


    <!-- LEÍRÁS (lépések) -->
<div>
  <h4 class="text-lg font-semibold mb-3 text-brand-700 pt-4">Leírás</h4>
  
  <div class="space-y-4">
    <div 
      v-for="(lepes, index) in recipe.leiras
        .split(/\d+\.\s*/)
        .filter(x => x.trim().length > 1)" 
      :key="index" 
      class="flex items-start gap-4 text-slate-800"
    >
      <!-- Számozott kör -->
      <div class="w-7 h-7 flex-shrink-0 bg-brand-600 text-accent-200 font-semibold rounded-full flex items-center justify-center text-sm pb-0.5">
        {{ index + 1 }}
      </div>
      
      <p class="leading-relaxed">{{ lepes.trim() }}</p>
    </div>
  </div>
</div>

<!-- HOZZÁVALÓK -->
<div>
  <h4 class="text-lg font-semibold mb-3 text-brand-700 pt-4">Hozzávalók</h4>
  
  <div class="space-y-3">
    <div 
      v-for="(hozzavalok, index) in recipe.hozzavalok" 
      :key="index" 
      class="flex items-start gap-3 text-slate-800"
    >
      <!-- Brand pötty -->
      <div class="w-2.5 h-2.5 mt-2 bg-brand-600 rounded-full flex-shrink-0"></div>
      
      <span class="leading-relaxed">
        {{ hozzavalok.nev }} <span>{{ hozzavalok.adag }}g</span>
      </span>
    </div>
  </div>
</div>


    <template #footer>
      <div class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-center w-full">

        <button v-if="!isLiked" @click="handleAddToFavorites" :disabled="isCheckingLiked"
          @mouseenter="isHovering = true" @mouseleave="isHovering = false" class="w-full p-2 rounded-full bg-accent-400 hover:bg-accent-400/50 
                 text-brand-700 font-bold shadow-md
                 transition-all hover:scale-[1.02]
                 flex items-center justify-center gap-2
                 disabled:opacity-50 disabled:cursor-not-allowed">
          <Heart v-if="isHovering" :stroke-width="2.5" fill="currentColor"
            class="w-15 h-15 text-brand-700 pt-0.5 transition-all" />
          <Heart v-else :stroke-width="2.5" class="w-15 h-15 font-bold pt-0.5" />

        </button>

        <button v-else @click="handleRemoveFromFavorites" @mouseenter="isHovering = true"
          @mouseleave="isHovering = false" class="w-full p-2 rounded-full bg-accent-400 hover:bg-accent-400/50 
           text-brand-700 font-bold shadow-md
           transition-all hover:scale-[1.02]
           flex items-center justify-center gap-2
           disabled:opacity-50 disabled:cursor-not-allowed">
          <Heart v-if="isHovering" :stroke-width="2.5" class="w-15 h-15 text-brand-700 pt-0.5 transition-all" />
          <Heart v-else :stroke-width="2.5" fill="currentColor"
            class="w-15 h-15 text-brand-700 pt-0.5 transition-all" />
        </button>
      </div>
    </template>
  </Dialog>
</template>