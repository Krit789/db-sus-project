import { createVuetify } from 'vuetify'
import * as components from "vuetify/components";
import * as directives from "vuetify/directives";
import { md3 } from 'vuetify/blueprints';
import '@mdi/font/css/materialdesignicons.css';


export default defineNuxtPlugin((nuxt) => {
   const vuetify = createVuetify({
    ssr: true,
    blueprint: md3,
    components,
    directives,
   })
    nuxt.vueApp.use(vuetify);
})