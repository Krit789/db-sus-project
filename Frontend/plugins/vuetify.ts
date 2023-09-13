import {createVuetify} from 'vuetify'
import {md3} from 'vuetify/blueprints';
import '@mdi/font/css/materialdesignicons.css';


export default defineNuxtPlugin((nuxt) => {
    const vuetify = createVuetify({
        ssr: true,
        blueprint: md3,
    })
    nuxt.vueApp.use(vuetify);
})