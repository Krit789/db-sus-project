<script lang="ts" setup>
// import { VDataTable } from "vuetify/labs/VDataTable";
import {VDataTable} from 'vuetify/labs/VDataTable';
import {useDisplay} from 'vuetify';
import '~/assets/stylesheets/global.css';
import '~/assets/stylesheets/index.css';
import '~/assets/stylesheets/management/menus.css';
import '~/assets/stylesheets/management/management.css';

const {mobile} = useDisplay();
const route = useRouter();
const {status, data, signIn, signOut} = useAuth();
useHead({
  title: 'Menu Management - Seatify Admin',
  meta: [{name: 'Seatify App', content: 'My amazing site.'}],
});
definePageMeta({
  middleware: ['allowed-roles-only'],
  meta: {permitted: ['GOD']},
});
</script>

<script lang="ts">
interface menu_category {
  c_id: number;
  c_name: string;
}

interface MenuItem {
  m_id: number;
  m_name: string;
  m_desc: string | null;
  m_price: number;
  m_img: string | null;
  c_id: number | null;
  c_name: string | null;
}

export default {
  data: () => ({
    groupBy: [{key: 'c_name', order: 'asc'}] as SortItem[],
    catRename: false,
    catRenameMode: 0,
    catDel: false,
    menuDialogMode: 0,
    addMenuDialog: false,
    manageCategory: false,
    menuName: '',
    menuDesc: '',
    menuID: 0,
    menuPrice: 69,
    menuCategoryID: 0,
    menuCategoryName: '',
    menuImgUrl: '',
    confDel: false,
    dtSearch: '',
    dtIsError: false,
    dtErrorData: '',
    menuCategory: [] as menu_category[],
    dtData: [] as MenuItem[],
    dtExpand: [],
    itemsPerPage: 10,
    dtLoading: false,
    snackbar: false,
    NotiColor: '',
    timeout: 2000,
    NotiIcon: '',
    NotiText: '',
    dtHeaders: [
      // { title: "Menu ID", align: "start", key: "m_id" },
      // { title: "Menu Description", align: " d-none", key: "item_desc" },
      // { title: "Category", align: " d-none", key: "name" },
      {title: 'Name', align: 'start', key: 'm_name'},
      {title: 'Price', align: 'start', key: 'm_price'},
    ] as DataTableHeader[],
  }),
  methods: {
    urlValidator(url: string) {
      const expression = /[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b([-a-zA-Z0-9()@:%_\+.~#?&//=]*)?/gi;
      const regex = new RegExp(expression);
      if (url.match(regex) || url === '') return true;
      return 'Invalid URL Format';
    },
    async loadData() {
      this.dtLoading = true;
      await $fetch('/api/data', {
        method: 'POST',
        body: {
          type: 5,
          usage: 'admin',
        },
        lazy: true,
      })
          .catch((error) => {
            this.dtIsError = true;
            this.dtErrorData = error.data;
          })
          .then((response) => {
            const {status, message} = response as {
              status: number;
              message: any;
            };
            this.dtData = message;
            this.dtLoading = false;
            this.dtIsError = false;
          });
    },
    async loadCategoryData() {
      this.dtLoading = true;
      await $fetch('/api/data', {
        method: 'POST',
        body: {
          type: 16,
          usage: 'admin',
        },
        lazy: true,
      })
          .catch((error) => {
            this.dtIsError = true;
            this.dtErrorData = error.data;
          })
          .then((response) => {
            const {status, message} = response as {
              status: number;
              message: any;
            };
            const newCategory = {
              c_id: 0,
              c_name: 'Uncategorized',
            };
            const nextIndex = Object.keys(message).length;
            message[nextIndex] = newCategory;
            this.menuCategory = message;
            this.dtLoading = false;
            this.dtIsError = false;
          });
    },
    async deleteMenu(menu_id: number) {
      this.dtLoading = true;
      await $fetch('/api/data', {
        method: 'POST',
        body: {
          type: 9,
          usage: 'admin',
          menu_id: menu_id,
        },
        lazy: true,
      })
          .catch((error) => {
            this.dtIsError = true;
            this.dtErrorData = error.data;
          })
          .then((response) => {
            const {status, message} = response as {
              status: number;
              message: any;
            };
            this.dtLoading = false;
            this.dtIsError = false;
            if (status == 0) {
              this.snackbar = true;
              this.NotiColor = 'error';
              this.NotiIcon = 'mdi-alert';
              this.NotiText = message;
            } else if (status == 1) {
              this.snackbar = true;
              this.NotiColor = 'success';
              this.NotiIcon = 'mdi-check';
              this.NotiText = message;
            }
            this.confDel = false;
            this.loadData();
          });
    },
    async deleteCategory(cat_id: number) {
      this.dtLoading = true;
      await $fetch('/api/data', {
        method: 'POST',
        body: {
          type: 15,
          usage: 'admin',
          c_id: cat_id,
        },
        lazy: true,
      })
          .catch((error) => {
            this.dtIsError = true;
            this.dtErrorData = error.data;
          })
          .then((response) => {
            const {status, message} = response as {
              status: number;
              message: any;
            };
            this.dtLoading = false;
            this.dtIsError = false;
            if (status == 0) {
              this.snackbar = true;
              this.NotiColor = 'error';
              this.NotiIcon = 'mdi-alert';
              this.NotiText = message;
            } else if (status == 1) {
              this.snackbar = true;
              this.NotiColor = 'success';
              this.NotiIcon = 'mdi-check';
              this.NotiText = message;
            }
            this.catDel = false;
            this.loadCategoryData();
            this.loadData();
          });
    },
    async managCategory(cat_id: number, cat_name: string) {
      this.dtLoading = true;

      let catQuery = {
        usage: 'admin',
        c_name: cat_name,
      };
      if (cat_id > 0) {
        catQuery = Object.assign({}, catQuery, {c_id: cat_id, type: 14});
      } else {
        catQuery = Object.assign({}, catQuery, {type: 6});
      }

      await $fetch('/api/data', {
        method: 'POST',
        body: catQuery,
        lazy: true,
      })
          .catch((error) => {
            this.dtIsError = true;
            this.dtErrorData = error.data;
          })
          .then((response) => {
            const {status, message} = response as {
              status: number;
              message: any;
            };
            this.dtLoading = false;
            this.dtIsError = false;
            if (status == 0) {
              this.snackbar = true;
              this.NotiColor = 'error';
              this.NotiIcon = 'mdi-alert';
              this.NotiText = message;
            } else if (status == 1) {
              this.snackbar = true;
              this.NotiColor = 'success';
              this.NotiIcon = 'mdi-check';
              this.NotiText = message;
            }
            this.catRename = false;
            this.loadCategoryData();
            this.loadData();
          });
    },
    async manageMenu(menu_id: number, menu_name: string, menu_price: number, menu_desc: string, menu_category_id: number, menu_img: string) {
      // this.dtLoading = true;
      let menuQuery = {
        usage: 'admin',
        m_name: menu_name,
        price: menu_price,
      };
      if (menu_id > 0) {
        menuQuery = Object.assign({}, menuQuery, {menu_id: menu_id, type: 8});
      } else {
        menuQuery = Object.assign({}, menuQuery, {type: 7});
      }
      if (menu_desc) {
        menuQuery = Object.assign({}, menuQuery, {m_desc: menu_desc});
      }
      if (menu_category_id > 0) {
        menuQuery = Object.assign({}, menuQuery, {m_category: menu_category_id});
      } else {
        menuQuery = Object.assign({}, menuQuery, {m_category: 0});
      }
      if (menu_img) {
        menuQuery = Object.assign({}, menuQuery, {img_url: menu_img});
      }
      console.log(menuQuery);
      await $fetch('/api/data', {
        method: 'POST',
        body: menuQuery,
        lazy: true,
      })
          .catch((error) => {
            this.dtIsError = true;
            this.dtErrorData = error.data;
          })
          .then((response) => {
            const {status, message} = response as {
              status: number;
              message: any;
            };
            this.dtLoading = false;
            this.dtIsError = false;
            if (status === 0) {
              this.snackbar = true;
              this.NotiColor = 'error';
              this.NotiIcon = 'mdi-alert';
              this.NotiText = message;
            } else if (status === 1) {
              this.snackbar = true;
              this.NotiColor = 'success';
              this.NotiIcon = 'mdi-check';
              this.NotiText = message;
            }
            this.addMenuDialog = false;
            this.loadData();
          });
    },
    priceRule() {
      if (this.menuPrice >= 0) return true;
      return 'Price cannot be negative';
    },
    requiredForm(value: string) {
      if (value) return true;
      return 'This field is required';
    },
  },

  beforeMount() {
    this.loadData();
    this.loadCategoryData();
  },
};
</script>
<template>
  <v-main class="management_main">
    <v-snackbar v-model="snackbar" :color="NotiColor" :timeout="timeout" location="top" multi-line>
      <v-icon :icon="NotiIcon" start></v-icon>
      {{ NotiText }}
    </v-snackbar>
    <v-dialog v-model="addMenuDialog" :fullscreen="mobile" :width="mobile ? '100%' : 'auto'">
      <v-card :width="mobile ? 'auto' : '400px'">
        <v-card-title v-text="menuDialogMode == 0 ? 'Add Menu' : 'Edit Menu'"></v-card-title>
        <v-form
            v-on:submit="
            () => {
              manageMenu(menuID, menuName, menuPrice, menuDesc, menuCategoryID, menuImgUrl);
            }
          "
            @submit.prevent>
          <v-card-text>
            <v-text-field v-model="menuName" :rules="[requiredForm]" label="Name *"></v-text-field>

            <v-textarea v-model="menuDesc" label="Description"></v-textarea>
            <v-text-field v-model="menuPrice" :prefix="'฿'" :rules="[priceRule]" label="Price" min="1"
                          oninput="validity.valid || (value=1)" type="number"></v-text-field>
            <v-text-field v-model="menuImgUrl" :rules="[urlValidator]" label="Image URL"></v-text-field>
            <v-select v-model="menuCategoryID" :items="menuCategory" hide-details item-title="c_name" item-value="c_id"
                      label="Category"></v-select>
            <v-btn append-icon="mdi-shape" class="mt-4" color="warning" variant="text" @click="manageCategory = true">
              Manage Category
            </v-btn>
          </v-card-text>
          <v-card-actions>
            <v-btn :append-icon="menuDialogMode == 0 ? 'mdi-plus' : 'mdi-content-save'" :disabled="!menuName"
                   :text="menuDialogMode == 0 ? 'Add' : 'Save'" color="success" type="submit"></v-btn>
            <v-btn
                color="primary"
                @click="
                () => {
                  addMenuDialog = false;
                }
              ">
              Cancel
            </v-btn>
          </v-card-actions>
        </v-form>
      </v-card>
    </v-dialog>
    <v-dialog v-model="manageCategory" :fullscreen="mobile" :width="mobile ? '100%' : 'auto'">
      <v-card :width="mobile ? 'auto' : '450px'">
        <v-card-title>Manage Category</v-card-title>
        <v-card-text>
          <v-table :density="mobile ? 'compact' : 'comfortable'" class="overflow-auto" fixed-header height="70vh">
            <thead>
            <tr>
              <th class="text-left">ID</th>
              <th class="text-left">Name</th>
              <th class="text-right">Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="item in menuCategory" :key="item.c_id">
              <td>{{ item.c_id }}</td>
              <td>{{ item.c_name }}</td>
              <td v-show="item.c_id !== 0" class="text-right">
                <v-tooltip location="top">
                  <template v-slot:activator="{ props }">
                    <v-icon
                        v-ripple
                        class="mr-3"
                        color="info"
                        v-bind="props"
                        @click="
                          () => {
                            menuCategoryName = item.c_name;
                            menuCategoryID = item.c_id;
                            catRenameMode = 0;
                            catRename = true;
                          }
                        ">
                      mdi-pencil
                    </v-icon>
                  </template>
                  <span>Rename</span>
                </v-tooltip>
                <v-tooltip location="top">
                  <template v-slot:activator="{ props }">
                    <v-icon
                        v-ripple
                        color="red"
                        v-bind="props"
                        @click="
                          () => {
                            menuCategoryID = item.c_id;
                            menuCategoryName = item.c_name;
                            catDel = true;
                          }
                        ">
                      mdi-delete
                    </v-icon>
                  </template>
                  <span>Delete</span>
                </v-tooltip>
              </td>
            </tr>
            </tbody>
          </v-table>
          <v-btn
              class="mt-3"
              prepend-icon="mdi-plus"
              variant="text"
              @click="
              () => {
                menuCategoryName = '';
                menuCategoryID = -1;
                catRenameMode = 1;
                catRename = true;
              }
            ">
            Create Category
          </v-btn>
        </v-card-text>
        <v-card-actions>
          <v-btn block @click="manageCategory = false">Close</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog v-model="confDel" :width="'auto'">
      <v-card :width="mobile ? 'auto' : '400px'">
        <v-card-title>Menu Deletion</v-card-title>
        <v-card-text>
          Are you sure that you want to delete
          <br/>
          <b>{{ menuName }}</b>
          ?
        </v-card-text>
        <v-card-actions>
          <v-btn
              color="success"
              prepend-icon="mdi-check"
              @click="
              () => {
                deleteMenu(menuID);
                menuID = 0;
              }
            ">
            Confirm
          </v-btn>
          <v-btn color="error" prepend-icon="mdi-cancel" @click="confDel = false">Cancel</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog v-model="catDel" :width="'auto'">
      <v-card :width="mobile ? 'auto' : '400px'">
        <v-card-title>Category Deletion</v-card-title>
        <v-card-text>
          Are you sure that you want to delete
          <b>{{ menuCategoryName }}</b>
          ? This deletion will remove all previously set category in the menus
        </v-card-text>
        <v-card-actions>
          <v-btn
              color="success"
              prepend-icon="mdi-check"
              @click="
              () => {
                deleteCategory(menuCategoryID);
              }
            ">
            Confirm
          </v-btn>
          <v-btn color="error" prepend-icon="mdi-cancel" @click="catDel = false">Cancel</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog v-model="catRename" :width="'auto'">
      <v-card :width="mobile ? '250px' : '400px'">
        <v-card-title>{{ catRenameMode === 0 ? 'Rename Category' : 'Create Category' }}</v-card-title>
        <v-card-text>
          <v-text-field v-model="menuCategoryName" label="Category Name"></v-text-field>
        </v-card-text>
        <v-card-actions>
          <v-btn
              :prepend-icon="catRenameMode === 0 ? 'mdi-content-save' : 'mdi-check'"
              color="success"
              @click="
              () => {
                managCategory(menuCategoryID, menuCategoryName);
              }
            ">
            {{ catRenameMode == 0 ? 'Save' : 'Create' }}
          </v-btn>
          <v-btn color="error" prepend-icon="mdi-cancel" @click="catRename = false">Cancel</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <div class="main_container management_container mx-auto blur-effect">
      <h1 class="text-h3 font-weight-bold mt-8 ml-8 text-left">Menu Management</h1>
      <p class="text-h5 font-weight-light ml-8 text-left">{{ mobile ? 'Tap' : 'Click' }} on each category and menu to
        see further informations and actions</p>
      <v-sheet class="mt-8 ma-md-8 ma-sm-5" rounded="lg">
        <v-alert v-if="dtIsError" class="ma-3" color="error" icon="$error" title="Fetch Error">{{
            dtErrorData
          }}
        </v-alert>

        <v-data-table :density="mobile ? 'compact' : 'comfortable'" :group-by="groupBy" :headers="dtHeaders"
                      :items="dtData" :loading="dtLoading" :search="dtSearch" class="elevation-1" fixed-header
                      height="60vh" item-value="m_id" items-per-page="-1" sticky>
          <template v-slot:top>
            <v-text-field v-model="dtSearch" placeholder="Search" prepend-inner-icon="mdi-book-search"></v-text-field>
          </template>
          <template v-slot:group-header="{ item, columns, toggleGroup, isGroupOpen }">
            <tr v-ripple class="table-hover" @click="toggleGroup(item)">
              <td :colspan="columns.length" class="text-start td-hover">
                <v-btn :icon="isGroupOpen(item) ? '$expand' : '$next'" size="small" variant="text"></v-btn>
                {{ item.value }} ({{ item.items.length }})
              </td>
            </tr>
          </template>
          <template v-slot:item="{ internalItem, item, toggleExpand, isExpanded }">
            <tr
                v-ripple
                class="table-hover"
                @click="
                () => {
                  toggleExpand(internalItem);
                }
              ">
              <td class="text-start td-hover"></td>
              <td class="text-start td-hover">{{ item.m_name }}</td>
              <td class="text-start td-hover">{{ item.m_price.toLocaleString() }} ฿</td>
            </tr>
          </template>

          <template v-slot:expanded-row="{ columns, item }">
            <tr>
              <td :colspan="columns.length" class="text-left">
                <v-container>
                  <v-row>
                    <v-col col="12" sm="6">
                      <b>Menu ID</b>
                      <br/>
                      {{ item.m_id }}
                      <br/>
                      <b>Description</b>
                      <br/>
                      {{ item.m_desc }}
                      <br/>
                      <br/>
                      <b>Actions</b>
                      <br/>
                      <v-btn
                          color="info"
                          prepend-icon="mdi-pencil"
                          variant="text"
                          @click="
                          () => {
                            menuDialogMode = 1;
                            menuName = item.m_name;
                            menuDesc = item.m_desc;
                            menuID = item.m_id;
                            menuPrice = item.m_price;
                            menuImgUrl = item.m_img;
                            menuCategoryID = item.c_id ? item.c_id : 0;
                            addMenuDialog = true;
                          }
                        ">
                        Edit Menu
                      </v-btn>
                      <v-btn
                          color="red"
                          prepend-icon="mdi-delete"
                          variant="text"
                          @click="
                          () => {
                            menuID = item.m_id;
                            menuName = item.m_name;
                            confDel = true;
                          }
                        ">
                        Remove Menu
                      </v-btn>
                    </v-col>
                    <v-col cols="12" md="4" sm="6">
                      <b>Preview</b>
                      <br/>
                      <v-card v-ripple class="text-center" elevation="4">
                        <v-img :src="item.m_img ? item.m_img : '/images/img-coming-soon.webp'" aspect="16/9" cover
                               height="300">
                          <template v-slot:error>
                            <v-img cover height="300" src="/images/img-error.webp" width="300"></v-img>
                          </template>
                        </v-img>
                        <v-card-title>
                          {{ item.m_name }}
                        </v-card-title>
                        <v-card-subtitle>
                          <v-chip color="warning">
                            {{ item.c_name }}
                          </v-chip>
                          <v-chip color="info">{{ item.m_price.toLocaleString() }}฿</v-chip>
                        </v-card-subtitle>
                        <v-card-text>
                          {{ item.m_desc }}
                        </v-card-text>
                      </v-card>
                    </v-col>
                  </v-row>
                </v-container>
              </td>
            </tr>
          </template>
        </v-data-table>
        <v-col class="pt-5 text-center">
          <v-btn :disabled="dtLoading" :variant="'tonal'" class="align-right mb-3" prepend-icon="mdi-refresh"
                 rounded="lg" text="Refresh" @click="loadData"></v-btn>
          <v-btn
              :disabled="dtLoading"
              :variant="'tonal'"
              class="ml-5 mb-3"
              color="success"
              prepend-icon="mdi-plus"
              rounded="lg"
              text="Add Menu"
              @click="
              () => {
                menuDialogMode = 0;
                menuID = -1;
                menuName = '';
                menuDesc = '';
                menuID = 0;
                menuPrice = 1;
                menuImgUrl = '';
                menuCategoryID = 0;
                addMenuDialog = true;
              }
            "></v-btn>
          <v-btn :disabled="dtLoading" :variant="'tonal'" class="ml-5 mb-3" color="warning" prepend-icon="mdi-shape"
                 rounded="lg" text="Manage Category" @click="manageCategory = true"></v-btn>
        </v-col>
      </v-sheet>
    </div>
  </v-main>
</template>

<style scoped></style>
