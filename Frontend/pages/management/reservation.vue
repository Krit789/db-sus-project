<script lang="ts" setup>
    import { VDataTable } from "vuetify/labs/VDataTable";
    import { DateTime } from "luxon";
    import {useDisplay} from "vuetify";
    import "~/assets/stylesheets/global.css";
    import "~/assets/stylesheets/index.css";
    import "~/assets/stylesheets/management/reservation.css";
    import "~/assets/stylesheets/management/management.css";
    const route = useRouter();
    const {mobile} = useDisplay();
    const { status, data, signIn, signOut } = useAuth();
    useHead({
        title: "Reservation Management - Seatify Admin",
        meta: [{ name: "Seatify App", content: "My amazing site." }],
    });
</script>

<script lang="ts">
    export default {
        data: () => ({
            acceptRes: false as boolean,
            resConfCode: "",
            confirmCancel: false as boolean,
            acceptError: "",
            cancelResID: 0,
            dtSearch: "",
            dtIsError: false,
            dtErrorData: "",
            dtData: [],
            itemsPerPage: 10,
            dtLoading: false,
            snackbar: false,
            NotiColor: "",
            timeout: 2000,
            NotiIcon: "",
            NotiText: "",
            dtHeaders: [
                {
                    title: "ID",
                    align: "center",
                    sortable: true,
                    key: "res_id",
                },
                { title: "User ID", align: "center", key: "user_id" },
                { title: "Location Name", align: "start", key: "loc_name" },
                { title: "First Name", align: " d-none", key: "first_name" }, // ' d-none' hides the header but keeps the search functionality
                { title: "Last Name", align: " d-none", key: "last_name" },
                { title: "Reserved On", align: "end", key: "res_on" },
                { title: "Reserved For", align: "end", key: "arrival" },
                { title: "Guests", align: "end", key: "cus_count" },
                { title: "Table", align: "end", key: "table_id" },
                { title: "Table Name", align: " d-none", key: "table_name" },
                { title: "Status", align: "end", key: "res_status" },
            ],
        }),
        methods: {
            async loadData() {
                this.dtLoading = true;
                await $fetch("/api/data", {
                    method: "POST",
                    body: {
                        type: 12,
                        usage: "admin",
                    },
                    lazy: true,
                })
                    .catch((error) => {
                        this.dtIsError = true;
                        this.dtErrorData = error.data;
                    })
                    .then(({ status, message }) => {
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
                    .then(({ status, message }) => {
                        this.dtLoading = false;
                        this.dtIsError = false;
                        if (status == 0) {
                            this.acceptError = message;
                            this.snackbar = true;
                            this.NotiColor = "error";
                            this.NotiIcon = "mdi-alert";
                            this.NotiText = message;
                        } else if (status == 1) {
                            this.acceptError = "";
                            this.snackbar = true;
                            this.NotiColor = "success";
                            this.NotiIcon = "mdi-check";
                            this.NotiText = message;
                        }
                        this.loadData();
                    });
            },
            async acceptReservation(res_code: string) {
                this.dtLoading = true;
                await $fetch("/api/data", {
                    method: "POST",
                    body: {
                        type: 1,
                        usage: "user",
                        res_code: res_code,
                    },
                    lazy: true,
                })
                    .catch((error) => {
                        this.dtIsError = true;
                        this.dtErrorData = error.data;
                    })
                    .then(({ status, message }) => {
                        this.dtLoading = false;
                        this.dtIsError = false;
                        if (status == 0) {
                            this.acceptError = message;
                            this.snackbar = true;
                            this.NotiColor = "error";
                            this.NotiIcon = "mdi-alert";
                            this.NotiText = message;
                        } else if (status == 1) {
                            this.acceptError = "";
                            this.snackbar = true;
                            this.NotiColor = "success";
                            this.NotiIcon = "mdi-check";
                            this.NotiText = message;
                        }
                        this.resConfCode = "";
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
    <v-main class="management_main">
        <v-snackbar v-model="snackbar" :color="NotiColor" :timeout="timeout" location="top">
            <v-icon :icon="NotiIcon" start></v-icon>
            {{ NotiText }}
        </v-snackbar>
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
                        "
                    >
                        Confirm
                    </v-btn>
                    <v-btn color="error" prepend-icon="mdi-cancel" @click="confirmCancel = false">Cancel</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <div class="main_container management_container mx-auto blur-effect">
            <h1 class="text-h3 font-weight-bold mt-8 ml-8 text-left">Reservation Management</h1>
            <v-sheet class="mt-8 ma-md-8 ma-sm-5 text-center" rounded="lg">
                <v-alert v-if="dtIsError" class="ma-3" color="error" icon="$error" title="Fetch Error">{{ dtErrorData }}</v-alert>
                <v-data-table
                    v-model:items-per-page="itemsPerPage"
                    :headers="dtHeaders"
                    :multi-sort="true"
                    :items="dtData"
                    :loading="dtLoading"
                    :search="dtSearch"
                    class="elevation-1"
                    item-value="res_id"
                    :density="(mobile) ? 'compact' : 'comfortable'"
                >
                    <template v-slot:top>
                        <v-card elevation="0">
                            <v-card-title class="text-left">Accept Reservation</v-card-title>
                            <v-card-item>
                                <v-container>
                                    <v-row>
                                        <v-col><v-text-field label="Reservation Code" v-bind:error-messages="acceptError" v-model="resConfCode"></v-text-field></v-col>
                                    </v-row>
                                    <v-row>
                                        <v-col>
                                            <v-btn
                                                variant="tonal"
                                                color="success"
                                                :disabled="dtLoading"
                                                prepend-icon="mdi-check"
                                                @click="
                                                    () => {
                                                        acceptReservation(resConfCode);
                                                    }
                                                "
                                            >
                                                Accept Reservation
                                            </v-btn>
                                        </v-col>
                                    </v-row>
                                </v-container>
                            </v-card-item>
                        </v-card>
                        <v-text-field v-model="dtSearch" placeholder="Search" prepend-inner-icon="mdi-book-search"></v-text-field>
                    </template>
                    <template v-slot:item="{ internalItem, item, toggleExpand, isExpanded }">
                        <tr
                            class="text-end table-hover"
                            @click="toggleExpand(internalItem)"
                        >
                            <td class="text-center td-hover">{{ item.res_id }}</td>
                            <td class="text-center td-hover">
                                {{ item.user_id }}
                                <v-tooltip activator="parent" location="top">{{ item.first_name + " " + item.last_name }}</v-tooltip>
                            </td>
                            <td class="text-left td-hover">
                                {{ item.loc_name }}
                                <v-tooltip activator="parent" location="top">ID: {{ item.loc_id }}</v-tooltip>
                            </td>
                            <td class="text-right td-hover">
                                {{ DateTime.fromSQL(item.res_on).toFormat("D") }}
                                <v-tooltip activator="parent" location="top">{{ DateTime.fromSQL(item.res_on).toFormat("fff") }}</v-tooltip>
                            </td>
                            <td class="text-right td-hover">
                                {{ DateTime.fromSQL(item.arrival).toFormat("D") }}
                                <v-tooltip activator="parent" location="top">{{ DateTime.fromSQL(item.arrival).toFormat("fff") }}</v-tooltip>
                            </td>
                            <td class="td-hover">{{ item.cus_count }}</td>
                            <td class="text-right td-hover">
                                {{ item.table_name }}
                            </td>
                            <td class="td-hover">
                                <v-tooltip location="top">
                                    <template v-slot:activator="{ props }">
                                        <v-icon v-bind="props">{{ item.res_status == "INPROGRESS" ? "mdi-progress-clock" : item.res_status == "FULFILLED" ? "mdi-check" : item.res_status == "CANCELLED" ? "mdi-close" : "mdi-help" }}</v-icon>
                                    </template>
                                    <span>{{ item.res_status == "INPROGRESS" ? "In Progress" : item.res_status == "FULFILLED" ? "Fulfilled" : item.res_status == "CANCELLED" ? "Cancelled" : "Unknown" }}</span>
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
                                                {{ DateTime.fromSQL(item.arrival).toFormat("DDDD") }}
                                                <br />
                                                <v-icon>mdi-clock-outline</v-icon>
                                                {{ DateTime.fromSQL(item.arrival).toFormat("t") }}
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
                                        <v-col class="text-right">
                                            <v-btn
                                                v-if="item.res_status == 'INPROGRESS'"
                                                color="error"
                                                variant="text"
                                                @click="
                                                    () => {
                                                        cancelResID = item.res_id;
                                                        confirmCancel = true;
                                                    }
                                                "
                                            >
                                                Cancel Reservation
                                            </v-btn>
                                            <v-btn v-if="item.res_status == 'CANCELLED'" color="error" variant="text" disabled>Cancelled</v-btn>
                                        </v-col>
                                    </v-row>
                                </v-container>
                            </td>
                        </tr>
                    </template>
                </v-data-table>
                <v-col>
                    <v-btn :disabled="dtLoading" :variant="'tonal'" class="align-right mb-3" prepend-icon="mdi-refresh" rounded="lg" text="Refresh" @click="loadData"></v-btn>
                </v-col>
            </v-sheet>
        </div>
    </v-main>
</template>

<style>
    .toggleUpDown {
        transition: transform 0.15s ease-in-out !important;
    }

    .toggleUpDown.rotate {
        transform: rotate(-180deg);
    }
</style>
