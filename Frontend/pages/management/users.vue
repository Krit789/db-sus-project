<script lang="ts" setup>
import {VDataTable} from "vuetify/labs/VDataTable";
import "~/assets/stylesheets/global.css";
import "~/assets/stylesheets/index.css";
import "~/assets/stylesheets/management/users.css";
import "~/assets/stylesheets/management/management.css";

const {status, data, signIn, signOut} = useAuth();
const route = useRouter();
useHead({
  title: "User Management - Seatify Admin",
  meta: [{name: "Seatify App", content: "My amazing site."}],
  link: [{rel: "icon", type: "image/png", href: "favicon.ico"}],
});

</script>
<script lang="ts">

export default {
  data: () => ({
    firstName: '',
    lastName: '',
    phoneNumber: '',
    userAction: false,
    dtErrorData: "",
    dtSearch: "",
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
          usage: "admin",
        },
        lazy: true,
      })
          .catch((error) => {
            this.dtIsError = true;
            this.dtErrorData = error.data;
          })
          .then(({message}) => {
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
  <v-main class="management_main">
    <v-dialog
        v-model="userAction"
        width="auto">
      <v-card width="500">
        <v-card-title>Modify User</v-card-title>
        <v-card-text>
          <v-text-field v-model="firstName" label="First Name" readonly></v-text-field>
          <v-text-field v-model="lastName" label="Last Name" readonly></v-text-field>
          <v-text-field prepend-icon="mdi-phone" v-model="phoneNumber" label="Phone number" readonly></v-text-field>
          <v-select prepend-icon="mdi-tag" :items="['User','Manager', 'Admin']" label="Role"></v-select>
          <v-btn prepend-icon="mdi-gavel" class="mr-5" color="error">Suspend User</v-btn>
          <v-btn prepend-icon="mdi-lock-reset" color="warning">Reset Password</v-btn>
        </v-card-text>
        <v-card-actions>
          <v-btn append-icon="mdi-check" color="success" @click="">Apply</v-btn>
          <v-btn append-icon="mdi-cancel" color="error" @click="userAction = false">Cancel</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <div class="main_container management_container mx-auto blur-effect">
      <h1 class="text-h3 font-weight-bold mt-8 ml-8 text-left">User Management</h1>
      <v-sheet class="mt-8 ma-md-8 ma-xs-1 text-center" rounded="lg">
        <v-alert v-if="dtIsError" class="ma-3" color="error" icon="$error" title="Fetch Error">{{
            dtErrorData
          }}
        </v-alert>
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
                      firstName = tabl.item.first_name
                      lastName = tabl.item.last_name
                      phoneNumber = tabl.item.telephone
                      userAction = true
                    }
                "
        >
          <template v-slot:top>
            <v-text-field v-model="dtSearch" placeholder="Search"
                          prepend-inner-icon="mdi-account-search"></v-text-field>
          </template>
        </v-data-table>
        <v-col>
          <v-btn :disabled="dtLoading"
                 :variant="'tonal'"
                 class="align-right mb-3"
                 prepend-icon="mdi-refresh"
                 rounded="lg"
                 text="Refresh"
                 @click="loadData"></v-btn>
        </v-col>
      </v-sheet>
    </div>
  </v-main>
</template>
