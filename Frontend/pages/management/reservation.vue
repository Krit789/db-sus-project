<script lang="ts" setup>
import {
  VDataTable,
  VDataTableServer
} from "vuetify/labs/VDataTable";

const { status, data, signIn, signOut } = useAuth();
async function test() {
  const locations: any = await fetch(
    "http://localhost:3000/proxy/api/booking/Booking/Booking-viewalllocation-user.php",
    {
      method: "POST",
      body: {},
    }
  ).catch((error) => error).then(() => {

  });
}
</script>
<script lang="ts">
export default {
  data: () => ({
    itemsPerPage: 10,
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
    testuser: [
      {
        id: 1,
        first_name: "WatSoner",
        last_name: "Onedermaner",
      },
      {
        id: 2,
        first_name: "Twonies",
        last_name: "Twothpickies",
      }
    ]
  })
}
</script>
<template>
  <Navbar>
    <v-main class="">
      <h1 class="text-h3 font-weight-bold mt-8 ml-8 text-left">Reservation Management</h1>
      <template>
        <template>
          <v-data-table v-model:items-per-page="itemsPerPage" :headers="headers" :items="desserts" item-value="name"
            class="elevation-1"></v-data-table>
        </template>
      </template>
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
  </Navbar>
</template>
