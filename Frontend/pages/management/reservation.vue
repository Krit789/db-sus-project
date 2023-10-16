<script lang="ts" setup>
import {VDataTable} from 'vuetify/labs/VDataTable';
import {DateTime} from 'luxon';
import {useDisplay} from 'vuetify';
import '~/assets/stylesheets/global.css';
import '~/assets/stylesheets/index.css';
import '~/assets/stylesheets/management/reservation.css';
import '~/assets/stylesheets/management/management.css';

const route = useRouter();
const {mobile} = useDisplay();
const {status, data, signIn, signOut} = useAuth();
useHead({
  title: 'Reservation Management - Seatify Admin',
  meta: [{name: 'Seatify App', content: 'My amazing site.'}],
});
definePageMeta({
  middleware: ['allowed-roles-only'],
  meta: {permitted: ['MANAGER', 'GOD']},
});
</script>

<script lang="ts">
interface LocationItem {
  l_id: number;
  l_name: string;
}

interface MenuItem {
  m_id: number;
  m_name: string;
  m_price: number;
  m_amount: number;
}

interface Reservation {
  res_id: number;
  res_status: 'FULFILLED' | 'CANCELLED' | 'INPROGRESS';
  point_used: number | null;
  cus_count: number;
  arrival: string;
  res_on: string;
  loc_id: number;
  loc_name: string;
  loc_addr: string;
  open_time: string;
  close_time: string;
  user_id: number;
  first_name: string;
  last_name: string;
  table_id: number;
  table_name: string;
}

export default {
  data: () => ({
    acceptRes: false as boolean,
    resConfCode: '',
    confirmCancel: false as boolean,
    acceptError: '',
    cancelResID: 0,
    dtSearch: '',
    dtIsError: false,
    dtErrorData: '',
    dtData: [] as Reservation[],
    preOrderMenu: [] as MenuItem[],
    locationsList: [] as LocationItem[],
    locationIDSelect: 0,
    pointUsed: 0,
    itemsPerPage: 10,
    dtLoading: false,
    foodViewDialog: false,
    snackbar: false,
    NotiColor: '',
    timeout: 2000,
    NotiIcon: '',
    NotiText: '',
    resTypeSelect: 2,
    resType: [
      {
        id: 0,
        title: 'All Reservation',
      },
      {
        id: 1,
        title: 'Previous Reservation',
      },
      {
        id: 2,
        title: 'Upcoming Reservation',
      },
    ],
    dtHeaders: [
      {
        title: 'ID',
        align: 'center',
        sortable: true,
        key: 'res_id',
      },
      {title: 'User ID', align: 'center', key: 'user_id'},
      {title: 'Location Name', align: 'start', key: 'loc_name'},
      {title: 'First Name', align: ' d-none', key: 'first_name'}, // ' d-none' hides the header but keeps the search functionality
      {title: 'Last Name', align: ' d-none', key: 'last_name'},
      {title: 'Reserved On', align: 'end', key: 'res_on'},
      {title: 'Reserved For', align: 'end', key: 'arrival'},
      {title: 'Guests', align: 'end', key: 'cus_count'},
      {title: 'Table', align: 'end', key: 'table_id'},
      {title: 'Table Name', align: ' d-none', key: 'table_name'},
      {title: 'Status', align: 'end', key: 'res_status'},
    ],
  }),
  methods: {
    async loadData() {
      this.dtLoading = true;
      let requestBody = {
        type: 12,
        usage: 'admin',
        range: this.resTypeSelect,
      };
      if (this.locationIDSelect > 0) {
        requestBody = Object.assign({}, requestBody, {loc_id: this.locationIDSelect});
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
            this.dtData = message;
            this.dtLoading = false;
            this.dtIsError = false;
          });
    },
    async loadLocation() {
      this.dtLoading = true;
      await $fetch('/api/data', {
        method: 'POST',
        body: {
          type: 22,
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
            const defaultLocation = {
              l_id: 0,
              l_name: 'All Branches',
            };
            const nextIndex = Object.keys(message).length;
            message[nextIndex] = defaultLocation;
            this.locationsList = message;
            this.dtLoading = false;
            this.dtIsError = false;
          });
    },
    async cancelReservation(res_id: Number) {
      this.dtLoading = true;
      await $fetch('/api/data', {
        method: 'POST',
        body: {
          type: 2,
          usage: 'user',
          res_id: res_id,
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
              this.acceptError = message;
              this.snackbar = true;
              this.NotiColor = 'error';
              this.NotiIcon = 'mdi-alert';
              this.NotiText = message;
            } else if (status == 1) {
              this.acceptError = '';
              this.snackbar = true;
              this.NotiColor = 'success';
              this.NotiIcon = 'mdi-check';
              this.NotiText = message;
            }
            this.loadData();
          });
    },
    async acceptReservation(res_code: string) {
      this.dtLoading = true;
      await $fetch('/api/data', {
        method: 'POST',
        body: {
          type: 1,
          usage: 'user',
          res_code: res_code,
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
              this.acceptError = message;
              this.snackbar = true;
              this.NotiColor = 'error';
              this.NotiIcon = 'mdi-alert';
              this.NotiText = message;
            } else if (status == 1) {
              this.acceptError = '';
              this.snackbar = true;
              this.NotiColor = 'success';
              this.NotiIcon = 'mdi-check';
              this.NotiText = message;
            }
            this.resConfCode = '';
            this.loadData();
          });
    },
    async loadOrderByResID(res_id: number) {
      await $fetch('/api/data', {
        method: 'POST',
        body: {
          type: 6,
          usage: 'user',
          res_id: res_id,
        },
        lazy: true,
      })
          .catch((error) => {
            this.dtIsError = true;
            this.dtErrorData = error.data;
          })
          .then((response) => {
            const {message, status} = response as {
              status: number;
              message: any;
            };
            if (status == 1) {
              this.preOrderMenu = message;
              this.dtIsError = false;
            } else {
              this.snackbar = true;
              this.NotiColor = 'error';
              this.NotiIcon = 'mdi-alert';
              this.NotiText = message;
            }
          });
    },
  },
  computed: {
    total: function () {
      return this.preOrderMenu.reduce((acc, item) => acc + item.m_price * item.m_amount, 0);
    },
  },
  beforeMount() {
    this.loadData();
    this.loadLocation();
  },
};
</script>
<template>
  <v-main class="management-main ">
    <v-snackbar v-model="snackbar" :color="NotiColor" :timeout="timeout" location="top">
      <v-icon :icon="NotiIcon" start></v-icon>
      {{ NotiText }}
    </v-snackbar>
    <v-dialog v-model="foodViewDialog" :width="'auto'">
      <v-card :width="mobile ? 'auto' : '400px'">
        <v-card-title>
          <h3 class="text-left">Reservation Order</h3>
        </v-card-title>
        <v-card-text>
          <v-table density="compact" fixed-header height="50vh">
            <thead>
            <tr>
              <th class="text-left">Menu Name</th>
              <th class="text-center px-0">Amount</th>
              <th class="text-right pl-0">Price</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="item in preOrderMenu" :key="item.m_id">
              <td class="pr-0">{{ item.m_name }}</td>
              <td class="text-center px-0">{{ item.m_amount }}</td>
              <td class="text-right pl-0" width="100">{{ (item.m_amount * item.m_price).toLocaleString() }} ฿</td>
            </tr>
            </tbody>
          </v-table>
          <v-table density="compact">
            <tbody>
            <tr>
              <td class="text-right font-weight-medium">
                <v-icon color="success">mdi-circle-multiple</v-icon>
                Points
              </td>
              <td class="text-right font-weight-medium">{{ (pointUsed ? pointUsed : 0).toLocaleString() }} pts.</td>
            </tr>
            <tr>
              <td class="text-right font-weight-medium">Total</td>
              <td class="text-right font-weight-medium">{{ (total - pointUsed).toLocaleString() }} ฿</td>
            </tr>
            </tbody>
          </v-table>
        </v-card-text>
        <v-card-actions>
          <v-btn
              block
              class="mt-3"
              prepend-icon="mdi-close"
              variant="text"
              @click="
              () => {
                foodViewDialog = false;
              }
            ">
            Close
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog v-model="confirmCancel" width="375">
      <v-card>
        <v-card-title>Reservation Cancellation</v-card-title>
        <v-card-subtitle>Are you sure? This action cannot be undone</v-card-subtitle>
        <v-card-item>We're going to cancel reservation id {{ cancelResID }}</v-card-item>
        <v-card-actions>
          <v-btn
              color="success"
              prepend-icon="mdi-check"
              @click="
              () => {
                cancelReservation(cancelResID);
                confirmCancel = false;
              }
            ">
            Confirm
          </v-btn>
          <v-btn color="error" prepend-icon="mdi-cancel" @click="confirmCancel = false">Cancel</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <div class="main_container management-container mx-auto blur-effect">
      <h1 class="text-h3 font-weight-bold mt-8 ml-8 text-left">Reservation Management</h1>
      <v-sheet class="mt-8 ma-md-8 ma-sm-5 text-center bg-transparent" rounded="lg">
        <v-alert v-if="dtIsError" class="ma-3" color="error" icon="$error" title="Fetch Error">{{
            dtErrorData
          }}
        </v-alert>
        <v-container>
          <v-row>
            <v-col>
              <v-data-table :density="mobile ? 'compact' : 'comfortable'" :headers="dtHeaders" :items="dtData"
                            :loading="dtLoading" :multi-sort="true" :search="dtSearch"
                            class="0 rounded-lg bg-transparent" fixed-header
                            height="40vh" item-value="res_id" items-per-page="-1" sticky>
                <template v-slot:top>
                  <v-card class="py-3 px-6 rounded-xl top-card-blur" elevation="2">
                    <v-card-item>
                      <v-container>
                        <div class="ml-0 accept-reservation reservation-header-text">Accept Reservation</div>
                        <v-row>
                          <v-col class="pb-0">
                            <v-text-field v-model="resConfCode" density="compact" label="Reservation Code"
                                          v-bind:error-messages="acceptError"></v-text-field>
                          </v-col>
                        </v-row>
                        <v-row>
                          <v-col class="pa-0">
                            <v-btn
                                :disabled="dtLoading"
                                color="success"
                                prepend-icon="mdi-check"
                                variant="tonal"
                                @click="
                                () => {
                                  acceptReservation(resConfCode);
                                }
                              ">
                              Accept Reservation
                            </v-btn>
                          </v-col>
                        </v-row>

                        <v-row class="mt-10">
                          <v-col cols="12" md="6">
                            <p class="reservation-header-text">Reservation Type</p>
                            <v-select
                                v-model="resTypeSelect"
                                :items="resType"
                                density="compact"
                                item-title="title"
                                item-value="id"
                                @update:modelValue="() => {loadData();}"></v-select>
                          </v-col>
                          <v-col cols="12" md="6">
                            <p class="reservation-header-text">Branch</p>
                            <v-select
                                v-model="locationIDSelect"
                                :items="locationsList"
                                density="compact"
                                item-title="l_name"
                                item-value="l_id"
                                @update:modelValue="() => {loadData();}"></v-select>
                          </v-col>
                        </v-row>
                      </v-container>
                    </v-card-item>
                  </v-card>

                  <v-text-field v-model="dtSearch" class="mt-10" placeholder="Search"
                                prepend-inner-icon="mdi-book-search"></v-text-field>
                </template>
                <template v-slot:item="{ internalItem, item, toggleExpand, isExpanded }">
                  <tr v-ripple="" class="text-end table-hover" @click="toggleExpand(internalItem)">
                    <td class="text-center td-hover bg-table">{{ item.res_id }}</td>
                    <td class="text-center td-hover bg-table">
                      {{ item.user_id }}
                      <v-tooltip activator="parent" location="top">{{
                          item.first_name + ' ' + item.last_name
                        }}
                      </v-tooltip>
                    </td>
                    <td class="text-left td-hover bg-table">
                      {{ item.loc_name }}
                      <v-tooltip activator="parent" location="top">ID: {{ item.loc_id }}</v-tooltip>
                    </td>
                    <td class="text-right td-hover bg-table">
                      {{ DateTime.fromSQL(item.res_on).toFormat('D') }}
                      <v-tooltip activator="parent" location="top">{{
                          DateTime.fromSQL(item.res_on).toFormat('fff')
                        }}
                      </v-tooltip>
                    </td>
                    <td class="text-right td-hover bg-table">
                      {{ DateTime.fromSQL(item.arrival).toFormat('D') }}
                      <v-tooltip activator="parent" location="top">{{
                          DateTime.fromSQL(item.arrival).toFormat('fff')
                        }}
                      </v-tooltip>
                    </td>
                    <td class="td-hover bg-table">{{ item.cus_count }}</td>
                    <td class="text-right td-hover bg-table">
                      {{ item.table_name }}
                    </td>
                    <td class="td-hover bg-table">
                      <v-tooltip location="top">
                        <template v-slot:activator="{ props }">
                          <v-icon v-bind="props">{{
                              item.res_status == 'INPROGRESS' ? 'mdi-progress-clock' : item.res_status == 'FULFILLED' ? 'mdi-check' : item.res_status == 'CANCELLED' ? 'mdi-close' : 'mdi-help'
                            }}
                          </v-icon>
                        </template>
                        <span>{{
                            item.res_status == 'INPROGRESS' ? 'In Progress' : item.res_status == 'FULFILLED' ? 'Fulfilled' : item.res_status == 'CANCELLED' ? 'Cancelled' : 'Unknown'
                          }}</span>
                      </v-tooltip>
                    </td>
                  </tr>
                </template>
                <template v-slot:expanded-row="{ columns, item }">
                  <tr>
                    <td :colspan="columns.length" class="text-left">
                      <v-container>
                        <v-row>
                          <v-col>
                            <b>Reserved By</b>
                            <p>{{ item.first_name }} {{ item.last_name }}</p>
                          </v-col>
                          <v-col>
                            <b>Reserved For</b>
                            <p>
                              <v-icon>mdi-calendar-blank</v-icon>
                              {{ DateTime.fromSQL(item.arrival).toFormat('DDDD') }}
                              <br/>
                              <v-icon>mdi-clock-outline</v-icon>
                              {{ DateTime.fromSQL(item.arrival).toFormat('t') }}
                            </p>
                          </v-col>
                          <v-col>
                            <b>Reserved At</b>
                            <p>{{ item.loc_name }}</p>
                          </v-col>
                          <v-col>
                            <b>Branch Address</b>
                            <p>{{ item.loc_addr }}</p>
                          </v-col>
                        </v-row>
                        <v-row>
                          <v-col class="text-left">
                            <v-btn
                                color="purple"
                                prepend-icon="mdi-food"
                                text="View Food Pre-Order"
                                variant="text"
                                @click="
                                () => {
                                  preOrderMenu = [];
                                  pointUsed = item.point_used;
                                  loadOrderByResID(item.res_id).then(() => {
                                    foodViewDialog = true;
                                  });
                                }
                              "></v-btn>
                            <v-btn
                                v-if="item.res_status == 'INPROGRESS'"
                                color="error"
                                prepend-icon="mdi-cancel"
                                variant="text"
                                @click="
                                () => {
                                  cancelResID = item.res_id;
                                  confirmCancel = true;
                                }
                              ">
                              Cancel Reservation
                            </v-btn>
                            <v-btn v-if="item.res_status == 'CANCELLED'" color="error" disabled variant="text">
                              Cancelled
                            </v-btn>
                          </v-col>
                        </v-row>
                      </v-container>
                    </td>
                  </tr>
                </template>
                <template v-slot:no-data>
                  <v-alert class="ma-3" color="info" icon="mdi-exclamation" title="Notice">
                    <p>We don't have any reservations to show you. Try selecting different filters.</p>
                  </v-alert>
                </template>
              </v-data-table>
            </v-col>
          </v-row>

          <v-row>
            <v-col>
              <v-btn :disabled="dtLoading" :variant="'tonal'" class="align-right mb-3" prepend-icon="mdi-refresh"
                     rounded="lg" text="Refresh" @click="loadData"></v-btn>
            </v-col>
          </v-row>
        </v-container>
      </v-sheet>
    </div>
  </v-main>
</template>
