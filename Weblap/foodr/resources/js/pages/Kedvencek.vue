<script setup>
import { ref, onMounted } from 'vue'
import { Heart, Trash2, Clock, Users, ChefHat } from 'lucide-vue-next'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'

const favorites = ref([])
const isLoading = ref(true)

// Kedvencek betöltése
onMounted(async () => {
  await loadFavorites()
})

const loadFavorites = async () => {
  isLoading.value = true
  try {
    const response = await fetch('/api/kedvencek')
    const data = await response.json()
    favorites.value = data
  } catch (error) {
    console.error('Hiba a kedvencek betöltésekor:', error)
  } finally {
    isLoading.value = false
  }
}

// Kedvenc eltávolítása
const removeFavorite = async (receptId) => {
  try {
    const response = await fetch(`/api/kedvencek/${receptId}`, {
      method: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
      }
    })

    if (response.ok) {
      // Eltávolítás a listából
      favorites.value = favorites.value.filter(f => f.id !== receptId)
    }
  } catch (error) {
    console.error('Hiba a kedvenc törlésekor:', error)
  }
}

const formatTime = (minutes) => {
  if (minutes < 60) return `${minutes} perc`
  const hours = Math.floor(minutes / 60)
  const mins = minutes % 60
  return mins > 0 ? `${hours}ó ${mins}p` : `${hours} óra`
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

const goToSwiper = () => {
  router.visit('/')
}
</script>

<template>
  <AppLayout>
    <div class="min-h-[calc(100vh-6rem)] py-8">
      
      <div class="mb-8 text-center">
        <h1 class="text-4xl font-bold text-accent-200 mb-2 flex items-center justify-center gap-3">
          <Heart class="w-10 h-10 fill-accent-400 text-accent-400" />
          Kedvenc Receptjeim
        </h1>
        <p class="text-accent-300">
          {{ favorites.length }} mentett recept
        </p>
      </div>

      <div v-if="isLoading" class="flex justify-center items-center py-20">
        <div class="animate-spin rounded-full h-16 w-16 border-4 border-accent-400 border-t-transparent"></div>
      </div>

      <div v-else-if="favorites.length === 0" 
           class="text-center space-y-6 py-20 animate-fade-in">
        <div class="w-24 h-24 mx-auto rounded-full bg-accent-400/30 
                    flex items-center justify-center">
          <Heart class="w-12 h-12 text-accent-500" />
        </div>
        <div>
          <h2 class="text-2xl font-bold text-accent-200 mb-2">
            Még nincs kedvenc recepted
          </h2>
          <p class="text-accent-300 mb-6">
            Kezdd el böngészni a recepteket és mentsd el a kedvenceidet!
          </p>
          <button 
            @click="goToSwiper"
            class="px-6 py-3 bg-gradient-to-r from-accent-500 to-accent-600 
                   hover:from-accent-600 hover:to-accent-700
                   text-white rounded-xl font-semibold 
                   transform hover:scale-105 transition-all shadow-lg">
            Receptek böngészése
          </button>
        </div>
      </div>

      <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-6 animate-fade-in">
        
        <div v-for="recipe in favorites" 
             :key="recipe.id"
             class="group relative rounded-3xl overflow-hidden shadow-xl 
                    transform hover:scale-[1.02] transition-all duration-300">
          
          <div class="bg-gradient-to-br from-accent-500/80 to-accent-600/80 p-1 h-full">
            <div class="relative h-full rounded-3xl overflow-hidden 
                        bg-gradient-to-br from-accent-300 via-accent-200 to-accent-300">
              
              <div class="relative h-64 overflow-hidden">
                <img :src="recipe.kep_url" 
                     :alt="recipe.nev"
                     class="w-full h-full object-cover transition-transform duration-300
                            group-hover:scale-110" />
                
                <div class="absolute inset-0 bg-gradient-to-b 
                            from-transparent via-transparent to-slate-900/60" />
                
                <button 
                  @click="removeFavorite(recipe.id)"
                  class="absolute top-4 right-4 p-3 rounded-full 
                         bg-red-500/80 hover:bg-red-600 
                         backdrop-blur-sm shadow-lg
                         transform hover:scale-110 transition-all
                         group/delete">
                  <Trash2 class="w-5 h-5 text-white" />
                </button>

                <div class="absolute bottom-0 left-0 right-0 p-4">
                  <h3 class="text-2xl font-bold text-white drop-shadow-lg">
                    {{ recipe.nev }}
                  </h3>
                </div>
              </div>

              <div class="p-5 space-y-3">
                
                <p class="text-slate-700 line-clamp-2 text-sm">
                  {{ recipe.leiras }}
                </p>

                <div class="flex items-center gap-3 flex-wrap">
                  <div class="flex items-center gap-2 bg-slate-200/50 
                              rounded-full px-3 py-1.5">
                    <Clock class="w-4 h-4 text-slate-700" />
                    <span class="text-xs font-semibold text-slate-700">
                      {{ formatTime(recipe.ido) }}
                    </span>
                  </div>
                  <div class="flex items-center gap-2 bg-slate-200/50 
                              rounded-full px-3 py-1.5">
                    <Users class="w-4 h-4 text-slate-700" />
                    <span class="text-xs font-semibold text-slate-700">
                      {{ recipe.adag }} adag
                    </span>
                  </div>
                </div>

                <div v-if="recipe.allergenek && recipe.allergenek.length > 0" 
                     class="flex items-center gap-2 flex-wrap">
                  <div v-for="allergen in recipe.allergenek" 
                       :key="allergen"
                       :class="[
                         'backdrop-blur-md rounded-full px-3 py-1 border',
                         getAllergenColor(allergen)
                       ]">
                    <span class="text-xs font-semibold">{{ allergen }}</span>
                  </div>
                </div>

              </div>

            </div>
          </div>
        </div>

      </div>

      <div v-if="!isLoading && favorites.length > 0" 
           class="text-center mt-12">
        <button 
          @click="goToSwiper"
          class="px-6 py-3 bg-gradient-to-r from-accent-500 to-accent-600 
                 hover:from-accent-600 hover:to-accent-700
                 text-white rounded-xl font-semibold 
                 transform hover:scale-105 transition-all shadow-lg">
          Több recept felfedezése
        </button>
      </div>

    </div>
  </AppLayout>
</template>

<style scoped>
@keyframes fade-in {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in {
  animation: fade-in 0.5s ease-out;
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>