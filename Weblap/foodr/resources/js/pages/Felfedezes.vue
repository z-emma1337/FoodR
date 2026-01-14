<template>
    <div class="flex min-h-screen">
        <div class="flex justify-end">
            <Menu :model="itemsLeft" class="w-64 user-menu">
                <template #start>
                    <div class="px-4 py-3 font-bold text-xl">
                        Food<span class="text-primary">Я</span>
                    </div>
                </template>

                <template #item="{ item, props }">
                    <a v-ripple v-bind="props.action" :href="item.url" class="flex items-center px-3 py-2 menu-item">
                        <span :class="item.icon" class="mr-2" />
                        <span>{{ item.label }}</span>
                        <Badge v-if="item.badge" class="ml-auto badge" :value="item.badge" />
                    </a>
                </template>
            </Menu>
        </div>

        <div class="flex-1 p-10 main-background">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-4xl font-bold text-white mb-2">Felfedezés</h1>
                    <p class="text-lg text-gray-400">
                        Böngéssz a receptek között
                    </p>
                </div>
                <SelectButton v-model="layout" :options="options" :allowEmpty="false" class="bg-zinc-800 rounded-xl">
                    <template #option="{ option }">
                        <i :class="[option === 'list' ? 'pi pi-bars' : 'pi pi-th-large']" class="text-white" />
                    </template>
                </SelectButton>
            </div>

            <div v-if="loading" class="text-center py-20">
                <i class="pi pi-spin pi-spinner text-white text-6xl"></i>
                <p class="text-white mt-4 text-xl">Receptek betöltése...</p>
            </div>

            <DataView v-else :value="recipes" :layout="layout">
                <template #grid="slotProps">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div v-for="recipe in slotProps.items" :key="recipe.id" 
                             class="group cursor-pointer transform transition-all duration-300 hover:scale-105">
                            <div class="bg-zinc-800 rounded-2xl overflow-hidden shadow-xl hover:shadow-2xl transition-shadow h-full flex flex-col border border-zinc-700">
                                <div class="relative h-64 overflow-hidden">
                                    <img 
                                        :src="recipe.kep_url" 
                                        :alt="recipe.nev"
                                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                                    />
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                                    
                                    <div class="absolute top-4 right-4 bg-zinc-900/90 backdrop-blur-sm px-3 py-1 rounded-full border border-zinc-700">
                                        <span class="text-sm font-semibold text-primary">
                                            👥 {{ recipe.adag }} adag
                                        </span>
                                    </div>

                                    <button class="absolute bottom-4 right-4 w-12 h-12 bg-zinc-900/90 backdrop-blur-sm rounded-full flex items-center justify-center hover:bg-primary transition-colors shadow-lg border border-zinc-700">
                                        <i class="pi pi-heart text-xl text-primary hover:text-white"></i>
                                    </button>
                                </div>

                                <div class="p-6 flex-1 flex flex-col">
                                    <h3 class="text-xl font-bold text-white mb-2 line-clamp-2">{{ recipe.nev }}</h3>
                                    <p class="text-gray-400 text-sm mb-4 line-clamp-3 flex-1">{{ recipe.leiras }}</p>
                                    
                                    <div class="flex items-center justify-between pt-4 border-t border-zinc-700">
                                        <div class="flex items-center gap-2">
                                            <Avatar 
                                                :label="recipe.felhasznalo?.nev?.charAt(0) || 'C'" 
                                                shape="circle" 
                                                class="bg-primary text-white"
                                            />
                                            <span class="text-sm text-gray-400">{{ recipe.felhasznalo?.nev || 'Chef' }}</span>
                                        </div>
                                        <button class="px-4 py-2 rounded-full font-semibold text-white transition-all hover:scale-105 bg-primary hover:bg-opacity-80">
                                            Megnézem
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <template #list="slotProps">
                    <div class="space-y-4">
                        <div v-for="recipe in slotProps.items" :key="recipe.id">
                            <div class="bg-zinc-800 rounded-2xl overflow-hidden shadow-xl hover:shadow-2xl transition-all p-6 border border-zinc-700">
                                <div class="flex flex-col md:flex-row gap-6">
                                    <!-- Image -->
                                    <div class="md:w-80 relative flex-shrink-0">
                                        <img 
                                            :src="recipe.kep_url" 
                                            :alt="recipe.nev"
                                            class="w-full h-64 md:h-full object-cover rounded-xl"
                                        />
                                        <div class="absolute top-4 right-4 bg-zinc-900/90 backdrop-blur-sm px-3 py-1 rounded-full border border-zinc-700">
                                            <span class="text-sm font-semibold text-primary">
                                                👥 {{ recipe.adag }} adag
                                            </span>
                                        </div>
                                    </div>

                                    <div class="flex-1 flex flex-col justify-between">
                                        <div>
                                            <h3 class="text-3xl font-bold text-white mb-3">{{ recipe.nev }}</h3>
                                            <p class="text-gray-400 text-lg mb-4">{{ recipe.leiras }}</p>
                                        </div>

                                        <div class="flex items-center justify-between pt-4 border-t border-zinc-700">
                                            <div class="flex items-center gap-3">
                                                <Avatar 
                                                    :label="recipe.felhasznalo?.nev?.charAt(0) || 'C'" 
                                                    shape="circle" 
                                                    class="bg-primary text-white"
                                                />
                                                <div>
                                                    <p class="text-sm font-semibold text-white">{{ recipe.felhasznalo?.nev || 'Chef' }}</p>
                                                    <p class="text-xs text-gray-500">{{ formatDate(recipe.created_at) }}</p>
                                                </div>
                                            </div>
                                            <div class="flex gap-3">
                                                <button class="w-12 h-12 bg-zinc-900 rounded-full flex items-center justify-center hover:bg-primary transition-colors border border-zinc-700">
                                                    <i class="pi pi-heart text-xl text-primary hover:text-white"></i>
                                                </button>
                                                <button class="px-6 py-3 rounded-full font-semibold text-white transition-all hover:scale-105 bg-primary hover:bg-opacity-80">
                                                    Megnézem
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </DataView>
        </div>

        <div class="flex justify-end">
            <Menu :model="itemsRight" class="w-64 user-menu">
                <template #start>
                    <div class="px-4 py-3 font-bold text-xl text-right">
                        Felhasználói fiók
                    </div>
                </template>

                <template #item="{ item, props }">
                    <a v-ripple v-bind="props.action" :href="item.url" class="flex items-center px-3 py-2 menu-item">
                        <span :class="item.icon" class="mr-2" />
                        <span>{{ item.label }}</span>
                    </a>
                </template>

                <template #end>
                    <div class="menu-bottom">
                        <a class="flex items-center px-3 py-2 menu-item logout">
                            <button type="button">Kijelentkezés</button>
                        </a>

                        <div class="p-3 flex items-center gap-2">
                            <Avatar image="/imgs/emcyPFP.png" shape="circle" />
                            <div>
                                <div class="font-bold">Emcy</div>
                                <div class="text-sm text-gray-500">emcy@foodr.test</div>
                            </div>
                        </div>
                    </div>
                </template>
            </Menu>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from 'axios';
import Menu from 'primevue/menu';
import Badge from 'primevue/badge';
import Avatar from 'primevue/avatar';
import DataView from 'primevue/dataview';
import SelectButton from 'primevue/selectbutton';

const recipes = ref([]);
const layout = ref('grid');
const options = ref(['list', 'grid']);
const loading = ref(true);

const itemsLeft = ref([
    {
        label: 'SwipeR',
        url: '/welcome',
        icon: 'pi pi-home'
    },
    {
        label: 'Kedvencek',
        url: '/kedvencek',
        icon: 'pi pi-shopping-cart',
        badge: 3
    },
    {
        label: 'Felfedezés',
        url: '/felfedezes',
        icon: 'pi pi-search'
    }
]);

const itemsRight = ref([
    {
        label: 'Felhasználói fiók beállítások',
        url: '/felhasznaloi-fiok-beallitasok',
        icon: 'pi pi-home'
    },
    {
        label: 'Profil szerkesztése',
        url: '/profil-szerkesztes',
        icon: 'pi pi-user-edit'
    },
    {
        label: 'Fiók biztonság',
        url: '/fiok-biztonsag',
        icon: 'pi pi-shield'
    },
    {
        label: 'Adatvédelmi beállítások',
        url: '/adatvedelmi-beallitasok',
        icon: 'pi pi-lock'
    },
    {
        label: 'Segítség / Support',
        url: '/support',
        icon: 'pi pi-question-circle'
    }
]);


const fetchRecipes = async () => {
    try {
        loading.value = true;
        const response = await axios.get('/recipes');
        recipes.value = response.data;
    } catch (error) {
        console.error('Error fetching recipes:', error);

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

.text-primary {
    color: #b21f24;
}

.bg-primary {
    background-color: #b21f24;
}
</style>