<script lang="ts" setup>
    import { VDataTable } from "vuetify/labs/VDataTable";
    import { DateTime } from "luxon";
    import { useDisplay } from "vuetify";
    import "~/assets/stylesheets/global.css";
    import "~/assets/stylesheets/index.css";
    import "~/assets/stylesheets/management/users.css";
    import "~/assets/stylesheets/management/management.css";

    const { mobile } = useDisplay();
    const { status, data, signIn, signOut } = useAuth();
    const route = useRouter();
    useHead({
        title: "User Management - Seatify Admin",
        meta: [{ name: "Seatify App", content: "My amazing site." }],
        link: [{ rel: "icon", type: "image/png", href: "favicon.ico" }],
    });
</script>
<script lang="ts">
    type User = {
        user_id: number;
        first_name: string;
        last_name: string;
        email: string;
        telephone: string | null;
        role: "USER" | "MANAGER" | "GOD";
        created_on: string;
        status: "ACTIVE" | "SUSPENDED";
    };

    export default {
        data: () => ({
            snackbar: false,
            NotiColor: "",
            timeout: 2000,
            NotiIcon: "",
            NotiText: "",
            userID: 0,
            firstName: "",
            lastName: "",
            phoneNumber: "",
            userRole: 1,
            userStatus: 0,
            loadingDialog: false,
            userStatusDialog: false,
            userStatusDialogType: 1, // 1 is reactivation : 2 is suspension
            userPWResetDialog: false,
            newUserPWDialog: false,
            userNewPW: "",
            userAction: false,
            dtErrorData: "",
            dtSearch: "",
            dtIsError: false,
            dtData: [] as User[],
            roles: [
                {
                    id: 1,
                    name: "User",
                },
                {
                    id: 2,
                    name: "Manager",
                },
                {
                    id: 3,
                    name: "Administrator",
                },
            ],
            itemsPerPage: 10,
            dtLoading: false,
            dtHeaders: [
                {
                    title: "User ID",
                    align: "center",
                    sortable: true,
                    key: "user_id",
                },
                { title: "First Name", align: "start", key: "first_name" },
                { title: "Last Name", align: "start", key: "last_name" },
                { title: "Email", align: " d-none", key: "email" },
                { title: "Telephone", align: " d-none", key: "telephone" },
                { title: "Role", align: "start", key: "role" },
                { title: "Created On", align: " d-none", key: "created_on" },
                { title: "Status", align: "start", key: "status" },
            ] as DataTableHeader[],
        }),
        methods: {
            async loadData() {
                this.dtLoading = true;
                await $fetch("/api/data", {
                    method: "POST",
                    body: {
                        type: 1,
                        usage: "admin",
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
            async updateRole(user_id: number, user_role: number) {
                await $fetch("/api/data", {
                    method: "POST",
                    body: {
                        type: 10,
                        usage: "admin",
                        u_role: user_role,
                        u_id: user_id,
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
                            this.snackbar = true;
                            this.NotiColor = "error";
                            this.NotiIcon = "mdi-alert";
                            this.NotiText = message;
                        } else if (status == 1) {
                            this.snackbar = true;
                            this.NotiColor = "success";
                            this.NotiIcon = "mdi-check";
                            this.NotiText = message;
                            this.loadData();
                        }
                        this.userAction = false;
                    });
            },
            async changeStatus(user_id: number, user_status: number) {
                await $fetch("/api/data", {
                    method: "POST",
                    body: {
                        type: 2,
                        usage: "admin",
                        u_id: user_id,
                        u_status: user_status,
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
                            this.snackbar = true;
                            this.NotiColor = "error";
                            this.NotiIcon = "mdi-alert";
                            this.NotiText = message;
                        } else if (status == 1) {
                            this.snackbar = true;
                            this.NotiColor = "success";
                            this.NotiIcon = "mdi-check";
                            this.NotiText = message;
                            this.loadData();
                        }
                        this.userStatusDialog = false;
                    });
            },
            async resetPassword(user_id: number) {
                await $fetch("/api/data", {
                    method: "POST",
                    body: {
                        type: 3,
                        usage: "admin",
                        u_id: user_id,
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
                            this.snackbar = true;
                            this.NotiColor = "error";
                            this.NotiIcon = "mdi-alert";
                            this.NotiText = message;
                        } else if (status == 1) {
                            this.snackbar = true;
                            this.NotiColor = "success";
                            this.NotiIcon = "mdi-check";
                            this.NotiText = "Password Resetted!";
                            this.userNewPW = message;
                            this.newUserPWDialog = true;
                        }
                        this.userPWResetDialog = false;
                        this.loadingDialog = false;
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
        <v-snackbar v-model="snackbar" :color="NotiColor" :timeout="timeout" location="top" multi-line>
            <v-icon :icon="NotiIcon" start></v-icon>
            {{ NotiText }}
        </v-snackbar>
        <v-dialog
      v-model="loadingDialog"
      :scrim="false"
      persistent
      width="auto"
    >
      <v-card
        color="primary"
      >
        <v-card-text>
          Making Changes
          <v-progress-linear
            indeterminate
            color="white"
            class="mb-0"
          ></v-progress-linear>
        </v-card-text>
      </v-card>
    </v-dialog>
        <v-dialog v-model="userAction" :width="mobile ? '100%' : '500px'" :fullscreen="mobile">
            <v-card :width="mobile ? '100%' : '500px'">
                <v-card-title>Modify User</v-card-title>
                <v-card-text>
                    <v-text-field variant="outlined" v-model="firstName" label="First Name" readonly></v-text-field>
                    <v-text-field variant="outlined" v-model="lastName" label="Last Name" readonly></v-text-field>
                    <v-text-field variant="outlined" prepend-icon="mdi-phone" v-model="phoneNumber" label="Phone number" readonly></v-text-field>
                    <v-select prepend-icon="mdi-tag" v-model="userRole" :items="roles" item-title="name" item-value="id" label="Role"></v-select>
                </v-card-text>
                <v-card-actions>
                    <v-btn
                        append-icon="mdi-check"
                        color="success"
                        @click="
                            () => {
                                updateRole(userID, userRole);
                            }
                        "
                    >
                        Apply
                    </v-btn>
                    <v-btn append-icon="mdi-cancel" color="error" @click="userAction = false">Cancel</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <v-dialog v-model="userStatusDialog" :width="mobile ? '100%' : '500px'" :fullscreen="mobile">
            <v-card :width="mobile ? '100%' : '500px'">
                <v-card-title>{{ userStatusDialogType === 2 ? "Account Suspension" : "Account Activation" }}</v-card-title>
                <v-card-text>
                    <p>You are changing the status of '{{ firstName + " " + lastName }}' ID: {{ userID }} to {{ userStatusDialogType === 2 ? "Suspended" : "Active" }} are you sure that you want to continue?</p>
                </v-card-text>
                <v-card-actions>
                    <v-btn
                        append-icon="mdi-check"
                        color="success"
                        @click="
                            () => {
                                changeStatus(userID, userStatusDialogType);
                            }
                        "
                    >
                        Confirm
                    </v-btn>
                    <v-btn append-icon="mdi-cancel" color="error" @click="userStatusDialog = false">Cancel</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <v-dialog v-model="userPWResetDialog" :width="mobile ? '100%' : '500px'" :fullscreen="mobile">
            <v-card :width="mobile ? '100%' : '500px'">
                <v-card-title>Password Reset</v-card-title>
                <v-card-text>
                    <p>You resetting the password of '{{ firstName + " " + lastName }}' ID: {{ userID }} to a randomly generated characters. Are you sure that you want to continue? A dialog showing the new password will follow.</p>
                </v-card-text>
                <v-card-actions>
                    <v-btn append-icon="mdi-check" color="success" @click="() => {loadingDialog = true; resetPassword(userID); userID = 0;}">Confirm</v-btn>
                    <v-btn append-icon="mdi-cancel" color="error" @click="userPWResetDialog = false">Cancel</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <v-dialog v-model="newUserPWDialog" :width="mobile ? '100%' : '500px'" :fullscreen="mobile">
            <v-card :width="mobile ? '100%' : '500px'">
                <v-card-title>New Password</v-card-title>
                <v-card-text>
                    <p>This is the new password for '{{ firstName + " " + lastName }}' ID: {{ userID }}. This dialog will only show up <b>once</b>! Make sure to keep it safe.</p>
                    <br>
                    <v-text-field label="New Password" v-model="userNewPW" readonly></v-text-field>
                </v-card-text>
                <v-card-actions>
                    <v-btn @click="() => {newUserPWDialog = false; userNewPW = '';}">Close</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <div class="main_container management_container mx-auto blur-effect">
            <h1 class="text-h3 font-weight-bold mt-8 ml-8 text-left">User Management</h1>
            <v-sheet class="mt-8 ma-md-8 ma-xs-1 text-center" rounded="lg">
                <v-alert v-if="dtIsError" class="ma-3" color="error" icon="$error" title="Fetch Error">{{ dtErrorData }}</v-alert>
                <v-data-table v-model:items-per-page="itemsPerPage" :headers="dtHeaders" :items="dtData" :loading="dtLoading" :search="dtSearch" :density="mobile ? 'compact' : 'comfortable'" class="elevation-1" item-value="user_id">
                    <template v-slot:top>
                        <v-text-field v-model="dtSearch" placeholder="Search" prepend-inner-icon="mdi-account-search"></v-text-field>
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
                            <td class="text-center td-hover">{{ item.user_id }}</td>
                            <td class="text-start td-hover">{{ item.first_name }}</td>
                            <td class="text-start td-hover">{{ item.last_name }}</td>
                            <td class="text-start td-hover">
                                <v-tooltip location="top">
                                    <template v-slot:activator="{ props }">
                                        <v-icon v-bind="props">{{ item.role == "USER" ? "mdi-account-outline" : item.role == "MANAGER" ? "mdi-account-tie" : item.role == "GOD" ? "mdi-account-star" : "mdi-help" }}</v-icon>
                                    </template>
                                    <span>{{ item.role == "USER" ? "Customer" : item.role == "MANAGER" ? "Manager" : item.role == "GOD" ? "Administrator" : item.role }}</span>
                                </v-tooltip>
                            </td>
                            <td class="text-start td-hover">
                                <v-tooltip location="top">
                                    <template v-slot:activator="{ props }">
                                        <v-icon v-bind="props">{{ item.status == "ACTIVE" ? "mdi-check" : item.status == "SUSPENDED" ? "mdi-close" : "mdi-help" }}</v-icon>
                                    </template>
                                    <span>{{ item.status == "ACTIVE" ? "Active" : item.status == "SUSPENDED" ? "Suspended" : item.status }}</span>
                                </v-tooltip>
                            </td>
                        </tr>
                    </template>
                    <template v-slot:expanded-row="{ columns, item }">
                        <tr>
                            <td :colspan="columns.length" class="text-left">
                                <v-container>
                                    <v-row>
                                        <v-col cols="12" md="4" sm="6">
                                            <b>Name</b>
                                            <p>{{ item.first_name }} {{ item.last_name }}</p>
                                        </v-col>
                                        <v-col cols="12" md="4" sm="6">
                                            <b>E-Mail</b>
                                            <p>{{ item.email }}</p>
                                        </v-col>
                                        <v-col cols="12" md="4" sm="6">
                                            <b>Telephone</b>
                                            <p>{{ item.telephone ? item.telephone : "-" }}</p>
                                        </v-col>
                                        <v-col cols="12" md="4" sm="6">
                                            <b>Created On</b>
                                            <p>
                                                <v-icon>mdi-calendar-blank</v-icon>
                                                {{ DateTime.fromSQL(item.created_on).toFormat("DDDD") }}
                                                <br />
                                                <v-icon>mdi-clock-outline</v-icon>
                                                {{ DateTime.fromSQL(item.created_on).toFormat("t") }}
                                            </p>
                                        </v-col>
                                    </v-row>
                                    <v-row>
                                        <v-col cols="12" md="12" sm="12">
                                            <v-btn
                                                prepend-icon="mdi-pencil"
                                                variant="text"
                                                class="mr-5"
                                                @click="
                                                    () => {
                                                        userID = item.user_id;
                                                        firstName = item.first_name;
                                                        lastName = item.last_name;
                                                        phoneNumber = item.telephone;
                                                        userRole = item.role == 'USER' ? 1 : item.role == 'MANAGER' ? 2 : item.role == 'GOD' ? 3 : 1;
                                                        userAction = true;
                                                    }
                                                "
                                            >
                                                Edit
                                            </v-btn>
                                            <v-btn
                                                v-if="item.status == 'ACTIVE'"
                                                variant="text"
                                                prepend-icon="mdi-gavel"
                                                class="mr-5"
                                                color="error"
                                                @click="
                                                    () => {
                                                        userID = item.user_id;
                                                        firstName = item.first_name;
                                                        lastName = item.last_name;
                                                        userStatusDialog = true;
                                                        userStatusDialogType = 2;
                                                    }
                                                "
                                            >
                                                Suspend User
                                            </v-btn>
                                            <v-btn
                                                v-else-if="item.status == 'SUSPENDED'"
                                                variant="text"
                                                prepend-icon="mdi-power"
                                                class="mr-5"
                                                color="success"
                                                @click="
                                                    () => {
                                                        userID = item.user_id;
                                                        firstName = item.first_name;
                                                        lastName = item.last_name;
                                                        userStatusDialog = true;
                                                        userStatusDialogType = 1;
                                                    }
                                                "
                                            >
                                                Activate User
                                            </v-btn>
                                            <v-btn
                                                variant="text"
                                                prepend-icon="mdi-lock-reset"
                                                color="warning"
                                                @click="
                                                    () => {
                                                        userID = item.user_id;
                                                        firstName = item.first_name;
                                                        lastName = item.last_name;
                                                        userNewPW = '';
                                                        userPWResetDialog = true;
                                                    }
                                                "
                                            >
                                                Reset Password
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
                </v-col>
            </v-sheet>
        </div>
    </v-main>
</template>
