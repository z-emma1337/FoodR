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
  root: { class: 'max-w-2xl !border-0 !shadow-none !bg-transparent' },
  header: { 
    class: 'bg-gradient-to-br from-brand-900 via-brand-700 to-brand-800 animate-gradient rounded-t-3xl !border-0' 
  },
  footer: { 
    class: 'bg-gradient-to-br from-brand-900 via-brand-700 to-brand-800 animate-gradient rounded-b-3xl !border-0 !p-4' 
  },
  content: { 
    class: 'bg-gradient-to-br from-accent-300 via-accent-200 to-accent-300 p-6 !border-0' 
  },
  mask: { 
    class: 'backdrop-blur-md' 
  }
}"
>
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
          <h4 class="text-lg font-semibold mb-3 text-brand-700">Leírás</h4>

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

    <template #footer>
      <div class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-center">
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
