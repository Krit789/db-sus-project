<script lang="ts" setup>
import {VDataTable} from "vuetify/labs/VDataTable";

const {status, data, signIn, signOut} = useAuth();
</script>
<script lang="ts">
export default {
  data: () => ({
    dtErrorData: '',
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
    ], }),
  methods: {
    async loadData() {
      this.dtLoading = true;
      await $fetch("/api/data", {
        method: "POST",
        body: {
          type: 7,
          usage: "user"
        },
        lazy: true,
      })
          .catch((error) => {this.dtIsError = true; this.dtErrorData = error.data})
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
  <Navbar>
    <v-main class="">
      <h1 class="text-h3 font-weight-bold my-8 ml-8 text-left">
        Branches Management
      </h1>
      <v-sheet class="mt-8 ma-md-8 ma-xs-1 text-center" rounded="lg">
        <v-alert v-if="dtIsError" class="ma-3" color="error">{{ dtErrorData }}</v-alert>
        <v-btn
            :disabled="dtLoading"
            class="align-right mb-3"
            prepend-icon="mdi-refresh"
            text="Refresh"
            @click="loadData"
        ></v-btn>
        <v-data-table
            v-model:items-per-page="itemsPerPage"
            :headers="dtHeaders"
            :items="dtData"
            :loading="dtLoading"
            class="elevation-1"
            item-value="id"
            @click:row="
            (val, tabl) => {
              console.log(tabl.item.columns.location_id);
            }
          "
        >
        </v-data-table>
      </v-sheet>
    </v-main>
  </Navbar>
</template>
