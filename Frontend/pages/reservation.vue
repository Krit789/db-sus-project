<script lang="ts" setup>
    import {
        VStepper,
        VStepperActions,
        VStepperHeader,
        VStepperItem,
        VStepperWindow,
        VStepperWindowItem,
    } from "vuetify/labs/VStepper";
    import { useDisplay } from "vuetify";
    import "~/assets/stylesheets/global.css";
    import "~/assets/stylesheets/index.css";

    const { mobile } = useDisplay();
    const { status, data } = useAuth();
    const route = useRoute();
    useHead({
        title: "Booking - Seatify",
        meta: [{ name: "Seatify App", content: "My amazing site." }],
    });
</script>

<script lang="ts">
    export default {
        data: () => ({
            stepper1: 0,
            hasLocation: false,
        }),
        beforeMount() {
            if (this.$route.query.location_id != null) {
                this.hasLocation = true;
                this.stepper1 = 1;
            } else {
                this.stepper1 = 0;
            }
        },
    };
</script>
<template>
    <v-main class="justify-center reservation_body">
        <div
            class="main_container mx-auto blur-effect account_container mt-10 py-1 px-1 min-h-40"
        >
            <h1 class="text-h3 font-weight-bold mt-8 ml-8 text-left">
                Reservation {{ $route.query.location_id }}
            </h1>
            <v-sheet
                class="mt-8 ma-md-8 ma-xs-1 text-center bg-transparent"
                rounded="0"
            >
                <v-stepper :mobile="mobile" v-model="stepper1">
                    <v-stepper-header>
                        <v-stepper-item
                            value="1"
                            :complete="hasLocation"
                            :disabled="hasLocation"
                        >
                            <template v-slot:title>Select Branch</template>
                        </v-stepper-item>

                        <v-divider></v-divider>

                        <v-stepper-item value="2">
                            <template v-slot:title>Choose Date</template>
                        </v-stepper-item>

                        <v-divider></v-divider>

                        <v-stepper-item value="3">
                            <template v-slot:title> Pick Your Seat </template>
                        </v-stepper-item>

                        <v-divider></v-divider>

                        <v-stepper-item value="4">
                            <template v-slot:title> Pre-Order Food </template>

                            <template v-slot:subtitle>Optional</template>
                        </v-stepper-item>
                        <v-divider></v-divider>
                        <v-stepper-item value="5">
                            <template v-slot:title> Summary </template>
                        </v-stepper-item>
                    </v-stepper-header>

                    <v-stepper-window>
                        <v-stepper-window-item
                            value="1"
                            :disabled="hasLocation"
                        >
                            <v-card title="Select Branches"
                                ><div class="ma-3">
                                    <v-btn
                                        @click="
                                            () => {
                                                stepper1--;
                                            }
                                        "
                                        >Back</v-btn
                                    >
                                    <v-btn
                                        @click="
                                            () => {
                                                stepper1++;
                                            }
                                        "
                                        >Next</v-btn
                                    >
                                </div>
                            </v-card>
                        </v-stepper-window-item>
                        <v-stepper-window-item value="2">
                            <v-card title="Choose Date">...</v-card>
                        </v-stepper-window-item>
                        <v-stepper-window-item value="3">
                            <v-card title="Pick Your Seat">...</v-card>
                        </v-stepper-window-item>
                        <v-stepper-window-item value="4">
                            <v-card title="Pre-Order Food">...</v-card>
                        </v-stepper-window-item>
                        <v-stepper-window-item value="5">
                            <v-card title="Summary">...</v-card>
                        </v-stepper-window-item>
                    </v-stepper-window>
                    <!-- <v-stepper-actions
            @click:prev="
              () => {
                stepper1--;
              }
            "
            @click:next="
              () => {
                stepper1++;
              }
            "
            :next-text="(stepper1 == 4) ? 'Confirm' : 'Next'"
          >
          </v-stepper-actions> -->
                </v-stepper>
            </v-sheet>
        </div>
    </v-main>
</template>
