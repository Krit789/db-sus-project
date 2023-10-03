<script lang="ts" setup>
  import { VDataTable } from 'vuetify/labs/VDataTable';
  import { VSkeletonLoader } from 'vuetify/labs/VSkeletonLoader';
  import { useDisplay } from 'vuetify';
  import { DateTime } from 'luxon';
  import '~/assets/stylesheets/global.css';
  import '~/assets/stylesheets/index.css';
  import '~/assets/stylesheets/management/branches.css';
  import '~/assets/stylesheets/management/management.css';

  const { mobile } = useDisplay();

  const { status, data, signIn, signOut } = useAuth();
  const route = useRouter();
  useHead({
    title: 'Branches Management - Seatify Admin',
    meta: [{ name: 'Seatify App', content: 'My amazing site.' }],
  });
  definePageMeta({
    middleware: ['allowed-roles-only'],
    meta: { permitted: ['MANAGER', 'GOD'] },
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
      tableName: '',
      tabNum: 0,
      tableID: 0,
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
          { title: 'Name', align: 'start', key: 'l_name' },
          { title: 'Address', align: ' d-none', key: 'l_addr' },
          { title: 'Status', align: ' d-none', key: 'l_status' },
          { title: 'Open', align: ' d-none', key: 'l_open_time' },
          { title: 'Close', align: ' d-none', key: 'l_close_time' },
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
            const { message } = response as { status: number; message: any };
            this.dtData = message;
            this.dtLoading = false;
            this.dtIsError = false;
          });
      },
      async loadTableByLocationID(loc_id: number) {
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
            const { status, message } = response as { status: number; message: any };
            if (status == 0) {
              this.snackbar = true;
              this.NotiColor = 'error';
              this.NotiIcon = 'mdi-alert';
              this.NotiText = message;
            } else if (status == 1) {
              this.locSeat = message;
            }
            this.dtIsError = false;
          });
      },
      async loadManager() {
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
            const { status, message } = response as { status: number; message: any };
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
          });
      },
      async loadMenuByLocationID(loc_id: number) {
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
            const { status, message } = response as { status: number; message: any };
            if (status == 0) {
              this.snackbar = true;
              this.NotiColor = 'error';
              this.NotiIcon = 'mdi-alert';
              this.NotiText = message;
            } else if (status == 1) {
              this.locMenu = message;
            }
            this.dtIsError = false;
          });
      },
      async createLocation(l_name: string, l_addr: string, l_open_time: string, l_close_time: string, l_layout_img: string) {
        this.loadingDialog = true;
        let requestBody = { type: 4, usage: 'admin', name: l_name, address: l_addr, open_time: l_open_time, close_time: l_close_time };
        if (l_layout_img) {
          requestBody = Object.assign({}, requestBody, { layout_img: l_layout_img });
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
            const { status, message } = response as { status: number; message: any }; // Destructure inside the callback
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
            this.addBranch = false;
            this.loadingDialog = false;
            this.dtIsError = false;
            this.loadData();
          });
      },
      async updateLocation(l_id: number,l_name: string, l_addr: string, l_open_time: string, l_close_time: string, l_layout_img: string, l_status: number) {
        this.loadingDialog = true;
        let requestBody = { type: 2, usage: 'manager', location_id: l_id, loc_name: l_name, address: l_addr, open_time: l_open_time, close_time: l_close_time, status: l_status };
        if (l_layout_img) {
          requestBody = Object.assign({}, requestBody, { layout_img: l_layout_img });
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
            const { status, message } = response as { status: number; message: any }; // Destructure inside the callback
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
      async assignManager(l_id: number, mgr_id: number) {
        this.loadingDialog = true;
        await $fetch('/api/data', {
          method: 'POST',
          body: {
            type: 11,
            usage: "admin",
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
            const { status, message } = response as { status: number; message: any }; // Destructure inside the callback
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
            this.managerDialog = false;
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
    <v-dialog v-model="loadingDialog" :scrim="false" persistent width="auto">
      <v-card color="primary">
        <v-card-text>
          Saving Changes
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
            <v-textarea prepend-inner-icon="mdi-map-marker" v-model="bAddress" :rules="[requiredForm]" label="Address"></v-textarea>
            <v-text-field prepend-inner-icon="mdi-floor-plan" v-model="blayout" :rules="[urlValidator]" label="Seat Layout Image URL"></v-text-field>
            <v-text-field prepend-inner-icon="mdi-clock-start" v-model="bOpenTime" label="Open Time" type="time"></v-text-field>
            <v-text-field prepend-inner-icon="mdi-clock-end" v-model="bCloseTime" label="Close Time" type="time"></v-text-field>
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
              <v-skeleton-loader type="image" height="auto"></v-skeleton-loader>
            </template>
            <template v-slot:error>
              <v-img cover height="300" src="/images/img-error.webp" width="300"></v-img>
            </template>
          </v-img>
        </v-card-text>
        <v-card-actions>
          <v-btn color="primary" block @click="layoutPreview = false">Close</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog v-model="addTableDialog" :width="'auto'">
      <v-card :width="mobile ? 'auto' : '400px'">
        <v-card-title>{{ addTableMode == 0 ? 'Create Table' : 'Rename Table' }}</v-card-title>
        <v-card-subtitle>{{ addTableMode == 0 ? 'Create a new table' : `Renaming table ${tableName} at ${bName}` }}</v-card-subtitle>
        <v-card-text>
          <v-text-field label="Table Name" v-model="tableName"></v-text-field>
        </v-card-text>
        <v-card-actions>
          <v-btn
            color="success"
            :prepend-icon="addTableMode == 0 ? 'mdi-check' : 'mdi-content-save'"
            @click="
              () => {
                // manageTable(tableName, tableID, bID);
                tableName = '';
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
        <v-card-text>Are you sure that you want to delete table {{ tableName }} at {{ bName }}?</v-card-text>
        <v-card-actions>
          <v-btn
            color="success"
            prepend-icon="mdi-check"
            @click="
              () => {
                // deleteTable(tableID);
                tableName = '';
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
          <v-autocomplete label="Menu Selection" :items="locMenu[0]" :item-props="menuItemProps" item-value="m_id" v-model="menuID"></v-autocomplete>
        </v-card-text>
        <v-card-actions>
          <v-btn
            color="success"
            prepend-icon="mdi-check"
            @click="
              () => {
                // addMenuRestriction(menu_id, loc_id);
                console.log(menuID);
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
            color="success"
            prepend-icon="mdi-check"
            @click="
              () => {
                // removeMenuRestriction(menu_id, loc_id);
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
    <v-dialog v-model="managerDialog" :width="'auto'">
      <v-card :width="mobile ? 'auto' : '400px'">
        <v-card-title>{{ bMgrID ? 'Manager Switch' : 'Manager Assignment' }}</v-card-title>
        <v-card-subtitle>{{ bMgrID ? 'Reassign new manager to manage your branch' : 'Assign manager to manage your branch' }}</v-card-subtitle>
        <v-card-text>
          <v-autocomplete label="Manager Selection" :items="managerList" :item-props="managerItemProps" item-value="u_id" v-model="bSelMgrID"></v-autocomplete>
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
      <v-card>
        <v-tabs v-model="tabNum" bg-color="primary" color="white">
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
                  <v-textarea prepend-inner-icon="mdi-map-marker" v-model="bAddress" label="Address"></v-textarea>
                  <v-text-field prepend-inner-icon="mdi-floor-plan" v-model="blayout" :rules="[urlValidator]" label="Seat Layout Image URL"></v-text-field>
                  <v-text-field prepend-inner-icon="mdi-clock-start" v-model="bOpenTime" label="Open Time" type="time"></v-text-field>
                  <v-text-field prepend-inner-icon="mdi-clock-end" v-model="bCloseTime" label="Close Time" type="time"></v-text-field>
                  <v-select prepend-inner-icon="mdi-list-status" v-model="bStatus" :items="bStatusList" item-title="name" item-value="id" label="Status"></v-select>
                </v-card-text>
                <v-btn prepend-icon="mdi-content-save" class="mb-2 mr-3" color="success" type="submit" variant="tonal" @click="() => {updateLocation(bID, bName, bAddress, DateTime.fromISO(bOpenTime).toFormat('yyyy-LL-dd TT'), DateTime.fromISO(bCloseTime).toFormat('yyyy-LL-dd TT'), blayout, bStatus)}">Save</v-btn>
                <v-btn
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
                <v-table fixed-header height="50vh" density="compact">
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
                  variant="text"
                  prepend-icon="mdi-plus"
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
                <v-table fixed-header height="50vh" density="compact">
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
                              v-bind="props"
                              color="info"
                              @click="
                                () => {
                                  tableID = item.table_id;
                                  tableName = item.name;
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
                  variant="text"
                  prepend-icon="mdi-plus"
                  @click="
                    () => {
                      tableID = 0;
                      tableName = '';
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
      </v-card>
    </v-dialog>
    <div class="main_container management_container mx-auto blur-effect">
      <h1 class="text-h3 font-weight-bold my-8 ml-8 text-left">Branches Management</h1>
      <v-sheet class="mt-8 ma-md-8 ma-xs-1 text-center" rounded="lg">
        <v-alert v-if="dtIsError" class="ma-3" color="error" icon="$error" title="Fetch Error">{{ dtErrorData }}</v-alert>
        <v-data-table
          v-model:items-per-page="itemsPerPage"
          :density="mobile ? 'compact' : 'comfortable'"
          :headers="dtHeaders"
          :items="dtData"
          :loading="dtLoading"
          :search="dtSearch"
          class="elevation-1"
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
                        <br />
                        <v-icon class="mr-2">mdi-map-marker</v-icon>
                        {{ item.l_addr }}
                      </p>
                    </v-col>
                    <v-col cols="12" md="3" sm="6">
                      <p>
                        <b>Operating Hours</b>
                        <br />
                        <v-icon class="mr-2">mdi-clock-outline</v-icon>
                        {{ DateTime.fromSQL(item.l_open_time).toFormat('t') }} -
                        {{ DateTime.fromSQL(item.l_close_time).toFormat('t') }}
                      </p>
                    </v-col>
                    <v-col cols="12" md="3" sm="6">
                      <p>
                        <b>Status</b>
                        <br />
                        <v-icon class="mr-2">{{ item.l_status == 'OPERATIONAL' ? 'mdi-check' : item.l_status == 'MAINTENANCE' ? 'mdi-hammer-wrench' : item.l_status == 'OUTOFORDER' ? 'mdi-close' : 'mdi-help' }}</v-icon>
                        {{ item.l_status == 'OPERATIONAL' ? 'Operational' : item.l_status == 'MAINTENANCE' ? 'Maintenance' : item.l_status == 'OUTOFORDER' ? 'Out of Order' : item.l_status }}
                      </p>
                    </v-col>
                    <v-col cols="12" md="3" sm="6">
                      <b>Manager</b>
                      <br />
                      <div v-if="item.l_mgr_id">
                        <v-icon class="mr-2">mdi-identifier</v-icon>
                        {{ item.l_mgr_id }}
                        <br />
                        <v-icon class="mr-2">mdi-account-circle</v-icon>
                        {{ item.mgr_fn + ' ' + item.mgr_ln }}
                        <br />
                        <v-icon class="mr-2">mdi-email</v-icon>
                        {{ item.mgr_email }}
                        <br />
                        <v-icon class="mr-2">mdi-phone</v-icon>
                        {{ item.mgr_tel }}
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
                        prepend-icon="mdi-floor-plan"
                        variant="text"
                        color="warning"
                        @click="
                          () => {
                            bName = item.l_name;
                            blayout = item.l_layout_img;
                            layoutPreview = true;
                          }
                        ">
                        View Seat Layout
                      </v-btn>
                    </v-col>
                  </v-row>
                </v-container>
              </td>
            </tr>
          </template>
        </v-data-table>
        <v-col>
          <v-btn :disabled="dtLoading" :variant="'tonal'" class="align-right mb-3" prepend-icon="mdi-refresh" rounded="lg" text="Refresh" @click="loadData"></v-btn>
          <v-btn
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
