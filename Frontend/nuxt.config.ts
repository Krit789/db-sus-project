// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
    devtools: {enabled: true},
    css: ["vuetify/styles/main.sass"],
    build: {
        transpile: ["vuetify"],
    },
});
