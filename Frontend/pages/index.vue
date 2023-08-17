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
    <v-main>
    <v-parallax
    src="https://images-wixmp-ed30a86b8c4ca887773594c2.wixmp.com/f/549e9b77-4c90-4c7f-8d0e-772a4ba70576/detu7o7-2822eeb6-d3ae-4744-a868-ade51aaae2f9.jpg?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiJ1cm46YXBwOjdlMGQxODg5ODIyNjQzNzNhNWYwZDQxNWVhMGQyNmUwIiwiaXNzIjoidXJuOmFwcDo3ZTBkMTg4OTgyMjY0MzczYTVmMGQ0MTVlYTBkMjZlMCIsIm9iaiI6W1t7InBhdGgiOiJcL2ZcLzU0OWU5Yjc3LTRjOTAtNGM3Zi04ZDBlLTc3MmE0YmE3MDU3NlwvZGV0dTdvNy0yODIyZWViNi1kM2FlLTQ3NDQtYTg2OC1hZGU1MWFhYWUyZjkuanBnIn1dXSwiYXVkIjpbInVybjpzZXJ2aWNlOmZpbGUuZG93bmxvYWQiXX0.ig5-A68gSqIF4RJn6AaIU-Z602hNKRIL1tK5NA-GQrc"
  >
    <div class="text-center pa-16" style="color:rgb(255, 255, 255);">
      <v-card text="" variant="outlined">
        <v-card-text class="text-h3 pt-10">Seatify</v-card-text>
        <v-card-text class="text-h5 pb-10">Satisfying your seaty needs.</v-card-text>
          <v-btn rounded="xl" elevation="24" class="bg-blue-darken-1 mb-n10">
            Book Now!
          </v-btn>
    </v-card>
    </div >
    <v-divider :thickness="3" class="border-opacity-100" color="white" inset></v-divider>
    
    </v-parallax>
  </v-main>
  </Navbar>
</template>
