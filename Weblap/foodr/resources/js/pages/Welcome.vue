<template>
    <div class="flex min-h-screen">
        <Menu :model="itemsLeft" class="w-64" style="background-color: darkred;">
            <template #start>
                <div class="px-4 py-3 font-bold text-xl">
                    Food<span class="text-primary">Я</span>
                </div>
            </template>

            <template #item="{ item, props }">
                <a v-ripple class="flex items-center px-3 py-2" v-bind="props.action" style="background-color:goldenrod /*burlywood darkkhaki*/; border-radius: 10px;">
                    <span :class="item.icon" class="mr-2" />
                    <span>{{ item.label }}</span>
                    <Badge v-if="item.badge" class="ml-auto"style="background-color: darkgoldenrod;" :value="item.badge" />
                </a>
            </template>

        </Menu>


        <div class="flex-1 p-10" style="background-color: RGB(139, 0, 0,0.3);">
            <h1 class="text-4xl font-bold mb-4">Üdvözöllek a FoodR-ben</h1>
            <p class="text-lg text-gray-600">
                Az Ön egyedi ételeinek válogatása.
            </p>
        </div>


        <div class="flex justify-end">
            <Menu :model="itemsRight" class="w-64" style="background-color: darkred;">
            
              <template #start>
                <div class="px-4 py-3 font-bold text-xl text-right">
                  Felhasználói fiók<span class="text-primary"></span>
                </div>
              </template>
          
              <template #item="{ item, props }">
                <a v-ripple class="flex items-center px-3 py-2" v-bind="props.action"
                   style="background-color: goldenrod; border-radius: 10px;">
                  <span :class="item.icon" class="mr-2"></span>
                  <span>{{ item.label }}</span>
                  <Badge v-if="item.badge" class="ml-auto" style="background-color: darkgoldenrod;" :value="item.badge" />
                </a>
              </template>
          
              <template #end>
                <div class="p-3 flex items-center gap-2">
                    <Avatar image="/imgs/emcyPFP.png" shape="circle" />
                    <div>
                        <div class="font-bold">Emcy</div>
                        <div class="text-sm text-gray-500">emcy@foodr.test</div>
                    </div>
                </div>
              </template>
          
            </Menu>
        </div>
    </div>
    
</template>

<script setup>
import { ref } from 'vue'
import Menu from 'primevue/menu'
import Badge from 'primevue/badge'
import Avatar from 'primevue/avatar'

const itemsLeft = ref([
    {
        label: 'SwipeR',
        icon: 'pi pi-home'
    },
    {
        label: 'Kedvencek',
        icon: 'pi pi-shopping-cart',
        badge: 3
    }
])

const itemsRight = ref([
    {
        label: 'Felhasználói fiók beállítások',
        icon: 'pi pi-home'
    },
    {
        label: 'Profil szerkesztése',
        icon: 'pi pi-user-edit'
    },
    {
        label: 'Fiók biztonság',
        icon: 'pi pi-shield'
    },
    {
        label: 'Adatvédelmi beállítások',
        icon: 'pi pi-lock'
    },
    {
        label: 'Segítség / Support',
        icon: 'pi pi-question-circle'
    },
    {
        separator: true
    },
    {
        label: 'Logout',
        icon: 'pi pi-sign-out'
    }
]);
</script>
