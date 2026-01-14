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
            <h1 class="text-4xl font-bold mb-4">Üdvözöllek a FoodR-ben</h1>
            <p class="text-lg text-gray-600">
                Az Ön egyedi ételeinek válogatása.
            </p>

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
    <div class="w-full md:w-2/12">
        <Divider layout="vertical" class="!hidden md:!flex"><b>OR</b></Divider>
        <Divider layout="horizontal" class="!flex md:!hidden" align="center"><b>OR</b></Divider>
    </div>
    <div class="w-full md:w-5/12 flex items-center justify-center py-5">
        <Button label="Sign Up" icon="pi pi-user-plus" severity="success"
            class="w-full max-w-[17.35rem] mx-auto"></Button>
    </div>

</template>

<script setup>
import { ref } from 'vue'
import Menu from 'primevue/menu'
import Badge from 'primevue/badge'
import Avatar from 'primevue/avatar'
import DataView from 'primevue/dataview';

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
])

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
</script>
<style scoped>
.text-primary {
    color: #b21f24;
}
</style>