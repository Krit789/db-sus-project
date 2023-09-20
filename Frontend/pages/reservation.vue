<script lang="ts" setup>
import {VStepper} from "vuetify/labs/VStepper";
import {useDisplay} from "vuetify";
import "~/assets/stylesheets/global.css";
import "~/assets/stylesheets/index.css";

const {mobile} = useDisplay();
const {status, data} = useAuth();
const route = useRoute();
useHead({
  title: "Booking - Seatify",
  meta: [{name: "Seatify App", content: "My amazing site."}],
});
</script>

<script lang="ts">
export default {
  data: () => ({}),
};
</script>
<template>
  <v-main class="justify-center reservation_body">
    <div class="main_container mx-auto blur-effect account_container mt-10 py-1 px-1 min-h-40">
      <h1 class="text-h3 font-weight-bold mt-8 ml-8 text-left">
        Reservation {{ $route.query.location_id }}
      </h1>
      <v-sheet class="mt-8 ma-md-8 ma-xs-1 text-center bg-transparent" rounded="0">
        <template v-if="route.query.location_id != null">
          <v-stepper
              :items="[
              'Select Branches',
              'Choose Date',
              'Choose Seat',
              'Summary',
            ]"
              :mobile="mobile"
              model-value="1"
          >
            <template v-slot:item.1>
              <v-card title="Select Branches">...</v-card>
            </template>

            <template v-slot:item.2>
              <v-card title="Choose Date">...</v-card>
            </template>

            <template v-slot:item.3>
              <v-card title="Choose Seat">...</v-card>
            </template>
            <template v-slot:item.4>
              <v-card title="Summary">...</v-card>
            </template>
          </v-stepper>
        </template>
        <template v-else>
          <v-stepper
              :items="[
              'Select Branches',
              'Choose Date',
              'Choose Seat',
              'Summary',
            ]"
              :mobile="mobile"
          >
            <template v-slot:item.1>
              <v-card title="Select Branches">...</v-card>
            </template>

            <template v-slot:item.2>
              <v-card title="Choose Date">...</v-card>
            </template>

            <template v-slot:item.3>
              <v-card title="Choose Seat">...</v-card>
            </template>
            <template v-slot:item.4>
              <v-card title="Summary">...</v-card>
            </template>
          </v-stepper>
        </template>
      </v-sheet>
    </div>
  </v-main>
</template>
