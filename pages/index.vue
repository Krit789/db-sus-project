<script setup lang="ts">
const route = useRoute()
const router = useRouter()
</script>

<template>
  <div>
    <h1>Nuxt Routing set up successfully!</h1>
    <p>Current route: {{ route.path }}</p>
    <v-btn v-on:click="() => router.push( {name: 'account'} )">
      Button
    </v-btn>
    <a href="https://nuxt.com/docs/getting-started/routing" target="_blank">Learn more about Nuxt Routing</a>
  </div>
</template>
