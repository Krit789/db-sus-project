<script lang="ts" setup>
import {useDisplay} from 'vuetify';
import '~/assets/stylesheets/global.css';
import '~/assets/stylesheets/index.css';
import '~/assets/stylesheets/account.css';

const {signOut, data} = useAuth();
const {mobile} = useDisplay();

useHead({
  title: 'My Account - Seatify',
  meta: [{name: 'Seatify App', content: 'My amazing site.'}],
});
</script>

<script lang="ts">
interface User {
  id: number;
  first_name: string;
  last_name: string;
  email: string;
  telephone: string | null;
  points: number;
}

interface StatusData {
  status: 'INPROGRESS' | 'CANCELLED' | 'FULFILLED';
  count: number;
}

export default {
  data: () => ({
    DialogueCP: false,
    editMode: false,
    dtIsError: false,
    dtErrorData: '',
    dtData: [] as StatusData[],
    inprogress: 0,
    fufilled: 0,
    cancelled: 0,
    Old_password: '',
    New_password: '',
    conf_password: '',
    loadingDialog: false,
    resetTokenDialog: false,
    fName: '',
    lName: '',
    telNum: '' as string | null,
    email: '',
    snackbar: false,
    NotiColor: '',
    timeout: 2000,
    NotiIcon: '',
    NotiText: '',
    dtLoading: false,
    confirm_new_password: '',
    accountData: {} as User,
  }),
  methods: {
    passwordValidation(value: String) {
      if (this.New_password === value) return true;
      return 'Both passwords must be similar.';
    },
    emailValidation(value: String) {
      if (
          String(value)
              .toLowerCase()
              .match(/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|.(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/)
      )
        return true;

      return 'E-Mail must be in correct format.';
    },
    async loadData() {
      this.dtLoading = true;
      await $fetch('/api/data', {
        method: 'POST',
        body: {
          type: 15,
          usage: 'user',
        },
        lazy: true,
      })
          .catch((error) => {
            this.dtIsError = true;
            this.dtErrorData = error.data;
          })
          .then((response) => {
            const {status, message} = response as {
              status: number;
              message: any;
            };
            this.dtData = message;
            this.dtLoading = false;
            this.dtIsError = false;
          });
    },
    async loadAccountData() {
      await $fetch('/api/data', {
        method: 'POST',
        body: {
          type: 12,
          usage: 'user',
        },
        lazy: true,
      })
          .catch((error) => {
            this.dtIsError = true;
            this.dtErrorData = error.data;
          })
          .then((response) => {
            const {status, message} = response as {
              status: number;
              message: User;
            };
            this.accountData = message;
            this.fName = message.first_name;
            this.lName = message.last_name;
            this.telNum = message.telephone;
            this.email = message.email;
          });
    },
    async updateUser(first_name: string, last_name: string, email: string, tel_num: string | null, password: string) {
      this.loadingDialog = true;
      let requestBody = {
        type: 13,
        usage: 'user',
        first_name: first_name,
        last_name: last_name,
        email: email,
        pswd: password,
      };
      if (tel_num) {
        requestBody = Object.assign({}, requestBody, {tel_num: tel_num});
      }
      await $fetch('/api/data', {
        method: 'POST',
        body: requestBody,
        lazy: true,
      })
          .catch((error) => {
            this.dtIsError = true;
            this.dtErrorData = error.data;
          })
          .then((response) => {
            const {status, message} = response as {
              status: number;
              message: any;
            };
            this.dtLoading = false;
            this.dtIsError = false;
            if (status != 1) {
              this.snackbar = true;
              this.NotiColor = 'error';
              this.NotiIcon = 'mdi-alert';
              this.NotiText = message;
            } else if (status == 1) {
              this.snackbar = true;
              this.NotiColor = 'success';
              this.NotiIcon = 'mdi-check';
              this.NotiText = message;
            }
            this.editMode = false;
            this.loadData();
            this.loadAccountData();
            this.loadingDialog = false;
          });
    },
    async updateUserPassword(password: string, new_password: string) {
      this.loadingDialog = true;
      await $fetch('/api/data', {
        method: 'POST',
        body: {
          type: 14,
          usage: 'user',
          pswd: password,
          new_pswd: new_password,
        },
        lazy: true,
      })
          .catch((error) => {
            this.dtIsError = true;
            this.dtErrorData = error.data;
          })
          .then((response) => {
            const {status, message} = response as {
              status: number;
              message: any;
            };
            this.dtLoading = false;
            this.dtIsError = false;
            if (status != 1) {
              this.snackbar = true;
              this.NotiColor = 'error';
              this.NotiIcon = 'mdi-alert';
              this.NotiText = message;
            } else if (status == 1) {
              this.snackbar = true;
              this.NotiColor = 'success';
              this.NotiIcon = 'mdi-check';
              this.NotiText = message;
            }
            this.Old_password = '';
            this.New_password = '';
            this.confirm_new_password = '';
            this.DialogueCP = false;
            this.loadData();
            this.loadingDialog = false;
          });
    },
    async resetToken() {
      this.loadingDialog = true;
      await $fetch('/api/data', {
        method: 'POST',
        body: {
          type: 16,
          usage: 'user',
        },
        lazy: true,
      })
          .catch((error) => {
            this.dtIsError = true;
            this.dtErrorData = error.data;
          })
          .then((response) => {
            const {status, message} = response as {
              status: number;
              message: any;
            };
            this.dtLoading = false;
            this.dtIsError = false;
            if (status != 1) {
              this.snackbar = true;
              this.NotiColor = 'error';
              this.NotiIcon = 'mdi-alert';
              this.NotiText = message;
            } else if (status == 1) {
              this.snackbar = true;
              this.NotiColor = 'success';
              this.NotiIcon = 'mdi-check';
              this.NotiText = message;
            }
            this.resetTokenDialog = false;
            this.loadingDialog = false;
          });
    },
  },
  beforeMount() {
    this.loadData();
    this.loadAccountData();
  },
};

function toTitleCase(str: string) {
  return str.replace(/\w\S*/g, function (txt: string) {
    return txt.charAt(0).toUpperCase() + txt.slice(1).toLowerCase();
  });
}
</script>

<template>
  <v-main class="justify-center account_main">
    <v-snackbar v-model="snackbar" :color="NotiColor" :timeout="timeout" location="top">
      <v-icon :icon="NotiIcon" start></v-icon>
      {{ NotiText }}
    </v-snackbar>
    <v-dialog v-model="resetTokenDialog" :width="'auto'">
      <v-card :width="mobile ? 'auto' : '400px'">
        <v-card-title>Token Reset</v-card-title>
        <v-card-text>Are you sure that you want to reset your token? This will make all authenticated instance stop
          working until they reauthenticate!
        </v-card-text>
        <v-card-actions>
          <v-btn
              color="success"
              prepend-icon="mdi-check"
              @click="
              () => {
                resetToken();
                signOut({
                  callbackUrl: '/',
                  redirect: true,
                }).then(() => {
                  $router.push('/');
                  NotiText = 'You have been logged out';
                  NotiColor = 'info';
                  NotiIcon = 'mdi-check-circle-outline';
                  snackbar = true;
                });
              }
            ">
            Confirm
          </v-btn>
          <v-btn color="error" prepend-icon="mdi-cancel" @click="resetTokenDialog = false">Cancel</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog v-model="loadingDialog" :scrim="false" persistent width="200px">
      <v-card color="primary">
        <v-card-text class="text-center">
          <p class="mb-1">Please Wait</p>
          <v-progress-linear class="mb-0" color="white" indeterminate></v-progress-linear>
        </v-card-text>
      </v-card>
    </v-dialog>
    <div class="main_container mx-auto">
      <v-row class="justify-center mt-8 px-3">
        <v-col class="user_rounded mt-10">
          <div class="justify-center">
            <v-img class="mt-5 bg-white ma-2 user_image" src="/ejudge_avatar280.png" width="170"></v-img>
          </div>
          <!--            <h2>Welcome Back!</h2>-->
          <h2 class="mt-6">{{ accountData.first_name }} {{ accountData.last_name }}</h2>
          <h3>{{ accountData.email }}</h3>
          <h3 class="mt-12">
            You are our
            <template v-if="data.role == 'USER'">Customer</template>
            <template v-else>{{ toTitleCase(data?.role) }}</template>
          </h3>
          <h4 v-if="data.role === 'USER'">
            <v-icon>mdi-circle-multiple</v-icon>
            {{ accountData.points }} points
          </h4>
        </v-col>
        <v-col class="user_rounded mt-10">
          <v-row class="mt-1 ml-2">
            <div class="font-weight-bold text-h3"><p class="text-left">Statistics</p></div>
          </v-row>
          <v-row class="justify-center">
            <v-card :width="mobile ? 'auto' : '80%'" class="text-center ma-2 status_box">
              <v-container>
                <v-row align="center" justify="center">
                  <v-col class="py-0 justify-center align-center" cols="2">
                    <v-icon class="icon_img">mdi-ticket-confirmation</v-icon>
                  </v-col>
                  <v-col class="text-left py-0 ml-3" col="10">
                    <v-card-title class="font-weight-bold text-h5">You have made</v-card-title>
                    <v-card-text class="font-weight-regular text-h5">{{ dtData.length }} reservations in total
                    </v-card-text>
                  </v-col>
                </v-row>
              </v-container>
            </v-card>
            <v-card :width="mobile ? 'auto' : '80%'" class="text-center ma-2 status_box">
              <v-container>
                <v-row align="center" justify="center">
                  <v-col class="py-0 justify-center align-center" cols="2">
                    <v-icon class="icon_img">mdi-book-check</v-icon>
                  </v-col>
                  <v-col class="text-left py-0 ml-3" col="10">
                    <v-card-title class="font-weight-bold text-h5">Your reservation got fulfilled</v-card-title>
                    <v-card-text class="font-weight-regular text-h5">
                      {{ dtData.filter((item) => item.status == 'FULFILLED').length }}
                      {{ dtData.filter((item) => item.status == 'FULFILLED').length === 1 ? 'time' : 'times' }}
                    </v-card-text>
                  </v-col>
                </v-row>
              </v-container>
            </v-card>
            <v-card :width="mobile ? 'auto' : '80%'" class="text-center ma-2 status_box">
              <v-container>
                <v-row align="center" justify="center">
                  <v-col class="py-0 justify-center align-center" cols="2">
                    <v-icon class="icon_img">mdi-tag-multiple</v-icon>
                  </v-col>
                  <v-col class="text-left py-0 ml-3" col="10">
                    <v-card-title class="font-weight-bold text-h5">You have</v-card-title>
                    <v-card-text class="font-weight-regular text-h5">
                      {{ dtData.filter((item) => item.status == 'INPROGRESS').length }} upcoming reservations
                    </v-card-text>
                  </v-col>
                </v-row>
              </v-container>
            </v-card>
            <v-card :width="mobile ? 'auto' : '80%'" class="text-center ma-2 status_box">
              <v-container>
                <v-row align="center" justify="center">
                  <v-col class="py-0 justify-center align-center" cols="2">
                    <v-icon class="icon_img">mdi-file-document-remove</v-icon>
                  </v-col>
                  <v-col class="text-left py-0 ml-3" col="10">
                    <v-card-title class="font-weight-bold text-h5">You cancelled</v-card-title>
                    <v-card-text class="font-weight-regular text-h5">
                      {{ dtData.filter((item) => item.status == 'CANCELLED').length }} reservations
                    </v-card-text>
                  </v-col>
                </v-row>
              </v-container>
            </v-card>
          </v-row>
        </v-col>
      </v-row>
      <div class="text-center mt-10 mb-15 ma-auto your_account user_rounded pa-4 mx-3">
        <v-card-text class="text-h3 font-weight-bold my-6">Your Account</v-card-text>
        <div class="mx-md-16 mx-sm-8 mx-xs-8">
          <v-text-field v-model="fName" :readonly="!editMode" label="First Name" variant="underlined"></v-text-field>
          <v-text-field v-model="lName" :readonly="!editMode" label="Last Name" variant="underlined"></v-text-field>
          <v-text-field v-model="telNum" :readonly="!editMode"
                        :rules="[(v) => (v || '').length <= 10 || 'Phone Number Must be shorter than 10 characters']"
                        label="Telephone Number" variant="underlined"></v-text-field>
          <v-text-field v-model="email" :readonly="!editMode" label="Email" variant="underlined"></v-text-field>
          <v-text-field v-if="editMode" v-model="conf_password" :readonly="!editMode" label="Confirm Password"
                        type="password" variant="underlined"></v-text-field>
        </div>

        <v-btn v-if="editMode == true" class="ma-2" color="#0373DE" rounded="lg" variant="tonal"
               @click="DialogueCP = true">Change Password
        </v-btn>
        <v-divider class="border-opacity-0"></v-divider>
        <v-btn
            v-if="editMode == false"
            class="ma-2"
            color="#0373DE"
            rounded="lg"
            variant="tonal"
            @click.stop="
            () => {
              conf_password = '';
              editMode = true;
            }
          ">
          Edit
        </v-btn>
        <!-- vv only appear on edit mode vv -->
        <v-btn
            v-if="editMode == true"
            class="ma-2"
            color="success"
            prepend-icon="mdi-content-save"
            rounded="lg"
            variant="tonal"
            @click.stop="
            () => {
              updateUser(fName, lName, email, telNum, conf_password);
            }
          ">
          Save
        </v-btn>

        <v-btn
            v-if="editMode == true"
            class="ma-2"
            color="error"
            prepend-icon="mdi-cancel"
            rounded="lg"
            variant="tonal"
            @click.stop="
            () => {
              conf_password = '';
              editMode = false;
            }
          ">
          Cancel
        </v-btn>
        <!-- ^^ only appear on edit mode ^^ -->
        <br/>
        <v-btn
            class="ma-2"
            color="warning"
            prepend-icon="mdi-logout-variant"
            rounded="lg"
            variant="tonal"
            @click.stop="
            () => {
              resetTokenDialog = true;
            }
          ">
          Reset Token
        </v-btn>
        <div class="text-center">
          <v-dialog v-model="DialogueCP" :fullscreen="mobile">
            <v-card class="blur-effect account_pane">
              <v-card-text>
                <h1 class="mb-3">Change Password</h1>
                <v-sheet class="mx-auto form_container" width="auto">
                  <v-form fast-fail @submit.prevent>
                    <v-text-field v-model="Old_password" label="Old Password" prepend-inner-icon="mdi-lock"
                                  type="password"></v-text-field>
                    <v-text-field v-model="New_password" label="New Password" prepend-inner-icon="mdi-lock"
                                  type="password"></v-text-field>
                    <v-text-field v-model="confirm_new_password" :rules="[passwordValidation]"
                                  label="Confirm New Password" prepend-inner-icon="mdi-lock-check"
                                  type="password"></v-text-field>
                  </v-form>
                </v-sheet>
              </v-card-text>
              <v-card-actions>
                <v-btn
                    class="mt-2 bg-blue-darken-1"
                    type="submit"
                    @click="
                    () => {
                      updateUserPassword(Old_password, New_password);
                    }
                  ">
                  Submit
                </v-btn>
                <v-btn color="primary" @click="DialogueCP = false">Cancel</v-btn>
              </v-card-actions>
            </v-card>
          </v-dialog>
        </div>
      </div>
    </div>
  </v-main>
</template>
