<!-- resources/js/Components/UI/RecipeCard.vue -->
<script setup>
import { Clock, Users } from 'lucide-vue-next'

const props = defineProps({
  recipe: {
    type: Object,
    required: true
  },
  isDragging: {
    type: Boolean,
    default: false
  },
  dragOffset: {
    type: Object,
    default: () => ({ x: 0, y: 0 })
  },
  rotation: {
    type: Number,
    default: 0
  },
  isBackground: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['dragstart', 'dragmove', 'dragend'])

const formatTime = (minutes) => {
  if (minutes < 60) return `${minutes} perc`
  const hours = Math.floor(minutes / 60)
  const mins = minutes % 60
  return mins > 0 ? `${hours}ó ${mins}p` : `${hours} óra`
}

// Allergén színezés
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
</script>

<template>
  <div
    :class="[
      'absolute inset-0 touch-none',
      isBackground ? 'rounded-3xl overflow-hidden transform scale-95 opacity-50 transition-all duration-300' : 'cursor-grab active:cursor-grabbing'
    ]"
    :style="{
      zIndex: isBackground ? 1 : 2,
      transform: isBackground ? 'scale(0.95)' : `translate(${dragOffset.x}px, ${dragOffset.y}px) rotate(${rotation}deg)`,
      transition: !isBackground && isDragging ? 'none' : 'transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275)'
    }"
    @mousedown.prevent="!isBackground && $emit('dragstart', $event)"
    @touchstart.prevent="!isBackground && $emit('dragstart', $event)">
    
    <!-- Accent Frame -->
    <div class="w-full h-full rounded-3xl overflow-hidden shadow-2xl pointer-events-none">
      <div class="bg-gradient-to-br from-accent-500/80 to-accent-600/80 p-1 w-full h-full">
        
        <!-- Kártya Content -->
        <div class="relative w-full h-full rounded-3xl overflow-hidden 
                    bg-gradient-to-br from-accent-300 via-accent-200 to-accent-300">
          
          <!-- Kép (teljes magasság) -->
          <div class="relative w-full h-full overflow-hidden">
            <img :src="recipe.kep_url" 
                 :alt="recipe.nev"
                 class="w-full h-full object-cover select-none pointer-events-none"
                 draggable="false" />
            
            <!-- Gradient overlay - erősebb alsó rész -->
            <div class="absolute inset-0 bg-gradient-to-b 
                        from-transparent via-transparent to-slate-900/80" />
            
            <!-- Like/Dislike indikátor (csak a felső kártyán) -->
            <div v-if="!isBackground && isDragging" class="absolute inset-0 flex items-center justify-center pointer-events-none">
              <div v-if="dragOffset.x > 50" 
                   class="transform rotate-12 border-8 border-green-500 
                          rounded-2xl px-8 py-4 text-6xl font-bold text-green-500
                          shadow-2xl bg-white/10 backdrop-blur-sm"
                   :style="{ opacity: Math.min(dragOffset.x / 100, 1) }">
                LIKE
              </div>
              <div v-if="dragOffset.x < -50" 
                   class="transform -rotate-12 border-8 border-red-500 
                          rounded-2xl px-8 py-4 text-6xl font-bold text-red-500
                          shadow-2xl bg-white/10 backdrop-blur-sm"
                   :style="{ opacity: Math.min(Math.abs(dragOffset.x) / 100, 1) }">
                DISLIKE
              </div>
            </div>

            <!-- Info Section - lent a képen -->
            <div class="absolute bottom-0 left-0 right-0 p-6 space-y-3">
              
              <!-- Név -->
              <h2 class="text-4xl font-bold text-white leading-tight drop-shadow-lg">
                {{ recipe.nev }}
              </h2>

              <!-- Idő és Adag -->
              <div class="flex items-center gap-3 flex-wrap">
                <div class="flex items-center gap-2 bg-white/20 backdrop-blur-md 
                            rounded-full px-4 py-2 shadow-lg">
                  <Clock class="w-4 h-4 text-white" />
                  <span class="text-sm font-semibold text-white">{{ formatTime(recipe.ido) }}</span>
                </div>
                <div class="flex items-center gap-2 bg-white/20 backdrop-blur-md 
                            rounded-full px-4 py-2 shadow-lg">
                  <Users class="w-4 h-4 text-white" />
                  <span class="text-sm font-semibold text-white">{{ recipe.adag }} adag</span>
                </div>
              </div>

              <!-- Allergének és Diet tagek -->
              <div v-if="recipe.allergenek && recipe.allergenek.length > 0" 
                   class="flex items-center gap-2 flex-wrap">
                <div v-for="allergen in recipe.allergenek" 
                     :key="allergen"
                     :class="[
                       'backdrop-blur-md rounded-full px-3 shadow-md border flex items-center',
                       getAllergenColor(allergen)
                     ]"
                     style="padding-top: 0.25rem; padding-bottom: 0.25rem;">
                  <span class="text-xs font-semibold leading-none">{{ allergen }}</span>
                </div>
              </div>

            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</template>
