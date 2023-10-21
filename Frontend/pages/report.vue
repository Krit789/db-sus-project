<script lang="ts" setup>
import {useDisplay} from 'vuetify';
import {VDataTable} from 'vuetify/labs/VDataTable';
import {DateTime} from 'luxon';
import '~/assets/stylesheets/global.css';
import '~/assets/stylesheets/report.css';
import {Bar} from 'vue-chartjs';
import {Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale} from 'chart.js';

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale);
definePageMeta({
  middleware: ['allowed-roles-only'],
  meta: {permitted: ['MANAGER', 'GOD']},
});
useHead({
  title: 'Report - Seatify Admin',
  meta: [{name: 'Seatify App', content: 'My amazing site.'}],
});

const {mobile} = useDisplay();
const router = useRouter();

</script>

<script lang="ts">
import JsonCSV from 'vue-json-csv';

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
  components: {
    JsonCSV,
  },
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
            backgroundColor: '#1E88E5', // Customize as needed
            // borderColor: 'rgba(75, 192, 192, 1)', // Customize as needed
            yAxisID: 'A',
            borderWidth: 1,
            data: [] as number[], // Will hold total_earning values
          },
          {
            label: 'Total Reservation',
            backgroundColor: '#4DB6AC', // Customize as needed
            yAxisID: 'B',
            // borderColor: 'rgba(75, 192, 192, 1)', // Customize as needed
            borderWidth: 1,
            data: [] as number[], // Will hold total_earning values
          },
        ],
      },
      earningsChartData: {
        labels: [] as string[], // Will hold l_name values
        datasets: [
          {
            label: 'Total Earnings Per Reservation',
            backgroundColor: '#1E88E5', // Customize as needed
            // borderColor: 'rgba(75, 192, 192, 1)', // Customize as needed
            borderWidth: 1,
            data: [] as number[], // Will hold total_earning values
          },
        ],
      },
      dtHeaders: [
        {title: 'Location ID', align: 'start', sortable: true, key: 'l_id'},
        {title: 'Name', align: 'center', key: 'l_name'},
      ],
    };
  },
  computed: {
    salesChartOptions() {
      return {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          x: {
            title: {
              display: true,
              text: 'Branches',
            },
          },
          A: {
            type: 'linear',
            position: 'left',
            beginAtZero: true,
            title: {
              display: true,
              text: 'Earnings',
            },
            ticks: {
              callback: (value, index, values) => {
                return value + ' ฿';
              },
            },
          },
          B: {
            type: 'linear',
            position: 'right',
            title: {
              display: true,
              text: 'Reservations',
            },
            ticks: {
              precision: 0,
            },
          },
        },
      };
    },
    earningChartOptions() {
      return {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              callback: (value, index, values) => {
                return value + ' ฿';
              },
            },
            title: {
              display: true,
              text: 'Earnings',
            },
          },
          x: {
            title: {
              display: true,
              text: 'Date',
            },
          },
        },
      };
    },
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
            const {status, message} = response as {
              status: number;
              message: any
            };
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
            const {status, message} = response as {
              status: number;
              message: any
            };
            if (status === 1) {
              this.reportData = message;
              this.extractDataForChart();
              this.createChartData(this.reportData[0]);
              // console.log(this.earningsChartData, this.salesChartData);
            } else {
            }
            this.loadingDialog = false;
            this.selectReport = false;
          });
    },
    extractDataForChart() {
      const locationData: SummaryInfo[] = this.reportData[1];
      this.salesChartData.datasets[0].data = [];
      this.salesChartData.datasets[1].data = [];
      this.salesChartData.labels = [];
      for (const entry of locationData) {
        this.salesChartData.labels.push(entry.l_name);
        this.salesChartData.datasets[0].data.push(entry.total_earning);
        this.salesChartData.datasets[1].data.push(entry.reservation_amount);
      }
    },
    createChartData(data) {
      // Create a map to store earnings data by location and date
      const earningsMap = new Map();

      // Loop through the data and populate the earningsMap
      data.forEach((entry) => {
        // console.log(DateTime.fromSQL(entry.arrival))
        if (entry.arrival === null) {
          return;
        }
        const arrivalDate = DateTime.fromSQL(entry.arrival).toISODate();
        const locationName = entry.l_name;

        // Initialize the location in the map if it doesn't exist
        if (!earningsMap.has(locationName)) {
          earningsMap.set(locationName, new Map());
        }

        // Initialize the date in the location's map if it doesn't exist
        if (!earningsMap.get(locationName).has(arrivalDate)) {
          earningsMap.get(locationName).set(arrivalDate, 0);
        }

        // Add balance_paid to the earnings for the location and date
        earningsMap.get(locationName).set(arrivalDate, earningsMap.get(locationName).get(arrivalDate) + entry.balance_paid);
      });

      // Create labels (dates) and datasets for Chart.js
      const labels = Array.from([...earningsMap.values()][0].keys()); // Get dates from the first location's map
      const datasets = [];

      earningsMap.forEach((locationData, locationName) => {
        const dataValues = Array.from(locationData.values());
        datasets.push({
          label: locationName,
          data: dataValues,
          backgroundColor: this.getRandomColor(), // Function to generate random colors
          // borderColor: this.getRandomColor(),
          borderWidth: 1, // Function to generate random colors
          // fill: false,
        });
      });
      this.earningsChartData.labels = labels;
      this.earningsChartData.datasets = datasets;
      // console.log(earningsMap, datasets, this.earningsChartData.labels,this.earningsChartData.datasets);
      // return { labels, datasets };
    },
    getRandomColor() {
      const letters = '0123456789ABCDEF';
      let color = '#';
      for (let i = 0; i < 6; i++) {
        color += letters[Math.floor(Math.random() * 16)];
      }
      return color;
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
      <v-card class="" style="overflow: initial; z-index: initial">
        <v-card-title class="report-popup-header mt-4 mb-2 ml-1">Select Branches To Create Report</v-card-title>
        <v-card-item>
          <v-no-ssr>
            <v-data-table v-model="selectedDT" :density="mobile ? 'compact' : 'comfortable'" :headers="dtHeaders"
                          :items="dtData" :loading="dtLoading" :search="dtSearch" class="elevation-1" height="40vh"
                          item-value="l_id" items-per-page="-1" select-strategy="page" show-expand show-select sticky>
              <template v-slot:top>
                <v-row>
                  <v-col>
                    <h4>Begin</h4>
                    <v-text-field v-model="reportBeginTime" type="datetime-local" variant="underlined"></v-text-field>
                  </v-col>
                  <v-col>
                    <h4>End</h4>
                    <v-text-field v-model="reportEndTime" type="datetime-local" variant="underlined"></v-text-field>
                  </v-col>
                </v-row>
                <v-container>
                  <v-row>
                    <v-col>
                      <v-text-field v-model="dtSearch" placeholder="Search"
                                    prepend-inner-icon="mdi-store-search"></v-text-field>
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
                          </p>
                        </v-col>
                        <v-col cols="12" md="3" sm="6">
                          <p>
                            <b>Status</b>
                            <br/>
                            <v-icon class="mr-2">{{
                                item.l_status == 'OPERATIONAL' ? 'mdi-check' : item.l_status == 'MAINTENANCE' ? 'mdi-hammer-wrench' : item.l_status == 'OUTOFORDER' ? 'mdi-close' : 'mdi-help'
                              }}
                            </v-icon>
                            {{
                              item.l_status == 'OPERATIONAL' ? 'Operational' : item.l_status == 'MAINTENANCE' ? 'Maintenance' : item.l_status == 'OUTOFORDER' ? 'Out of Order' : item.l_status
                            }}
                          </p>
                        </v-col>
                        <v-col cols="12" md="3" sm="6">
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
                <v-alert class="my-5 text-left" color="info" icon="mdi-exclamation" title="Notice">
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
          <v-btn color="red" prepend-icon="mdi-close" @click="selectReport = false">Close</v-btn>
          <v-btn :disabled="selectedDT.length <= 0" color="success" prepend-icon="mdi-chart-timeline-variant-shimmer"
                 @click="loadReportData(DateTime.fromISO(reportBeginTime), DateTime.fromISO(reportEndTime), selectedDT)">
            Generate Report
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <div v-show="!selectReport || salesChartData.datasets[0].data.length !== 0"
         class="main_container mx-auto blur-effect py-4 px-2 mt-8 account_container justify-center">
      <h1 class="text-h3 font-weight-bold mt-8 ml-8 text-left">Report {{
          reportBeginTime !== null && reportEndTime !== null ? `for ${DateTime.fromISO(reportBeginTime).toFormat('D')} till ${DateTime.fromISO(reportEndTime).toFormat('D')}` : ''
        }}</h1>
      <v-sheet class="mt-8 ma-md-8 ma-xs-1 text-center" rounded="lg">
        <v-alert v-if="dtIsError" class="ma-3" color="error" icon="$error" title="Fetch Error">{{
            dtErrorData
          }}
        </v-alert>
      </v-sheet>
      <v-btn class="ml-10" color="info" prepend-icon="mdi-select-multiple-marker" variant="tonal"
             @click="selectReport = true">Re-select Branches
      </v-btn>
      <v-container>
        <v-row class="mt-6">
          <v-col class="pa-0 ma-0">
            <h3 class="ml-5 mb-3 pl-3 graph-title">Total Branch Earnings & Reservations</h3>
            <v-sheet class="rounded-xl mx-5 px-8 pa-3 overflow-auto" style="height: 50vh">
              <Bar id="locationSales" :key="reportData[1]" :data="salesChartData" :options="salesChartOptions"/>
            </v-sheet>
            <v-btn class="ml-10 mt-3" color="success" prepend-icon="mdi-download-circle" variant="text" @click="">
              <JsonCSV v-if="reportData[0]" :data="reportData[0]" name="total_earnings.csv">Download as CSV</JsonCSV>
            </v-btn>
          </v-col>
          <v-col class="pa-0 ma-0">
            <h3 class="ml-5 mb-3 pl-3 graph-title">Branch Earnings</h3>
            <v-sheet class="rounded-xl mx-5 px-8 pa-3 overflow-auto" style="height: 50vh">
              <Bar id="locationEarning" :key="reportData[1]" :data="earningsChartData" :options="earningChartOptions"/>
            </v-sheet>
            <v-btn class="ml-10 mt-3" color="success" prepend-icon="mdi-download-circle" variant="text" @click="">
              <JsonCSV v-if="reportData[1]" :data="reportData[1]" name="reservation_earnings.csv">Download as CSV</JsonCSV>
            </v-btn>
          </v-col>
        </v-row>
        <v-row class="py-10 px-5">
          <v-col class="box-container">
            <h3 class="ml-5 mb-1 summary-title">Branch Summary</h3>
            <v-sheet class="mt-5 ma-md-8 ma-xs-1 text-center bg-transparent" rounded="lg">
              <v-table :density="mobile ? 'compact' : 'comfortable'" class="mx-3 bg-transparent" fixed-header
                       height="300px">
                <thead>
                <tr>
                  <th class="text-right">ID</th>
                  <th class="text-left">Location</th>
                  <th class="text-right">Reservations</th>
                  <th class="text-right">Earnings</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="loc in reportData[1]" :key="loc.location_id">
                  <td class="text-right">{{ loc.location_id }}</td>
                  <td class="text-left">{{ loc.l_name }}</td>
                  <td class="text-right">{{ loc.reservation_amount.toLocaleString() }}</td>
                  <td class="text-right">{{ loc.total_earning.toLocaleString() }} ฿</td>
                </tr>
                </tbody>
              </v-table>
              <v-no-ssr>
              <v-table class="mr-10 mt-2 bg-transparent" height="40px">
                <tr class="text-h5">
                  <td :width="mobile ? 'auto' : '500px'" class="text-right"></td>
                  <td class="text-right"><b>Total</b></td>
                  <td class="text-right">
                    {{ salesChartData.datasets[1].data.reduce((partialSum, a) => partialSum + a, 0).toLocaleString() }}
                    Reservations
                  </td>
                  <td class="text-right">
                    {{ salesChartData.datasets[0].data.reduce((partialSum, a) => partialSum + a, 0).toLocaleString() }}
                    ฿
                  </td>
                </tr>
              </v-table>
            </v-no-ssr>
            </v-sheet>
          </v-col>
        </v-row>
      </v-container>
    </div>
  </v-main>
</template>
