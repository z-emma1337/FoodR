import '../css/app.css'
import '../css/prime-overrides.css'

import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import type { DefineComponent } from 'vue'
import { createApp, h } from 'vue'
import { initializeTheme } from './composables/useAppearance'

import PrimeVue from 'primevue/config'
import Aura from '@primeuix/themes/aura'

// PRIMEVUE KOMPONENSEK (EZ HIÁNYZOTT)
import Menu from 'primevue/menu'
import Avatar from 'primevue/avatar'
import Badge from 'primevue/badge'
import DataView from 'primevue/dataview'
import Button from 'primevue/button'
import Tag from 'primevue/tag'
import SelectButton from 'primevue/selectbutton'
import Divider from 'primevue/divider'

const appName = import.meta.env.VITE_APP_NAME || 'FoodR'

createInertiaApp({
    title: (title) => (title ? `${title} | ${appName}` : appName),

    resolve: (name: string) => {
        return resolvePageComponent(
            `./pages/${name}.vue`,
            import.meta.glob<DefineComponent>('./pages/**/*.vue')
        )
    },


    setup({ el, App, props, plugin }) {
        const vueApp = createApp({ render: () => h(App, props) })

        vueApp
            .use(plugin)
            .use(PrimeVue, {
                theme: {
                    preset: Aura,
                },
            })

            // ⬇⬇⬇ GLOBÁLIS PRIMEVUE REGISZTRÁCIÓ ⬇⬇⬇
            .component('Menu', Menu)
            .component('Avatar', Avatar)
            .component('Badge', Badge)
            .component('DataView', DataView)
            .component('Button', Button)
            .component('Tag', Tag)
            .component('SelectButton', SelectButton)
            .component('Divider', Divider)

            .mount(el)
    },

    progress: {
        color: '#4B5563',
    },
})

// light / dark mód
initializeTheme()

module.exports = {
  theme: {
    extend: {
      keyframes: {
        float: {
          '0%, 100%': { transform: 'translateY(0px)' },
          '50%': { transform: 'translateY(-10px)' },
        },
      },
      animation: {
        float: 'float 4s ease-in-out infinite',
      },
    },
  },
}
