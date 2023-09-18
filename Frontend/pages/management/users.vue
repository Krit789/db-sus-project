<script lang="ts" setup>
import {VDataTable} from "vuetify/labs/VDataTable";

const {status, data, signIn, signOut} = useAuth();

useHead({
  title: "User Management - Seatify Admin",
  meta: [{name: "Seatify App", content: "My amazing site."}],
  link: [{rel: "icon", type: "image/png", href: "favicon.ico"}],
});
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
        title: "User ID",
        align: "start",
        sortable: true,
        key: "user_id",
      },
      {title: "First Name", align: "end", key: "first_name"},
      {title: "Last Name", align: "end", key: "last_name"},
      {title: "Email", align: "end", key: "email"},
      {title: "Telephone", align: "end", key: "telephone"},
      {title: "Role", align: "end", key: "role"},
      {title: "Created On", align: "end", key: "created_on"},
      {title: "Status", align: "end", key: "status"},
    ],
  }),
  methods: {
    async loadData() {
      this.dtLoading = true;
      await $fetch("/api/data", {
        method: "POST",
        body: {
          type: 1,
          usage: "admin"
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
      <h1 class="text-h3 font-weight-bold mt-8 ml-8 text-left">
        User Management
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
              console.log(tabl.item.columns.user_id);
            }
          "
        ></v-data-table>
      </v-sheet>
    </v-main>
  </Navbar>
</template>
