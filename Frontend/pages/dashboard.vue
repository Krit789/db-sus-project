<script lang="ts" setup>
    import { VDataTable } from "vuetify/labs/VDataTable";
    import "~/assets/stylesheets/dashboard.css";
    import "~/assets/stylesheets/global.css";

    const { status, data, signIn, signOut } = useAuth();

    useHead({
        title: "My Reservation - Seatify",
        meta: [{ name: "Seatify App", content: "My amazing site." }],
    });
</script>
<script lang="ts">
    export default {
        data: () => ({
            expandedDT: [],
            codeDialog: false,
            reservationCode: null,
            dtIsError: false,
            dtErrorData: "",
            isError: false,
            errorData: "",
            dtData: [],
            itemsPerPage: 10,
            dtLoading: false,
            dtHeaders: [
                { title: "Status", align: "start", key: "res_status" },
                { title: "Location Name", align: "start", key: "location_name" },
                { title: "No. of Customer", align: "end", key: "cus_count" },
                { title: "Table", align: "end", key: "table_name" },
                { title: "Reserved For", align: "start", key: "arrival" },
                { title: "", key: "data-table-expand" },
            ],
        }),
        methods: {
            async loadData() {
                this.dtLoading = true;
                await $fetch("/api/data", {
                    method: "POST",
                    body: {
                        type: 8,
                        usage: "user",
                    },
                    lazy: true,
                })
                    .catch((error) => {
                        this.dtIsError = true;
                        this.dtErrorData = error.data;
                    })
                    .then(({ message }) => {
                        this.dtData = message;
                        this.dtLoading = false;
                        this.dtIsError = false;
                    });
            },
            async cancelReservation(res_id: Number) {
                this.dtLoading = true;
                await $fetch("/api/data", {
                    method: "POST",
                    body: {
                        type: 2,
                        usage: "user",
                        res_id: res_id,
                    },
                    lazy: true,
                })
                    .catch((error) => {
                        this.dtIsError = true;
                        this.dtErrorData = error.data;
                    })
                    .then(({ message }) => {
                        this.dtLoading = false;
                        this.dtIsError = false;
                        this.loadData();
                    });
            },
        },
        beforeMount() {
            this.loadData();
        },
    };
</script>

<template>
    <v-main class="justify-center dashboard_body">
        <div class="dashboard_container main_container mx-auto blur-effect mt-10 py-1 px-1 min-h-40">
            <h1 class="text-h3 font-weight-bold mt-8 ml-8 text-left">My Dashboard</h1>
            <v-sheet class="mt-8 ma-md-8 ma-sm-5 text-center" rounded="lg">
                <v-alert v-if="dtIsError" class="ma-3" color="error" icon="$error" title="Fetch Error">{{ dtErrorData }}</v-alert>
                <v-no-ssr>
                    <v-data-table
                        v-model:items-per-page="itemsPerPage"
                        :expanded="expandedDT"
                        :headers="dtHeaders"
                        :items="dtData"
                        :loading="dtLoading"
                        class="elevation-0"
                        item-value="res_id"
                        loading-text="We're looking for your reservation, Hang tight!"
                        @click:row="
                            (val, tabl) => {
                                reservationCode = tabl.item.raw.res_code;
                                codeDialog = !codeDialog;
                            }
                        "
                    >
                        <template v-slot:expanded-row="{ columns, item }">
                            <tr>
                                <td :colspan="columns.length" class="text-left">
                                    <v-container>
                                        <v-row>
                                            <v-col col="12" sm="6">
                                                <b>Address</b>
                                                <br />
                                                {{ item.raw.location_address }}
                                                <br />
                                            </v-col>
                                            <v-col col="12" sm="6">
                                                <div v-if="item.raw.res_status === 'INPROGRESS'">
                                                    <v-tooltip>
                                                        <template v-slot:activator="{ props }">
                                                            <v-icon color="info" v-bind="props" class="mr-3" @click="">mdi-pencil</v-icon>
                                                        </template>
                                                        <span>Edit Reservation</span>
                                                    </v-tooltip>
                                                    <v-tooltip>
                                                        <template v-slot:activator="{ props }">
                                                            <v-icon
                                                                v-bind="props"
                                                                color="red"
                                                                @click="
                                                                    () => {
                                                                        cancelReservation(item.raw.res_id);
                                                                    }
                                                                "
                                                            >
                                                                mdi-cancel
                                                            </v-icon>
                                                        </template>
                                                        <span>Cancel Reservation</span>
                                                    </v-tooltip>
                                                </div>
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
                        <v-text-field
          label="Code"
          readonly
          variant="outlined"
          rows="1"
          row-height="15"
          no-resize
          v-model="reservationCode"
        ></v-text-field>
                    </v-card-text>
                    <v-card-actions>
                        <v-btn color="primary" block @click="codeDialog = false">Done</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </div>
    </v-main>
</template>
