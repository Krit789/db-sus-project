<script lang="ts" setup>
import { VStepper } from "vuetify/labs/VStepper";
import { useDisplay } from "vuetify";

const { mobile } = useDisplay();
const { status, data } = useAuth();

useHead({
  title: "Report - Seatify Admin",
  meta: [{ name: "Seatify App", content: "My amazing site." }],
});
</script>

<script lang="ts"></script>

<template>
  <Navbar>
    <v-main>
      <h1 class="text-h3 font-weight-bold mt-8 ml-8 text-left">Report</h1>
      <v-sheet class="mt-8 ma-md-8 ma-xs-1 text-center" rounded="lg"> </v-sheet>
      <Credit />
    </v-main>
  </Navbar>
</template>
