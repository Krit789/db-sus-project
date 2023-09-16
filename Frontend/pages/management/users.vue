<script lang="ts" setup>
import {VDataTable} from "vuetify/labs/VDataTable";
const {status, data, signIn, signOut} = useAuth();
</script>
<script lang="ts">
export default {
  data: () => ({
    itemsPerPage: 10,
    dtLoading: false,
    dtHeaders: [
      {
        title: 'User ID',
        align: 'start',
        sortable: true,
        key: 'id',
      },
      {title: 'First Name', align: 'end', key: 'first_name'},
      {title: 'Last Name', align: 'end', key: 'last_name'},
      {title: 'Email', align: 'end', key: 'email'},
      {title: 'Telephone', align: 'end', key: 'telephone'},
      {title: 'Role', align: 'end', key: 'role'},
      {title: 'Created On', align: 'end', key: 'created_on'},
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
      },{
        id: 2,
        first_name: "WatStwo",
        last_name: "twoderman",
        email: "eeeeeeeeee",
        telephone: "0000000000",
        role: "USER",
        created_on: "YYYY-MM-DD HH:MI:SS",
      },{
        id: 3,
        first_name: "WatSone",
        last_name: "Onederman",
        email: "ee",
        telephone: "0000000000",
        role: "USER",
        created_on: "YYYY-MM-DD HH:MI:SS",
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
              "type": 20,
              "token": token
            },
            lazy: true,
            server: true
          }
      ).catch((error) => error).then(({status, message}) => {
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
        <h1 class="text-h3 font-weight-bold mt-8 ml-8 text-left">User Management</h1>
        <v-btn text="Click Me to fetch data table" @click="reservations(data?.value.name)"></v-btn>
        <v-sheet class="mt-8 ma-md-8 ma-xs-1 text-center" rounded="lg">
        <v-data-table v-model:items-per-page="itemsPerPage"
                      :headers="dtHeaders" :items="testPlacement" :loading="dtLoading"
                      class="elevation-1" item-value="id"
                      @click:row="(val, tabl) => { console.log(tabl.item.columns.id) }"></v-data-table>
      </v-sheet>
      </v-main>
    </Navbar>
</template>
