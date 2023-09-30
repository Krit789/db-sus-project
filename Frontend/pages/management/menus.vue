<script lang="ts" setup>
    import { VDataTable } from "vuetify/labs/VDataTable";
    import { useDisplay } from "vuetify";
    import "~/assets/stylesheets/global.css";
    import "~/assets/stylesheets/index.css";
    import "~/assets/stylesheets/management/menus.css";
    import "~/assets/stylesheets/management/management.css";
    const { mobile } = useDisplay();
    const route = useRouter();
    const { status, data, signIn, signOut } = useAuth();
    useHead({
        title: "Reservation Management - Seatify Admin",
        meta: [{ name: "Seatify App", content: "My amazing site." }],
    });
</script>

<script lang="ts">
    interface menu_category {
        category_id: Number;
        name: String;
    }
    interface MenuItem {
        m_id: number;
        m_name: string;
        m_desc: string | null;
        m_price: number;
        m_img: string | null;
        c_id: number | null;
        c_name: string | null;
    }

    export default {
        data: () => ({
            groupBy: [{ key: "c_name", order: "asc" }],
            addMenuDialog: false,
            menuName: "",
            menuDesc: "",
            menuID: 0,
            menuPrice: 0,
            menuCategoryID: 0,
            menuImgUrl: "",
            confDel: false,
            dtSearch: "",
            dtIsError: false,
            dtErrorData: "",
            menuCategory: [] as menu_category[],
            dtData: [] as MenuItem[],
            itemsPerPage: 10,
            dtLoading: false,
            snackbar: false,
            NotiColor: "",
            timeout: 2000,
            NotiIcon: "",
            NotiText: "",
            dtHeaders: [
                { title: "Menu ID", align: "start", key: "m_id" },
                // { title: "Menu Description", align: " d-none", key: "item_desc" },
                // { title: "Category", align: " d-none", key: "name" },
                { title: "Name", align: "start", key: "m_name" },
                { title: "Price", align: "start", key: "m_price" },
            ],
        }),
        methods: {
            async loadData() {
                this.dtLoading = true;
                await $fetch("/api/data", {
                    method: "POST",
                    body: {
                        type: 5,
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
            async deleteMenu(menu_id: Number) {
                this.dtLoading = true;
                await $fetch("/api/data", {
                    method: "POST",
                    body: {
                        type: 9,
                        usage: "admin",
                        menu_id: menu_id,
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
                        }
                        this.confDel = false;
                        this.loadData();
                    });
            },
            async addMenu(menu_name: String, menu_price: Number, menu_desc: String, menu_category_id: Number, menu_img: String) {
                this.dtLoading = true;
                let menuQuery = {
                    type: 7,
                    usage: "admin",
                    m_name: menu_name,
                    m_price: menu_price,
                };
                if (!menu_desc) {
                    menuQuery = Object.assign({}, menuQuery, { m_desc: menu_desc });
                }
                if (!menu_category_id) {
                    menuQuery = Object.assign({}, menuQuery, { m_category: menu_category_id });
                }
                if (!menu_img) {
                    menuQuery = Object.assign({}, menuQuery, { img_url: menu_img });
                }
                console.log(menuQuery);
                // await $fetch("/api/data", {
                //     method: "POST",
                //     body: menuQuery,
                //     lazy: true,
                // })
                //     .catch((error) => {
                //         this.dtIsError = true;
                //         this.dtErrorData = error.data;
                //     })
                //     .then(({ status, message }) => {
                //         this.dtLoading = false;
                //         this.dtIsError = false;
                //         if (status == 0) {
                //             this.snackbar = true;
                //             this.NotiColor = "error";
                //             this.NotiIcon = "mdi-alert";
                //             this.NotiText = message;
                //         } else if (status == 1) {
                //             this.snackbar = true;
                //             this.NotiColor = "success";
                //             this.NotiIcon = "mdi-check";
                //             this.NotiText = message;
                //         }
                //         this.confDel = false;
                //         this.loadData();
                //     });
            },
        },

        beforeMount() {
            this.loadData().then(() => {
                // Create an empty map to store unique pairs of category_id and name
                const uniquePairsMap = new Map();

                // Iterate through the JSON data and add unique pairs to the map
                this.dtData.forEach((item) => {
                    const { c_id, c_name } = item;
                    const key = `${c_id}_${c_name}`;

                    // Check if the key is not already in the map
                    if (!uniquePairsMap.has(key)) {
                        uniquePairsMap.set(key, { c_id, c_name });
                    }
                });

                // Convert the map values (unique pairs) to an array
                const uniquePairsArray = Array.from(uniquePairsMap.values());

                // Print the unique pairs
                this.menuCategory = uniquePairsArray;
            });
        },
    };
</script>
<template>
    <v-main class="management_main">
        <v-snackbar v-model="snackbar" :color="NotiColor" :timeout="timeout" location="top">
            <v-icon :icon="NotiIcon" start></v-icon>
            {{ NotiText }}
        </v-snackbar>
        <v-dialog v-model="addMenuDialog" :width="mobile ? '100%' : 'auto'" :fullscreen="mobile">
            <v-card :width="mobile ? 'auto' : '400px'">
                <v-card-title>Add Menu</v-card-title>
                <v-card-text>
                    <v-text-field v-model="menuName" label="Name"></v-text-field>

                    <v-textarea v-model="menuDesc" label="Description"></v-textarea>
                    <v-text-field v-model="menuImgUrl" label="Image URL"></v-text-field>
                    <v-select :items="['Not Assign', 'Seafood', 'Drinks', 'Dessert']" label="Category"></v-select>
                    <v-btn append-icon="mdi-plus">Create Category</v-btn>
                </v-card-text>
                <v-card-actions>
                    <v-btn append-icon="mdi-plus" color="success" @click="">Add</v-btn>
                    <v-btn color="primary" @click="() => {addMenuDialog = false}">Cancel</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <v-dialog v-model="confDel" :width="'auto'">
            <v-card :width="mobile ? 'auto' : '400px'">
                <v-card-title>Menu Deletion</v-card-title>
                <v-card-text>
                    Are you sure that you want to delete
                    <br />
                    <b>{{ menuName }}</b>
                    ?
                </v-card-text>
                <v-card-actions>
                    <v-btn
                        prepend-icon="mdi-check"
                        color="success"
                        @click="
                            () => {
                                deleteMenu(menuID);
                                menuID = 0;
                            }
                        "
                    >
                        Confirm
                    </v-btn>
                    <v-btn prepend-icon="mdi-cancel" color="error" @click="confDel = false">Cancel</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <div class="main_container management_container mx-auto blur-effect">
            <h1 class="text-h3 font-weight-bold mt-8 ml-8 text-left">Menu Management</h1>
            <v-sheet class="mt-8 ma-md-8 ma-sm-5" rounded="lg">
                <v-alert v-if="dtIsError" class="ma-3" color="error" icon="$error" title="Fetch Error">{{ dtErrorData }}</v-alert>

                <v-data-table
                    v-model:items-per-page="itemsPerPage"
                    :headers="dtHeaders"
                    :items="dtData"
                    :loading="dtLoading"
                    :search="dtSearch"
                    class="elevation-1"
                    item-value="menu_id"
                    :density="mobile ? 'compact' : 'comfortable'"
                    :group-by="groupBy"
                    @click:row="
                        (val, tabl) => {
                            console.log(tabl.item.columns.menu_id);
                        }
                    "
                >
                    <template v-slot:top>
                        <v-text-field v-model="dtSearch" placeholder="Search" prepend-inner-icon="mdi-book-search"></v-text-field>
                    </template>

                    <template v-slot:item="{ internalItem, item, toggleExpand, isExpanded }">
                        <tr
                            class="table-hover"
                            @click="
                                () => {
                                    toggleExpand(internalItem);
                                }
                            "
                        >
                            <td class="text-start td-hover"></td>
                            <td class="text-start td-hover">{{ item.m_id }}</td>
                            <td class="text-start td-hover">{{ item.m_name }}</td>
                            <td class="text-start td-hover">{{ item.m_price }} ฿</td>
                        </tr>
                    </template>

                    <template v-slot:expanded-row="{ columns, item }">
                        <tr>
                            <td :colspan="columns.length" class="text-left">
                                <v-container>
                                    <v-row>
                                        <v-col col="12" sm="6">
                                            <b>Description</b>
                                            <br />
                                            {{ item.m_desc }}
                                            <br />
                                            <br />
                                            <b>Actions</b>
                                            <br />
                                            <v-btn variant="text" color="info" prepend-icon="mdi-pencil">Edit Menu</v-btn>
                                            <v-btn
                                                variant="text"
                                                color="red"
                                                prepend-icon="mdi-delete"
                                                @click="
                                                    () => {
                                                        menuDeleteID = item.m_id;
                                                        menuDeleteName = item.m_name;
                                                        confDel = true;
                                                    }
                                                "
                                            >
                                                Remove Menu
                                            </v-btn>
                                        </v-col>
                                        <v-col cols="12" md="4" sm="6">
                                            <b>Preview</b>
                                            <br />
                                            <v-card elevation="4" v-ripple class="text-center">
                                                <v-img :src="item.m_img ? item.m_img : 'https://livingstonbagel.com/wp-content/uploads/2016/11/food-placeholder.jpg'" cover aspect="16/9" height="300">
                                                    <template v-slot:error>
                                                        <v-img cover height="300" src="https://picsum.photos/500/300?image=232" width="300"></v-img>
                                                    </template>
                                                </v-img>
                                                <v-card-title>
                                                    {{ item.m_name }}
                                                </v-card-title>
                                                <v-card-subtitle>
                                                    <v-chip color="warning">
                                                        {{ item.c_name }}
                                                    </v-chip>
                                                    <v-chip color="info">{{ item.m_price }}฿</v-chip>
                                                </v-card-subtitle>
                                                <v-card-text>
                                                    {{ item.m_desc }}
                                                </v-card-text>
                                            </v-card>
                                        </v-col>
                                    </v-row>
                                </v-container>
                            </td>
                        </tr>
                    </template>
                </v-data-table>
                <v-col class="pt-5 text-center">
                    <v-btn :disabled="dtLoading" :variant="'tonal'" class="align-right mb-3" prepend-icon="mdi-refresh" rounded="lg" text="Refresh" @click="loadData"></v-btn>
                    <v-btn :disabled="dtLoading" :variant="'tonal'" class="ml-5 mb-3" color="success" prepend-icon="mdi-plus" rounded="lg" text="Add Menu" @click="addMenuDialog = true"></v-btn>
                </v-col>
            </v-sheet>
        </div>
    </v-main>
</template>
