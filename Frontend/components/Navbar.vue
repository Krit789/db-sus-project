<script lang="ts" setup>
import { useDisplay } from "vuetify";

const { mobile } = useDisplay();
const { status, data, signIn, signOut } = useAuth();
const route = useRoute();
const mySignInHandler = async ({
  email,
  password,
}: {
  email: string;
  password: string;
}) => {
  const { error, url } = await signIn("credentials", {
    email,
    password,
    redirect: false,
    callbackUrl: route.path,
  });
  if (error) {
    // Do your custom error handling here
    alert(error);
  } else {
    // No error, continue with the sign in, e.g., by following the returned redirect:
    return navigateTo(url, { external: true });
  }
};
</script>

<script lang="ts">
export default {
  data: () => ({
    first_name: "",
    last_name: "",
    phone: "",
    email: "",
    emailReg: "",
    password: "",
    passwordReg: "",
    passwordRegConfirm: "",
    dialogIn: false,
    dialogRe: false,
    drawer: false,
    group: null,
    items: [
      {
        title: "Home",
        value: "home",
        action: "u-home",
        props: {
            prependIcon: 'mdi-home',
          }
      },
      {
        title: "Booking",
        value: "booking",
        action: "u-booking",
        props: {
            prependIcon: 'mdi-book-plus-multiple',
          }
      },
      {
        title: "Status",
        value: "status",
        action: "u-status",
        props: {
            prependIcon: 'mdi-list-status',
          }
      },
    ],
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
          .match(
            /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|.(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
          )
      )
        return true;

      return "E-Mail must be in correct format.";
    },
    navActions(actions: String) {
      switch (actions){
        case "u-home":
          this.$router.push("/");
          break;
        case "u-booking":
        this.$router.push("/booking");
          break;
        case "u-status":
        // this.$router.push("");
          break;
      }
    },
  },
  computed: {
    isLoginValid() {
      return this.emailValidation(this.email) && this.password != "";
    },
    isRegisValid() {
      return (
        this.emailValidation(this.emailReg) &&
        this.passwordValidation(this.passwordRegConfirm) &&
        this.first_name != "" &&
        this.last_name != "" &&
        this.phone != ""
      );
    },
  },
  watch: {
    group() {
      this.drawer = false;
    },
  },
};
</script>

<template>
  <v-card>
    <v-layout>
      <v-app-bar color="#F1F1F1" elevation="8" prominent>
        <v-app-bar-nav-icon v-if="status == 'authenticated'" variant="text"
          @click.stop="drawer = !drawer"></v-app-bar-nav-icon>
        <v-toolbar-title><p>Seatify | Seat Reservation
            Service</p></v-toolbar-title>
        <div v-if="status == 'unauthenticated'">
          <v-btn id="regisActivator" color="blue" variant="text">Register</v-btn>
          <v-btn id="loginActivator" background-color="#D9D9D9">Login</v-btn>
        </div>
        <div v-else-if="status == 'authenticated' && !mobile">
          <v-btn variant="text">
            <p @click="() => $router.push({ name: 'account' })">
              {{ data.firstName }}
            </p>
          </v-btn>
          <v-btn color="blue" variant="text" @click="signOut()">Sign Out</v-btn>
        </div>
      </v-app-bar>
      <v-navigation-drawer v-if="status == 'authenticated'" v-model="drawer" location="left" temporary>
        <v-list>
          <v-list-item
            :title="data.firstName + ' ' + data.lastName"
            :subtitle="data.email"
          >
            <template v-slot:append>
              <v-btn
                size="small"
                variant="text"
                color="grey"
                icon="mdi-cog"
                @click="() => $router.push({ name: 'account' })"
              ></v-btn>
            </template>
          </v-list-item>
          </v-list>
          <v-divider></v-divider>
        <v-list>
          <v-list-item v-for="(item, index) in items"
                  :key="index" @click="navActions(item.action)" :prepend-icon="item.props.prependIcon">
                  <v-list-item-title>{{ item.title }}</v-list-item-title>
                </v-list-item>
        </v-list>
        <v-divider></v-divider>
        <v-list>
          <v-list-item @click="signOut()" prepend-icon="mdi-logout" base-color="red" title="Sign Out" value="signout"></v-list-item>
      </v-list>
      </v-navigation-drawer>
      <div class="text-center">
        <v-dialog v-model="dialogIn" :fullscreen="mobile" activator="#loginActivator">
          <v-card>
            <v-card-text>
              <h1 class="mb-3">Sign In</h1>
              <v-sheet class="mx-auto" width="auto">
                <v-form fast-fail @submit.prevent>
                  <v-text-field prepend-inner-icon="mdi-email" v-model="email" :rules="[emailValidation]"
                    label="E-Mail"></v-text-field>
                  <v-text-field prepend-inner-icon="mdi-lock" v-model="password" label="Password"
                    type="password"></v-text-field>
                  <v-btn :disabled="!isLoginValid" block class="mt-2 bg-blue-darken-1" type="submit" @click="
                    mySignInHandler({ email: email, password: password })
                    ">Submit
                  </v-btn>
                </v-form>
              </v-sheet>
            </v-card-text>
            <v-card-actions>
              <v-btn block color="primary" @click="dialogIn = false">Cancel</v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
      </div>
      <v-dialog v-model="dialogRe" :fullscreen="mobile" activator="#regisActivator">
        <v-card>
          <v-card-text>
            <h1 class="mb-3">Register</h1>
            <v-sheet class="mx-auto w-100" width="auto">
              <v-form fast-fail @submit.prevent>
                <v-row>
                  <v-col cols="12" sm="4">
                    <v-text-field v-model="first_name" label="First Name"></v-text-field>
                  </v-col>
                  <v-col cols="12" sm="4">
                    <v-text-field v-model="last_name" label="Last Name"></v-text-field>
                  </v-col>
                </v-row>
                <v-row>
                  <v-col cols="12" sm="4">
                    <v-text-field prepend-inner-icon="mdi-email" v-model="emailReg" :rules="[emailValidation]"
                      label="E-Mail"></v-text-field>
                  </v-col>
                  <v-col cols="12" sm="4">
                    <v-text-field prepend-inner-icon="mdi-phone" v-model="phone" label="Phone Number"></v-text-field>
                  </v-col>
                </v-row>
                <v-row>
                  <v-col cols="12" sm="4">
                    <v-text-field prepend-inner-icon="mdi-lock" v-model="passwordReg" label="Password"
                      type="password"></v-text-field>
                  </v-col>
                  <v-col cols="12" sm="4">
                    <v-text-field prepend-inner-icon="mdi-lock-check" v-model="passwordRegConfirm"
                      :rules="[passwordValidation]" label="Confirm Password" type="password"></v-text-field>
                  </v-col>
                </v-row>
                <v-btn :disabled="!isRegisValid" block class="mt-2 bg-blue-darken-1">
                  Submit
                </v-btn>
              </v-form>
            </v-sheet>
          </v-card-text>
          <v-card-actions>
            <v-btn block color="primary" @click="dialogRe = false">Cancel</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
      <slot />
    </v-layout>
  </v-card>
</template>
