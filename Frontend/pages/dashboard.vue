<script lang="ts" setup>
  import { VDataTable } from 'vuetify/labs/VDataTable';
  import '~/assets/stylesheets/dashboard.css';
  import '~/assets/stylesheets/global.css';
  import { DateTime } from 'luxon';

  const { status, data, signIn, signOut } = useAuth();

  useHead({
    title: 'My Reservation - Seatify',
    meta: [{ name: 'Seatify App', content: 'My amazing site.' }],
  });
</script>
<script lang="ts">
  export default {
    data: () => ({
      expandedDT: [],
      codeDialog: false,
      reservationCode: null,
      dtIsError: false,
      dtErrorData: '',
      isError: false,
      errorData: '',
      dtData: [],
      preOrderMenu: [],
      itemsPerPage: 10,
      dtLoading: false,
      cancelReservationDialog: false,
      cancelResID: 0,
      snackbar: false,
      NotiColor: '',
      timeout: 2000,
      NotiIcon: '',
      NotiText: '',
      resTypeSelect: 2,
      loadingDialog: false,
      foodViewDialog: false,
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
        { title: 'Status', align: 'start', key: 'res_status' },
        { title: 'Location Name', align: 'center', key: 'location_name' },
        { title: 'No. of Customer', align: 'end', key: 'cus_count' },
        { title: 'Table', align: 'start', key: 'table_name' },
        { title: 'Reserved For', align: 'start', key: 'arrival' },
        // {title: "", key: "data-table-expand"},
      ],
    }),
    methods: {
      async loadData() {
        this.dtLoading = true;
        await $fetch('/api/data', {
          method: 'POST',
          body: {
            type: 8,
            usage: 'user',
            range: this.resTypeSelect,
          },
          lazy: true,
        })
          .catch((error) => {
            this.dtIsError = true;
            this.dtErrorData = error.data;
          })
          .then((response) => {
            const { message } = response as {
              status: number;
              message: any;
            };
            this.dtData = message;
            this.dtLoading = false;
            this.dtIsError = false;
          });
      },
      async loadOrderByResID(res_id: number) {
        this.loadingDialog = true;
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
            const { message, status } = response as {
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
            this.loadingDialog = false;
          });
      },
      async cancelReservation(res_id: Number) {
        this.loadingDialog = true;
        this.cancelReservationDialog = false;
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
            const { status, message } = response as {
              status: number;
              message: any;
            };
            if (status === 1) {
              this.snackbar = true;
              this.NotiColor = 'success';
              this.NotiIcon = 'mdi-check';
              this.NotiText = message;
              this.loadData();
            } else {
              this.snackbar = true;
              this.NotiColor = 'error';
              this.NotiIcon = 'mdi-alert';
              this.NotiText = message;
            }
            this.loadingDialog = false;
            this.dtIsError = false;
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
    },
  };
</script>

<template>
  <v-main class="justify-center dashboard_main">
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
    <v-dialog v-model="cancelReservationDialog" :width="'auto'">
      <v-card :width="mobile ? 'auto' : '400px'">
        <v-card-title>Reservation Cancellation</v-card-title>
        <v-card-text>Are you sure that you want to cancel this reservation?</v-card-text>
        <v-card-actions>
          <v-btn
            :disabled="loadingDialog"
            color="success"
            prepend-icon="mdi-check"
            @click="
              () => {
                cancelReservation(cancelResID);
                cancelResID = 0;
              }
            ">
            Confirm
          </v-btn>
          <v-btn color="error" prepend-icon="mdi-cancel" @click="cancelReservationDialog = false">Cancel</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog  v-model="foodViewDialog" :width="'auto'">
      <v-card :width="mobile ? 'auto' : '400px'">
        <v-card-title>
      <h3 class="text-left">Your Order</h3>
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
              <td class="text-right pl-0" width="100">{{ item.m_amount * item.m_price }} ฿</td>
            </tr>
          </tbody>
        </v-table>
        <v-table>
          <tbody><tr>
            <td class="text-right font-weight-medium">Total</td>
            <td class="text-right font-weight-medium">{{ total }} ฿</td>
          </tr></tbody>
        </v-table>
      </v-card-text>
        <v-card-actions>
        <v-btn
          class="mt-3"
          prepend-icon="mdi-close"
          variant="text"
          block
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
    <div class="dashboard_container main_container mx-auto blur-effect mt-10 py-1 px-1 min-h-40">
      <h1 class="text-h3 font-weight-bold mt-8 ml-8 text-left">My Reservation</h1>
      <v-sheet class="mt-8 ma-md-8 ma-sm-5 text-center" rounded="lg">
        <v-container>
          <v-row>
            <v-col>
              <p class="text-h5 text-left">Reservation Type</p>
              <v-select
                v-model="resTypeSelect"
                :items="resType"
                item-value="id"
                item-title="title"
                @update:modelValue="
                  () => {
                    loadData();
                  }
                "></v-select>
            </v-col>
          </v-row>
        </v-container>
        <v-alert v-if="dtIsError" class="ma-3" color="error" icon="$error" title="Fetch Error">{{ dtErrorData }}</v-alert>
        <v-no-ssr>
          <v-data-table
            items-per-page="-1"
            :expanded="expandedDT"
            :headers="dtHeaders"
            :items="dtData"
            :loading="dtLoading"
            class="elevation-0"
            item-value="res_id"
            :density="mobile ? 'compact' : 'comfortable'"
            loading-text="We're looking for your reservation, Hang tight!"
            height="40vh"
            sticky
            fixed-header>
            <template v-slot:item="{ internalItem, item, toggleExpand, isExpanded }">
              <tr v-ripple class="text-end table-hover" @click="toggleExpand(internalItem)">
                <td class="td-hover text-left">
                  <v-tooltip location="top">
                    <template v-slot:activator="{ props }">
                      <v-icon v-bind="props" :color="item.res_status == 'INPROGRESS' ? 'warning' : item.res_status == 'FULFILLED' ? 'success' : item.res_status == 'CANCELLED' ? 'red' : 'grey'">
                        {{ item.res_status == 'INPROGRESS' ? 'mdi-progress-clock' : item.res_status == 'FULFILLED' ? 'mdi-check' : item.res_status == 'CANCELLED' ? 'mdi-close' : 'mdi-help' }}
                      </v-icon>
                    </template>
                    <span>{{ item.res_status == 'INPROGRESS' ? 'In Progress' : item.res_status == 'FULFILLED' ? 'Fulfilled' : item.res_status == 'CANCELLED' ? 'Cancelled' : 'Unknown' }}</span>
                  </v-tooltip>
                </td>
                <td class="text-center td-hover">{{ item.location_name }}</td>
                <td class="td-hover">{{ item.cus_count }}</td>
                <td class="text-left td-hover">
                  {{ item.table_name }}
                </td>
                <td class="text-left td-hover">
                  {{ DateTime.fromSQL(item.arrival).toFormat('t D') }}
                  <v-tooltip activator="parent" location="top">{{ DateTime.fromSQL(item.arrival).toFormat('t DDDD') }}</v-tooltip>
                </td>
              </tr>
            </template>
            <template v-slot:expanded-row="{ columns, item }">
              <tr>
                <td :colspan="columns.length" class="text-left">
                  <v-container>
                    <v-row>
                      <v-col col="12" sm="6">
                        <b>Branch Address</b>
                        <br />
                        {{ item.location_address }}
                        <br />
                      </v-col>
                      <v-col col="12" sm="6">
                        <b>Arrival Time</b>
                        <br />
                        <p>
                          <v-icon>mdi-calendar-blank</v-icon>
                          {{ DateTime.fromSQL(item.arrival).toFormat('DDDD') }}
                          <br />
                          <v-icon>mdi-clock-outline</v-icon>
                          {{ DateTime.fromSQL(item.arrival).toFormat('t') }}
                        </p>
                        <br />
                      </v-col>
                    </v-row>
                    <v-row>
                      <v-col col="12" md="12" sm="6">
                        <v-btn
                          v-if="item.res_status === 'INPROGRESS'"
                          variant="text"
                          color="success"
                          prepend-icon="mdi-card-account-details-outline"
                          @click="
                            () => {
                              reservationCode = item.res_code;
                              codeDialog = !codeDialog;
                            }
                          ">
                          Reservation Code
                        </v-btn>
                        <v-btn
                          color="purple"
                          prepend-icon="mdi-food"
                          text="View Food Pre-Order"
                          variant="text"
                          v-bind="props"
                          @click="
                            () => {
                              preOrderMenu = [];
                              loadOrderByResID(item.res_id);
                              foodViewDialog = true;
                            }
                          "></v-btn>
                        <v-btn
                          v-if="item.res_status === 'INPROGRESS'"
                          color="info"
                          prepend-icon="mdi-pencil"
                          text="Edit Reservation"
                          variant="text"
                          v-bind="props"
                          @click="
                            () => {
                              // cancelReservation(item.res_id);
                            }
                          "></v-btn>
                        <v-btn
                          v-if="item.res_status === 'INPROGRESS'"
                          color="red"
                          prepend-icon="mdi-cancel"
                          text="Cancel Reservation"
                          variant="text"
                          v-bind="props"
                          @click="
                            () => {
                              cancelResID = item.res_id;
                              cancelReservationDialog = true;
                            }
                          "></v-btn>
                      </v-col>
                    </v-row>
                  </v-container>
                </td>
              </tr>
            </template>
          </v-data-table>
        </v-no-ssr>
        <v-btn :disabled="dtLoading" :elevation="0" class="align-right my-6" prepend-icon="mdi-refresh" rounded="lg" text="Refresh" variant="outlined" @click="loadData"></v-btn>
      </v-sheet>
      <v-dialog v-model="codeDialog" width="auto">
        <v-card>
          <v-card-title>Your Reservation Code</v-card-title>
          <v-card-subtitle>Show this code to staff to confirm your reservation</v-card-subtitle>
          <v-card-text class="text-center">
            <v-text-field v-model="reservationCode" label="Code" no-resize readonly row-height="15" rows="1" variant="outlined"></v-text-field>
          </v-card-text>
          <v-card-actions>
            <v-btn block color="primary" @click="codeDialog = false">Done</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </div>
  </v-main>
</template>
