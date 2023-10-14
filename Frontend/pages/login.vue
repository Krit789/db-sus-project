<script lang="ts" setup>
import "~/assets/stylesheets/global.css";
import "~/assets/stylesheets/index.css";

import {useDisplay} from "vuetify";

const {mobile} = useDisplay();
definePageMeta({
  auth: {
    unauthenticatedOnly: true,
    navigateAuthenticatedTo: "/",
  },
});
useHead({
  title: "Seatify: Authentication Required",
  meta: [
    {
      name: "Seatify: Seat Reservation Service",
      content: "Seatify, The Seat Reservation Service that makes the difference.",
    },
  ],
});
const {status, data, signIn} = useAuth();
const mySignInHandler = async ({email, password}: {
  email: string;
  password: string
}) => {
  const {error} = await signIn("credentials", {
    email,
    password,
    redirect: true,
    callbackUrl: "/",
  });
  return !error;
};
</script>

<script lang="ts">
export default {
  data: () => ({
    first_name: "",
    last_name: "",
    phone: "",
    emailReg: "",
    passwordReg: "",
    passwordRegConfirm: "",
    email: "",
    password: "",
    snackbar: false,
    NotiText: "",
    NotiColor: "",
    NotiIcon: "",
    wantLogin: true,
    isCardLoading: false,
  }),
  methods: {
    passwordValidation(value: String) {
      if (this.passwordReg === value) return true;
      return "Both passwords must be similar.";
    },
    emailValidation(value: String) {
      if (
          String(value)
              .toLowerCase()
              .match(/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|.(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/)
      )
        return true;

      return "E-Mail must be in correct format.";
    },
    makeRegistration: async function () {
      this.isCardLoading = true;
      await $fetch("/proxy/api/account/create-user.php", {
        method: "POST",
        body: {
          fn: this.first_name,
          ln: this.last_name,
          email: this.emailReg,
          password: this.passwordReg,
          tele: this.phone,
        },
      })
          .catch((error) => error.data)
          .then((response) => {
            const {status, message} = response as {
              status: number;
              message: any
            };
            if (status == 1) {
              this.NotiText = "Registration Successful. Login with your account to begin!";
              this.NotiColor = "success";
              this.NotiIcon = "mdi-check-circle-outline";
              this.snackbar = true;
            } else if (status == 2) {
              this.NotiText = "Email already in use!";
              this.NotiColor = "error";
              this.NotiIcon = "mdi-alert";
              this.snackbar = true;
            } else {
              this.NotiText = message;
              this.NotiColor = "error";
              this.NotiIcon = "mdi-alert";
              this.snackbar = true;
            }
            this.isCardLoading = false;
          });
    },
  },
  computed: {
    isLoginValid: function () {
      return this.emailValidation(this.email) && this.password != "";
    },
    isRegisValid() {
      return this.emailValidation(this.emailReg) && this.passwordValidation(this.passwordRegConfirm) && this.first_name != "" && this.last_name != "" && this.emailReg != "" && this.passwordRegConfirm != "";
    },
  },
};
</script>

<template>
  <v-container class="h-100 justify-center fill-height fluid" fill-height fluid=""
               style="background: #BBDEFB;min-height: 100vh;">
    <v-main class="my-a">
      <v-row class="mb-15" justify="center">
        <v-snackbar v-model=" snackbar" :color="NotiColor" :timeout="2000" location="top">
          <v-icon :icon="NotiIcon" start=""></v-icon>
          {{ NotiText }}
        </v-snackbar>
        <v-card v-if="wantLogin" :loading="isCardLoading ? 'blue' : undefined"
                :width="mobile ? '100%' : '700px'" class="blur-effect account_pane my-2">
          <v-form class="justify-center" fast-fail="" @submit.prevent>
            <v-card-title class="mt-4 ml-4 pb-3 text-center">
              <h1>Login</h1>
            </v-card-title>
            <v-card-subtitle class="ml-4 pb-1 text-center">
              <h4 class="font-weight-medium">The best reservation experience is just a click away!</h4>
            </v-card-subtitle>
            <v-card-text>
              <v-sheet class="mx-auto form_container bg-transparent" width="auto">
                <v-text-field v-model="email" :rules="[emailValidation]" label="E-Mail"
                              prepend-inner-icon="mdi-email"></v-text-field>
                <v-text-field v-model="password" label="Password" prepend-inner-icon="mdi-lock"
                              type="password"></v-text-field>
              </v-sheet>
              <p>
                Don't have a account?
                <a
                    class="like-a-link"
                    @click="
                () => {
                  wantLogin = false;
                }
              "
                >
                  Register Here
                </a>
              </p>
            </v-card-text>
            <v-card-actions class="ml-3 mb-3 justify-center">
              <v-btn
                  :disabled="!isLoginValid"
                  class="mt-2 bg-blue-darken-1 h-[22px] mw-50"
                  rounded="lg"
                  type="submit"
                  @click="
              () => {
                isCardLoading = true;
                mySignInHandler({
                  email: email,
                  password: password,
                }).then((val) => {
                  if (val) {
                    NotiText = 'Sign In Success!';
                    NotiColor = 'success';
                    NotiIcon = 'mdi-check-circle-outline';
                    snackbar = true;
                  } else {
                    NotiText = 'Sign In Failure!';
                    NotiColor = 'error';
                    NotiIcon = 'mdi-alert-circle';
                    snackbar = true;
                  }
                  isCardLoading = false;
                });
              }
            "
              >
                Submit
              </v-btn>
              <v-btn :variant="'plain'" class="mt-2 cancel_button" color="primary" rounded="lg"
                     @click="$router.push('/')">Back
              </v-btn>
            </v-card-actions>
          </v-form>
        </v-card>
        <v-card v-else :loading="isCardLoading ? 'blue' : undefined"
                :width="mobile ? '100%' : '700px'" class="blur-effect account_pane my-2">
          <v-form fast-fail="" @submit.prevent>
            <v-card-title class="mt-4 ml-4 pb-3 text-center">
              <h1>Register</h1>
            </v-card-title>
            <v-card-subtitle class="ml-4 pb-1 text-center">
              <h4 class="font-weight-medium">Get ready to enjoy the best reservation experience!</h4>
            </v-card-subtitle>
            <v-card-text>
              <v-sheet class="mx-auto w-100 form_container bg-transparent">
                <v-row>
                  <v-col cols="12" sm="6">
                    <v-text-field v-model="first_name" label="First Name *"></v-text-field>
                  </v-col>
                  <v-col cols="12" sm="6">
                    <v-text-field v-model="last_name" label="Last Name *"></v-text-field>
                  </v-col>
                </v-row>
                <v-row>
                  <v-col cols="12" sm="6">
                    <v-text-field v-model="emailReg" :rules="[emailValidation]" label="E-Mail *"
                                  prepend-inner-icon="mdi-email"></v-text-field>
                  </v-col>
                  <v-col cols="12" sm="6">
                    <v-text-field v-model="phone" label="Phone Number" prepend-inner-icon="mdi-phone"></v-text-field>
                  </v-col>
                </v-row>
                <v-row>
                  <v-col cols="12" sm="6">
                    <v-text-field v-model="passwordReg" label="Password *" prepend-inner-icon="mdi-lock"
                                  type="password"></v-text-field>
                  </v-col>
                  <v-col cols="12" sm="6">
                    <v-text-field v-model="passwordRegConfirm" :rules="[passwordValidation]"
                                  label="Confirm Password *" prepend-inner-icon="mdi-lock-check"
                                  type="password"></v-text-field>
                  </v-col>
                </v-row>
              </v-sheet>
              <p>
                Already have a account?
                <a
                    class="like-a-link"
                    @click="
                () => {
                  wantLogin = true;
                }
              "
                >
                  Login Here
                </a>
              </p>
            </v-card-text>
            <v-card-actions class="ml-3 mb-3">
              <v-btn :disabled="!isRegisValid" class="mt-2 bg-blue-darken-1 h-[22px] mw-50" rounded="lg" type="submit"
                     @click="makeRegistration">Submit
              </v-btn>
            </v-card-actions>
          </v-form>
        </v-card>
      </v-row>
    </v-main>

  </v-container>
</template>
