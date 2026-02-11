<script setup>
import { Clock, Users, ChefHat } from 'lucide-vue-next'
import Dialog from 'primevue/dialog'
import Button from 'primevue/button'
import { computed } from 'vue'

const props = defineProps({
  recipe: Object,
  visible: Boolean
})

const emit = defineEmits(['update:visible'])

// Computed property for v-model
const dialogVisible = computed({
  get: () => props.visible,
  set: (value) => emit('update:visible', value)
})

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


</script>

<template>
  <Dialog 
    v-model:visible="dialogVisible"
    :modal="true"
    :dismissableMask="true"
    :draggable="false"
    :closable="true"
    :style="{ width: '90vw', maxWidth: '900px' }"
    class="recipe-modal "
  >
    <template #header>
      <div class="flex items-center gap-3">
        <ChefHat class="w-6 h-6 text-brand-600" />
        <span class="text-xl sm:text-2xl font-bold text-slate-900">Recept részletei</span>
      </div>
    </template>

    <div v-if="recipe" class="space-y-6 ">
      
      <!-- Image -->
      <div class="relative rounded-2xl overflow-hidden aspect-video sm:aspect-[21/9]">
        <img :src="recipe.kep_url" 
             :alt="recipe.nev"
             class="w-full h-full object-cover" />
        <div class="absolute inset-0 bg-gradient-to-b 
                    from-transparent via-transparent to-slate-900/60" />
        
        <!-- Title Overlay -->
        <div class="absolute bottom-0 left-0 right-0 p-4 sm:p-6">
          <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-white drop-shadow-2xl">
            {{ recipe.nev }}
          </h2>
        </div>
      </div>

      <!-- Meta Info -->
      <div class="flex flex-wrap gap-3">
        <div class="flex items-center gap-2 bg-accent-400/40 rounded-xl px-4 py-3 shadow">
          <Clock class="w-5 h-5 text-brand-700" />
          <div>
            <p class="text-xs text-slate-700 font-medium">Elkészítési idő</p>
            <p class="text-sm font-bold text-slate-900">{{ formatTime(recipe.ido) }}</p>
          </div>
        </div>
        <div class="flex items-center gap-2 bg-accent-400/40 rounded-xl px-4 py-3 shadow">
          <Users class="w-5 h-5 text-brand-700" />
          <div>
            <p class="text-xs text-slate-700 font-medium">Adagok</p>
            <p class="text-sm font-bold text-slate-900">{{ recipe.adag }} adag</p>
          </div>
        </div>
      </div>

      <!-- Allergens -->
      <div v-if="recipe.allergenek && recipe.allergenek.length > 0">
        <h3 class="text-lg font-bold text-slate-900 mb-3 flex items-center gap-2">
          <ChefHat class="w-5 h-5" />
          Allergének és étkezési típusok
        </h3>
        <div class="flex flex-wrap gap-2">
          <span v-for="allergen in recipe.allergenek" 
                :key="allergen"
                :class="[
                  'px-4 py-2 rounded-full font-semibold text-sm shadow-md',
                  getAllergenColor(allergen)
                ]">
            {{ allergen }}
          </span>
        </div>
      </div>

      <!-- Description -->
      <div>
        <h3 class="text-lg font-bold text-slate-900 mb-3">Leírás</h3>
        <p class="text-slate-800 leading-relaxed text-base">
          {{ recipe.leiras }}
        </p>
      </div>

      <!-- Additional Info Placeholder -->
      <div class="bg-accent-400/30 rounded-2xl p-6 border-2 border-accent-500/40">
        <p class="text-sm text-slate-700 text-center">
          <span class="font-semibold text-slate-900">💡 Tipp:</span> 
          A részletes elkészítési útmutató hamarosan elérhető lesz!
        </p>
      </div>

    </div>

    <template #footer>
      <div class="flex flex-col sm:flex-row gap-3">
        <Button 
          label="Bezárás" 
          severity="secondary"
          @click="dialogVisible = false"
          class="flex-1"
        />
        <Button 
          label="Kedvencekhez adás" 
          icon="pi pi-heart"
          severity="success"
          class="flex-1"
        />
      </div>
    </template>
  </Dialog>
</template>

<style>
/* PrimeVue Dialog customization - MATCHING AppLayout STYLE with ORANGE BORDER */

/* Main Dialog Container - ROUNDED-2XL with BORDER */
.recipe-modal .p-dialog {
  border-radius: 1rem !important; /* rounded-2xl = 1rem (16px) */
  overflow: hidden;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
  border: 3px solid var(--p-orange-700, #c2410c) !important; /* Orange-700 border */
}

/* Header - Orange/Amber Gradient */
.recipe-modal .p-dialog-header {
  background: linear-gradient(to bottom right, 
    var(--color-accent-300), 
    var(--color-accent-200), 
    var(--color-accent-300)
  );
  border-bottom: none;
  padding: 1.25rem;
  border-radius: 1rem 1rem 0 0; /* Match rounded-2xl on top */
}

/* Content - Same Gradient as Sidebars */
.recipe-modal .p-dialog-content {
  background: linear-gradient(to bottom right, 
    var(--color-accent-300), 
    var(--color-accent-200), 
    var(--color-accent-300)
  );
  padding: 1.5rem;
  max-height: 60vh;
  overflow-y: auto;
}

/* Footer - Orange/Amber Gradient */
.recipe-modal .p-dialog-footer {
  background: linear-gradient(to bottom right, 
    var(--color-accent-200), 
    var(--color-accent-300)
  );
  border-top: none;
  padding: 1.25rem;
  border-radius: 0 0 1rem 1rem; /* Match rounded-2xl on bottom */
}

/* Backdrop with Blur */
.recipe-modal .p-dialog-mask {
  background-color: rgba(0, 0, 0, 0.6);
  backdrop-filter: blur(4px);
}

/* Mobile responsiveness */
@media (max-width: 640px) {
  .recipe-modal .p-dialog {
    border-radius: 1rem !important;
    border-width: 2px !important; /* Slightly thinner on mobile */
  }
  
  .recipe-modal .p-dialog-content {
    padding: 1rem;
    max-height: 65vh;
  }
  
  .recipe-modal .p-dialog-header,
  .recipe-modal .p-dialog-footer {
    padding: 1rem;
  }
}

/* Scrollbar styling */
.recipe-modal .p-dialog-content::-webkit-scrollbar {
  width: 8px;
}

.recipe-modal .p-dialog-content::-webkit-scrollbar-track {
  background: rgba(0, 0, 0, 0.05);
  border-radius: 8px;
}

.recipe-modal .p-dialog-content::-webkit-scrollbar-thumb {
  background: rgba(0, 0, 0, 0.2);
  border-radius: 8px;
}

.recipe-modal .p-dialog-content::-webkit-scrollbar-thumb:hover {
  background: rgba(0, 0, 0, 0.4);
}

/* Animate gradient like AppLayout */
.recipe-modal .p-dialog-header,
.recipe-modal .p-dialog-content,
.recipe-modal .p-dialog-footer {
  background-size: 200% 200%;
  animation: gradient 18s ease infinite;
}

@keyframes gradient {
  0% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}
</style>