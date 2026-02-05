<script setup>
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
    'Vegán': 'bg-green-500/90 text-white',
    'Vegetáriánus': 'bg-lime-500/90 text-white',
    'Glutén': 'bg-amber-500/90 text-white',
    'Tojás': 'bg-yellow-500/90 text-slate-800',
    'Tej': 'bg-blue-500/90 text-white',
    'Dió': 'bg-orange-500/90 text-white',
    'Földimogyoró': 'bg-red-500/90 text-white',
  }
  return colors[allergen] || 'bg-slate-500/90 text-white'
}

const openModal = () => {
  emit('openModal', props.recipe)
}
</script>

<template>
  <div @click="openModal"
       class="rounded-2xl overflow-hidden cursor-pointer
              bg-gradient-to-br from-accent-300 via-accent-200 to-accent-300
              shadow-lg hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">

    <!-- IMAGE -->
    <div class="relative aspect-[4/3] sm:aspect-[3/4]">
      <img
        :src="recipe.kep_url"
        :alt="recipe.nev"
        class="w-full h-full object-cover"
      />
      <div class="absolute inset-0 bg-gradient-to-b
                  from-transparent via-transparent to-slate-900/70" />
      
      <!-- Allergen badges on image -->
      <div v-if="recipe.allergenek && recipe.allergenek.length > 0" 
           class="absolute top-3 left-3 right-3 flex flex-wrap gap-1.5">
        <span v-for="allergen in recipe.allergenek.slice(0, 4)" 
              :key="allergen"
              :class="[
                'text-xs font-bold px-2.5 py-1 rounded-full shadow-lg backdrop-blur-sm',
                getAllergenColor(allergen)
              ]">
          {{ allergen }}
        </span>
        <span v-if="recipe.allergenek.length > 4"
              class="text-xs font-bold px-2.5 py-1 rounded-full shadow-lg backdrop-blur-sm
                     bg-slate-700/90 text-white">
          +{{ recipe.allergenek.length - 4 }}
        </span>
      </div>

      <!-- Recipe name overlay -->
      <div class="absolute bottom-0 left-0 right-0 p-3">
        <h3 class="text-base sm:text-lg font-bold text-white drop-shadow-lg line-clamp-2">
          {{ recipe.nev }}
        </h3>
      </div>
    </div>

    <!-- INFO -->
    <div class="p-3 sm:p-4 space-y-2">
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
      
      <p class="text-xs sm:text-sm text-slate-600 hover:text-slate-700 transition">
        Kattints a részletekért →
      </p>
    </div>

  </div>
</template>