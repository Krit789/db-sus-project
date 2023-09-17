<script lang="ts" setup>
import { VDataTable } from "vuetify/labs/VDataTable";
const { status, data, signIn, signOut } = useAuth();

useHead({
  title:'User Management - Seatify Admin',
  meta: [
    { name: 'Seatify App', content: 'My amazing site.' }
  ],
})

</script>
<script lang="ts">
export default {
  data: () => ({
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
      { title: "First Name", align: "end", key: "first_name" },
      { title: "Last Name", align: "end", key: "last_name" },
      { title: "Email", align: "end", key: "email" },
      { title: "Telephone", align: "end", key: "telephone" },
      { title: "Role", align: "end", key: "role" },
      { title: "Created On", align: "end", key: "created_on" },
      { title: "Status", align: "end", key: "status" },
    ],
    testPlacement: [
      {
        id: 1,
        first_name: "WatSone",
        last_name: "Onederman",
        email: "eeeeeeee",
        telephone: "0000000000",
        role: "USER",
        created_on: "YYYY-MM-DD HH:MI:SS",
      },
      {
        id: 2,
        first_name: "WatStwo",
        last_name: "twoderman",
        email: "eeeeeeeeee",
        telephone: "0000000000",
        role: "USER",
        created_on: "YYYY-MM-DD HH:MI:SS",
      },
      {
        id: 3,
        first_name: "WatSone",
        last_name: "Onederman",
        email: "ee",
        telephone: "0000000000",
        role: "USER",
        created_on: "YYYY-MM-DD HH:MI:SS",
      },
    ],
  }),
  methods: {
    loadData() {
      this.dtLoading = true;
      $fetch("/api/data", {
        method: "POST",
        body: {
          type: 20,
        },
      })
        .catch((error) => error.data)
        .then(({ status, message }) => {
          this.dtData = message;
          this.dtLoading = false;
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
        <v-btn
          class="align-right"
          text="Refresh"
          prepend-icon="mdi-refresh"
          @click="loadData"
          :disabled="dtLoading"
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
