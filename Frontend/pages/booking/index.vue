<script lang="ts" setup>
import { VStepper } from 'vuetify/labs/VStepper';
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
    rounded="lg"
    class="mt-8 ma-md-8 ma-xs-1 text-center">
    <v-stepper :mobile="mobile" :items="['Select Branches', 'Choose Date', 'Choose Seat', 'Summary']">
  <template v-slot:item.1>
    <v-card title="Select Branches" flat>...</v-card>
  </template>

  <template v-slot:item.2>
    <v-card title="Choose Date" flat>...</v-card>
  </template>

  <template v-slot:item.3>
    <v-card title="Choose Seat" flat>...</v-card>
  </template>
  <template v-slot:item.4>
    <v-card title="Summary" flat>...</v-card>
  </template>
</v-stepper>
</v-sheet>
      <Credit />
    </v-main>
  </Navbar>
</template>
