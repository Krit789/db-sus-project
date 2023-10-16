import {createVuetify} from 'vuetify';
import {md3} from 'vuetify/blueprints';
import '@mdi/font/css/materialdesignicons.css';

const myCustomLightTheme = {
    dark: false,
    colors: {
        background: '#FFFFFF',
        surface: '#FFFFFF',
        primary: '#0373DE',
        secondary: '#C8E4FE',
    },
};

export default defineNuxtPlugin((nuxt) => {
    const vuetify = createVuetify({
        ssr: true,
        blueprint: md3,
        theme: {
            defaultTheme: 'myCustomLightTheme',
            themes: {
                myCustomLightTheme,
            },
        },
    });
    nuxt.vueApp.use(vuetify);
});
