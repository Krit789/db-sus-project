<script lang="ts" setup>
import { VDataTable } from "vuetify/labs/VDataTable";
const { status, data, signIn, signOut } = useAuth();

useHead({
  title: "Reservation Management - Seatify Admin",
  meta: [{ name: "Seatify App", content: "My amazing site." }],
});
</script>

<script lang="ts">
export default {
  data: () => ({
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
      { title: "User ID", align: "end", key: "user_id" },
      { title: "Reserved On", align: "end", key: "create_time" },
      { title: "Reserved For", align: "end", key: "arrival" },
      { title: "Status", align: "end", key: "status" },
      { title: "No. of Customer", align: "end", key: "cus_count" },
      { title: "Table ID", align: "end", key: "table_id" },
    ],
    testPlacement: [
      {
        id: 1,
        userID: 1,
        create_time: "YYYY-MM-DD HH:MI:SS",
        arrival: "YYYY-MM-DD HH:MI:SS",
        status: "INPROGRESS",
        cus_count: 5,
        table_id: 2,
      },
      {
        id: 2,
        userID: 2,
        create_time: "YYYY-MM-DD HH:MI:SS",
        arrival: "YYYY-MM-DD HH:MI:SS",
        status: "INPROGRESS",
        cus_count: 15,
        table_id: 1,
      },
      {
        id: 3,
        userID: 1,
        create_time: "YYYY-MM-DD HH:MI:SS",
        arrival: "YYYY-MM-DD HH:MI:SS",
        status: "INPROGRESS",
        cus_count: 25,
        table_id: 1,
      },
    ],
  }),
  methods: {
    async loadData() {
      this.dtLoading = true;
      await $fetch("/api/data", {
        method: "POST",
        body: {
          type: 31,
        },
        lazy: true,
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
        Reservation Management
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
              console.log(tabl.item.columns.res_id);
            }
          "
        ></v-data-table>
      </v-sheet>
    </v-main>
  </Navbar>
</template>
