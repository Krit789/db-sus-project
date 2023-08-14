<script setup lang="ts">
const {
  status,
  data
} = useAuth()
</script>

<template>
<div v-if="status == 'authenticated'">
  <h1>Welcome to Account</h1>
  <p>First Name: {{ data.firstName }}</p>
  <p>Last Name: {{ data.lastName }}</p>
  <p>Tel Number: {{ data.tel }}</p>
</div>
  <v-btn v-on:click="() => $router.push( {name: 'index'} )">
    Back to Index
  </v-btn>
<!--  <v-img-->
<!--      alt="ejudge"-->
<!--      class="mx-auto my-4 rounded-lg border"-->
<!--      src="https://ejudge.it.kmitl.ac.th/uploads/4496_1670336142_tP3P4Svj.jpg"-->
<!--      width="20%"-->
<!--  ></v-img>-->
</template>