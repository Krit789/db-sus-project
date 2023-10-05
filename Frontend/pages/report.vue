<script lang="ts" setup>
  import { useDisplay } from 'vuetify';
  import { VDataTable } from 'vuetify/labs/VDataTable';
  import { DateTime } from 'luxon';
  import '~/assets/stylesheets/global.css';
  import '~/assets/stylesheets/report.css';

  definePageMeta({
    middleware: ['allowed-roles-only'],
    meta: { permitted: ['MANAGER', 'GOD'] },
  });
  useHead({
    title: 'Report - Seatify Admin',
    meta: [{ name: 'Seatify App', content: 'My amazing site.' }],
  });

  const { mobile } = useDisplay();
  const { status, data } = useAuth();
</script>

<script lang="ts">
  export default {
    data() {
      return {
        selectedDT: [],
        dtSearch: '',
        dtErrorData: '',
        dtIsError: false,
        dtData: [],
        itemsPerPage: 10,
        dtLoading: false as boolean,
        dtHeaders: [
          {
            title: 'Location ID',
            align: 'start',
            sortable: true,
            key: 'l_id',
          },
          { title: 'Name', align: 'center', key: 'l_name' },
          // { title: "Manager", align: "end", key: "mgr_id" },
          // { title: "Address", align: "end", key: "l_addr" },
          // { title: "Status", align: "end", key: "l_status" },
          // { title: "Open", align: "end", key: "l_open_time" },
          // { title: "Close", align: "end", key: "l_close_time" },
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
    },
    beforeMount() {
      this.loadData();
    },
  };
</script>

<template>
  <v-main class="justify-center report_main">
    <div class="main_container mx-auto blur-effect py-4 px-2 mt-8 account_container justify-center">
      <h1 class="text-h3 font-weight-bold mt-8 ml-8 text-left">Report</h1>
      <v-sheet class="mt-8 ma-md-8 ma-xs-1 text-center" rounded="lg">
        <v-alert v-if="dtIsError" class="ma-3" color="error" icon="$error" title="Fetch Error">{{ dtErrorData }}</v-alert>
        <v-no-ssr>
          <v-data-table
            v-model="selectedDT"
            :density="mobile ? 'compact' : 'comfortable'"
            :headers="dtHeaders"
            :items="dtData"
            :loading="dtLoading"
            :search="dtSearch"
            class="elevation-1"
            item-value="l_id"
            show-select
            show-expand
            >
            <template v-slot:top>
              <v-text-field v-model="dtSearch" placeholder="Search" prepend-inner-icon="mdi-store-search"></v-text-field>
            </template>
            <!-- <template v-slot:item="{ internalItem, item, toggleExpand, toggleSelect }">
              <tr v-ripple class="table-hover">
                <td class="text-start td-hover">
                  <v-checkbox-btn
                    v-model="internalItem.columns.isSelected"
                    @click="
                      () => {
                        toggleSelect(internalItem);
                      }
                    "></v-checkbox-btn>
                </td>
                <td
                  class="text-start td-hover"
                  @click="
                    () => {
                      toggleExpand(internalItem);
                    }
                  ">
                  {{ item.l_id }}
                </td>
                <td
                  class="text-start td-hover"
                  @click="
                    () => {
                      toggleExpand(internalItem);
                    }
                  ">
                  {{ item.l_name }}
                </td>
              </tr>
            </template> -->
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
              <v-alert icon="mdi-exclamation" title="Notice" color="info">
                <p>You don't have anything to report on.</p>
              </v-alert>
            </template>
          </v-data-table>
        </v-no-ssr>
        <v-btn :disabled="dtLoading" class="align-right my-3" prepend-icon="" text="Get Report" @click="console.log(selectedDT)"></v-btn>
      </v-sheet>
      <div class="w-100 justify-center align-center">
        <v-btn :disabled="dtLoading" :variant="'outlined'" class="align-right mb-3 w-100 mx-auto" prepend-icon="mdi-refresh" rounded="lg" text="Refresh" @click="loadData"></v-btn>
      </div>
    </div>
  </v-main>
</template>
