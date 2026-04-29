<script setup>
import { Clock, ShoppingBasket, Users } from 'lucide-vue-next'
import { ref, onMounted, onUnmounted } from 'vue'
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
  },
  nextCardScale: {
    type: Number,
    default: 0.95
  },
  nextCardOpacity: {
    type: Number,
    default: 0.5
  }
})

const emit = defineEmits(['dragstart', 'dragmove', 'dragend'])

const formatTime = (minutes) => {
  if (minutes < 60) return `${minutes} perc`
  const hours = Math.floor(minutes / 60)
  const mins = minutes % 60
  return mins > 0 ? `${hours}ó ${mins}p` : `${hours} óra`
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

const swipeAnimacio = ref(false);

let elrejtesTimer = null;
let ismetlesTimer = null;

const animacioInditas = () => {
  swipeAnimacio.value = true;
  elrejtesTimer = setTimeout(() => {
    swipeAnimacio.value = false;
  }, 3000);

  ismetlesTimer = setInterval(() => {
    swipeAnimacio.value = true;
    elrejtesTimer = setTimeout(() => {
      swipeAnimacio.value = false;
    }, 3000);
  }, 15000);
};

onMounted(() => {
  if (!props.isBackground) animacioInditas();
});

onUnmounted(() => {
  clearTimeout(elrejtesTimer);
  clearInterval(ismetlesTimer);
});

</script>

<template>
  <div :class="[
    'absolute inset-0 touch-none recipe-card',
    isBackground ? 'rounded-3xl overflow-hidden' : 'cursor-grab active:cursor-grabbing'
  ]" :style="{
    top: '0',
    left: '0',
    right: '0',
    bottom: '0',
    maxHeight: '100%',
    maxWidth: '100%',
    zIndex: isBackground ? 1 : 2,
    transform: isBackground
      ? `scale(${nextCardScale})`
      : `translate(${dragOffset.x}px, ${dragOffset.y}px) rotate(${rotation}deg)`,
    willChange: isBackground ? 'transform, opacity' : 'transform',
    opacity: isBackground ? nextCardOpacity : 1,
    transition: !isBackground && isDragging
      ? 'none'
      : isBackground
        ? 'transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.3s ease-out'
        : 'transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275)'
  }" @mousedown.prevent="!isBackground && $emit('dragstart', $event)"
    @touchstart.prevent="!isBackground && $emit('dragstart', $event)">

    <img v-if="!isBackground && !isDragging && swipeAnimacio"
      class="Swipe absolute bottom-20 left-1/2 -translate-x-1/2 z-10 pointer-events-none select-none"
      src="/imgs/swipe.png" alt="" />


    <div :title="recipe.nev" class="relative w-full h-full rounded-3xl overflow-hidden 
                    bg-gradient-to-br from-accent-300 via-accent-200 to-accent-300  border-accent-600 border-6">

      <div class="relative w-full h-full overflow-hidden">
        <img :src="recipe.kep_url" :alt="recipe.nev" class="w-full h-full object-cover select-none pointer-events-none"
          draggable="false" />

        <div class="absolute inset-0 bg-gradient-to-b 
                        from-transparent via-transparent to-slate-900/80" />


        <div v-if="!isBackground && isDragging"
          class="absolute inset-0 flex items-center justify-center pointer-events-none">

          <div v-if="dragOffset.x > 50" class="transform rotate-12 border-8 border-green-500 
                          rounded-3xl px-8 py-4 text-6xl font-bold text-green-500
                          shadow-2xl bg-white/10 backdrop-blur-sm"
            :style="{ opacity: Math.min(dragOffset.x / 100, 1) }">
            LIKE
          </div>
          <div v-if="dragOffset.x < -50" class="transform -rotate-12 border-8 border-red-500 
                          rounded-3xl px-8 py-4 text-6xl font-bold text-red-500
                          shadow-2xl bg-white/10 backdrop-blur-sm"
            :style="{ opacity: Math.min(Math.abs(dragOffset.x) / 100, 1) }">
            DISLIKE
          </div>

        </div>



        <div class="absolute bottom-0 left-0 right-0 p-3 sm:p-6 space-y-2 sm:space-y-3">


          <h2 class="text-2xl sm:text-4xl font-bold text-white leading-tight drop-shadow-lg">
            {{ recipe.nev }}
          </h2>




          <div class="flex items-center gap-3 flex-wrap">

            <div class="flex items-center gap-2 bg-white/20 backdrop-blur-md 
                            rounded-3xl px-4 py-2 shadow-lg">
              <Clock class="w-4 h-4 text-white" />
              <span class="text-sm font-semibold text-white">{{ formatTime(recipe.ido) }}</span>
            </div>
            <div class="flex items-center gap-2 bg-white/20 backdrop-blur-md 
                            rounded-3xl px-4 py-2 shadow-lg">
              <Users class="w-4 h-4 text-white" />
              <span class="text-sm font-semibold text-white">{{ recipe.adag }} adag</span>
            </div>
            <div class="flex items-center gap-2 bg-white/20 backdrop-blur-md 
                            rounded-3xl px-4 py-2 shadow-lg">
              <ShoppingBasket class="w-4 h-4 text-white" />
              <span class="text-sm font-semibold text-white">{{ recipe.hozzavalok.length }} hozzávaló</span>
            </div>
          </div>

          <div v-if="recipe.allergenek && recipe.allergenek.length > 0"
            class="flex items-center gap-1.5 sm:gap-2 flex-wrap">
            <div v-for="allergen in recipe.allergenek" :key="allergen" :class="[
              'backdrop-blur-md rounded-3xl px-3 shadow-md border flex items-center',
              getAllergenColor(allergen)
            ]" style="padding-top: 0.2rem; padding-bottom: 0.2rem;">
              <span class="text-[10px] sm:text-xs font-semibold leading-none">{{ allergen }}</span>

            </div>


          </div>

        </div>
      </div>

    </div>
  </div>

</template>