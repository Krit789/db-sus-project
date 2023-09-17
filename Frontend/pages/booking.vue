<script lang="ts" setup>
import { VStepper } from "vuetify/labs/VStepper";
import { useDisplay } from "vuetify";
const { mobile } = useDisplay();
const { status, data } = useAuth();
const route = useRoute();
useHead({
  title:'Booking - Seatify',
  meta: [
    { name: 'Seatify App', content: 'My amazing site.' }
  ],
});

</script>

<script lang="ts">
export default {
  data: () => ({

  }),
}
</script>
<template>
  <Navbar>
    <v-main>
      <h1 class="text-h3 font-weight-bold mt-8 ml-8 text-left">Bookings {{ $route.query.location_id }}</h1>
      <v-sheet class="mt-8 ma-md-8 ma-xs-1 text-center" rounded="lg">
        <template v-if="route.query.location_id != null">
          <v-stepper
            :items="['Select Branches', 'Choose Date', 'Choose Seat', 'Summary']"
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
            :items="['Select Branches', 'Choose Date', 'Choose Seat', 'Summary']"
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
      <Credit />
    </v-main>
  </Navbar>
</template>
