<script lang="ts" setup>
import {VDataTable} from "vuetify/labs/VDataTable";

const {status, data, signIn, signOut} = useAuth();
const route = useRouter();
import "~/assets/stylesheets/global.css";
import "~/assets/stylesheets/index.css";
import "~/assets/stylesheets/management/branches.css";
</script>
<script lang="ts">
export default {
  data: () => ({
    addBranch: false,
    dtSearch: "",
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
  <v-main class="">
    <v-dialog
        v-model="addBranch"
        width="auto"
    >
      <v-card width="400">
        <v-card-title>Add Branch</v-card-title>
        <v-card-text>
          <v-text-field label="Name"></v-text-field>
          <v-textarea label="Address"></v-textarea>
          <v-text-field label="Open Time" type="time"></v-text-field>
          <v-text-field label="Close Time" type="time"></v-text-field>
        </v-card-text>
        <v-card-actions>
          <v-btn append-icon="mdi-plus" color="success" @click="">Add</v-btn>
          <v-btn color="primary" @click="addBranch = false">Cancel</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <h1 class="text-h3 font-weight-bold my-8 ml-8 text-left">Branches Management</h1>
    <v-sheet class="mt-8 ma-md-8 ma-xs-1 text-center" rounded="lg">
      <v-alert v-if="dtIsError" class="ma-3" color="error" icon="$error" title="Fetch Error">{{ dtErrorData }}</v-alert>
      <v-btn :disabled="dtLoading" class="align-right mb-3" prepend-icon="mdi-refresh" text="Refresh"
             @click="loadData"></v-btn>
      <v-btn :disabled="dtLoading" class="ml-5 align-right mb-3" color="success" prepend-icon="mdi-plus"
             text="Add Branch"
             @click="addBranch = true"></v-btn>
      <v-data-table
          v-model:items-per-page="itemsPerPage"
          :headers="dtHeaders"
          :items="dtData"
          :loading="dtLoading"
          :search="dtSearch"
          class="elevation-1"
          item-value="id"
          @click:row="
                    (val, tabl) => {
                        $router.push('/management/branches/' + tabl.item.columns.location_id);
                    }
                "
      >
        <template v-slot:top>
          <v-text-field v-model="dtSearch" placeholder="Search" prepend-inner-icon="mdi-store-search"></v-text-field>
        </template>
      </v-data-table>
    </v-sheet>
  </v-main>
</template>
