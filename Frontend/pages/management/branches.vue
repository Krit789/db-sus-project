<script lang="ts" setup>
import {VDataTable} from 'vuetify/labs/VDataTable';
import {VSkeletonLoader} from 'vuetify/labs/VSkeletonLoader';
import {useDisplay} from 'vuetify';
import {DateTime} from 'luxon';
import '~/assets/stylesheets/global.css';
import '~/assets/stylesheets/index.css';
import '~/assets/stylesheets/management/branches.css';
import '~/assets/stylesheets/management/management.css';

const {mobile} = useDisplay();

const {data} = useAuth();
const route = useRouter();
useHead({
  title: 'Branches Management - Seatify Admin',
  meta: [{name: 'Seatify App', content: 'My amazing site.'}],
});
definePageMeta({
  middleware: ['allowed-roles-only'],
  meta: {permitted: ['MANAGER', 'GOD']},
});
</script>
<script lang="ts">
interface ManagerItem {
  u_id: number;
  u_name: string;
  u_email: string;
}

// Menu Item interface
interface MenuItem {
  m_id: number;
  m_name: string;
  c_id: number | null;
  c_name?: string;
  m_price: number;
}

// Complete Data type (including both menu and restricted menu)
type CompleteMenuData = [MenuItem[], MenuItem[]];

type Location = {
  l_id: number;
  l_name: string;
  l_addr: string;
  l_open_time: string;
  l_close_time: string;
  l_status: 'OPERATIONAL' | 'MAINTENANCE' | 'OUTOFORDER';
  l_layout_img: string | null;
  l_mgr_id: number | null;
  l_creation: string;
  mgr_fn: string | null;
  mgr_ln: string | null;
  mgr_tel: string | null;
  mgr_email: string | null;
};

type Table = {
  table_id: number;
  name: string;
  capacity: number;
};
export default {
  data: () => ({
    layoutPreview: false,
    managerDialog: false,
    addTableDialog: false,
    addTableMode: 0, // 0 for adding table : 1 for renaming table
    delTableDialog: false,
    delMenuResDialog: false,
    addMenuResDialog: false,
    loadingDialog: false,
    delLocDialog: false,
    tableName: '',
    tableNameDialog: '',
    tabNum: 1,
    tableID: 0,
    tableCapacity: 1,
    bEditor: false,
    bID: 0,
    bName: '',
    bAddress: '',
    bOpenTime: '',
    bCloseTime: '',
    blayout: '',
    bMgrID: 0,
    bSelMgrID: 0,
    bMgrName: '',
    bStatus: 1,
    menuID: null as number | null,
    menuName: '',
    addBranch: false,
    dtSearch: '',
    dtErrorData: '',
    dtIsError: false,
    dtData: [] as Location[] | [],
    managerList: [] as ManagerItem[] | [],
    locSeat: [] as Table[] | [],
    locMenu: [] as CompleteMenuData | [],
    itemsPerPage: 10,
    dtLoading: false,
    snackbar: false,
    NotiColor: '',
    timeout: 2000,
    NotiIcon: '',
    NotiText: '',
    bStatusList: [
      {
        id: 1,
        name: 'Operational',
      },
      {
        id: 2,
        name: 'Maintenance',
      },
      {
        id: 3,
        name: 'Out Of Order',
      },
    ],
    dtHeaders: [
      [
        {
          title: 'Location ID',
          align: 'start',
          sortable: true,
          key: 'l_id',
        },
        {title: 'Name', align: 'start', key: 'l_name'},
        {title: 'Status', align: 'center', key: 'l_status'},
        {title: 'Address', align: ' d-none', key: 'l_addr'},
        {title: 'Status', align: ' d-none', key: 'l_status'},
        {title: 'Open', align: ' d-none', key: 'l_open_time'},
        {title: 'Close', align: ' d-none', key: 'l_close_time'},
      ],
    ],
  }),
  methods: {
    menuItemProps(item: any) {
      return {
        id: item.m_id,
        title: item.m_name,
        subtitle: item.c_name,
      };
    },
    managerItemProps(item: any) {
      return {
        id: item.u_id,
        title: item.u_name,
        subtitle: item.u_email,
      };
    },
    async loadData() {
      this.dtLoading = true;
      await $fetch('/api/data', {
        method: 'POST',
        body: {
          type: 17,
          usage: 'admin',
        },
        lazy: true,
      })
          .catch((error) => {
            this.dtIsError = true;
            this.dtErrorData = error.data;
          })
          .then((response) => {
            const {message} = response as {
              status: number;
              message: any;
            };
            this.dtData = message;
            this.dtLoading = false;
            this.dtIsError = false;
          });
    },
    async loadTableByLocationID(loc_id: number) {
      this.loadingDialog = true;
      await $fetch('/api/data', {
        method: 'POST',
        body: {
          type: 10,
          usage: 'manager',
          l_id: loc_id,
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
            if (status == 0) {
              this.snackbar = true;
              this.NotiColor = 'error';
              this.NotiIcon = 'mdi-alert';
              this.NotiText = message;
            } else if (status == 1) {
              this.locSeat = message;
            }
            this.loadingDialog = false;
            this.dtIsError = false;
          });
    },

    async loadManager() {
      this.loadingDialog = true;
      await $fetch('/api/data', {
        method: 'POST',
        body: {
          type: 18,
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
            if (status == 0) {
              this.snackbar = true;
              this.NotiColor = 'error';
              this.NotiIcon = 'mdi-alert';
              this.NotiText = message;
            } else if (status == 1) {
              const unAssign = {
                u_id: 0,
                u_name: 'None',
                u_email: '',
              };
              const nextIndex = Object.keys(message).length;
              message[nextIndex] = unAssign;
              this.managerList = message;
            }
            this.dtIsError = false;
            this.loadingDialog = false;
          });
    },
    async loadMenuByLocationID(loc_id: number) {
      this.loadingDialog = true;
      await $fetch('/api/data', {
        method: 'POST',
        body: {
          type: 3,
          usage: 'manager',
          l_id: loc_id,
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
            if (status == 0) {
              this.snackbar = true;
              this.NotiColor = 'error';
              this.NotiIcon = 'mdi-alert';
              this.NotiText = message;
            } else if (status == 1) {
              this.locMenu = message;
            }
            this.dtIsError = false;
            this.loadingDialog = false;
          });
    },
    async manageTable(table_name: string, table_id: number, table_capacity: number, loc_id: number) {
      this.addTableDialog = false;
      this.loadingDialog = true;
      let requestBody = {usage: 'manager'};
      if (loc_id > 0 && table_id <= 0) {
        requestBody = Object.assign({}, requestBody, {
          type: 5,
          t_name: table_name,
          capacity: table_capacity,
          location_id: loc_id,
        });
      } else {
        requestBody = Object.assign({}, requestBody, {
          type: 7,
          table_id: table_id,
          capacity: table_capacity,
          t_name: table_name,
        });
      }
      await $fetch('/api/data', {
        method: 'POST',
        body: requestBody,
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
              this.loadTableByLocationID(this.bID);
            }
            this.loadingDialog = false;
            this.dtIsError = false;
          });
    },
    async deleteTable(table_id: number) {
      this.delTableDialog = false;
      this.loadingDialog = false;
      await $fetch('/api/data', {
        method: 'POST',
        body: {
          type: 6,
          usage: 'manager',
          table_id: table_id,
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
              this.loadTableByLocationID(this.bID);
            }
            this.loadingDialog = false;
            this.dtIsError = false;
          });
    },
    async addMenuRestriction(loc_id: number, menu_id: number | null) {
      this.addMenuResDialog = false;
      this.loadingDialog = true;
      await $fetch('/api/data', {
        method: 'POST',
        body: {
          type: 4,
          usage: 'manager',
          location_id: loc_id,
          menu: menu_id,
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
              this.loadMenuByLocationID(loc_id);
            }
            this.loadingDialog = false;
            this.dtIsError = false;
          });
    },
    async removeMenuRestriction(loc_id: number, menu_id: number | null) {
      this.delMenuResDialog = false;
      this.loadingDialog = true;
      await $fetch('/api/data', {
        method: 'POST',
        body: {
          type: 11,
          usage: 'manager',
          location_id: loc_id,
          menu: menu_id,
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
              this.loadMenuByLocationID(loc_id);
            }
            this.loadingDialog = false;
            this.dtIsError = false;
          });
    },
    async createLocation(l_name: string, l_addr: string, l_open_time: string, l_close_time: string, l_layout_img: string) {
      this.addBranch = false;
      this.loadingDialog = true;
      let requestBody = {
        type: 4,
        usage: 'admin',
        name: l_name,
        address: l_addr,
        open_time: l_open_time,
        close_time: l_close_time,
      };
      if (l_layout_img) {
        requestBody = Object.assign({}, requestBody, {layout_img: l_layout_img});
      }
      console.log(requestBody);
      await $fetch('/api/data', {
        method: 'POST',
        body: requestBody,
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
            }; // Destructure inside the callback
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
            this.loadingDialog = false;
            this.dtIsError = false;
            this.loadData();
          });
    },
    async updateLocation(l_id: number, l_name: string, l_addr: string, l_open_time: string, l_close_time: string, l_layout_img: string, l_status: number) {
      this.loadingDialog = true;
      let requestBody = {
        type: 2,
        usage: 'manager',
        location_id: l_id,
        loc_name: l_name,
        address: l_addr,
        open_time: l_open_time,
        close_time: l_close_time,
        status: l_status,
      };
      if (l_layout_img) {
        requestBody = Object.assign({}, requestBody, {layout_img: l_layout_img});
      }
      console.log(requestBody);
      await $fetch('/api/data', {
        method: 'POST',
        body: requestBody,
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
            }; // Destructure inside the callback
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
            this.dtIsError = false;
            this.loadingDialog = false;
            this.loadData();
          });
    },
    async deleteLocation(loc_id: number) {
      this.delLocDialog = false;
      this.loadingDialog = true;
      await $fetch('/api/data', {
        method: 'POST',
        body: {
          type: 20,
          usage: 'admin',
          l_id: loc_id,
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
              this.loadData();
            }
            this.loadingDialog = false;
            this.dtIsError = false;
          });
    },

    async assignManager(l_id: number, mgr_id: number) {
      this.managerDialog = false;
      this.loadingDialog = true;
      await $fetch('/api/data', {
        method: 'POST',
        body: {
          type: 11,
          usage: 'admin',
          l_id: l_id,
          u_id: mgr_id,
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
            }; // Destructure inside the callback
            if (status == 0) {
              this.snackbar = true;
              this.NotiColor = 'error';
              this.NotiIcon = 'mdi-alert';
              this.NotiText = message;
            } else if (status == 1) {
              this.snackbar = true;
              this.NotiColor = 'success';
              this.NotiIcon = 'mdi-check';
              this.bMgrID = mgr_id;
              this.NotiText = message;
              this.loadData();
            }
            this.loadingDialog = false;
          });
    },
    urlValidator(url: string) {
      const expression = /[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b([-a-zA-Z0-9()@:%_\+.~#?&//=]*)?/gi;
      const regex = new RegExp(expression);
      if (url.match(regex)) return true;
      return 'Invalid URL Format';
    },
    requiredForm(value: string) {
      if (value) return true;
      return 'This field is required';
    },
  },

  beforeMount() {
    this.loadData();
  },
};
</script>
<template>
  <v-main class="management_main">
    <v-snackbar v-model="snackbar" :color="NotiColor" :timeout="timeout" location="top" multi-line>
      <v-icon :icon="NotiIcon" start></v-icon>
      {{ NotiText }}
    </v-snackbar>
    <v-dialog v-model="loadingDialog" :scrim="false" persistent width="200px">
      <v-card color="primary">
        <v-card-text class="text-center">
          <p class="mb-1">Please Wait</p>
          <v-progress-linear class="mb-0" color="white" indeterminate></v-progress-linear>
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-dialog v-model="addBranch" :fullscreen="mobile" :width="mobile ? '100%' : '400px'">
      <v-card :width="mobile ? 'auto' : '400px'">
        <v-form
            fast-fail
            @submit.prevent="
            () => {
              createLocation(bName, bAddress, DateTime.fromISO(bOpenTime).toFormat('yyyy-LL-dd TT'), DateTime.fromISO(bCloseTime).toFormat('yyyy-LL-dd TT'), blayout);
            }
          ">
          <v-card-title>Add Branch</v-card-title>
          <v-card-text>
            <v-text-field v-model="bName" :rules="[requiredForm]" label="Name" required></v-text-field>
            <v-textarea v-model="bAddress" :rules="[requiredForm]" label="Address"
                        prepend-inner-icon="mdi-map-marker"></v-textarea>
            <v-text-field v-model="blayout" :rules="[urlValidator]" label="Seat Layout Image URL"
                          prepend-inner-icon="mdi-floor-plan"></v-text-field>
            <v-text-field v-model="bOpenTime" label="Open Time" prepend-inner-icon="mdi-weather-sunset-up"
                          type="time"></v-text-field>
            <v-text-field v-model="bCloseTime" label="Close Time" prepend-inner-icon="mdi-weather-night"
                          type="time"></v-text-field>
          </v-card-text>
          <v-card-actions>
            <v-btn append-icon="mdi-plus" color="success" type="submit">Add</v-btn>
            <v-btn color="primary" @click="addBranch = false">Cancel</v-btn>
          </v-card-actions>
        </v-form>
      </v-card>
    </v-dialog>
    <v-dialog v-model="layoutPreview" :fullscreen="mobile" :width="mobile ? '100%' : '800px'">
      <v-card :width="mobile ? '100%' : '800px'">
        <v-card-title>Layout Preview</v-card-title>
        <v-card-subtitle>
          Previewing layout of
          <b>{{ bName }}</b>
        </v-card-subtitle>
        <v-card-text>
          <v-img :src="blayout ? blayout : '/images/img-coming-soon.webp'" height="auto">
            <template v-slot:placeholder>
              <v-skeleton-loader height="auto" type="image"></v-skeleton-loader>
            </template>
            <template v-slot:error>
              <v-img cover height="300" src="/images/img-error.webp" width="300"></v-img>
            </template>
          </v-img>
        </v-card-text>
        <v-card-actions>
          <v-btn block color="primary" @click="layoutPreview = false">Close</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog v-model="addTableDialog" :width="'auto'">
      <v-card :width="mobile ? 'auto' : '400px'">
        <v-card-title>{{ addTableMode == 0 ? 'Create Table' : 'Edit Table' }}</v-card-title>
        <v-card-subtitle>{{
            addTableMode == 0 ? 'Create a new table' : `Renaming table ${tableName} at ${bName}`
          }}
        </v-card-subtitle>
        <v-card-text>
          <v-text-field v-model="tableNameDialog"
                        :rules="[(v) => (v || '').length <= 5 || 'Table Name must be 5 characters or less']"
                        label="Table Name" persistent-counter prepend-icon="mdi-table-furniture"></v-text-field>
          <v-text-field v-model="tableCapacity" label="Capacity" min="1" oninput="validity.valid || (value=1);"
                        prepend-icon="mdi-account-multiple" type="number"></v-text-field>
        </v-card-text>
        <v-card-actions>
          <v-btn
              :disabled="loadingDialog"
              :prepend-icon="addTableMode == 0 ? 'mdi-check' : 'mdi-content-save'"
              color="success"
              @click="
              () => {
                if (addTableMode == 0) {
                  manageTable(tableNameDialog, 0, tableCapacity, bID);
                } else {
                  manageTable(tableNameDialog, tableID, tableCapacity, bID);
                }
                tableName = '';
                tableID = 0;
              }
            ">
            {{ addTableMode == 0 ? 'Confirm' : 'Save' }}
          </v-btn>
          <v-btn color="error" prepend-icon="mdi-cancel" @click="addTableDialog = false">Cancel</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog v-model="delTableDialog" :width="'auto'">
      <v-card :width="mobile ? 'auto' : '400px'">
        <v-card-title>Table Deletion</v-card-title>
        <v-card-text>Are you sure that you want to delete table {{ tableNameDialog }} at {{ bName }}?</v-card-text>
        <v-card-actions>
          <v-btn
              :disabled="loadingDialog"
              color="success"
              prepend-icon="mdi-check"
              @click="
              () => {
                deleteTable(tableID);
                tableNameDialog = '';
                tableName = '';
                tableID = 0;
              }
            ">
            Confirm
          </v-btn>
          <v-btn color="error" prepend-icon="mdi-cancel" @click="delTableDialog = false">Cancel</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog v-model="addMenuResDialog" :width="'auto'">
      <v-card :width="mobile ? 'auto' : '400px'">
        <v-card-title>Create Menu Restriction</v-card-title>
        <v-card-subtitle>Prevents unwanted menus from showing up in your branch</v-card-subtitle>
        <v-card-text>
          <v-autocomplete v-model="menuID" :item-props="menuItemProps" :items="locMenu[0]" item-value="m_id"
                          label="Menu Selection"></v-autocomplete>
        </v-card-text>
        <v-card-actions>
          <v-btn
              color="success"
              prepend-icon="mdi-check"
              @click="
              () => {
                // addMenuRestriction(menu_id, loc_id);
                addMenuRestriction(bID, menuID);
                menuID = 0;
                menuName = '';
              }
            ">
            Confirm
          </v-btn>
          <v-btn color="error" prepend-icon="mdi-cancel" @click="addMenuResDialog = false">Cancel</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog v-model="delMenuResDialog" :width="'auto'">
      <v-card :width="mobile ? 'auto' : '400px'">
        <v-card-title>Remove Menu Restriction</v-card-title>
        <v-card-text>
          Are you sure that you want to continue?
          <b>{{ menuName }}</b>
          will show up in your branch menu list.
        </v-card-text>
        <v-card-actions>
          <v-btn
              :disabled="loadingDialog"
              color="success"
              prepend-icon="mdi-check"
              @click="
              () => {
                removeMenuRestriction(bID, menuID);
                menuID = 0;
                menuName = '';
              }
            ">
            Confirm
          </v-btn>
          <v-btn color="error" prepend-icon="mdi-cancel" @click="delMenuResDialog = false">Cancel</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog v-model="delLocDialog" :width="'auto'">
      <v-card width="400px">
        <v-card-title>Delete Location</v-card-title>
        <v-card-text>
          Are you sure that you want to continue?
          <b>{{ bName }}</b>
          and its properties will be remove permanently.
        </v-card-text>
        <v-card-actions>
          <v-btn
              :disabled="loadingDialog"
              color="success"
              prepend-icon="mdi-check"
              @click="
              () => {
                deleteLocation(bID);
                bName = '';
                bID = 0;
              }
            ">
            Confirm
          </v-btn>
          <v-btn color="error" prepend-icon="mdi-cancel" @click="delLocDialog = false">Cancel</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog v-model="managerDialog" :width="'auto'">
      <v-card :width="mobile ? 'auto' : '400px'">
        <v-card-title>{{ bMgrID ? 'Manager Switch' : 'Manager Assignment' }}</v-card-title>
        <v-card-subtitle>
          {{ bMgrID ? 'Reassign new manager to manage your branch' : 'Assign manager to manage your branch' }}
        </v-card-subtitle>
        <v-card-text>
          <v-autocomplete v-model="bSelMgrID" :item-props="managerItemProps" :items="managerList" item-value="u_id"
                          label="Manager Selection"></v-autocomplete>
        </v-card-text>
        <v-card-actions>
          <v-btn
              color="success"
              prepend-icon="mdi-check"
              @click="
              () => {
                assignManager(bID, bSelMgrID);
              }
            ">
            Confirm
          </v-btn>
          <v-btn
              color="error"
              prepend-icon="mdi-cancel"
              @click="
              () => {
                managerDialog = false;
                bSelMgrID = 0;
              }
            ">
            Cancel
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog v-model="bEditor" :fullscreen="mobile" :width="mobile ? '100%' : '500px'" persistent>
      <v-card class="tab-card blur-effect">
        <v-tabs v-model="tabNum" bg-color="bg-transparent" color="black" style="height: 44px; padding: 0px 14px">
          <v-tab value="one">General</v-tab>
          <v-tab
              value="two"
              @click="
              () => {
                if (locMenu?.length == 0) loadMenuByLocationID(bID);
              }
            ">
            Menus
          </v-tab>
          <v-tab
              value="three"
              @click="
              () => {
                if (locSeat.length == 0) loadTableByLocationID(bID);
              }
            ">
            Tables
          </v-tab>
        </v-tabs>

        <div class="tab-bg">
          <v-card-text>
            <v-window v-model="tabNum">
              <v-window-item value="one">
                <h3 class="text-left">General</h3>
                <v-form
                    fast-fail
                    @submit.prevent="
                    (val) => {
                      console.log(val);
                    }
                  ">
                  <v-card-text>
                    <v-text-field v-model="bName" :rules="[requiredForm]" label="Name" required></v-text-field>
                    <v-textarea v-model="bAddress" label="Address" prepend-inner-icon="mdi-map-marker"></v-textarea>
                    <v-text-field v-model="blayout" :rules="[urlValidator]" label="Seat Layout Image URL"
                                  prepend-inner-icon="mdi-floor-plan"></v-text-field>
                    <v-text-field v-model="bOpenTime" label="Open Time" prepend-inner-icon="mdi-weather-sunset-up"
                                  type="time"></v-text-field>
                    <v-text-field v-model="bCloseTime" label="Close Time" prepend-inner-icon="mdi-weather-night"
                                  type="time"></v-text-field>
                    <v-select v-model="bStatus" :items="bStatusList" item-title="name" item-value="id" label="Status"
                              prepend-inner-icon="mdi-list-status"></v-select>
                  </v-card-text>
                  <v-btn
                      class="mb-2 mr-3"
                      color="success"
                      prepend-icon="mdi-content-save"
                      type="submit"
                      variant="tonal"
                      @click="
                      () => {
                        updateLocation(bID, bName, bAddress, DateTime.fromISO(bOpenTime).toFormat('yyyy-LL-dd TT'), DateTime.fromISO(bCloseTime).toFormat('yyyy-LL-dd TT'), blayout, bStatus);
                      }
                    ">
                    Save
                  </v-btn>
                  <v-btn
                      v-if="data.role == 'GOD'"
                      :prepend-icon="bMgrID ? 'mdi-account-switch-outline' : 'mdi-clipboard-account'"
                      class="mb-2"
                      color="info"
                      type="submit"
                      variant="text"
                      @click="
                      () => {
                        managerDialog = true;
                        bSelMgrID = bMgrID;
                        if (managerList.length === 0) loadManager();
                      }
                    ">
                    {{ bMgrID ? 'Change Manager' : 'Assign Manager' }}
                  </v-btn>
                </v-form>
              </v-window-item>

              <v-window-item value="two">
                <h3 class="text-left">Menu Restriction</h3>
                <v-card-text>
                  <v-table density="compact" fixed-header height="50vh">
                    <thead>
                    <tr>
                      <th class="text-left">Menu ID</th>
                      <th class="text-left">Menu Name</th>
                      <th class="text-center">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="item in locMenu[1]" :key="item.m_id">
                      <td>{{ item.m_id }}</td>
                      <td>{{ item.m_name }}</td>
                      <td class="text-center">
                        <v-tooltip location="top">
                          <template v-slot:activator="{ props }">
                            <v-icon
                                color="red"
                                v-bind="props"
                                @click="
                                  () => {
                                    menuID = item.m_id;
                                    menuName = item.m_name;
                                    delMenuResDialog = true;
                                  }
                                ">
                              mdi-delete
                            </v-icon>
                          </template>
                          <span>Delete Restriction</span>
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
                        addMenuResDialog = true;
                      }
                    ">
                    Create Restriction
                  </v-btn>
                </v-card-text>
              </v-window-item>

              <v-window-item value="three">
                <h3 class="text-left">Tables</h3>
                <v-card-text>
                  <v-table density="compact" fixed-header height="50vh">
                    <thead>
                    <tr>
                      <th class="text-left">Table ID</th>
                      <th class="text-left">Table Name</th>
                      <th class="text-left">
                        {{ mobile ? '' : 'Capacity' }}
                        <v-icon v-if="mobile">mdi-account-multiple</v-icon>
                      </th>
                      <th class="text-left">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="item in locSeat" :key="item.table_id">
                      <td>{{ item.table_id }}</td>
                      <td>{{ item.name }}</td>
                      <td>{{ item.capacity }}</td>
                      <td>
                        <v-tooltip location="top">
                          <template v-slot:activator="{ props }">
                            <v-icon
                                class="mr-3"
                                color="info"
                                v-bind="props"
                                @click="
                                  () => {
                                    tableID = item.table_id;
                                    tableName = item.name;
                                    tableNameDialog = tableName;
                                    tableCapacity = item.capacity;
                                    addTableMode = 1;
                                    addTableDialog = true;
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
                                color="red"
                                v-bind="props"
                                @click="
                                  () => {
                                    tableID = item.table_id;
                                    tableName = item.name;
                                    tableNameDialog = tableName;
                                    delTableDialog = true;
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
                        tableID = 0;
                        tableName = '';
                        tableNameDialog = tableName;
                        addTableMode = 0;
                        addTableDialog = true;
                      }
                    ">
                    Create Table
                  </v-btn>
                </v-card-text>
              </v-window-item>
            </v-window>
          </v-card-text>
          <v-card-actions>
            <v-btn
                block
                color="primary"
                @click="
                () => {
                  bEditor = false;
                  locSeat = [];
                  locMenu = [];
                  managerList = [];
                }
              ">
              Close
            </v-btn>
          </v-card-actions>
        </div>
      </v-card>
    </v-dialog>
    <div class="main_container management_container mx-auto blur-effect">
      <h1 class="text-h3 font-weight-bold my-8 ml-8 text-left">Branches Management</h1>
      <v-sheet class="mt-8 ma-md-8 ma-xs-1 text-center bg-transparent" rounded="lg">
        <v-alert v-if="dtIsError" class="ma-3" color="error" icon="$error" title="Fetch Error">{{
            dtErrorData
          }}
        </v-alert>
        <v-data-table
            v-model:items-per-page="itemsPerPage"
            :density="mobile ? 'compact' : 'comfortable'"
            :headers="dtHeaders"
            :items="dtData"
            :loading="dtLoading"
            :search="dtSearch"
            class="elevation-0 bg-transparent"
            item-value="l_id"
            @click:row="
            (val, tabl) => {
              bEditor = true;
            }
          ">
          <template v-slot:top>
            <v-text-field v-model="dtSearch" placeholder="Search" prepend-inner-icon="mdi-store-search"></v-text-field>
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
              <td class="text-start td-hover">{{ item.l_id }}</td>
              <td class="text-start td-hover">{{ item.l_name }}</td>
              <td class="text-center td-hover">
                <v-tooltip location="top">
                  <template v-slot:activator="{ props }">
                    <v-icon v-bind="props">{{
                        item.l_status == 'OPERATIONAL' ? 'mdi-check' : item.l_status == 'MAINTENANCE' ? 'mdi-hammer-wrench' : item.l_status == 'OUTOFORDER' ? 'mdi-close' : 'mdi-help'
                      }}
                    </v-icon>
                  </template>
                  <span>
                    {{
                      item.l_status == 'OPERATIONAL' ? 'Operational' : item.l_status == 'MAINTENANCE' ? 'Maintenance' : item.l_status == 'OUTOFORDER' ? 'Out Of Order' : item.l_status
                    }}
                  </span>
                </v-tooltip>
              </td>
            </tr>
          </template>
          <template v-slot:expanded-row="{ columns, item }">
            <tr>
              <td :colspan="columns.length" class="text-left">
                <v-container>
                  <v-row>
                    <v-col cols="12" md="3" sm="6">
                      <p>
                        <b>Address</b>
                        <br/>
                        <v-icon class="mr-2">mdi-map-marker</v-icon>
                        {{ item.l_addr }}
                      </p>
                    </v-col>
                    <v-col cols="12" md="3" sm="6">
                      <p>
                        <b>Operating Hours</b>
                        <br/>
                        <v-icon class="mr-2">mdi-clock-outline</v-icon>
                        {{ DateTime.fromSQL(item.l_open_time).toFormat('t') }} -
                        {{ DateTime.fromSQL(item.l_close_time).toFormat('t') }}
                        <br/>
                        <br/>
                        <b>Created On</b>
                        <br/>
                        <v-icon class="mr-2">mdi-calendar-star</v-icon>
                        {{ DateTime.fromSQL(item.l_creation).toFormat('DDDD') }}
                      </p>
                    </v-col>
                    <v-col cols="12" md="3" sm="6">
                      <p>
                        <b>Status</b>
                        <br/>
                        <v-icon>{{
                            item.l_status == 'OPERATIONAL' ? 'mdi-check' : item.l_status == 'MAINTENANCE' ? 'mdi-hammer-wrench' : item.l_status == 'OUTOFORDER' ? 'mdi-close' : 'mdi-help'
                          }}
                        </v-icon>
                        {{
                          item.l_status == 'OPERATIONAL' ? 'Operational' : item.l_status == 'MAINTENANCE' ? 'Maintenance' : item.l_status == 'OUTOFORDER' ? 'Out of Order' : item.l_status
                        }}
                      </p>
                    </v-col>
                    <v-col v-if="data.role === 'GOD'" cols="12" md="3" sm="6">
                      <b>Manager</b>
                      <br/>
                      <div v-if="item.l_mgr_id">
                        <v-icon class="mr-2">mdi-identifier</v-icon>
                        {{ item.l_mgr_id }}
                        <br/>
                        <v-icon class="mr-2">mdi-account-circle</v-icon>
                        {{ item.mgr_fn + ' ' + item.mgr_ln }}
                        <br/>
                        <v-icon class="mr-2">mdi-email</v-icon>
                        {{ item.mgr_email }}
                        <br/>
                        <div v-if="item.mgr_tel">
                          <v-icon class="mr-2">mdi-phone</v-icon>
                          {{ item.mgr_tel }}
                        </div>
                      </div>
                      <div v-else>
                        <v-icon class="mr-2">mdi-help</v-icon>
                        Unassigned
                      </div>
                    </v-col>
                  </v-row>
                  <v-row>
                    <v-col cols="12">
                      <v-btn
                          prepend-icon="mdi-pencil"
                          variant="text"
                          @click="
                          () => {
                            bID = item.l_id;
                            bName = item.l_name;
                            bAddress = item.l_addr;
                            blayout = item.l_layout_img;
                            bStatus = item.l_status == 'OPERATIONAL' ? 1 : item.l_status == 'MAINTENANCE' ? 2 : item.l_status == 'OUTOFORDER' ? 3 : item.l_status;
                            bOpenTime = DateTime.fromSQL(item.l_open_time).toFormat('T');
                            bCloseTime = DateTime.fromSQL(item.l_close_time).toFormat('T');
                            bMgrID = item.l_mgr_id;
                            bMgrName = `${item.mgr_fn} ${item.mgr_fn}`;
                            tabNum = 0;
                            bEditor = true;
                          }
                        ">
                        Manage Branch
                      </v-btn>
                      <v-btn
                          color="warning"
                          prepend-icon="mdi-floor-plan"
                          variant="text"
                          @click="
                          () => {
                            bName = item.l_name;
                            blayout = item.l_layout_img;
                            layoutPreview = true;
                          }
                        ">
                        View Seat Layout
                      </v-btn>
                      <v-btn
                          v-if="data.role == 'GOD'"
                          color="red"
                          prepend-icon="mdi-delete"
                          variant="text"
                          @click="
                          () => {
                            bID = item.l_id;
                            bName = item.l_name;
                            delLocDialog = true;
                          }
                        ">
                        Delete Branch
                      </v-btn>
                    </v-col>
                  </v-row>
                </v-container>
              </td>
            </tr>
          </template>
          <template v-slot:no-data>
            <v-alert class="my-5" color="info" icon="mdi-exclamation" title="Notice">
              <p class="text-left">You don't have branch to manage.</p>
            </v-alert>
          </template>
        </v-data-table>
        <v-col>
          <v-btn :disabled="dtLoading" :variant="'tonal'" class="align-right mb-3" prepend-icon="mdi-refresh"
                 rounded="lg" text="Refresh" @click="loadData"></v-btn>
          <v-btn
              v-if="data.role === 'GOD'"
              :disabled="dtLoading"
              :variant="'tonal'"
              class="ml-5 align-right mb-3"
              color="success"
              prepend-icon="mdi-plus"
              rounded="lg"
              text="Add Branch"
              @click="
              () => {
                bName = '';
                bAddress = '';
                blayout = '';
                bOpenTime = '';
                bCloseTime = '';
                addBranch = true;
              }
            "></v-btn>
        </v-col>
      </v-sheet>
    </div>
  </v-main>
</template>
