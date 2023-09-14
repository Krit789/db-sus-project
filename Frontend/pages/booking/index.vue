<script lang="ts" setup>
import {VStepper} from 'vuetify/labs/VStepper';
import {useDisplay} from "vuetify";

const {mobile} = useDisplay();
const {
  status,
  data
} = useAuth()

async function test() {
  const locations: any = await fetch(
      "http://localhost:3000/proxy/api/booking/Booking/Booking-viewalllocation-user.php",
      {
        method: "POST",
        body: {},
      }
  ).catch((error) => error).then(() => {

  });
}
</script>

<script lang="ts">
</script>


<template>
  <Navbar>
    <v-main>
      <h1 class="text-h3 font-weight-bold mt-8 ml-8 text-left">Bookings</h1>
      <v-sheet
          class="mt-8 ma-md-8 ma-xs-1 text-center"
          rounded="lg">
        <v-stepper :items="['Select Branches', 'Choose Date', 'Choose Seat', 'Summary']" :mobile="mobile">
          <template v-slot:item.1>
            <v-card flat="" title="Select Branches">...</v-card>
          </template>

          <template v-slot:item.2>
            <v-card flat="" title="Choose Date">...</v-card>
          </template>

          <template v-slot:item.3>
            <v-card flat="" title="Choose Seat">...</v-card>
          </template>
          <template v-slot:item.4>
            <v-card flat="" title="Summary">...</v-card>
          </template>
        </v-stepper>
      </v-sheet>
      <Credit/>
    </v-main>
  </Navbar>
</template>
