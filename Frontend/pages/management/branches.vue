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

    type Table = {
        table_id: number;
        name: string;
        capacity: number;
    };
    export default {
        data: () => ({
            tabNum: null,
            layoutPreview: false,
            addTableDialog: false,
            addTableMode: 0, // 0 for adding table : 1 for renaming table
            delTableDialog: false,
            tableName: "",
            tableID: 0,
            bEditor: false,
            bID: 0,
            bName: "",
            bAddress: "",
            bOpenTime: "",
            bCloseTime: "",
            blayout: "",
            addBranch: false,
            dtSearch: "",
            dtErrorData: "",
            dtIsError: false,
            dtData: [] as Location[],
            locSeat: [] as Table[],
            itemsPerPage: 10,
            dtLoading: false,
            snackbar: false,
            NotiColor: "",
            timeout: 2000,
            NotiIcon: "",
            NotiText: "",
            dtHeaders: [[
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
            ]],
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
                    .then((response) => {
                        const { message } = response as { status: number; message: any; };
                        this.dtData = message;
                        this.dtLoading = false;
                        this.dtIsError = false;
                    });
            },
            async loadTableByLocationID(loc_id: number) {
                await $fetch("/api/data", {
                    method: "POST",
                    body: {
                        type: 10,
                        usage: "manager",
                        l_id: loc_id,
                    },
                    lazy: true,
                })
                    .catch((error) => {
                        this.dtIsError = true;
                        this.dtErrorData = error.data;
                    })
                    .then((response) => {
                        const { status, message } = response as { status: number; message: any; };
                        if (status == 0) {
                            this.snackbar = true;
                            this.NotiColor = "error";
                            this.NotiIcon = "mdi-alert";
                            this.NotiText = message;
                        } else if (status == 1) {
                            this.locSeat = message;
                        }
                        this.dtIsError = false;
                    });
            },
            async createLocation(l_name: string, l_addr: string, l_open_time: string, l_close_time: string, l_layout_img: string) {
                let requestBody = { type: 4, usage: "admin", name: l_name, address: l_addr, open_time: l_open_time, close_time: l_close_time };
                if (l_layout_img) {
                    requestBody = Object.assign({}, requestBody, {layout_img : l_layout_img})
                }
                console.log(requestBody);
                await $fetch("/api/data", {
                    method: "POST",
                    body: requestBody,
                    lazy: true,
                })
                    .catch((error) => {
                        this.dtIsError = true;
                        this.dtErrorData = error.data;
                    })
                    .then((response) => {
                        const { status, message } = response as { status: number; message: any; }; // Destructure inside the callback
                        if (status == 0) {
                            this.snackbar = true;
                            this.NotiColor = "error";
                            this.NotiIcon = "mdi-alert";
                            this.NotiText = message;
                        } else if (status == 1) {
                            this.snackbar = true;
                            this.NotiColor = "success";
                            this.NotiIcon = "mdi-check";
                            this.NotiText = message;
                        }
                        this.addBranch = false;
                        this.dtIsError = false;
                        this.loadData()
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
        <v-snackbar v-model="snackbar" :color="NotiColor" :timeout="timeout" location="top" multi-line>
            <v-icon :icon="NotiIcon" start></v-icon>
            {{ NotiText }}
        </v-snackbar>
        <v-dialog v-model="addBranch" width="auto">
            <v-card width="400">
                <v-form
                    fast-fail
                    @submit.prevent="
                        () => {
                            createLocation(bName, bAddress, DateTime.fromISO(bOpenTime).toFormat('yyyy-LL-dd TT'), DateTime.fromISO(bCloseTime).toFormat('yyyy-LL-dd TT'), blayout);
                        }
                    "
                >
                    <v-card-title>Add Branch</v-card-title>
                    <v-card-text>
                        <v-text-field v-model="bName" :rules="[requiredForm]" label="Name" required></v-text-field>
                        <v-textarea v-model="bAddress" :rules="[requiredForm]" label="Address"></v-textarea>
                        <v-text-field v-model="blayout" :rules="[urlValidator]" label="Seat Layout Image URL"></v-text-field>
                        <v-text-field v-model="bOpenTime" label="Open Time" type="time"></v-text-field>
                        <v-text-field v-model="bCloseTime" label="Close Time" type="time"></v-text-field>
                    </v-card-text>
                    <v-card-actions>
                        <v-btn append-icon="mdi-plus" color="success" type="submit">Add</v-btn>
                        <v-btn color="primary" @click="addBranch = false">Cancel</v-btn>
                    </v-card-actions>
                </v-form>
            </v-card>
        </v-dialog>
        <v-dialog v-model="layoutPreview" :fullscreen="mobile" :width="mobile ? '100%' : '800px'">
            <v-card :width="mobile ? '100%' : '800px'">
                <v-card-title>Layout Preview</v-card-title>
                <v-card-subtitle>
                    Previewing layout of
                    <b>{{ bName }}</b>
                </v-card-subtitle>
                <v-card-text>
                    <v-img :src="blayout ? blayout : '/images/img-coming-soon.webp'" height="auto">
                        <template v-slot:placeholder>
                            <v-skeleton-loader type="image"></v-skeleton-loader>
                        </template>
                        <template v-slot:error>
                            <v-img cover height="300" src="/images/img-error.webp" width="300"></v-img>
                        </template>
                    </v-img>
                </v-card-text>
                <v-card-actions>
                    <v-btn color="primary" @click="layoutPreview = false">Close</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <v-dialog v-model="addTableDialog" :width="'auto'">
            <v-card :width="mobile ? 'auto' : '400px'">
                <v-card-title>{{ addTableMode == 0 ? "Create Table" : "Rename Table" }}</v-card-title>
                <v-card-subtitle>{{ addTableMode == 0 ? "Create a new table" : `Renaming table ${tableName} at ${bName}` }}</v-card-subtitle>
                <v-card-text>
                    <v-text-field label="Table Name" v-model="tableName"></v-text-field>
                </v-card-text>
                <v-card-actions>
                    <v-btn
                        color="success"
                        :prepend-icon="addTableMode == 0 ? 'mdi-check' : 'mdi-content-save'"
                        @click="
                            () => {
                                // manageTable(tableName, tableID, bID);
                                tableName = '';
                            }
                        "
                    >
                        {{ addTableMode == 0 ? "Confirm" : "Save" }}
                    </v-btn>
                    <v-btn color="error" prepend-icon="mdi-cancel" @click="addTableDialog = false">Cancel</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <v-dialog v-model="delTableDialog" :width="'auto'">
            <v-card :width="mobile ? 'auto' : '400px'">
                <v-card-title>Table Deletion</v-card-title>
                <v-card-text>Are you sure that you want to delete table {{ tableName }} at {{ bName }}?</v-card-text>
                <v-card-actions>
                    <v-btn
                        color="success"
                        prepend-icon="mdi-check"
                        @click="
                            () => {
                                // deleteTable(tableID);
                                tableName = '';
                            }
                        "
                    >
                        Confirm
                    </v-btn>
                    <v-btn color="error" prepend-icon="mdi-cancel" @click="delTableDialog = false">Cancel</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <v-dialog v-model="bEditor" :fullscreen="mobile" :width="mobile ? '100%' : '500px'" persistent>
            <v-card>
                <v-tabs v-model="tabNum" bg-color="primary" color="white">
                    <v-tab value="one">General</v-tab>
                    <v-tab value="two">Menus</v-tab>
                    <v-tab
                        value="three"
                        @click="
                            () => {
                                loadTableByLocationID(bID);
                            }
                        "
                    >
                        Tables
                    </v-tab>
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
                                    <v-text-field v-model="blayout" :rules="[urlValidator]" label="Seat Layout Image URL"></v-text-field>
                                    <v-text-field v-model="bOpenTime" label="Open Time" type="time"></v-text-field>
                                    <v-text-field v-model="bCloseTime" label="Close Time" type="time"></v-text-field>
                                </v-card-text>
                                <v-btn append-icon="mdi-content-save" class="mb-2" color="success" type="submit" variant="tonal">Save</v-btn>
                            </v-form>
                        </v-window-item>

                        <v-window-item value="two">
                            <h3 class="text-left">Menu Restriction</h3>
                        </v-window-item>

                        <v-window-item value="three">
                            <h3 class="text-left">Tables</h3>
                            <v-card-text>
                                <v-table fixed-header height="50vh" density="compact">
                                    <thead>
                                        <tr>
                                            <th class="text-left">Table ID</th>
                                            <th class="text-left">Table Name</th>
                                            <th class="text-left">
                                                {{ mobile ? "" : "Capacity" }}
                                                <v-icon v-if="mobile">mdi-account-multiple</v-icon>
                                            </th>
                                            <th class="text-left">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="item in locSeat" :key="item.table_id">
                                            <td>{{ item.table_id }}</td>
                                            <td>{{ item.name }}</td>
                                            <td>{{ item.capacity }}</td>
                                            <td>
                                                <v-tooltip location="top">
                                                    <template v-slot:activator="{ props }">
                                                        <v-icon
                                                            class="mr-3"
                                                            v-bind="props"
                                                            color="info"
                                                            @click="
                                                                () => {
                                                                    tableID = item.table_id;
                                                                    tableName = item.name;
                                                                    addTableMode = 1;
                                                                    addTableDialog = true;
                                                                }
                                                            "
                                                        >
                                                            mdi-pencil
                                                        </v-icon>
                                                    </template>
                                                    <span>Rename</span>
                                                </v-tooltip>
                                                <v-tooltip location="top">
                                                    <template v-slot:activator="{ props }">
                                                        <v-icon
                                                            color="red"
                                                            v-bind="props"
                                                            @click="
                                                                () => {
                                                                    tableID = item.table_id;
                                                                    tableName = item.name;
                                                                    delTableDialog = true;
                                                                }
                                                            "
                                                        >
                                                            mdi-delete
                                                        </v-icon>
                                                    </template>
                                                    <span>Delete</span>
                                                </v-tooltip>
                                            </td>
                                        </tr>
                                    </tbody>
                                </v-table>
                                <v-btn
                                    class="mt-3"
                                    variant="text"
                                    prepend-icon="mdi-plus"
                                    @click="
                                        () => {
                                            tableID = 0;
                                            tableName = '';
                                            addTableMode = 0;
                                            addTableDialog = true;
                                        }
                                    "
                                >
                                    Create Table
                                </v-btn>
                            </v-card-text>
                        </v-window-item>
                    </v-window>
                </v-card-text>
                <v-card-actions>
                    <v-btn block color="primary" @click="bEditor = false">Close</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <div class="main_container management_container mx-auto blur-effect">
            <h1 class="text-h3 font-weight-bold my-8 ml-8 text-left">Branches Management</h1>
            <v-sheet class="mt-8 ma-md-8 ma-xs-1 text-center" rounded="lg">
                <v-alert v-if="dtIsError" class="ma-3" color="error" icon="$error" title="Fetch Error">{{ dtErrorData }}</v-alert>
                <v-data-table
                    v-model:items-per-page="itemsPerPage"
                    :density="mobile ? 'compact' : 'comfortable'"
                    :headers="dtHeaders"
                    :items="dtData"
                    :loading="dtLoading"
                    :search="dtSearch"
                    class="elevation-1"
                    item-value="l_id"
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
                                                {{ DateTime.fromSQL(item.l_open_time).toFormat("t") }} -
                                                {{ DateTime.fromSQL(item.l_close_time).toFormat("t") }}
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
                                                prepend-icon="mdi-pencil"
                                                variant="text"
                                                @click="
                                                    () => {
                                                        bID = item.l_id;
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
                                            <v-btn
                                                prepend-icon="mdi-floor-plan"
                                                variant="text"
                                                color="warning"
                                                @click="
                                                    () => {
                                                        bName = item.l_name;
                                                        blayout = item.l_layout_img;
                                                        layoutPreview = true;
                                                    }
                                                "
                                            >
                                                View Seat Layout
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
                                bOpenTime = '';
                                bCloseTime = '';
                                addBranch = true;
                            }
                        "
                    ></v-btn>
                </v-col>
            </v-sheet>
        </div>
    </v-main>
</template>
