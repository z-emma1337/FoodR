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

  </Dialog>
</template>
