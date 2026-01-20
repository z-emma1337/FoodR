<script setup>
import { ref, onMounted } from "vue";
import { Head, router } from '@inertiajs/vue3'
import axios from 'axios';
import { User, Settings, Shield, Lock, HelpCircle, LogOut, Heart, Search, Home } from 'lucide-vue-next'
import Avatar from 'primevue/avatar';
import DataView from 'primevue/dataview';
import SelectButton from 'primevue/selectbutton';

const user = {
  name: 'Emcy',
  email: 'emcy@foodr.test',
  avatar: '/imgs/emcyPFP.png'
};

const recipes = ref([]);
const layout = ref('grid');
const options = ref(['list','grid']);
const loading = ref(true);

const leftItems = ref([
  { label: 'SwipeR', url: '/welcome', icon: Home },
  { label: 'Kedvencek', url: '/kedvencek', icon: Heart },
  { label: 'Felfedezés', url: '/felfedezes', icon: Search },
]);

const rightItems = ref([
  { label: 'Felhasználói fiók beállítások', url: '/felhasznaloi-fiok-beallitasok', icon: User },
  { label: 'Profil szerkesztése', url: '/profil-szerkesztes', icon: Settings },
  { label: 'Fiók biztonság', url: '/fiok-biztonsag', icon: Shield },
  { label: 'Adatvédelmi beállítások', url: '/adatvedelmi-beallitasok', icon: Lock },
  { label: 'Segítség / Support', url: '/support', icon: HelpCircle },
]);

const logout = () => {
  router.post('/logout');
};

const fetchRecipes = async () => {
  try {
    loading.value = true;
    const response = await axios.get('/recipes');
    recipes.value = response.data;
  } catch (error) {
    console.error('Error fetching recipes:', error);

    // fallback receptek
    recipes.value = [
      {
        id: 1,
        nev: "Avocado Toast Supreme",
        leiras: "Krémes avokádó ropogós kovászos kenyéren, tökéletesen pochírozott tojással, friss kelkáposztával és chili pehellyel.",
        adag: 2,
        kep_url: "https://images.unsplash.com/photo-1588137378633-dea1336ce1e2?w=800&h=1000&fit=crop",
        created_at: new Date().toISOString(),
        felhasznalo: { nev: "Chef Maria" }
      },
      {
        id: 2,
        nev: "Mediterrán Tészta Tál",
        leiras: "Friss tészta koktélparadicsommal, olajbogyóval, feta sajttal és illatos bazsalikommal.",
        adag: 4,
        kep_url: "https://images.unsplash.com/photo-1621996346565-e3dbc646d9a9?w=800&h=1000&fit=crop",
        created_at: new Date().toISOString(),
        felhasznalo: { nev: "Chef Antonio" }
      },
      {
        id: 3,
        nev: "Csípős Thai Curry",
        leiras: "Gazdag kókuszos curry omlós csirkével, élénk zöldségekkel és aromás thai fűszerekkel.",
        adag: 4,
        kep_url: "https://images.unsplash.com/photo-1455619452474-d2be8b1e70cd?w=800&h=1000&fit=crop",
        created_at: new Date().toISOString(),
        felhasznalo: { nev: "Chef Somchai" }
      },
      {
        id: 4,
        nev: "Klasszikus Margherita Pizza",
        leiras: "Hagyományos olasz pizza friss mozzarellával, érett paradicsommal, illatos bazsalikommal.",
        adag: 2,
        kep_url: "https://images.unsplash.com/photo-1574071318508-1cdbab80d002?w=800&h=1000&fit=crop",
        created_at: new Date().toISOString(),
        felhasznalo: { nev: "Chef Giuseppe" }
      },
      {
        id: 5,
        nev: "Japán Ramen Tál",
        leiras: "Autentikus ramen gazdag levessel, rugalmas tésztával, omlós sertéshússal és lágytojással.",
        adag: 2,
        kep_url: "https://images.unsplash.com/photo-1569718212165-3a8278d5f624?w=800&h=1000&fit=crop",
        created_at: new Date().toISOString(),
        felhasznalo: { nev: "Chef Takeshi" }
      },
      {
        id: 6,
        nev: "Görög Saláta Tál",
        leiras: "Ropogós uborka, szaftos paradicsom, kalamata olajbogyó és krémes feta sajt olívaolajban.",
        adag: 2,
        kep_url: "https://images.unsplash.com/photo-1540189549336-e6e99c3679fe?w=800&h=1000&fit=crop",
        created_at: new Date().toISOString(),
        felhasznalo: { nev: "Chef Maria" }
      }
    ];
  } finally {
    loading.value = false;
  }
};

const formatDate = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('hu-HU', { month: 'short', day: 'numeric', year: 'numeric' });
};

onMounted(() => {
  fetchRecipes();
});
</script>

<template>
  <Head title="FoodR – Felfedezés" />

  <div class="relative flex min-h-screen overflow-hidden
              bg-gradient-to-br from-brand-900 via-brand-700 to-brand-800
              animate-gradient">

    <!-- glow overlay -->
    <div class="pointer-events-none absolute inset-0
                bg-gradient-to-br from-accent-500 via-transparent to-accent-600
                blur-3xl animate-gradient-slow"></div>

    <!-- LEFT SIDEBAR -->
    <aside class="hidden md:flex md:w-64 flex-col bg-accent-300 backdrop-blur-xl shadow-2xl shadow-brand-900 border-r border-accent-500 z-10">
      <div class="p-6 pb-4">
        <h1 class="text-6xl font-bold text-slate-900 text-center">
          <span class="text-accent-400 text-outline-shadow">Food</span>
          <span class="text-brand-500 text-outline-shadow">R</span>
        </h1>
      </div>

      <nav class="flex-1 px-3 space-y-1">
        <a v-for="item in leftItems" :key="item.label" :href="item.url"
           class="group flex items-center px-4 py-3 rounded-xl text-slate-800
                  transition-all duration-200 hover:bg-accent-400 hover:text-slate-900 hover:scale-[1.02]">
          <component :is="item.icon"
                     class="h-5 w-5 mr-3 text-slate-700 transition-all duration-200 group-hover:text-brand-700 group-hover:scale-110" />
          <span class="font-medium">{{ item.label }}</span>
        </a>
      </nav>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="flex-1 flex flex-col p-6 md:p-10 z-10">
      <div class="max-w-5xl mx-auto w-full">
        <!-- HERO CARD -->
        <div class="rounded-3xl overflow-hidden shadow-2xl mb-8">
          <div class="bg-gradient-to-br from-accent-500/70 to-accent-600/70 p-1">
            <div class="rounded-3xl bg-accent-300 p-8 md:p-12">
              <h1 class="text-3xl font-semibold text-slate-900 mb-4">
                Fedezd fel a recepteket,
                <span class="text-brand-700">{{ user.name }}</span> 👋
              </h1>
              <p class="text-slate-700 max-w-xl">
                Böngéssz a receptek között, találd meg a kedvenceidet,
                és építsd fel saját FoodR ízlésvilágodat.
              </p>
            </div>
          </div>
        </div>

        <!-- VIEW OPTIONS -->
        <div class="flex justify-end mb-4">
          <SelectButton v-model="layout" :options="options" :allowEmpty="false" class="bg-zinc-800 rounded-xl">
            <template #option="{ option }">
              <i :class="[option === 'list' ? 'pi pi-bars' : 'pi pi-th-large']" class="text-white" />
            </template>
          </SelectButton>
        </div>

        <!-- RECIPES -->
        <div v-if="loading" class="text-center py-20">
          <i class="pi pi-spin pi-spinner text-white text-6xl"></i>
          <p class="text-white mt-4 text-xl">Receptek betöltése...</p>
        </div>

        <DataView v-else :value="recipes" :layout="layout">
          <template #grid="slotProps">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
              <div v-for="recipe in slotProps.items" :key="recipe.id" class="group cursor-pointer transform transition-all duration-300 hover:scale-105">
                <div class="bg-accent-300 rounded-2xl overflow-hidden shadow-xl hover:shadow-2xl transition-shadow h-full flex flex-col border border-accent-400">
                  <div class="relative h-64 overflow-hidden">
                    <img :src="recipe.kep_url" :alt="recipe.nev" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" />
                    <div class="absolute inset-0 bg-gradient-to-t from-accent-500/20 via-transparent to-transparent"></div>
                    <div class="absolute top-4 right-4 bg-accent-200/90 backdrop-blur-sm px-3 py-1 rounded-full border border-accent-400">
                      <span class="text-sm font-semibold text-primary">👥 {{ recipe.adag }} adag</span>
                    </div>
                    <button class="absolute bottom-4 right-4 w-12 h-12 bg-accent-200/90 backdrop-blur-sm rounded-full flex items-center justify-center hover:bg-primary transition-colors shadow-lg border border-accent-400">
                      <i class="pi pi-heart text-xl text-primary hover:text-white"></i>
                    </button>
                  </div>
                  <div class="p-6 flex-1 flex flex-col">
                    <h3 class="text-xl font-bold text-slate-900 mb-2 line-clamp-2">{{ recipe.nev }}</h3>
                    <p class="text-slate-700 text-sm mb-4 line-clamp-3 flex-1">{{ recipe.leiras }}</p>
                    <div class="flex items-center justify-between pt-4 border-t border-accent-400">
                      <div class="flex items-center gap-2">
                        <Avatar :label="recipe.felhasznalo?.nev?.charAt(0) || 'C'" shape="circle" class="bg-primary text-white" />
                        <span class="text-sm text-slate-700">{{ recipe.felhasznalo?.nev || 'Chef' }}</span>
                      </div>
                      <button class="px-4 py-2 rounded-full font-semibold text-white transition-all hover:scale-105 bg-primary hover:bg-opacity-80">Megnézem</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </template>

          <template #list="slotProps">
            <div class="space-y-4">
              <div v-for="recipe in slotProps.items" :key="recipe.id" class="bg-accent-300 rounded-2xl overflow-hidden shadow-xl hover:shadow-2xl transition-all p-6 border border-accent-400">
                <div class="flex flex-col md:flex-row gap-6">
                  <div class="md:w-80 relative flex-shrink-0">
                    <img :src="recipe.kep_url" :alt="recipe.nev" class="w-full h-64 md:h-full object-cover rounded-xl" />
                    <div class="absolute top-4 right-4 bg-accent-200/90 backdrop-blur-sm px-3 py-1 rounded-full border border-accent-400">
                      <span class="text-sm font-semibold text-primary">👥 {{ recipe.adag }} adag</span>
                    </div>
                  </div>
                  <div class="flex-1 flex flex-col justify-between">
                    <div>
                      <h3 class="text-3xl font-bold text-slate-900 mb-3">{{ recipe.nev }}</h3>
                      <p class="text-slate-700 text-lg mb-4">{{ recipe.leiras }}</p>
                    </div>
                    <div class="flex items-center justify-between pt-4 border-t border-accent-400">
                      <div class="flex items-center gap-3">
                        <Avatar :label="recipe.felhasznalo?.nev?.charAt(0) || 'C'" shape="circle" class="bg-primary text-white" />
                        <div>
                          <p class="text-sm font-semibold text-slate-900">{{ recipe.felhasznalo?.nev || 'Chef' }}</p>
                          <p class="text-xs text-slate-600">{{ formatDate(recipe.created_at) }}</p>
                        </div>
                      </div>
                      <div class="flex gap-3">
                        <button class="w-12 h-12 bg-accent-200/90 rounded-full flex items-center justify-center hover:bg-primary transition-colors border border-accent-400">
                          <i class="pi pi-heart text-xl text-primary hover:text-white"></i>
                        </button>
                        <button class="px-6 py-3 rounded-full font-semibold text-white transition-all hover:scale-105 bg-primary hover:bg-opacity-80">Megnézem</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </template>
        </DataView>
      </div>
    </main>

    <!-- RIGHT SIDEBAR -->
    <aside class="hidden lg:flex lg:w-80 flex-col bg-accent-300 backdrop-blur-xl shadow-2xl shadow-brand-900/30 border-l border-accent-500/30 z-10">
      <div class="p-6 pb-4">
        <h3 class="text-xl font-semibold text-slate-900">Felhasználói fiók</h3>
      </div>
      <nav class="flex-1 px-3 space-y-1">
        <a v-for="item in rightItems" :key="item.label" :href="item.url"
           class="group flex items-center px-4 py-3 rounded-xl text-slate-800 transition-all duration-200 hover:bg-accent-400 hover:text-slate-900 hover:scale-[1.02]">
          <component :is="item.icon" class="h-5 w-5 mr-3 text-slate-700 transition-all duration-200 group-hover:text-brand-700 group-hover:scale-110" />
          <span class="font-medium">{{ item.label }}</span>
        </a>
      </nav>

      <!-- USER CARD -->
      <div class="p-5 mt-auto border-t border-accent-400 bg-accent-300 backdrop-blur-xl rounded-t-3xl">
        <div class="flex items-center gap-3 mb-4 p-3 rounded-2xl bg-accent-200">
          <Avatar :image="user.avatar" shape="circle" class="!w-12 !h-12" />
          <div>
            <div class="font-semibold text-slate-900">{{ user.name }}</div>
            <div class="text-sm text-slate-600">{{ user.email }}</div>
          </div>
        </div>
        <button @click="logout" class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-2xl bg-brand-700 text-accent-200 font-medium shadow-xl shadow-brand-800/40 transition-all hover:bg-brand-800 hover:scale-[1.02] focus:outline-none focus:ring-4 focus:ring-brand-500">
          <LogOut class="h-5 w-5" />
          Kijelentkezés
        </button>
      </div>
    </aside>
  </div>
</template>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.text-primary { color: #b21f24; }
.bg-primary { background-color: #b21f24; }
</style>
