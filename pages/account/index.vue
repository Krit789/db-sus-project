<script setup>
const router = useRouter()
</script>

<template>
    <h1>Account</h1>
    <v-btn v-on:click="() => router.push( {name: 'index'} )">
      Button
    </v-btn>
</template>