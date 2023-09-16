<script lang="ts" setup>
import {
  VDataTable
} from "vuetify/labs/VDataTable";
const { status, data, signIn, signOut } = useAuth();
</script>
<script lang="ts">
export default {
  data: () => ({
    itemsPerPage: 10,
    dtLoading: false,
    dtHeaders: [
      {
        title: 'ID',
        align: 'start',
        sortable: false,
        key: 'id',
      },
      { title: 'User ID', align: 'end', key: 'userID' },
      { title: 'Reserved On', align: 'end', key: 'create_time' },
      { title: 'Reserved For', align: 'end', key: 'arrival' },
      { title: 'Status', align: 'end', key: 'status' },
      { title: 'No. of Customer', align: 'end', key: 'cus_count' },
      { title: 'Table ID', align: 'end', key: 'table_id' },
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
      }, {
        id: 2,
        userID: 2,
        create_time: "YYYY-MM-DD HH:MI:SS",
        arrival: "YYYY-MM-DD HH:MI:SS",
        status: "INPROGRESS",
        cus_count: 15,
        table_id: 1,
      }, {
        id: 3,
        userID: 1,
        create_time: "YYYY-MM-DD HH:MI:SS",
        arrival: "YYYY-MM-DD HH:MI:SS",
        status: "INPROGRESS",
        cus_count: 25,
        table_id: 1,
      },
    ],
  }), methods: {
    reservations(token) {
      console.log(token)
      this.dtLoading = true;
      useFetch(
        "http://localhost:3000/proxy/api/control.php",
        {
          method: "POST",
          body: {
            "type": 9,
            "token": token
          },
          lazy: true,
          server: true
        }
      ).catch((error) => error).then(({ status, message }) => {
        this.testPlacement = message;
        this.dtLoading = false;
      });
    }
  }
}
</script>
<template>
  <Navbar>
    <v-main class="">
      <h1 class="text-h3 font-weight-bold mt-8 ml-8 text-left">Reservation Management</h1>
      <v-btn text="Click Me to fetch data table" @click="reservations(data?.value.name)"></v-btn>
      <v-sheet class="mt-8 ma-md-8 ma-xs-1 text-center" rounded="lg">
        <v-data-table @click:row="(val, tabl) => { console.log(tabl.item.columns.id) }"
          v-model:items-per-page="itemsPerPage" :headers="dtHeaders" :loading="dtLoading" :items="testPlacement" item-value="id"
          class="elevation-1"></v-data-table>
      </v-sheet>
      <!-- <v-table
                fixed-header
                height="auto"
            >
                <thead>
                <tr>
                    <th class="text-left">
                    Reservation id
                    </th>
                    <th class="text-left">
                    User
                    </th>
                    <th class="text-left">
                    Count
                    </th>
                    <th class="text-left">
                    Table
                    </th>
                    <th class="text-left">
                    Created on
                    </th>
                    <th class="text-left">
                    Arrival by
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
                    v-for="(item, index) in testPlacement"
                    :key="index"
                >
                    <td class="text-left">{{ item.id }}</td>
                    <template v-for="user in testuser">
                        <td v-if="user.id == item.userID">{{ user.id }} : {{ user.first_name }} {{ user.last_name}}</td>
                    </template>
                    <td class="text-left">{{ item.cus_count }}</td>
                    <td class="text-left">{{ item.table_id }}</td>
                    <td class="text-left">{{ item.arrival }}</td>
                    <td class="text-left">{{ item.create_time }}</td>
                    <td class="text-left">{{ item.status }}</td>
                    <td>
                        <v-btn variant="text">
                            Detail
                        </v-btn>
                    </td>
                </tr>
                </tbody>
            </v-table> -->
    </v-main>
  </Navbar></template>
