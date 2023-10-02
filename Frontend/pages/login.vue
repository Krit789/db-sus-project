<script lang="ts" setup>

import "~/assets/stylesheets/global.css";
import "~/assets/stylesheets/index.css";

import {useDisplay} from "vuetify";
const {mobile} = useDisplay();
definePageMeta({auth: false});
const {status, data, signIn} = useAuth();
const route = useRoute();
const mySignInHandler = async ({email, password}: { email: string; password: string }) => {
  const {error} = await signIn("credentials", {
    email,
    password,
    redirect: false,
    callbackUrl: "/",
  });
  if (error) {
    // Do your custom error handling here
    return false;
  } else {
    // No error, continue with the sign in, e.g., by following the returned redirect:
    return true;
  }
};
</script>

<script lang="ts">
export default {
  data: () => ({
    email: "",
    password: "",
    snackbar: false,
    NotiText: "",
    NotiColor: "",
    NotiIcon: "",
    isCardLoading: false,
  }),
  methods: {
    emailValidation(value: String) {
      if (
          String(value)
              .toLowerCase()
              .match(/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|.(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/)
      )
        return true;

      return "E-Mail must be in correct format.";
    },
  },
  computed: {
    isLoginValid: function () {
      return this.emailValidation(this.email) && this.password != "";
    },
  },
}
</script>

<template>
    <v-main class="mt-5">
            <v-form class="justify-center" fast-fail @submit.prevent>
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
                       @click="$router.push('/');">Back
                </v-btn>
              </v-card-actions>
            </v-form>
    </v-main>
</template>
