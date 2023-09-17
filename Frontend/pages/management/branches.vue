<script lang="ts" setup>
import { VDataTable } from "vuetify/labs/VDataTable";
const { status, data, signIn, signOut } = useAuth();
</script>
<script lang="ts">
export default {
  data: () => ({
    dtData: [],
    itemsPerPage: 10,
    dtLoading: false,
    dtHeaders: [
      {
        title: "Name",
        align: "start",
        sortable: true,
        key: "name",
      },
      { title: "Manager", align: "end", key: "managerID" },
      { title: "Address", align: "end", key: "address" },
      { title: "Status", align: "end", key: "status" },
      { title: "Open", align: "end", key: "open_time" },
      { title: "Close", align: "end", key: "close_time" },
    ],
    testPlacement: [
      {
        name: "location1",
        address: "1/1 onestead, onestreet, one, one, 11111",
        status: "OPERATIONAL",
        managerID: 1,
      },
      {
        name: "location2",
        address: "2/2 twostead, twostreet, two, two, 22222",
        status: "MAINTENANCE",
        managerID: 2,
      },
      {
        name: "location3",
        address: "3/3 tristead, tristreet, tri, tri, 33333",
        status: "OUTOFORDER",
        managerID: 1,
      },
      {
        name: "location4",
        address: "4/4 fourstead, fourstreet, four, four, 44444",
        status: "OPERATIONAL",
        managerID: 2,
      },
    ],
    testManager: [
      {
        id: 1,
        first_name: "WatSone",
        last_name: "Onederman",
      },
      {
        id: 2,
        first_name: "Twoney",
        last_name: "Twothpick",
      },
    ],
  }),
  methods: {
    loadData() {
      this.dtLoading = true;
      $fetch("/api/data", {
        method: "POST",
        body: {
          type: 7,
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
      <h1 class="text-h3 font-weight-bold my-8 ml-8 text-left">
        Branches Management
      </h1>
      <v-sheet class="mt-8 ma-md-8 ma-xs-1 text-center" rounded="lg">
        <v-btn class="align-right" text="Refresh" prepend-icon="mdi-refresh" @click="loadData" :disabled="dtLoading"></v-btn>
        <v-data-table
          v-model:items-per-page="itemsPerPage"
          :headers="dtHeaders"
          :items="dtData"
          :loading="dtLoading"
          class="elevation-1"
          item-value="id"
          @click:row="
            (val, tabl) => {
              console.log(tabl.item.columns.id);
            }
          "
        >
        </v-data-table>
      </v-sheet>
      <!-- <v-table
          fixed-header
          height="auto"
      >
        <thead>
        <tr>
          <th class="text-left">
            Name
          </th>
          <th class="text-left">
            Manager
          </th>
          <th class="text-left">
            Address
          </th>
          <th class="text-left">
            Status
          </th>
          <th class="text-left">
            Action
          </th>
        </tr>
        </thead>
        <tbody>
        <tr
            v-for="item in testPlacement"
            :key="item.name"
        >
          <td class="text-left">{{ item.name }}</td>
          <template v-for="manager in testManager">
            <td v-if="manager.id == item.managerID">{{ manager.first_name }} {{ manager.last_name }}</td>
          </template>
          <td class="text-left">{{ item.address }}</td>
          <td class="text-left">{{ item.status }}</td>
          <td>
            <v-btn variant="text">
              Manage
            </v-btn>
          </td>
        </tr>
        </tbody>
      </v-table> -->
    </v-main>
  </Navbar>
</template>
