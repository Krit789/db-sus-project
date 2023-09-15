<script lang="ts" setup>
definePageMeta({ auth: false })
const {
  status,
  data,
  signIn,
  signOut,
} = useAuth()
</script>

<script lang="ts">
import "~/assets/stylesheets/global.css";
import "~/assets/stylesheets/index.css";
</script>

<template>
  <Navbar>
    <v-main class="mt-n16">
      <v-parallax
          src="images/home/banner.webp"
      >
        <div class="text-center mt-16 first-box">
          <h1 class="text-h2 font-weight-bold text-white mt-16  ">Seatify</h1>
          <p class="text-h5 ma-5 text-white">Satisfying your seaty needs.</p>
          <v-btn class="blue_button" prepend-icon="mdi-login-variant" rounded="lg" size="x-large">Book Now!
          </v-btn>
        </div>
        <div class="text-center ma-lg-16 ma-sm-8 ma-xs-0 my-8">
          <v-card class="px-lg-10 px-sm-5 px-xs-0" text="">
            <v-card-text class="text-h3 font-weight-bold my-6">Locations</v-card-text>
            <v-sheet
                class="mx-auto pb-5"
                elevation="0"
                max-width="1800"
                rounded="lg"
            >
              <v-slide-group :show-arrows="true">
                <v-slide-group-item
                    v-for="n in 15"
                    :key="n"
                    v-slot="{ isSelected, toggle, selectedClass }"
                >
                  <!-- Put Location card here -->
                  <v-card
                      class="ma-2"
                      max-width="350"
                  >
                    <template v-slot:loader="{ isActive }">
                      <v-progress-linear
                          :active="isActive"
                          color="deep-purple"
                          height="4"
                          indeterminate=""
                      ></v-progress-linear>
                    </template>

                    <v-img
                        cover=""
                        height="250"
                        src="https://cdn.vuetifyjs.com/images/cards/cooking.png"
                    ></v-img>

                    <v-card-item>
                      <v-card-title>Restaurant Name</v-card-title>
                    </v-card-item>
                    <v-card-text>
                      <div class="my-4 text-subtitle-1">
                        Location Name
                      </div>
                      <div>Location description. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</div>
                    </v-card-text>

                    <v-divider class="mx-4 mb-1"></v-divider>
                    <v-btn
                        v-if="status == 'authenticated'"
                        class="mb-2"
                        color="deep-purple-lighten-2"
                        variant="outlined"
                        @click=""
                    >
                      Reserve
                    </v-btn>
                  </v-card>
                  <!-- Location card ends here -->
                </v-slide-group-item>
              </v-slide-group>
            </v-sheet>
          </v-card>
        </div>
      </v-parallax>
      <div class="bg-grey-lighten-4 mb-3">
        <Features/>
        <Credit/>
      </div>
    </v-main>
  </Navbar>
</template>

<style>
@import "@/assets/stylesheets/global.css";
@import "@/assets/stylesheets/index.css";
</style>