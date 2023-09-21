<script lang="ts" setup>
import {useDisplay} from "vuetify";
import {VDataTable} from "vuetify/labs/VDataTable";
const {mobile} = useDisplay();
const {status, data} = useAuth();

useHead({
  title: "Report - Seatify Admin",
  meta: [{name: "Seatify App", content: "My amazing site."}],
});
</script>

<script lang="ts">
export default {
  data: () => ({
    selectedDT: [],
    dtSearch: '',
    dtErrorData: "",
    dtIsError: false,
    dtData: [],
    itemsPerPage: 10,
    dtLoading: false,
    dtHeaders: [
      {
        title: "Location ID",
        align: "start",
        sortable: true,
        key: "location_id",
      },
      {title: "Name", align: "start", key: "name"},
      {title: "Manager", align: "end", key: "managerID"},
      {title: "Address", align: "end", key: "address"},
      {title: "Status", align: "end", key: "status"},
      {title: "Open", align: "end", key: "open_time"},
      {title: "Close", align: "end", key: "close_time"},
    ],
  }),
  methods: {
    async loadData() {
      this.dtLoading = true;
      await $fetch("/api/data", {
        method: "POST",
        body: {
          type: 7,
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
  <v-main>
    <h1 class="text-h3 font-weight-bold mt-8 ml-8 text-left">Report</h1>
    <v-sheet class="mt-8 ma-md-8 ma-xs-1 text-center" rounded="lg">
      <v-alert
          v-if="dtIsError"
          class="ma-3"
          color="error"
          icon="$error"
          title="Fetch Error"
      >{{ dtErrorData }}
      </v-alert>
      <v-btn
          :disabled="dtLoading"
          class="align-right mb-3"
          prepend-icon="mdi-refresh"
          text="Refresh"
          @click="loadData"
      ></v-btn>
      <v-data-table
          v-model="selectedDT"
          :headers="dtHeaders"
          :items="dtData"
          :loading="dtLoading"
          class="elevation-1"
          item-value="location_id"
          show-select
          :search="dtSearch"
          @click:row="
                    (val, tabl) => {
                        console.log(tabl.item.columns.location_id);
                    }
                "
      >
      <template v-slot:top>
                                        <v-text-field
                                        prepend-inner-icon="mdi-store-search"
                                            v-model="dtSearch"
                                            placeholder="Search"
                                        ></v-text-field>
                                    </template>
      </v-data-table>
      <v-btn
          :disabled="dtLoading"
          class="align-right my-3"
          prepend-icon=""
          text="Get Report"
          @click=""
      ></v-btn>
    </v-sheet>
    <Credit/>
  </v-main>
</template>
