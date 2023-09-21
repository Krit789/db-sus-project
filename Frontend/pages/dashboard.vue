<script lang="ts" setup>
import {VDataTable} from "vuetify/labs/VDataTable";
import "~/assets/stylesheets/dashboard.css";
import "~/assets/stylesheets/global.css";

const {status, data, signIn, signOut} = useAuth();

useHead({
  title: "My Reservation - Seatify",
  meta: [{name: "Seatify App", content: "My amazing site."}],
});
</script>
<script lang="ts">
export default {
  data: () => ({
    dtIsError: false,
    dtErrorData: "",
    dtData: [],
    itemsPerPage: 10,
    dtLoading: false,
    dtHeaders: [
      {
        title: "ID",
        align: "start",
        sortable: true,
        key: "res_id",
      },
      // {title: "User ID", align: "end", key: "user_id"},
      {title: "Reserved On", align: "end", key: "create_time"},
      {title: "Reserved For", align: "end", key: "arrival"},
      {title: "Status", align: "end", key: "status"},
      {title: "No. of Customer", align: "end", key: "cus_count"},
      {title: "Table ID", align: "end", key: "table_id"},
    ],
  }),
  methods: {
    async loadData() {
      this.dtLoading = true;
      await $fetch("/api/data", {
        method: "POST",
        body: {
          type: 8,
          usage: "user",
        },
        lazy: true,
      })
          .catch((error) => {
            this.dtIsError = true;
            this.dtErrorData = error.data;
          })
          .then(({status, message}) => {
            this.dtData = message;
            this.dtLoading = false;
            this.dtIsError = false;
          });
    },
  },
  beforeMount() {
    this.loadData();
  },
};
</script>

<template>
  <v-main class="justify-center dashboard_body">
    <div class="dashboard_container main_container mx-auto blur-effect mt-10 py-1 px-1 min-h-40">
      <h1 class="text-h3 font-weight-bold mt-8 ml-8 text-left">
        My Dashboard
      </h1>
      <v-sheet class="mt-8 ma-md-8 ma-sm-5 text-center" rounded="lg">
        <v-alert
            v-if="dtIsError"
            class="ma-3"
            color="error"
            icon="$error"
            title="Fetch Error"
        >{{ dtErrorData }}
        </v-alert>

        <v-data-table
            v-model:items-per-page="itemsPerPage"
            :headers="dtHeaders"
            :items="dtData"
            :loading="dtLoading"
            class="elevation-0"
            item-value="id"
            loading-text="We're looking for your reservation, Hang tight!"
            @click:row="(val, tabl) => {console.log(tabl.item.columns.res_id);}"
        ></v-data-table>
        <v-btn
            :disabled="dtLoading"
            :elevation="0"
            class="align-right my-6"
            prepend-icon="mdi-refresh"
            rounded="lg"
            text="Refresh"
            variant="outlined"
            @click="loadData"
        ></v-btn>

      </v-sheet>
    </div>
  </v-main>
</template>
