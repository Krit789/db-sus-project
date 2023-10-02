<script lang="ts" setup>
    import { VDataTable } from "vuetify/labs/VDataTable";
    import { useDisplay } from "vuetify";
    import { DateTime } from "luxon";
    import "~/assets/stylesheets/global.css";
    import "~/assets/stylesheets/index.css";
    import "~/assets/stylesheets/management/branches.css";
    import "~/assets/stylesheets/management/management.css";
    const { mobile } = useDisplay();

    const { status, data, signIn, signOut } = useAuth();
    const route = useRouter();
    useHead({
        title: "Branches Management - Seatify Admin",
        meta: [{ name: "Seatify App", content: "My amazing site." }],
    });
</script>
<script lang="ts">
    type Location = {
        l_id: number;
        l_name: string;
        l_addr: string;
        l_open_time: string;
        l_close_time: string;
        l_status: "OPERATIONAL" | "MAINTENANCE" | "OUTOFORDER";
        l_layout_img: string | null;
        l_mgr_id: number | null;
        mgr_fn: string | null;
        mgr_ln: string | null;
        mgr_tel: string | null;
        mgr_email: string | null;
    };
    export default {
        data: () => ({
            tabNum: null,
            bEditor: false,
            bName: "",
            bAddress: "",
            bOpenTime: null,
            bCloseTime: null,
            blayout: "",
            addBranch: false,
            dtSearch: "",
            dtErrorData: "",
            dtIsError: false,
            dtData: [] as Location[],
            itemsPerPage: 10,
            dtLoading: false,
            dtHeaders: [
                {
                    title: "Location ID",
                    align: "start",
                    sortable: true,
                    key: "l_id",
                },
                { title: "Name", align: "start", key: "l_name" },
                { title: "Address", align: " d-none", key: "l_addr" },
                { title: "Status", align: " d-none", key: "l_status" },
                { title: "Open", align: " d-none", key: "l_open_time" },
                { title: "Close", align: " d-none", key: "l_close_time" },
            ],
        }),
        methods: {
            async loadData() {
                this.dtLoading = true;
                await $fetch("/api/data", {
                    method: "POST",
                    body: {
                        type: 17,
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
            urlValidator(url: string) {
                const expression = /[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b([-a-zA-Z0-9()@:%_\+.~#?&//=]*)?/gi;
                const regex = new RegExp(expression);
                if (url.match(regex)) return true;
                return "Invalid URL Format";
            },
            requiredForm(value: string) {
                if (value) return true;
                return "This field is required";
            },
        },

        beforeMount() {
            this.loadData();
        },
    };
</script>
<template>
    <v-main class="management_main">
        <v-dialog v-model="addBranch" width="auto">
            <v-card width="400">
                <v-form
                    fast-fail
                    @submit.prevent="
                        (val) => {
                            console.log(val);
                        }
                    "
                >
                    <v-card-title>Add Branch</v-card-title>
                    <v-card-text>
                        <v-text-field v-model="bName" :rules="[requiredForm]" label="Name" required></v-text-field>
                        <v-textarea v-model="bAddress" label="Address"></v-textarea>
                        <v-text-field v-model="blayout" label="Seat Layout Image URL" :rules="[urlValidator]"></v-text-field>
                        <v-text-field v-model="bOpenTime" label="Open Time" type="time"></v-text-field>
                        <v-text-field v-model="bCloseTime" label="Close Time" type="time"></v-text-field>
                    </v-card-text>
                    <v-card-actions>
                        <v-btn append-icon="mdi-plus" type="submit" color="success">Add</v-btn>
                        <v-btn color="primary" @click="addBranch = false">Cancel</v-btn>
                    </v-card-actions>
                </v-form>
            </v-card>
        </v-dialog>
        <v-dialog v-model="bEditor" :width="mobile ? '100%' : '500px'" :fullscreen="mobile" persistent>
            <v-card>
                <v-tabs v-model="tabNum" bg-color="primary" color="white">
                    <v-tab value="one">General</v-tab>
                    <v-tab value="two">Menus</v-tab>
                    <v-tab value="three">Tables</v-tab>
                </v-tabs>

                <v-card-text>
                    <v-window v-model="tabNum">
                        <v-window-item value="one">
                            <h3 class="text-left">General</h3>
                            <v-form
                                fast-fail
                                @submit.prevent="
                                    (val) => {
                                        console.log(val);
                                    }
                                "
                            >
                                <v-card-text>
                                    <v-text-field v-model="bName" :rules="[requiredForm]" label="Name" required></v-text-field>
                                    <v-textarea v-model="bAddress" label="Address"></v-textarea>
                                    <v-text-field v-model="blayout" label="Seat Layout Image URL" :rules="[urlValidator]"></v-text-field>
                                    <v-text-field v-model="bOpenTime" label="Open Time" type="time"></v-text-field>
                                    <v-text-field v-model="bCloseTime" label="Close Time" type="time"></v-text-field>
                                </v-card-text>
                                    <v-btn class="mb-2" variant="tonal" append-icon="mdi-content-save" type="submit" color="success">Save</v-btn>
                            </v-form>
                        </v-window-item>

                        <v-window-item value="two">1
                            <h3 class="text-left">Menu Restriction</h3>
                        </v-window-item>

                        <v-window-item value="three">
                            <h3 class="text-left">Tables</h3>
                        </v-window-item>
                    </v-window>
                </v-card-text>
                <v-card-actions>
                    <v-btn color="primary" block @click="bEditor = false">Close</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <div class="main_container management_container mx-auto blur-effect">
            <h1 class="text-h3 font-weight-bold my-8 ml-8 text-left">Branches Management</h1>
            <v-sheet class="mt-8 ma-md-8 ma-xs-1 text-center" rounded="lg">
                <v-alert v-if="dtIsError" class="ma-3" color="error" icon="$error" title="Fetch Error">{{ dtErrorData }}</v-alert>
                <v-data-table
                    v-model:items-per-page="itemsPerPage"
                    :headers="dtHeaders"
                    :items="dtData"
                    :loading="dtLoading"
                    :search="dtSearch"
                    class="elevation-1"
                    item-value="l_id"
                    :density="mobile ? 'compact' : 'comfortable'"
                    @click:row="
                        (val, tabl) => {
                            bEditor = true;
                        }
                    "
                >
                    <template v-slot:top>
                        <v-text-field v-model="dtSearch" placeholder="Search" prepend-inner-icon="mdi-store-search"></v-text-field>
                    </template>
                    <template v-slot:item="{ internalItem, item, toggleExpand, isExpanded }">
                        <tr
                            v-ripple
                            class="table-hover"
                            @click="
                                () => {
                                    toggleExpand(internalItem);
                                }
                            "
                        >
                            <td class="text-start td-hover">{{ item.l_id }}</td>
                            <td class="text-start td-hover">{{ item.l_name }}</td>
                        </tr>
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
                                                {{ item.l_addr }}
                                            </p>
                                        </v-col>
                                        <v-col cols="12" md="3" sm="6">
                                            <p>
                                                <b>Operating Hours</b>
                                                <br />
                                                {{ DateTime.fromSQL(item.l_open_time).toFormat("t") }} - {{ DateTime.fromSQL(item.l_close_time).toFormat("t") }}
                                            </p>
                                        </v-col>
                                        <v-col cols="12" md="3" sm="6">
                                            <p>
                                                <b>Status</b>
                                                <br />
                                                <v-icon class="mr-2">{{ item.l_status == "OPERATIONAL" ? "mdi-check" : item.l_status == "MAINTENANCE" ? "mdi-hammer-wrench" : item.l_status == "OUTOFORDER" ? "mdi-close" : "mdi-help" }}</v-icon>
                                                {{ item.l_status == "OPERATIONAL" ? "Operational" : item.l_status == "MAINTENANCE" ? "Maintenance" : item.l_status == "OUTOFORDER" ? "Out of Order" : item.l_status }}
                                            </p>
                                        </v-col>
                                        <v-col cols="12" md="3" sm="6">
                                            <p>
                                                <b>Manager</b>
                                                <br />
                                                <v-icon class="mr-2">mdi-identifier</v-icon>
                                                {{ item.l_mgr_id }}
                                                <br />
                                                <v-icon class="mr-2">mdi-account-circle</v-icon>
                                                {{ item.mgr_fn + " " + item.mgr_ln }}
                                                <br />
                                                <v-icon class="mr-2">mdi-email</v-icon>
                                                {{ item.mgr_email }}
                                                <br />
                                                <v-icon class="mr-2">mdi-phone</v-icon>
                                                {{ item.mgr_tel }}
                                            </p>
                                        </v-col>
                                    </v-row>
                                    <v-row>
                                        <v-col cols="12">
                                            <v-btn
                                                variant="text"
                                                prepend-icon="mdi-pencil"
                                                @click="
                                                    () => {
                                                        bName = item.l_name;
                                                        bAddress = item.l_addr;
                                                        blayout = item.l_layout_img;
                                                        bOpenTime = DateTime.fromSQL(item.l_open_time).toFormat('T');
                                                        bCloseTime = DateTime.fromSQL(item.l_close_time).toFormat('T');
                                                        bEditor = true;
                                                    }
                                                "
                                            >
                                                Manage Branch
                                            </v-btn>
                                        </v-col>
                                    </v-row>
                                </v-container>
                            </td>
                        </tr>
                    </template>
                </v-data-table>
                <v-col>
                    <v-btn :disabled="dtLoading" :variant="'tonal'" class="align-right mb-3" prepend-icon="mdi-refresh" rounded="lg" text="Refresh" @click="loadData"></v-btn>
                    <v-btn
                        :disabled="dtLoading"
                        :variant="'tonal'"
                        class="ml-5 align-right mb-3"
                        color="success"
                        prepend-icon="mdi-plus"
                        rounded="lg"
                        text="Add Branch"
                        @click="
                            () => {
                                bName = '';
                                bAddress = '';
                                blayout = '';
                                bOpenTime = null;
                                bCloseTime = null;
                                addBranch = true;
                            }
                        "
                    ></v-btn>
                </v-col>
            </v-sheet>
        </div>
    </v-main>
</template>
