<script lang="ts" setup>
    import { VDataTable } from "vuetify/labs/VDataTable";
    import { DateTime } from 'luxon';
    const route = useRouter();
    const { status, data, signIn, signOut } = useAuth();
    useHead({
        title: "Reservation Management - Seatify Admin",
        meta: [{ name: "Seatify App", content: "My amazing site." }],
    });
    import "~/assets/stylesheets/global.css";
    import "~/assets/stylesheets/index.css";
    import "~/assets/stylesheets/management/reservation.css";
    import "~/assets/stylesheets/management/management.css";
</script>

<script lang="ts">
    export default {
        data: () => ({
            acceptRes: false,
            dtExpanded: [],
            dtSearch: "",
            dtIsError: false,
            dtErrorData: "",
            dtData: [],
            itemsPerPage: 10,
            dtLoading: false,
            dtHeaders: [
                { title: "Status", align: "end", key: "res_status" },
                {
                    title: "ID",
                    align: "start",
                    sortable: true,
                    key: "res_id",
                },
                { title: "User ID", align: "end", key: "user_id" },
                { title: "Reserved On", align: "end", key: "res_on" },
                { title: "Reserved For", align: "end", key: "arrival" },

                { title: "Guests", align: "end", key: "cus_count" },
                { title: "Table", align: "end", key: "table_id" },
                { title: "", key: "data-table-expand" },
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
        },
        beforeMount() {
            this.loadData();
        },
    };
</script>
<template>
    <v-main class="management_main">
        <v-dialog v-model="acceptRes" width="auto">
            <v-card width="400">
                <v-card-title>Accept Customer Reservations</v-card-title>
                <v-card-text>
                    <v-text-field label="Reservation Code"></v-text-field>
                </v-card-text>
                <v-card-actions>
                    <v-btn color="success" prepend-icon="mdi-check" @click="">Confirm</v-btn>
                    <v-btn color="primary" @click="acceptRes = false">Cancel</v-btn>
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
                    :items="dtData"
                    :loading="dtLoading"
                    :search="dtSearch"
                    class="elevation-1"
                    item-value="res_id"
                    @click:row="
                        (val, tabl) => {
                            console.log(tabl.item.columns.res_id);
                        }
                    "
                >
                    <template v-slot:top>
                        <v-text-field v-model="dtSearch" placeholder="Search" prepend-inner-icon="mdi-book-search"></v-text-field>
                    </template>
                    <template v-slot:item="{ item, toggleExpand, isExpanded }">
                        <tr class="text-end" @click="
                                () => {
                                    console.log(item);
                                }">
                            <td class="text-center">
                                <v-tooltip>
                                    <template v-slot:activator="{ props }">
                                        <v-icon v-bind="props">{{ item.raw.res_status == "INPROGRESS" ? "mdi-progress-clock" : item.raw.res_status == "FULFILLED" ? "mdi-check" : item.raw.res_status == "CANCELLED" ? "mdi-close" : "mdi-help" }}</v-icon>
                                    </template>
                                    <span>{{ item.raw.res_status == "INPROGRESS" ? "In Progress" : item.raw.res_status == "FULFILLED" ? "Fulfilled" : item.raw.res_status == "CANCELLED" ? "Cancelled" : "Unknown" }}</span>
                                </v-tooltip>
                            </td>
                            <td>{{ item.raw.res_id }}</td>
                            <td>{{ item.raw.user_id }}</td>
                            <td>{{ DateTime.fromSQL(item.raw.res_on).toFormat('D') }}</td>
                            <td>{{ DateTime.fromSQL(item.raw.arrival).toFormat('f') }}</td>
                            <td>{{ item.raw.cus_count }}</td>
                            <td>{{ item.raw.table_id }} | <b>{{ item.raw.table_name }}</b></td>
                            <td><v-btn @click="toggleExpand(item)" variant="text"><v-icon class="toggleUpDown" :class='{ "rotate": isExpanded(item) }'>mdi-chevron-down</v-icon></v-btn></td>
                        </tr>
                    </template>
                    <template v-slot:expanded-row="{ columns, item }">
                        <tr>
                            <td :colspan="columns.length" class="text-left">
                                <v-container>
                                    <v-row>
                                        <v-col>
                                            <b>Reserved By</b>
                                            <p>{{ item.raw.first_name }} {{ item.raw.last_name }}</p>
                                        </v-col>
                                        <v-col>
                                            <b>Reserved For</b>
                                            <p>
                                              <v-icon>mdi-calendar-blank</v-icon> {{ DateTime.fromSQL(item.raw.arrival).toFormat('DDDD') }}<br />
                                                <v-icon>mdi-clock-outline</v-icon> {{ DateTime.fromSQL(item.raw.arrival).toFormat('t') }}
                                            </p>
                                        </v-col>
                                        <v-col>
                                            <b>Reserved At</b>
                                            <p>{{ item.raw.loc_name }}</p>
                                        </v-col>
                                        <v-col>
                                            <b>Branch Address</b>
                                            <p>{{ item.raw.loc_addr }}</p>
                                        </v-col>
                                    </v-row>
                                </v-container>
                            </td>
                        </tr>
                    </template>
                </v-data-table>
                <v-col>
                    <v-btn :disabled="dtLoading" :variant="'tonal'" class="align-right mb-3" prepend-icon="mdi-refresh" rounded="lg" text="Refresh" @click="loadData"></v-btn>
                    <v-btn :disabled="dtLoading" :variant="'tonal'" class="align-right mb-3 ml-5" color="green" prepend-icon="mdi-plus" rounded="lg" text="Accept Reservation" @click="acceptRes = true"></v-btn>
                </v-col>
            </v-sheet>
        </div>
    </v-main>
</template>

<style>
.toggleUpDown {
    transition: transform .15s ease-in-out !important;  
}

.toggleUpDown.rotate {
    transform: rotate(-180deg);
}</style>
