<script lang="ts" setup>
definePageMeta({auth: false})
const {
  status,
  data,
  signIn,
  signOut,
} = useAuth()
</script>
<template>
  <Navbar>
    <div class="text-center " style="color:red;">
      <v-btn color="Blue" rounded>
        Book Now!
      </v-btn>
    </div>
  </Navbar>
</template>
