<script lang="ts" setup>
import {VStepper, VStepperHeader, VStepperItem, VStepperWindow, VStepperWindowItem,} from "vuetify/labs/VStepper";
import {VDataTable} from "vuetify/labs/VDataTable";
import {useDisplay} from "vuetify";
import "~/assets/stylesheets/global.css";
import "~/assets/stylesheets/reservation.css";

const {mobile} = useDisplay();
const {status, data} = useAuth();
const route = useRoute();
useHead({
  title: "Booking - Seatify",
  meta: [{name: "Seatify App", content: "My amazing site."}],
});
</script>

<script lang="ts">
function renameKey(obj, oldKey, newKey) {
  obj[newKey] = obj[oldKey];
  delete obj[oldKey];
}

export default {
  data: () => ({
    isError: false,
    isTimeValid: false,
    errorData: "",
    stepper1: 0,
    pageSpinner: false,
    hasLocation: false,
    locationList: [],
    menuList: [],
    seatList: [],
    filterSeatList: [],
    filterSeatCount: 0,
    branchLayout: "",
    selectedLocID: 0,
    selectedLoc: {},
    selectedTime: null,
    selectedSeat: "",
    foodPreOrderList: [],
    dtSearch: "",
    resDateTime: "",
    resGuest: 1,
    dtHeaders: [
      {
        title: "ID",
        align: "start",
        key: "location_id",
      },
      {title: "Name", align: "start", key: "name"},
      {title: "Address", align: "end", key: "address"},
      {title: "Open", align: "end", key: "open_time"},
      {title: "Close", align: "end", key: "close_time"},
    ],
  }),
  methods: {
    findSeatforSelectedDT() {
      this.selectedTime = DateTime.fromISO(this.resDateTime);
      this.loadAvailableTable(
          this.selectedLocID,
          DateTime.fromISO(this.resDateTime).toFormat(
              "yyyy-LL-dd TT",
          ),
      );
    },
    async loadLocation() {
      this.pageSpinner = true;
      await $fetch("/api/data", {
        method: "POST",
        body: {
          type: 7,
          usage: "user",
        },
        lazy: true,
      })
          .catch((error) => {
            this.isError = true;
            this.errorData = error.data;
          })
          .then(({status, message}) => {
            this.locationList = message;
            this.pageSpinner = false;
            this.isError = false;
          });
    },
    async loadLocationByID(locID: Number) {
      this.pageSpinner = true;
      await $fetch("/api/data", {
        method: "POST",
        body: {
          type: 10,
          usage: "user",
          location_id: locID,
        },
        lazy: true,
      })
          .catch((error) => {
            this.isError = true;
            this.errorData = error.data;
          })
          .then(({status, message}) => {
            this.selectedLoc = message[0];
            this.pageSpinner = false;
            this.isError = false;
          });
    },
    async loadAvailableTable(locID: Number, arriavalTime: string) {
      this.pageSpinner = true;
      await $fetch("/api/data", {
        method: "POST",
        body: {
          type: 11,
          usage: "user",
          location_id: locID,
          arrival: arriavalTime,
        },
        lazy: true,
      })
          .catch((error) => {
            this.isError = true;
            this.errorData = error.data;
          })
          .then(({status, message}) => {
            this.seatList = message;
            this.pageSpinner = false;
            this.isError = false;
          });
    },
    seatRule() {
      if (this.filterSeatCount >= 1) return true;
      return "No seat with specified condition available";
    },
    isDateTimeValidRule() {
      if (this.isDateTimeInRange() && this.isDateTimeInOperation())
        return true;
      if (!this.isDateTimeInOperation())
        return "Reservation must be in operational time";
      if (!this.isDateTimeInRange())
        return "Reservation date must be > 2 hours and < 14 days into the future";
      return "Reservation date must be > 2 hours and < 14 days into the future and must be in operational time";
    },
    isDateTimeInRange() {
      const time = DateTime.fromISO(this.resDateTime);
      const start = DateTime.now().plus({hours: 2});
      const end = DateTime.now().plus({days: 14});
      const interval = Interval.fromDateTimes(start, end);

      return interval.contains(time);
    },
    isDateTimeInOperation() {
      const time = DateTime.fromISO(this.resDateTime);
      const openTime = DateTime.fromSQL(this.selectedLoc.open_time);
      const closeTime = DateTime.fromSQL(
          this.selectedLoc.close_time,
      ).minus({minutes: 30});

      const timeHours = time.hour;
      const timeMinutes = time.minute;
      const startHours = openTime.hour;
      const startMinutes = openTime.minute;
      const endHours = closeTime.hour;
      const endMinutes = closeTime.minute;

      return (
          (timeHours > startHours ||
              (timeHours === startHours &&
                  timeMinutes >= startMinutes)) &&
          (timeHours < endHours ||
              (timeHours === endHours && timeMinutes <= endMinutes))
      );
    },
  },

  computed: {
    filteredSeatListCompute() {
      this.filterSeatList = JSON.parse(JSON.stringify(this.seatList));
      // this.filterSeatList.forEach( obj => renameKey( obj, 'name', 'title' ) );
      this.filterSeatCount = this.filterSeatList.filter(
          (item) => Number(item.capacity) >= this.resGuest,
      ).length;
      return this.filterSeatList.filter(
          (item) => Number(item.capacity) >= this.resGuest,
      );
    },
  },
  beforeMount() {
    if (this.$route.query.location_id != null) {
      this.hasLocation = true;
      this.selectedLocID = Number(this.$route.query.location_id);
      this.loadLocationByID(Number(this.$route.query.location_id));
      this.stepper1 = 1;
    } else {
      this.stepper1 = 0;
    }
    this.loadLocation();
  },
};
</script>
<template>
  <v-main class="justify-center reservation_body">
    <div
        class="main_container mx-auto blur-effect account_container mt-10 py-1 px-1 min-h-40"
    >
      <h1 class="text-h3 font-weight-bold mt-8 ml-8 text-left">
        Reservation
      </h1>
      <v-sheet
          class="mt-8 ma-md-8 ma-xs-1 text-center bg-transparent"
          rounded="0"
      >
        <v-stepper v-model="stepper1" :mobile="mobile">
          <v-alert
              v-if="isError"
              class="ma-3"
              color="error"
              icon="$error"
              title="Error"
          >{{ errorData }}
          </v-alert>
          <v-stepper-header>
            <v-stepper-item
                :complete="hasLocation"
                :disabled="hasLocation"
                value="1"
            >
              <template v-slot:title>Select Branch</template>
            </v-stepper-item>

            <v-divider></v-divider>

            <v-stepper-item value="2">
              <template v-slot:title>Choose Time</template>
            </v-stepper-item>

            <v-divider></v-divider>

            <v-stepper-item value="3">
              <template v-slot:title> Pick Your Seat</template>
            </v-stepper-item>

            <v-divider></v-divider>

            <v-stepper-item value="4">
              <template v-slot:title> Pre-Order Food</template>

              <template v-slot:subtitle>Optional</template>
            </v-stepper-item>
            <v-divider></v-divider>
            <v-stepper-item value="5">
              <template v-slot:title> Summary</template>
            </v-stepper-item>
          </v-stepper-header>

          <v-stepper-window :touch="false">
            <v-stepper-window-item
                :disabled="hasLocation"
                value="1"
            >
              <v-card title=""
              >
                <v-card-text
                ><h3
                    class="text-h4 font-weight-medium text-left"
                >
                  Select Branches
                </h3></v-card-text
                >
                <v-btn
                    :disabled="pageSpinner"
                    class="align-right mb-3"
                    prepend-icon="mdi-refresh"
                    text="Refresh"
                    @click="loadLocation"
                ></v-btn>
                <v-text-field
                    v-model="dtSearch"
                    placeholder="Search"
                ></v-text-field>
                <v-data-table
                    :headers="dtHeaders"
                    :items="locationList"
                    :loading="pageSpinner"
                    :search="dtSearch"
                    class="elevation-1"
                    v-on:click:row="
                                        (val, tabl) => {
                                            selectedLoc = loadLocationByID(
                                                tabl.item.columns.location_id,
                                            );
                                            selectedLocID =
                                                tabl.item.columns.location_id;
                                            stepper1++;
                                        }
                                    "
                ></v-data-table>
              </v-card>
            </v-stepper-window-item>
            <v-stepper-window-item value="2">
              <v-card>
                <v-card-text>
                  <h3
                      class="text-h4 font-weight-medium text-left"
                  >
                    Choose Reservation Time
                  </h3>
                </v-card-text>

                <v-container>
                  <v-row justify="space-around">
                    <v-col cols="12" sm="6">
                      <h3
                          class="text-left font-weight-medium"
                      >
                        Date & Time
                      </h3>
                      <v-text-field
                          v-model="resDateTime"
                          :rules="[isDateTimeValidRule]"
                          prepend-inner-icon="mdi-calendar-multiselect-outline"
                          required
                          type="datetime-local"
                      ></v-text-field>
                    </v-col>
                  </v-row>
                </v-container>

                <div class="ma-3">
                  <v-btn
                      v-if="!hasLocation"
                      class="mr-5"
                      @click="
                                            () => {
                                                stepper1--;
                                            }
                                        "
                  >Back
                  </v-btn
                  >
                  <v-btn
                      :disabled="
                                            !(
                                                isDateTimeInRange() &&
                                                isDateTimeInOperation()
                                            )
                                        "
                      @click="
                                            () => {
                                                stepper1++;
                                                findSeatforSelectedDT();
                                            }
                                        "
                  >Next
                  </v-btn
                  >
                </div>
              </v-card
              >
            </v-stepper-window-item>
            <v-stepper-window-item value="3">
              <v-card>
                <v-card-text>
                  <h3
                      class="text-h4 font-weight-medium text-left"
                  >
                    Guest & Seating
                  </h3>
                </v-card-text>
                <v-container class="align-self-center">
                  <v-row
                  >
                    <v-col>
                      <h3
                          class="text-left font-weight-medium"
                      >
                        Seat Layout
                      </h3>
                      <v-img
                          :src="
                                                    selectedLoc.layout_img_url
                                                "
                          class=""
                          width="600"
                      ></v-img>
                    </v-col>
                    <v-col>
                      <h3
                          class="text-left font-weight-medium"
                      >
                        How many guests are coming?
                      </h3>
                      <v-text-field
                          v-model="resGuest"
                          :rules="[isDateTimeValidRule]"
                          min="1"
                          oninput="validity.valid||(value=1);"
                          prepend-inner-icon="mdi-account-multiple"
                          required
                          type="number"
                      ></v-text-field>
                      <h3
                          class="text-left font-weight-medium"
                      >
                        Pick Your Seat
                      </h3>
                      <v-select
                          v-model="selectedSeat"
                          :disabled="filterSeatCount == 0"
                          :items="filteredSeatListCompute"
                          :rules="[seatRule]"
                          item-title="name"
                          item-value="table_id"
                          label="Table Name"
                          prepend-inner-icon="mdi-sofa-single-outline"
                      ></v-select>
                    </v-col>
                  </v-row>
                </v-container>
                <div class="ma-3">
                  <v-btn
                      class="mr-5"
                      @click="
                                            () => {
                                                stepper1--;
                                            }
                                        "
                  >Back
                  </v-btn
                  >
                  <v-btn
                      :disabled="!seatRule"
                      @click="
                                            () => {
                                                stepper1++;
                                            }
                                        "
                  >Next
                  </v-btn
                  >
                </div>
              </v-card
              >
            </v-stepper-window-item>
            <v-stepper-window-item value="4">
              <v-card>
                <v-card-text>
                  <h3
                      class="text-h4 font-weight-medium text-left"
                  >
                    Pre-Order Food
                  </h3>
                </v-card-text>
                <div class="ma-3">
                  <v-btn
                      class="mr-5"
                      @click="
                                            () => {
                                                stepper1--;
                                            }
                                        "
                  >Back
                  </v-btn
                  >
                  <v-btn
                      @click="
                                            () => {
                                                stepper1++;
                                            }
                                        "
                  >Next
                  </v-btn
                  >
                </div>
              </v-card
              >
            </v-stepper-window-item>
            <v-stepper-window-item value="5">
              <v-card>
                <v-card-text>
                  <h3
                      class="text-h4 font-weight-medium text-left"
                  >
                    Summary
                  </h3>
                  <h3>Location</h3>
                  <p>{{ selectedLoc?.name }}</p>
                  <h3>Date and Time</h3>
                  <p>{{ selectedTime }}</p>
                  <h3>Seat</h3>
                  <p>{{ selectedSeat }}</p>
                </v-card-text>
                <div class="ma-3">
                  <v-btn
                      class="mr-5"
                      @click="
                                            () => {
                                                stepper1--;
                                            }
                                        "
                  >Back
                  </v-btn
                  >
                  <v-btn
                      @click="
                                            () => {
                                                console.log('Confirm');
                                            }
                                        "
                  >Confirm
                  </v-btn
                  >
                </div>
              </v-card
              >
            </v-stepper-window-item>
          </v-stepper-window>
        </v-stepper>
      </v-sheet>
    </div>
  </v-main>
</template>
