<script lang="ts" setup>
import {VDataTable} from "vuetify/labs/VDataTable";

const route = useRouter()
const {status, data, signIn, signOut} = useAuth();
useHead({
  title: "Reservation Management - Seatify Admin",
  meta: [{name: "Seatify App", content: "My amazing site."}],
});
</script>

<script lang="ts">
export default {
  data: () => ({
    dtSearch: "",
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
      {title: "User ID", align: "end", key: "user_id"},
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
          type: 12,
          usage: "admin",
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
    <h1 class="text-h3 font-weight-bold mt-8 ml-8 text-left">Reservation Management</h1>
    <v-sheet class="mt-8 ma-md-8 ma-sm-5 text-center" rounded="lg">
      <v-alert v-if="dtIsError" class="ma-3" color="error" icon="$error" title="Fetch Error">{{ dtErrorData }}</v-alert>
      <v-btn :disabled="dtLoading" class="align-right mb-3" prepend-icon="mdi-refresh" text="Refresh"
             @click="loadData"></v-btn>
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
                        console.log(tabl.item.columns.res_id);
                    }
                "
      >
        <template v-slot:top>
          <v-text-field v-model="dtSearch" placeholder="Search" prepend-inner-icon="mdi-book-search"></v-text-field>
        </template>
      </v-data-table>
    </v-sheet>
  </v-main>
</template>
