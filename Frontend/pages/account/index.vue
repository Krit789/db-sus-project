<script lang="ts" setup>
const {
  status,
  data
} = useAuth()
</script>

<template>
  <Navbar>
    <div v-if="status == 'authenticated'">
      <h1>Welcome to Account</h1>
      <p>First Name: {{ data.firstName }}</p>
      <p>Last Name: {{ data.lastName }}</p>
      <p>Tel Number: {{ data.tel }}</p>
    </div>
    <v-btn v-on:click="() => $router.push({ name: 'index' })">
      Back to Index
    </v-btn>
  </Navbar>
</template>