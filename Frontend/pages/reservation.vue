<script lang="ts" setup>
  import { VStepper, VStepperHeader, VStepperItem, VStepperWindow, VStepperWindowItem } from 'vuetify/labs/VStepper';
  import { VSkeletonLoader } from 'vuetify/labs/VSkeletonLoader';
  import { DateTime, Interval } from 'luxon';
  import { VDataTable } from 'vuetify/labs/VDataTable';
  import { useDisplay } from 'vuetify';
  import '~/assets/stylesheets/global.css';
  import '~/assets/stylesheets/reservation.css';

  const { mobile } = useDisplay();
  const { status, data } = useAuth();
  const route = useRoute();

  definePageMeta({
    middleware: ['allowed-roles-only'],
    meta: { permitted: ['USER'] },
  });

  useHead({
    title: 'Booking - Seatify',
    meta: [{ name: 'Seatify App', content: 'My amazing site.' }],
  });
</script>

<script lang="ts">
  interface MenuObject {
    id: number;
    item_name: string;
    amount: number;
    price: number;
    // Add more properties as needed
  }

  interface SeatObject {
    table_id: number;
    name: string;
    capacity: number;
    location_id: number;
    // Add more properties as needed
  }

  interface MenuListObject {
    id: number;
    item_name: string;
    item_desc: string;
    mc_id: number;
    price: number;
    img_url: string;
    mc_name: string;
    // Add more properties as needed
  }

  interface LocationObject {
    location_id: number;
    name: string;
    address: string;
    open_time: string;
    close_time: string;
    status: string;
    creation_date: string;
    layout_img_url: string;
    manager_id: number;
  }

  export default {
    data: () => ({
      isError: false,
      isTimeValid: false,
      errorData: '',
      stepper1: 0,
      pageSpinner: false,
      hasLocation: false,
      locationList: [] as LocationObject[],
      menuList: [] as MenuListObject[],
      seatList: [] as SeatObject[],
      filterSeatList: [] as SeatObject[],
      filterSeatCount: 0,
      branchLayout: '',
      selectedLocID: 0,
      selectedLoc: {} as LocationObject,
      selectedTime: '' as string | DateTime,
      selectedSeat: null as SeatObject | null,
      foodPreOrderList: [] as MenuObject[],
      dtSearch: '',
      resDateTime: '',
      resGuest: 1,
      dtHeaders: [
        { title: 'Name', align: 'start', key: 'name' },
        { title: 'Close Time', align: 'center', key: 'close_time' },
      ],
    }),
    methods: {
      addMenu(obj: MenuObject): void {
        if (!this.isMenuIDinPreOrder(obj.id)) {
          this.foodPreOrderList.push(obj);
        } else {
          this.updateMenuById(1, obj.id);
        }
      },
      removeMenuById(id: number): void {
        this.foodPreOrderList = this.foodPreOrderList.filter((item) => item.id !== id);
      },
      updateMenuById(addOrReduce: number, id: number): void {
        let tmp: MenuObject;
        for (let i = 0; i < this.foodPreOrderList.length; i++) {
          if (this.foodPreOrderList[i].id === id) {
            tmp = this.foodPreOrderList[i];
            if (addOrReduce == 1) {
              tmp.amount++;
            } else {
              tmp.amount--;
              if (tmp.amount == 0) {
                this.removeMenuById(id);
              }
            }
            break;
          }
        }
      },
      isMenuIDinPreOrder(id: Number) {
        for (let i = 0; i < this.foodPreOrderList.length; i++) {
          if (this.foodPreOrderList[i].id === id) return true;
        }
        return false;
      },
      findSeatforSelectedDT() {
        this.selectedTime = DateTime.fromISO(this.resDateTime);
        this.loadAvailableTable(this.selectedLocID, DateTime.fromISO(this.resDateTime).toFormat('yyyy-LL-dd TT'));
      },
      async loadLocation() {
        this.pageSpinner = true;
        await $fetch('/api/data', {
          method: 'POST',
          body: {
            type: 7,
            usage: 'user',
          },
          lazy: true,
        })
          .catch((error) => {
            this.isError = true;
            this.errorData = error.data;
          })
          .then((response) => {
            const { status, message } = response as { status: number; message: any };
            this.locationList = message;
            this.pageSpinner = false;
            this.isError = false;
          });
      },
      async loadMenusFromLocation(locID: Number) {
        this.pageSpinner = true;
        await $fetch('/api/data', {
          method: 'POST',
          body: {
            type: 5,
            usage: 'user',
            location_id: locID,
          },
          lazy: true,
        })
          .catch((error) => {
            this.isError = true;
            this.errorData = error.data;
          })
          .then((response) => {
            const { status, message } = response as { status: number; message: any };
            this.menuList = message;
            this.pageSpinner = false;
            this.isError = false;
          });
      },
      async loadLocationByID(locID: Number) {
        this.pageSpinner = true;
        await $fetch('/api/data', {
          method: 'POST',
          body: {
            type: 10,
            usage: 'user',
            location_id: locID,
          },
          lazy: true,
        })
          .catch((error) => {
            this.isError = true;
            this.errorData = error.data;
          })
          .then((response) => {
            const { status, message } = response as { status: number; message: any };
            this.selectedLoc = message[0];
            this.pageSpinner = false;
            this.isError = false;
          });
      },
      async loadAvailableTable(locID: Number, arriavalTime: string) {
        this.pageSpinner = true;
        await $fetch('/api/data', {
          method: 'POST',
          body: {
            type: 11,
            usage: 'user',
            location_id: locID,
            arrival: arriavalTime,
          },
          lazy: true,
        })
          .catch((error) => {
            this.isError = true;
            this.errorData = error.data;
          })
          .then((response) => {
            const { status, message } = response as { status: number; message: any };
            this.seatList = message;
            this.pageSpinner = false;
            this.isError = false;
          });
      },
      async makeReservation() {
        // console.log(this.foodPreOrderList)
        this.pageSpinner = true;
        await $fetch('/api/data', {
          method: 'POST',
          body: {
            type: 3,
            usage: 'user',
            location_id: this.selectedLocID,
            arrival: DateTime.fromISO(this.resDateTime).toFormat('yyyy-LL-dd TT'),
            cus_count: this.resGuest,
            table_id: this.selectedSeat?.table_id,
            menu: this.foodPreOrderList,
          },
          lazy: true,
        })
          .catch((error) => {
            this.isError = true;
            this.errorData = error.data;
          })
          .then((response) => {
            const { status, message } = response as { status: number; message: any };
            if (status === 1) {
              alert('Booking Successful');
              this.$router.push('/');
            } else {
              alert('Booking Failure');
            }
          });
      },
      seatRule() {
        if (this.filterSeatCount >= 1) return true;
        return 'No seat with specified condition available';
      },
      isDateTimeValidRule() {
        if (this.isDateTimeInRange() && this.isDateTimeInOperation()) return true;
        if (!this.isDateTimeInOperation()) return 'Reservation must be in operational time';
        if (!this.isDateTimeInRange()) return 'Reservation date must be > 2 hours and < 14 days into the future';
        return 'Reservation date must be > 2 hours and < 14 days into the future and must be in operational time';
      },
      isDateTimeInRange() {
        const time = DateTime.fromISO(this.resDateTime);
        const start = DateTime.now().plus({ hours: 1 });
        const end = DateTime.now().plus({ days: 14 });
        const interval = Interval.fromDateTimes(start, end);

        return interval.contains(time);
      },
      isDateTimeInOperation() {
        const time = DateTime.fromISO(this.resDateTime);
        const openTime = DateTime.fromSQL(this.selectedLoc.open_time);
        const closeTime = DateTime.fromSQL(this.selectedLoc.close_time).minus({ minutes: 30 });

        const timeHours = time.hour;
        const timeMinutes = time.minute;
        const startHours = openTime.hour;
        const startMinutes = openTime.minute;
        const endHours = closeTime.hour;
        const endMinutes = closeTime.minute;

        return (timeHours > startHours || (timeHours === startHours && timeMinutes >= startMinutes)) && (timeHours < endHours || (timeHours === endHours && timeMinutes <= endMinutes));
      },
    },

    computed: {
      filteredSeatListCompute() {
        this.filterSeatList = JSON.parse(JSON.stringify(this.seatList));
        this.filterSeatCount = this.filterSeatList.filter((item) => Number(item.capacity) >= this.resGuest).length;
        return this.filterSeatList.filter((item) => Number(item.capacity) >= this.resGuest);
      },
      total: function () {
        return this.foodPreOrderList.reduce((acc, item) => acc + item.price * item.amount, 0);
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
  <v-main class="justify-center reservation_main">
    <div class="main_container mx-auto blur-effect account_body mt-10 py-1 px-1 min-h-40">
      <h1 class="text-h3 font-weight-bold mt-8 ml-8 text-left">Reservation</h1>
      <v-sheet class="mt-8 ma-md-8 ma-xs-1 text-center bg-transparent" rounded="0">
        <v-stepper v-model="stepper1" :mobile="mobile">
          <v-alert v-if="isError" class="ma-3" color="error" icon="$error" title="Error">
            {{ errorData }}
          </v-alert>
          <v-stepper-header>
            <v-stepper-item :complete="hasLocation" :disabled="hasLocation" value="1">
              <template v-slot:title>Select Branch</template>
            </v-stepper-item>
            <v-divider></v-divider>

            <v-stepper-item value="2">
              <template v-slot:title>Choose Time</template>
            </v-stepper-item>

            <v-divider></v-divider>

            <v-stepper-item value="3">
              <template v-slot:title>Pick Your Seat</template>
            </v-stepper-item>

            <v-divider></v-divider>

            <v-stepper-item value="4">
              <template v-slot:title>Pre-Order Food</template>
              <template v-slot:subtitle>Optional</template>
            </v-stepper-item>

            <v-divider></v-divider>

            <v-stepper-item value="5">
              <template v-slot:title>Summary</template>
            </v-stepper-item>
          </v-stepper-header>

          <v-stepper-window :touch="false">
            <v-stepper-window-item :disabled="hasLocation" value="1">
              <v-card title="">
                <v-card-text>
                  <h3 class="text-h4 font-weight-medium text-left">Select Branches</h3>
                  <p class="text-h6 font-weight-light text-left">Click on the row to see more infomation of the branches</p>
                </v-card-text>
                <v-no-ssr>
                  <v-data-table :density="mobile ? 'compact' : 'comfortable'" :headers="dtHeaders" :items="locationList" :loading="pageSpinner" :search="dtSearch" class="elevation-1" item-value="location_id">
                    <template v-slot:top>
                      <v-text-field v-model="dtSearch" placeholder="Search" prepend-inner-icon="mdi-text-search"></v-text-field>
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
                        <td class="text-start td-hover">{{ item.name }}</td>
                        <td class="text-center td-hover">{{ DateTime.fromISO(item.close_time).toFormat('t') }}</td>
                      </tr>
                    </template>

                    <template v-slot:expanded-row="{ columns, item }">
                      <tr>
                        <td :colspan="columns.length" class="text-left">
                          <v-container>
                            <v-row>
                              <v-col col="12" sm="6">
                                <b>Operating Hours</b>
                                <br />
                                {{ DateTime.fromISO(item.open_time).toFormat('t') }} -
                                {{ DateTime.fromISO(item.close_time).toFormat('t') }}
                                <br />
                              </v-col>
                              <v-col col="12" sm="6">
                                <b>Address</b>
                                <br />
                                {{ item.address }}
                              </v-col>
                            </v-row>
                            <v-row>
                              <v-col col="12">
                                <v-btn
                                  prepend-icon="mdi-check-decagram"
                                  variant="tonal"
                                  @click="
                                    () => {
                                      loadLocationByID(item.location_id);
                                      selectedLocID = item.location_id;
                                      stepper1++;
                                    }
                                  ">
                                  Choose This Location
                                </v-btn>
                              </v-col>
                            </v-row>
                          </v-container>
                        </td>
                      </tr>
                    </template>
                  </v-data-table>
                </v-no-ssr>
                <v-btn :disabled="pageSpinner" class="align-right my-3" prepend-icon="mdi-refresh" rounded="2xl" text="Refresh" variant="tonal" @click="loadLocation"></v-btn>
              </v-card>
            </v-stepper-window-item>
            <v-stepper-window-item value="2">
              <v-card>
                <v-card-text>
                  <h3 class="text-h4 font-weight-medium text-left">Choose Reservation Time</h3>
                  <p class="text-h6 font-weight-light text-left">
                    <b>{{ selectedLoc.name }}</b>
                    is operating from {{ DateTime.fromISO(selectedLoc.open_time).toFormat('t') }} till {{ DateTime.fromISO(selectedLoc.close_time).toFormat('t') }}
                  </p>
                </v-card-text>
                <v-container>
                  <v-row justify="space-around">
                    <v-col cols="12" sm="6">
                      <h3 class="text-left font-weight-medium">Date & Time</h3>
                      <v-text-field v-model="resDateTime" :rules="[isDateTimeValidRule]" prepend-inner-icon="mdi-calendar-multiselect-outline" required type="datetime-local"></v-text-field>
                    </v-col>
                  </v-row>
                </v-container>

                <v-container>
                  <v-row>
                    <v-col>
                      <v-btn
                        v-if="!hasLocation"
                        prepend-icon="mdi-arrow-left"
                        variant="tonal"
                        @click="
                          () => {
                            resDateTime = '';
                            stepper1--;
                          }
                        ">
                        Back
                      </v-btn>
                    </v-col>
                    <v-col>
                      <v-btn
                        :disabled="!(isDateTimeInRange() && isDateTimeInOperation())"
                        class="text-right"
                        prepend-icon="mdi-arrow-right"
                        @click="
                          () => {
                            stepper1++;
                            findSeatforSelectedDT();
                          }
                        ">
                        Next
                      </v-btn>
                    </v-col>
                  </v-row>
                </v-container>
              </v-card>
            </v-stepper-window-item>
            <v-stepper-window-item value="3">
              <v-card>
                <v-card-text>
                  <h3 class="text-h4 font-weight-medium text-left">Guest & Seating</h3>
                </v-card-text>
                <v-container class="align-self-center">
                  <v-row>
                    <v-col>
                      <h3 class="text-left font-weight-medium">Seat Layout</h3>
                      <v-img :src="selectedLoc.layout_img_url" width="600">
                        <template v:slot:placeholder>
                          <v-skeleton-loader width="600"></v-skeleton-loader>
                        </template>
                        <template v-slot:error>
                          <v-img cover width="600" src="/images/img-error.webp"></v-img>
                        </template>
                      </v-img>
                    </v-col>
                    <v-col>
                      <h3 class="text-left font-weight-medium">How many guests are coming?</h3>
                      <v-text-field
                        v-model="resGuest"
                        :on-update:model-value="
                          () => {
                            selectedSeat = null;
                          }
                        "
                        :rules="[seatRule]"
                        min="1"
                        oninput="validity.valid || (value=1);"
                        prepend-inner-icon="mdi-account-multiple"
                        required
                        type="number"></v-text-field>
                      <h3 class="text-left font-weight-medium">Pick Your Seat</h3>
                      <v-select v-model="selectedSeat" :disabled="filterSeatCount == 0" :items="filteredSeatListCompute" :rules="[seatRule]" item-title="name" item-value="table_id" label="Table Name" prepend-inner-icon="mdi-table-chair" return-object></v-select>
                    </v-col>
                  </v-row>
                </v-container>
                <v-container>
                  <v-row>
                    <v-col>
                      <v-btn
                        prepend-icon="mdi-arrow-left"
                        variant="tonal"
                        @click="
                          () => {
                            selectedSeat = null;
                            stepper1--;
                          }
                        ">
                        Back
                      </v-btn>
                    </v-col>
                    <v-col>
                      <v-btn
                        :disabled="filterSeatCount == 0 || selectedSeat == null"
                        prepend-icon="mdi-arrow-right"
                        @click="
                          () => {
                            loadMenusFromLocation(selectedLocID);
                            stepper1++;
                          }
                        ">
                        Next
                      </v-btn>
                    </v-col>
                  </v-row>
                </v-container>
              </v-card>
            </v-stepper-window-item>
            <v-stepper-window-item value="4">
              <v-card-text>
                <h3 class="text-h4 font-weight-medium text-left">Pre-Order Food</h3>
                <p class="text-h6 font-weight-light text-left">Select foods that's just right for you!</p>
              </v-card-text>
              <v-container class="ma-0 pa-0">
                <v-row>
                  <v-col cols="12" md="8" sm="12">
                    <h3 class="ml-3 text-left font-weight-medium">Menus</h3>
                    <v-lazy :min-height="200" :options="{ threshold: 0.5 }" transition="fade-transition">
                      <v-card class="overflow-auto" elevation="0" height="525">
                        <v-container>
                          <v-row>
                            <v-col
                              v-for="food in menuList"
                              :key="food.id"
                              cols="12"
                              md="6"
                              sm="6"
                              @click="
                                () => {
                                  addMenu({ id: food.id, item_name: food.item_name, amount: 1, price: food.price });
                                }
                              ">
                              <v-card v-ripple>
                                <v-img :src="food.img_url ? food.img_url : '/images/img-coming-soon.webp'" aspect="16/9" cover height="300">
                                  <template v-slot:error>
                                    <v-img cover height="300" src="/images/img-error.webp" width="300"></v-img>
                                  </template>
                                </v-img>
                                <v-card-title>
                                  {{ food.item_name }}
                                </v-card-title>
                                <v-card-subtitle>
                                  <v-chip color="warning">
                                    {{ food.mc_name }}
                                  </v-chip>
                                  <v-chip color="info">{{ food.price }}฿</v-chip>
                                </v-card-subtitle>
                                <v-card-text>
                                  {{ food.item_desc }}
                                </v-card-text>
                              </v-card>
                            </v-col>
                          </v-row>
                        </v-container>
                      </v-card>
                    </v-lazy>
                  </v-col>
                  <v-col cols="12" md="4" sm="12">
                    <v-card elevation="0">
                      <h3 class="pr-0 mr-0 text-left font-weight-medium">Your Order</h3>
                      <div v-if="foodPreOrderList.length > 0">
                        <v-table fixed-header height="300px" :density="mobile ? 'compact' : 'comfortable'">
                          <thead>
                            <tr>
                              <th class="text-left">Name</th>
                              <th class="text-right">Amount</th>
                              <th class="text-right">Price</th>
                              <th class="text-right"></th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr v-for="order in foodPreOrderList" :key="order.id">
                              <td class="text-left">{{ order.item_name }}</td>
                              <td class="text-right">
                                <v-icon
                                  v-ripple
                                  size="x-small"
                                  color="red"
                                  icon="mdi-minus mr-1"
                                  @click="
                                    () => {
                                      updateMenuById(0, order.id);
                                    }
                                  "></v-icon>
                                {{ order.amount }}
                                <v-icon
                                  v-ripple
                                  size="x-small"
                                  color="green"
                                  icon="mdi-plus ml-1"
                                  @click="
                                    () => {
                                      updateMenuById(1, order.id);
                                    }
                                  "></v-icon>
                              </td>
                              <td class="text-right">{{ order.amount * order.price }} ฿</td>
                              <td class="text-right">
                                <v-icon
                                  color="red"
                                  size="x-small"
                                  @click="
                                    () => {
                                      removeMenuById(order.id);
                                    }
                                  ">
                                  mdi-delete
                                </v-icon>
                              </td>
                            </tr>
                          </tbody>
                        </v-table>
                        <v-table class="mx-3 mt-3 text-h6 font-weight-medium">
                          <tbody>
                            <td class="text-center"><b>Total</b></td>
                            <td class="text-center">{{ total }} ฿</td>
                          </tbody>
                        </v-table>
                      </div>
                      <div v-else>
                        <p class="text-left ml-5">
                          <v-icon>mdi-information-slab-circle-outline</v-icon>
                          You haven't made any order yet! Select food from the menu to see it show up here.
                        </p>
                      </div>
                    </v-card>
                  </v-col>
                </v-row>
              </v-container>
              <v-container>
                <v-row>
                  <v-col>
                    <v-btn
                      prepend-icon="mdi-arrow-left"
                      variant="tonal"
                      @click="
                        () => {
                          stepper1--;
                        }
                      ">
                      Back
                    </v-btn>
                  </v-col>
                  <v-col>
                    <v-btn
                      prepend-icon="mdi-arrow-right"
                      @click="
                        () => {
                          stepper1++;
                        }
                      ">
                      Next
                    </v-btn>
                  </v-col>
                </v-row>
              </v-container>
            </v-stepper-window-item>
            <v-stepper-window-item value="5">
              <v-card>
                <v-card-text>
                  <h3 class="text-h4 font-weight-medium text-left">Summary</h3>
                  <v-container>
                    <v-row>
                      <v-col>
                        <v-card class="pa-3 text-left" height="150">
                          <div class="mt-3 ml-3">
                            <h3 class="font-weight-bold">
                              <v-icon icon="mdi-map-marker"></v-icon>
                              Location
                            </h3>
                            <p class="ml-12 text-h6 font-weight-light">
                              {{ selectedLoc.name }}
                            </p>
                          </div>
                        </v-card>
                      </v-col>
                      <v-col>
                        <v-card class="pa-3 text-left" height="150">
                          <div class="mt-3 ml-3">
                            <h3 class="font-weight-bold">
                              <v-icon icon="mdi-clock-time-three"></v-icon>
                              Date and Time
                            </h3>
                            <p class="ml-12 text-h6 font-weight-light">
                              <v-icon>mdi-calendar-blank</v-icon>
                              {{ DateTime.fromISO(selectedTime).toFormat('DDDD') }}
                              <br />
                              <v-icon>mdi-clock-outline</v-icon>
                              {{ DateTime.fromISO(selectedTime).toFormat('t') }}
                            </p>
                          </div>
                        </v-card>
                      </v-col>
                      <v-col>
                        <v-card class="pa-3 text-left" height="150">
                          <div class="mt-3 ml-3">
                            <h3 class="font-weight-bold">
                              <v-icon>mdi-table-chair</v-icon>
                              Table
                            </h3>
                            <p class="ml-12 text-h6 font-weight-light">{{ selectedSeat?.name }}</p>
                          </div>
                        </v-card>
                      </v-col>
                    </v-row>
                    <v-row>
                      <v-col>
                        <v-card class="pa-3">
                          <h3 class="ml-5 mt-3 text-left text-h5 font-weight-medium">Your Order</h3>
                          <div v-if="foodPreOrderList.length > 0">
                            <v-table class="mx-3" fixed-header max-height="300px" :density="mobile ? 'compact' : 'comfortable'">
                              <thead>
                                <tr>
                                  <th class="text-left">Name</th>
                                  <th class="text-right">Amount</th>
                                  <th class="text-right">Price</th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr v-for="order in foodPreOrderList" :key="order.id">
                                  <td class="text-left">{{ order.item_name }}</td>
                                  <td class="text-right">{{ order.amount }}</td>
                                  <td class="text-right">{{ order.amount * order.price }} ฿</td>
                                </tr>
                              </tbody>
                            </v-table>
                            <v-table class="mx-3 text-h6">
                              <tbody>
                                <td class="text-right">Total</td>
                                <td class="text-right">{{ total }} ฿</td>
                              </tbody>
                            </v-table>
                          </div>
                          <div v-else>
                            <p class="text-left ml-5">
                              <v-icon class="my-3">mdi-information-slab-circle-outline</v-icon>
                              You didn't pre-order anything
                            </p>
                          </div>
                        </v-card>
                      </v-col>
                    </v-row>
                  </v-container>
                </v-card-text>
                <v-container>
                  <v-row>
                    <v-col>
                      <v-btn
                        prepend-icon="mdi-arrow-left"
                        variant="tonal"
                        @click="
                          () => {
                            stepper1--;
                          }
                        ">
                        Back
                      </v-btn>
                    </v-col>
                    <v-col>
                      <v-btn
                        color="success"
                        prepend-icon="mdi-check"
                        @click="
                          () => {
                            makeReservation();
                          }
                        ">
                        Confirm
                      </v-btn>
                    </v-col>
                  </v-row>
                </v-container>
              </v-card>
            </v-stepper-window-item>
          </v-stepper-window>
        </v-stepper>
      </v-sheet>
    </div>
  </v-main>
</template>
