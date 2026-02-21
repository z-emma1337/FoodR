<script setup>
// RecipeGalleryCard.vue
import { Clock, Users } from 'lucide-vue-next'

const props = defineProps({
  recipe: Object
})

const emit = defineEmits(['openModal'])

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
</script>

<template>
  <div 
    @click="openModal" 
    class="rounded-2xl overflow-hidden cursor-pointer
           bg-gradient-to-br from-accent-300 via-accent-200 to-accent-300
           shadow-lg hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 
           border-accent-600 border-3 flex flex-col group w-full h-full"
  >
    <!-- Kép konténer – zoom wrapper -->
    <div class="relative w-full aspect-[4/3] sm:aspect-[3/4] flex-shrink-0 overflow-hidden">
      <img 
        :src="recipe.kep_url" 
        :alt="recipe.nev" 
        class="absolute inset-0 w-full h-full object-cover 
               transition-transform duration-500 ease-out 
               group-hover:scale-110 origin-center"
      />
      <div class="absolute inset-0 bg-gradient-to-b from-slate-900/70 via-transparent to-slate-900/70 transition-opacity duration-300 group-hover:opacity-80" />

      <!-- Allergének badge-ek -->
      <div 
        v-if="recipe.allergenek && recipe.allergenek.length > 0"
        class="absolute top-3 left-3 right-3 flex flex-wrap gap-1.5 z-10"
      >
        <span 
          v-for="allergen in recipe.allergenek.slice(0, 4)" 
          :key="allergen" 
          :class="[
            'text-xs font-bold px-2.5 py-1 rounded-full shadow-lg backdrop-blur-sm',
            getAllergenColor(allergen)
          ]"
        >
          {{ allergen }}
        </span>
        <span 
          v-if="recipe.allergenek.length > 4" 
          class="text-xs font-bold px-2.5 py-1 rounded-full shadow-lg backdrop-blur-sm bg-slate-700/90 text-white"
        >
          +{{ recipe.allergenek.length - 4 }}
        </span>
      </div>

      <!-- Név – kép alján -->
      <div class="absolute bottom-0 left-0 right-0 p-3 z-10">
        <h3 class="text-base sm:text-lg font-bold text-white drop-shadow-lg line-clamp-2">
          {{ recipe.nev }}
        </h3>
      </div>
    </div>

    <!-- Alsó info + gomb konténer -->
    <div class="p-3 sm:p-4 flex flex-col flex-grow justify-between min-h-[140px]">
      <!-- Idő + adag – fent -->
      <div class="flex gap-2 text-xs sm:text-sm flex-wrap items-center justify-center mb-4">
        <div class="flex items-center gap-1.5 bg-slate-700/20 rounded-full px-2.5 py-1">
          <Clock class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-slate-700" />
          <span class="text-slate-700 font-semibold">{{ formatTime(recipe.ido) }}</span>
        </div>
        <div class="flex items-center gap-1.5 bg-slate-700/20 rounded-full px-2.5 py-1">
          <Users class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-slate-700" />
          <span class="text-slate-700 font-semibold">{{ recipe.adag }} adag</span>
        </div>
      </div>

      <!-- Gomb – alulra ragadva -->
      <button 
        @click.stop="openModal"
        class="w-full py-2.5 sm:py-3 rounded-xl bg-brand-700 text-accent-200 
               hover:bg-brand-800 transition-all hover:scale-[1.02]
               font-medium shadow-md flex items-center justify-center gap-2 mt-auto"
      >
        Részletek
      </button>
    </div>
  </div>
</template>