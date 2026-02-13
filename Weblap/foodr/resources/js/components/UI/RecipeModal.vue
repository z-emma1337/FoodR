<script setup>
import { Clock, Users, ChefHat, X as CloseIcon, Heart, HeartOff, Check } from 'lucide-vue-next'
import Dialog from 'primevue/dialog'
import { computed, ref, watch } from 'vue'
import { router, usePage } from '@inertiajs/vue3'

const props = defineProps({
  recipe: Object,
  visible: Boolean
})

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
  const styles = {
    'Vegán': 'bg-green-500/60 text-slate-900 border border-green-600',
    'Vegetáriánus': 'bg-lime-500/60 text-slate-900 border border-lime-600',
    'Glutén': 'bg-amber-500/60 text-slate-900 border border-amber-600',
    'Tojás': 'bg-yellow-500/60 text-slate-900 border border-yellow-600',
    'Tej': 'bg-blue-500/60 text-slate-900 border border-blue-600',
    'Dió': 'bg-orange-500/60 text-slate-900 border border-orange-600',
    'Földimogyoró': 'bg-red-500/60 text-slate-900 border border-red-600',
  }
  return styles[allergen] || 'bg-slate-500 text-white border border-slate-600'
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

const showSuccessMessage = (message) => {
  console.log('✅', message)
  setTimeout(() => {
    dialogVisible.value = false
  }, 500)
}
</script>

<template>
  <Dialog v-model:visible="dialogVisible" :modal="true" :dismissableMask="true" :draggable="false" :closable="false"
    :style="{ width: '90vw', maxWidth: '900px' }" class="recipe-modal">
    <template #header>
      <h3 class="text-2xl font-bold text-accent-200">
        {{ recipe?.nev }}
      </h3>
    </template>

    <div v-if="recipe" class="space-y-6">

      <div class="relative rounded-3xl overflow-hidden aspect-video sm:aspect-[21/9] shadow-lg">
        <img :src="recipe.kep_url" :alt="recipe.nev" class="w-full h-full object-cover" />
        <div class="absolute inset-0 bg-gradient-to-b from-transparent via-transparent to-slate-900/60" />

        <div class="absolute bottom-0 left-0 right-0 p-4 sm:p-6">
          <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-white drop-shadow-2xl">
            {{ recipe.nev }}
          </h2>
        </div>
      </div>

      <!-- Fő háttér (sidebar stílus) -->
      <div class="relative space-y-6 rounded-3xl p-6
                  bg-gradient-to-br from-accent-300 via-accent-200 to-accent-300
         animate-card-gradient backdrop-blur-xl shadow-xl">
<img :src="recipe.kep_url" alt="Recept kép" class="w-full h-64 object-cover rounded-3xl shadow-lg border-brand-600 border-6">

        <!-- Recipe Info -->
        <div class="flex flex-wrap gap-4 text-sm text-slate-700">
          <div class="flex items-center gap-2">
            <Clock :size="18" class="text-brand-600" />
            <span>{{ formatTime(recipe.ido) }}</span>
          </div>
        </div>
        <div class="flex items-center gap-2 bg-accent-400/40 rounded-2xl px-4 py-3 shadow-md">
          <Users class="w-5 h-5 text-brand-700" />
          <div>
            <p class="text-xs text-slate-700 font-medium">Adagok</p>
            <p class="text-sm font-bold text-slate-900">{{ recipe.adag }} adag</p>
          </div>
        </div>
      </div>

      <div v-if="recipe.allergenek && recipe.allergenek.length > 0">
        <h3 class="text-lg font-bold text-slate-900 mb-3 flex items-center gap-2">
          <ChefHat class="w-5 h-5" />
          Allergének és étkezési típusok
        </h3>
        <div class="flex flex-wrap gap-2">
          <span v-for="allergen in recipe.allergenek" :key="allergen" :class="[
            'px-4 py-2 rounded-2xl font-semibold text-sm shadow-md',
            getAllergenColor(allergen)
          ]">
            {{ allergen }}
          </span>
        </div>
      </div>

      <div>
        <h3 class="text-lg font-bold text-slate-900 mb-3">Leírás</h3>
        <p class="text-slate-800 leading-relaxed text-base">
          {{ recipe.leiras }}
        </p>
      </div>

      <div class="bg-accent-400/30 rounded-3xl p-6 border-2 border-accent-500/40 shadow-md">
        <p class="text-sm text-slate-700 text-center">
          <span class="font-semibold text-slate-900">💡 Tipp:</span>
          A részletes elkészítési útmutató hamarosan elérhető lesz!
        </p>
      </div>

    </div>

    <template #footer>
      <div class="flex flex-col sm:flex-row gap-3">
        <button @click="dialogVisible = false" class="flex-1 p-3 rounded-3xl bg-accent-400/50 hover:bg-accent-400 
                 text-slate-900 font-semibold shadow-md
                 transition-all hover:scale-[1.02]
                 flex items-center justify-center gap-2">
          <CloseIcon class="w-4 h-4" />
          Bezárás
        </button>

        <button v-if="!isLiked" @click="handleAddToFavorites" :disabled="isCheckingLiked" class="flex-1 p-3 rounded-3xl bg-brand-700 hover:bg-brand-800 
                 text-accent-200 font-semibold shadow-md
                 transition-all hover:scale-[1.02]
                 flex items-center justify-center gap-2
                 disabled:opacity-50 disabled:cursor-not-allowed">
          <Heart class="w-4 h-4" />
          Kedvencekhez
        </button>

        <button v-else @click="handleRemoveFromFavorites" class="flex-1 p-3 rounded-3xl bg-green-600 hover:bg-red-600 
                 text-white font-semibold shadow-md
                 transition-all hover:scale-[1.02]
                 flex items-center justify-center gap-2 group">
          <Check class="w-4 h-4 group-hover:hidden" />
          <HeartOff class="w-4 h-4 hidden group-hover:block" />
          <span class="group-hover:hidden">Kedvencekben</span>
          <span class="hidden group-hover:block">Eltávolítás</span>
        </button>
      </div>
    </template>
  </Dialog>
</template>

<style>
.recipe-modal .p-dialog {
  border-radius: 1rem !important;
  overflow: hidden;

  border: 5px solid #f97316 !important;
}

.recipe-modal .p-dialog-header {
  background: linear-gradient(to bottom right,
      #fdba74,
      #fed7aa,
      #fdba74);
  border-bottom: none;
  padding: 1.25rem 1.5rem;
  border-radius: 1rem 1rem 0 0;
  border-color: #f97316;
}

.recipe-modal .p-dialog-content {
  background: linear-gradient(to bottom right,
      #fdba74,
      #fed7aa,
      #fdba74);
  padding: 1.5rem;
  max-height: 60vh;
  overflow-y: auto;
  border-color: #f97316;
}

.recipe-modal .p-dialog-footer {
  background: linear-gradient(to bottom right,
      #fed7aa,
      #fdba74);
  border-top: none;
  padding: 1.25rem 1.5rem;
  border-radius: 0 0 1rem 1rem;
  border-color: #f97316;
}

.recipe-modal .p-dialog-mask {
  background-color: rgba(0, 0, 0, 0.6);
  backdrop-filter: blur(4px);
}

@media (max-width: 640px) {
  .recipe-modal .p-dialog {
    border-radius: 1rem !important;
    border-width: 3px !important;
  }

  .recipe-modal .p-dialog-content {
    padding: 1rem;
    max-height: 65vh;
  }

  .recipe-modal .p-dialog-header,
  .recipe-modal .p-dialog-footer {
    padding: 1rem 1.25rem;
  }
}

.recipe-modal .p-dialog-content::-webkit-scrollbar {
  width: 10px;
}

.recipe-modal .p-dialog-content::-webkit-scrollbar-track {
  background: rgba(0, 0, 0, 0.05);
  border-radius: 10px;
}

.recipe-modal .p-dialog-content::-webkit-scrollbar-thumb {
  background: rgba(0, 0, 0, 0.2);
  border-radius: 10px;
}

.recipe-modal .p-dialog-content::-webkit-scrollbar-thumb:hover {
  background: rgba(0, 0, 0, 0.4);
}

.recipe-modal .p-dialog-header,
.recipe-modal .p-dialog-content,
.recipe-modal .p-dialog-footer {
  background-size: 200% 200%;
  animation: gradient 18s ease infinite;
}

@keyframes gradient {
  0% {
    background-position: 0% 50%;
  }

  50% {
    background-position: 100% 50%;
  }

  100% {
    background-position: 0% 50%;
  }
}

.recipe-modal .p-dialog-header-close {
  display: none !important;
}
</style>