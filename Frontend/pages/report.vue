<script lang="ts" setup>
  import { useDisplay } from "vuetify";
  import { VDataTable } from "vuetify/labs/VDataTable";
  import "~/assets/stylesheets/global.css";
  import "~/assets/stylesheets/report.css";

  definePageMeta({
    middleware: ["allowed-roles-only"],
    meta: { permitted: ["MANAGER", "GOD"] },
  });
  useHead({
    title: "Report - Seatify Admin",
    meta: [{ name: "Seatify App", content: "My amazing site." }],
  });

  const { mobile } = useDisplay();
  const { status, data } = useAuth();
</script>

<script lang="ts">
  export default {
    data() {
      return {
        selectedDT: [],
        dtSearch: "",
        dtErrorData: "",
        dtIsError: false,
        dtData: [],
        itemsPerPage: 10,
        dtLoading: false as boolean,
        dtHeaders: [
          {
            title: "Location ID",
            align: "start",
            sortable: true,
            key: "l_id",
          },
          { title: "Name", align: "start", key: "l_name" },
          { title: "Manager", align: "end", key: "mgr_id" },
          { title: "Address", align: "end", key: "l_addr" },
          { title: "Status", align: "end", key: "l_status" },
          { title: "Open", align: "end", key: "l_open_time" },
          { title: "Close", align: "end", key: "l_close_time" },
        ],
      };
    },
    methods: {
      async loadData() {
        this.dtLoading = true;
        await $fetch("/api/data", {
          method: "POST",
          body: {
            type: 17,
            usage: "admin",
          },
          lazy: true,
        })
          .catch((error) => {
            this.dtIsError = true;
            this.dtErrorData = error.data;
          })
          .then((response) => {
            const { status, message } = response as { status: number; message: any };
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
  <v-main class="justify-center report_main">
    <div class="main_container mx-auto blur-effect py-4 px-2 mt-8 account_container justify-center">
      <h1 class="text-h3 font-weight-bold mt-8 ml-8 text-left">Report</h1>
      <v-sheet class="mt-8 ma-md-8 ma-xs-1 text-center" rounded="lg">
        <v-alert v-if="dtIsError" class="ma-3" color="error" icon="$error" title="Fetch Error">{{ dtErrorData }}</v-alert>
<v-no-ssr>
        <v-data-table
          v-model="selectedDT"
          :headers="dtHeaders"
          :items="dtData"
          :loading="dtLoading"
          :search="dtSearch"
          class="elevation-1"
          item-value="l_id"
          show-select
          @click:row="
            (val, tabl) => {
              console.log(tabl.item.l_id);
            }
          "
        >
          <template v-slot:top>
            <v-text-field v-model="dtSearch" placeholder="Search" prepend-inner-icon="mdi-store-search"></v-text-field>
          </template>
          <template v-slot:no-data>
            <v-alert icon="mdi-exclamation" title="Notice" color="info">
              <p>You don't have anything to report on.</p>
            </v-alert>
          </template>
        </v-data-table>
      </v-no-ssr>
        <v-btn :disabled="dtLoading" class="align-right my-3" prepend-icon="" text="Get Report" @click="console.log(selectedDT)"></v-btn>
      </v-sheet>
      <div class="w-100 justify-center align-center">
        <v-btn :disabled="dtLoading" :variant="'outlined'" class="align-right mb-3 w-100 mx-auto" prepend-icon="mdi-refresh" rounded="lg" text="Refresh" @click="loadData"></v-btn>
      </div>
    </div>
  </v-main>
</template>
