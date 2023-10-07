<script lang="ts" setup>
  import { useDisplay } from 'vuetify';
  import { VDataTable } from 'vuetify/labs/VDataTable';
  import { DateTime } from 'luxon';
  import '~/assets/stylesheets/global.css';
  import '~/assets/stylesheets/report.css';
  import { Bar } from 'vue-chartjs';
  import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js';

  ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale);
  definePageMeta({
    middleware: ['allowed-roles-only'],
    meta: { permitted: ['MANAGER', 'GOD'] },
  });
  useHead({
    title: 'Report - Seatify Admin',
    meta: [{ name: 'Seatify App', content: 'My amazing site.' }],
  });

  const { mobile } = useDisplay();
  const router = useRouter();
</script>

<script lang="ts">
  interface LocationInfo {
    location_id: number;
    l_name: string;
    l_address: string;
    manager_fname: string;
    manager_lname: string;
    res_id: number | null;
    arrival: string | null;
    balance_paid: number;
    menu_amount: number;
  }

  interface SummaryInfo {
    location_id: number;
    l_name: string;
    l_address: string;
    manager_fname: string;
    manager_lname: string;
    total_earning: number;
    reservation_amount: number;
  }
  type Location = {
    l_id: number;
    l_name: string;
    l_addr: string;
    l_open_time: string;
    l_close_time: string;
    l_status: 'OPERATIONAL' | 'MAINTENANCE' | 'OUTOFORDER';
    l_layout_img: string;
    l_mgr_id: number | null;
    mgr_fn: string | null;
    mgr_ln: string | null;
    mgr_tel: string | null;
    mgr_email: string | null;
  };

  type CompleteReportData = [LocationInfo[], SummaryInfo[]];

  export default {
    data() {
      return {
        selectedDT: [] as number[],
        reportData: [] as CompleteReportData[],
        dtSearch: '',
        dtErrorData: '',
        dtIsError: false,
        dtData: [] as Location[],
        loadingDialog: false,
        itemsPerPage: 10,
        dtLoading: false as boolean,
        reportBeginTime: null as DateTime | null,
        reportEndTime: null as DateTime | null,
        selectReport: true,
        snackbar: false,
        NotiColor: '',
        timeout: 2000,
        NotiIcon: '',
        NotiText: '',
        salesChartData: {
          labels: [] as string[], // Will hold l_name values
          datasets: [
            {
              label: 'Total Earnings',
              backgroundColor: 'rgba(75, 192, 192, 0.2)', // Customize as needed
              borderColor: 'rgba(75, 192, 192, 1)', // Customize as needed
              borderWidth: 1,
              data: [] as number[], // Will hold total_earning values
            },
          ],
        },
        salesChartOptions: {
          responsive: true,
        },
        dtHeaders: [
          { title: 'Location ID', align: 'start', sortable: true, key: 'l_id' },
          { title: 'Name', align: 'center', key: 'l_name' },
        ],
      };
    },
    methods: {
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
            const { status, message } = response as { status: number; message: any };
            this.dtData = message;
            this.dtLoading = false;
            this.dtIsError = false;
          });
      },
      async loadReportData(start_time: DateTime, end_time: DateTime, locations: any) {
        this.loadingDialog = true;
        await $fetch('/api/data', {
          method: 'POST',
          body: {
            type: 21,
            usage: 'admin',
            start: start_time,
            end: end_time,
            loc_id: locations,
          },
          lazy: true,
        })
          .catch((error) => {
            this.dtIsError = true;
            this.dtErrorData = error.data;
          })
          .then((response) => {
            const { status, message } = response as { status: number; message: any };
            if (status === 1) {
              this.reportData = message;
              this.extractDataForChart();
              console.log(this.salesChartData)
            } else {
            }
            this.loadingDialog = false;
            this.selectReport = false;
          });
      },
      extractDataForChart() {
        const locationData: SummaryInfo[] = this.reportData[1];
        for (const entry of locationData) {
          this.salesChartData.labels.push(entry.l_name);
          this.salesChartData.datasets[0].data.push(entry.total_earning);
        }
      },
    },
    beforeMount() {
      this.loadData();
    },
  };
</script>

<template>
  <v-main class="justify-center report_main">
    <v-snackbar v-model="snackbar" :color="NotiColor" :timeout="timeout" location="top">
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
    <v-dialog v-model="selectReport" :fullscreen="mobile" :width="mobile ? '100%' : '900px'" persistent>
      <v-card style="overflow: initial; z-index: initial">
        <v-card-title>Select Braches To Create Report</v-card-title>
        <v-card-item>
          <v-no-ssr>
            <v-data-table v-model="selectedDT" :density="mobile ? 'compact' : 'comfortable'" :headers="dtHeaders" items-per-page="-1" select-strategy="page" :items="dtData" :loading="dtLoading" :search="dtSearch" class="elevation-1" item-value="l_id" show-select show-expand sticky height="40vh">
              <template v-slot:top>
                <v-row>
                  <v-col>
                    <h4>Begin</h4>
                    <v-text-field v-model="reportBeginTime" variant="underlined" type="datetime-local"></v-text-field>
                  </v-col>
                  <v-col>
                    <h4>End</h4>
                    <v-text-field v-model="reportEndTime" variant="underlined" type="datetime-local"></v-text-field>
                  </v-col>
                </v-row>
                <v-container>
                  <v-row>
                    <v-col>
                      <v-text-field v-model="dtSearch" placeholder="Search" prepend-inner-icon="mdi-store-search"></v-text-field>
                    </v-col>
                  </v-row>
                </v-container>
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
                    </v-container>
                  </td>
                </tr>
              </template>
              <template v-slot:no-data>
                <v-alert class="my-5 text-left" icon="mdi-exclamation" title="Notice" color="info">
                  <p>You don't have anything to report on.</p>
                </v-alert>
              </template>
            </v-data-table>
          </v-no-ssr>
        </v-card-item>
        <v-card-actions>
          <v-btn
            prepend-icon="mdi-arrow-left"
            @click="
              () => {
                router.back();
              }
            ">
            Go Back
          </v-btn>
          <v-btn :disabled="selectedDT.length <= 0" prepend-icon="mdi-chart-timeline-variant-shimmer" color="success" @click="loadReportData(DateTime.fromISO(reportBeginTime), DateTime.fromISO(reportEndTime), selectedDT)">Generate Report</v-btn>
          <v-btn prepend-icon="mdi-chart-timeline-variant-shimmer" color="red" @click="selectReport = false">Close</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <div v-if="!selectReport" class="main_container mx-auto blur-effect py-4 px-2 mt-8 account_container justify-center">
      <h1 class="text-h3 font-weight-bold mt-8 ml-8 text-left">Report</h1>
      <v-sheet class="mt-8 ma-md-8 ma-xs-1 text-center" rounded="lg">
        <v-alert v-if="dtIsError" class="ma-3" color="error" icon="$error" title="Fetch Error">{{ dtErrorData }}</v-alert>
      </v-sheet>
      <Bar id="locationSales" :options="salesChartOptions" :data="salesChartData" />
      <div class="w-100 justify-center align-center"></div>
    </div>
  </v-main>
</template>
