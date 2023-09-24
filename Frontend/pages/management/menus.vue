<script lang="ts" setup>
import {VDataTable} from "vuetify/labs/VDataTable";

const route = useRouter();
const {status, data, signIn, signOut} = useAuth();
useHead({
  title: "Reservation Management - Seatify Admin",
  meta: [{name: "Seatify App", content: "My amazing site."}],
});
</script>

<script lang="ts">
export default {
  data: () => ({
    addMenuDialog: false,
    dtExpanded: [],
    dtSearch: "",
    dtIsError: false,
    dtErrorData: "",
    dtData: [],
    itemsPerPage: 10,
    dtLoading: false,
    dtHeaders: [
      {title: "Category", align: "start", key: "name"},
      {title: "Name", align: "end", key: "item_name"},
      {title: "Price (฿)", align: "end", key: "price"},
      {title: "", key: "data-table-expand"},
    ],
  }),
  methods: {
    async loadData() {
      this.dtLoading = true;
      await $fetch("/api/data", {
        method: "POST",
        body: {
          type: 5,
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
    <v-dialog
      width="auto"
      v-model="addMenuDialog"
    >
      <v-card width="400">
        <v-card-title>Add Menu</v-card-title>
        <v-card-text>
          <v-text-field label="Name"></v-text-field>
          
          <v-textarea label="Description"></v-textarea><v-text-field label="Image URL"></v-text-field>
          <v-select :items="['Not Assign','Seafood', 'Drinks', 'Dessert']" label="Category"></v-select>
          <v-btn append-icon="mdi-plus">Create Category</v-btn>
        </v-card-text>
        <v-card-actions>
        <v-btn color="success" append-icon="mdi-plus" @click="">Add</v-btn>
          <v-btn color="primary" @click="addMenuDialog = false">Cancel</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <h1 class="text-h3 font-weight-bold mt-8 ml-8 text-left">Menu Management</h1>
    <v-sheet class="mt-8 ma-md-8 ma-sm-5 text-center" rounded="lg">
      <v-alert v-if="dtIsError" class="ma-3" color="error" icon="$error" title="Fetch Error">{{ dtErrorData }}</v-alert>
      <v-btn :disabled="dtLoading" class="align-right mb-3" prepend-icon="mdi-refresh" text="Refresh"
             @click="loadData"></v-btn>
             <v-btn :disabled="dtLoading" color="success" class=" ml-5 mb-3" prepend-icon="mdi-plus" text="Add Menu" @click="addMenuDialog = true"></v-btn>
      <v-data-table
          v-model:items-per-page="itemsPerPage"
          :headers="dtHeaders"
          :items="dtData"
          :loading="dtLoading"
          :search="dtSearch"
          class="elevation-1"
          expanded="dtExpanded"
          item-value="menu_id"
          @click:row="
                    (val, tabl) => {
                        console.log(tabl.item.columns.menu_id);
                    }
                "
      >
        <template v-slot:top>
          <v-text-field v-model="dtSearch" placeholder="Search" prepend-inner-icon="mdi-book-search"></v-text-field>
        </template>
        <template v-slot:expanded-row="{ columns, item }">
                            <tr>
                                <td :colspan="columns.length" class="text-left">
                                    <v-container>
                                        <v-row>
                                            <v-col col="12" sm="6">
                                                <b>Description</b>
                                                <br />
                                                {{ item.raw.item_desc }}
                                                <br />
                                                <b>Actions</b>
                                                <br />
                                                <v-icon color="info">mdi-pencil</v-icon>
                                                <v-icon color="red">mdi-delete</v-icon>
                                            </v-col>
                                            <v-col col="12" sm="6">
                                                <b>Image</b>
                                                <br />
                                                <v-img height="300" width="300" aspect-ratio="1/1" cover :src="item.raw.img_url ? item.raw.img_url : 'https://livingstonbagel.com/wp-content/uploads/2016/11/food-placeholder.jpg'"></v-img>
                                            </v-col>
                                        </v-row>
                                    </v-container>
                                </td>
                            </tr>
                        </template>
      </v-data-table>
    </v-sheet>
  </v-main>
</template>
