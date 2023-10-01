<script lang="ts" setup>
    import { VDataTable } from "vuetify/labs/VDataTable";
    import { useDisplay } from "vuetify";
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
    export default {
        data: () => ({
            tabNum: null,
            bEditor: false,
            addBranch: false,
            dtSearch: "",
            dtErrorData: "",
            dtIsError: false,
            dtData: [],
            itemsPerPage: 10,
            dtLoading: false,
            dtHeaders: [
                {
                    title: "Location ID",
                    align: "start",
                    sortable: true,
                    key: "location_id",
                },
                { title: "Name", align: "start", key: "name" },
                { title: "Manager", align: "end", key: "managerID" },
                { title: "Address", align: "end", key: "address" },
                { title: "Status", align: "end", key: "status" },
                { title: "Open", align: "end", key: "open_time" },
                { title: "Close", align: "end", key: "close_time" },
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
                <v-card-title>Add Branch</v-card-title>
                <v-card-text>
                    <v-text-field label="Name"></v-text-field>
                    <v-textarea label="Address"></v-textarea>
                    <v-text-field label="Seat Layout Image URL" :rules="[urlValidator]"></v-text-field>
                    <v-text-field label="Open Time" type="time"></v-text-field>
                    <v-text-field label="Close Time" type="time"></v-text-field>
                </v-card-text>
                <v-card-actions>
                    <v-btn append-icon="mdi-plus" color="success" @click="">Add</v-btn>
                    <v-btn color="primary" @click="addBranch = false">Cancel</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <v-dialog v-model="bEditor" :width="mobile ? '100%' : 'auto'" :fullscreen="mobile" persistent>
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
                        </v-window-item>

                        <v-window-item value="two">
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
                    item-value="location_id"
                    :density="mobile ? 'compact' : 'comfortable'"
                    @click:row="
                        (val, tabl) => {
                            // $router.push('/management/branches/' + tabl.item.columns.location_id);
                            bEditor = true;
                        }
                    "
                >
                    <template v-slot:top>
                        <v-text-field v-model="dtSearch" placeholder="Search" prepend-inner-icon="mdi-store-search"></v-text-field>
                    </template>
                </v-data-table>
                <v-col>
                    <v-btn :disabled="dtLoading" :variant="'tonal'" class="align-right mb-3" prepend-icon="mdi-refresh" rounded="lg" text="Refresh" @click="loadData"></v-btn>
                    <v-btn :disabled="dtLoading" :variant="'tonal'" class="ml-5 align-right mb-3" color="success" prepend-icon="mdi-plus" rounded="lg" text="Add Branch" @click="addBranch = true"></v-btn>
                </v-col>
            </v-sheet>
        </div>
    </v-main>
</template>
