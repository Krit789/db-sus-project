<script lang="ts" setup>
definePageMeta({auth: false});
const {status, data} = useAuth();
useHead({
  title: "Seatify: Seat Reservation Service",
  meta: [
    {
      name: "Seatify: Seat Reservation Service",
      content:
          "Seatify, The Seat Reservation Service that makes the difference.",
    },
  ],
});
useSeoMeta({
  title: "Seatify",
  ogTitle: "Seatify",
  description:
      "Seatify, The Seat Reservation Service that makes the difference.",
  ogDescription:
      "Seatify, The Seat Reservation Service that makes the difference.",
  ogImage: "/og_image.jpg",
  twitterCard: "summary_large_image",
});
</script>

<script lang="ts">
import "~/assets/stylesheets/global.css";
import "~/assets/stylesheets/index.css";
</script>

<template>
  <v-main class="mt-n16">
    <v-parallax src="images/home/banner.webp">
      <div class="text-center mt-16 first-box">
        <h1 class="text-h2 font-weight-bold text-white mt-16">Seatify</h1>
        <p class="text-h5 ma-5 text-white">Satisfying your seaty needs.</p>
        <v-btn
            prepend-icon="mdi-login-variant"
            rounded="lg"
            size="x-large"
            @click="$router.push('/reservation')"
        >
          Reserve Now!
        </v-btn>
      </div>
      <div class=" text-center ma-lg-16 ma-sm-8 ma-xs-0 my-8">
        <v-card class="blur-effect location_container px-lg-10 px-sm-5 px-xs-0" text="">
          <v-card-text class="text-h3 font-weight-bold my-6 bg-transparent"
          >Locations
          </v-card-text
          >
          <v-sheet
              class="mx-auto pb-5 bg-transparent"
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
                <v-card class="ma-2" max-width="350">
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
                    <div class="my-4 text-subtitle-1">Location Name</div>
                    <div>
                      Location description. Lorem ipsum dolor sit amet,
                      consectetur adipiscing elit.
                    </div>
                  </v-card-text>

                  <v-divider class="mx-4 mb-1"></v-divider>
                  <v-btn
                      v-if="status == 'authenticated'"
                      class="my-3"
                      color="#0373DE"
                      rounded="lg"
                      variant="outlined"
                      @click="
                        $router.push({
                          path: '/reservation',
                          query: { location_id: n },
                        })
                      "
                  >
                    Reserve
                  </v-btn>
                </v-card>
                <!-- Location card ends here -->
              </v-slide-group-item>
            </v-slide-group>
          </v-sheet>
          <v-btn
              class="mb-3"
              color="#0373DE"
              rounded="lg"
              size="large"
              variant="text"
              @click="$router.push('/booking')"
          >All Location
          </v-btn>
        </v-card>
      </div>
    </v-parallax>
    <div class="bg-grey-lighten-4 mb-3">
      <Features class="mt-0"/>
      <Credit class="bg-transparent"/>
    </div>
  </v-main>
</template>

<style>
@import "@/assets/stylesheets/global.css";
@import "@/assets/stylesheets/index.css";
</style>
