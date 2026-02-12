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

const showSuccessMessage = (message) => {
  console.log('✅', message)
  setTimeout(() => {
    dialogVisible.value = false
  }, 500)
}
</script>

<template>
<Dialog
  v-model:visible="dialogVisible"
  modal
  :pt="{
    root: { class: 'max-w-2xl' },
    header: { 
      class: 'bg-gradient-to-br from-brand-900 via-brand-700 to-brand-800 animate-gradient' 
    },
    content: { 
      class: 'bg-gradient-to-br from-accent-300 via-accent-200 to-accent-300 rounded-b-3xl p-6' 
    },
    mask: { 
      class: 'backdrop-blur-md' 
    }
  }"
>
    <template #header>
      <h3 class="text-2xl font-bold text-brand-500 text-outline-shadow">
        {{ recipe?.nev }}
      </h3>
    </template>

    <div class="relative p-1 rounded-3xl overflow-hidden">
      
      <!-- Glow háttér a modalban (ugyanaz mint az oldalon) -->
      <div class="absolute inset-0 pointer-events-none">
      </div>

      <!-- Fő háttér (sidebar stílus) -->
      <div class="relative space-y-6 rounded-3xl p-6
                  bg-gradient-to-br from-accent-300 via-accent-200 to-accent-300
         animate-card-gradient backdrop-blur-xl shadow-xl">

        <!-- Recipe Info -->
        <div class="flex flex-wrap gap-4 text-sm text-slate-700">
          <div class="flex items-center gap-2">
            <Clock :size="18" class="text-brand-600" />
            <span>{{ formatTime(recipe.ido) }}</span>
          </div>

          <div v-if="recipe.adag" class="flex items-center gap-2">
            <Users :size="18" class="text-brand-600" />
            <span>{{ recipe.adag }} adag</span>
          </div>
        </div>

        <!-- Allergens -->
        <div class="flex flex-wrap gap-2">
          <span
            v-for="allergen in recipe.allergenek"
            :key="allergen"
            :class="getAllergenColor(allergen)"
            class="px-3 py-1 rounded-full text-xs font-medium shadow"
          >
            {{ allergen }}
          </span>
        </div>

        <!-- Ingredients -->
        <div>
          <h4 class="text-lg font-semibold mb-3 text-brand-700">Hozzávalók</h4>

          <ul class="space-y-2">
            <li
              v-for="(lepesek, index) in recipe.leiras
                .split(/\d+\.\s*/)
                .filter(x => x.trim().length > 1)"
              :key="index"
              class="flex items-start gap-3 text-slate-800"
            >

              <span>{{ index + 1 }}. {{ lepesek }}</span>
            </li>
          </ul>
        </div>

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
