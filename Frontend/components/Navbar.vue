<script lang="ts" setup>
import {useDisplay} from "vuetify";

const {mobile} = useDisplay();
const {status, data, signIn, signOut} = useAuth();
const route = useRoute();
const mySignInHandler = async ({
                                 email,
                                 password,
                               }: {
  email: string;
  password: string;
}) => {
  const {error, url} = await signIn("credentials", {
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
    return navigateTo(url, {external: true});
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
      switch (actions) {
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
      <v-app-bar class="blur-effect" elevation="8" prominent>
        <v-app-bar-nav-icon variant="text"
                            @click.stop="drawer = !drawer"></v-app-bar-nav-icon>
        <v-toolbar-title><NuxtLink to="/" :custom="true">Seatify | Seat Reservation
          Service</NuxtLink></v-toolbar-title>
        <div v-if="status == 'unauthenticated' && !mobile">
          <v-btn color="blue" variant="text" @click="dialogRe = true">Register</v-btn>
          <v-btn background-color="#D9D9D9" @click="dialogIn = true">Login</v-btn>
        </div>
        <div v-else-if="status == 'authenticated' && !mobile">
          <NuxtLink to="/account" :custom="true">
          <v-btn variant="text">
            <p>
              {{ data.firstName }}
            </p>
          </v-btn>
        </NuxtLink>
          <v-btn color="blue" variant="text" @click="signOut()">Sign Out</v-btn>
        </div>
      </v-app-bar>
      <v-navigation-drawer v-model="drawer" location="left" temporary>
        <div v-if="status == 'authenticated'">
          <v-list>
            <v-list-item
                :subtitle="data.email"
                :title="data.firstName + ' ' + data.lastName"
            >
              <template v-slot:append>
                <v-btn
                    color="grey"
                    icon="mdi-cog"
                    size="small"
                    variant="text"
                    @click="() => {$router.push('/account')}"
                ></v-btn>
              </template>
            </v-list-item>
          </v-list>
          <v-divider></v-divider>
          <v-list>
            <v-list-item v-for="(item, index) in items"
                         :key="index" :prepend-icon="item.props.prependIcon" @click="navActions(item.action)">
              <v-list-item-title>{{ item.title }}</v-list-item-title>
            </v-list-item>
          </v-list>
          <v-divider></v-divider>
          <v-list>
            <v-list-item base-color="red" prepend-icon="mdi-logout" title="Sign Out" value="signout"
                         @click="signOut()"></v-list-item>
          </v-list>
        </div>
        <div v-else>
          <v-list>
            <v-list-item
                subtitle="Sign In to Continue"
                title="Guest"
            >
            </v-list-item>
          </v-list>
          <v-divider></v-divider>
          <v-list>
            <v-list-item id="loginActivator" prepend-icon="mdi-login-variant" @click="">
              <v-list-item-title>Login</v-list-item-title>
            </v-list-item>
            <v-list-item id="regisActivator" prepend-icon="mdi-account-plus" @click="">
              <v-list-item-title>Register</v-list-item-title>
            </v-list-item>
          </v-list>
          <v-divider></v-divider>
        </div>
      </v-navigation-drawer>
      <div class="text-center">
        <v-dialog v-model="dialogIn" :fullscreen="mobile" activator="#loginActivator">
          <v-card class="blur-effect account_pane">
            <v-card-text>
              <h1 class="mb-3">Sign In</h1>
              <v-sheet class="mx-auto form_container" width="auto">
                <v-form fast-fail @submit.prevent>
                  <v-text-field v-model="email" :rules="[emailValidation]" label="E-Mail"
                                prepend-inner-icon="mdi-email"></v-text-field>
                  <v-text-field v-model="password" label="Password" prepend-inner-icon="mdi-lock"
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
        <v-card class="blur-effect account_pane">
          <v-card-text>
            <h1 class="mb-3">Register</h1>
            <v-sheet class="mx-auto w-100 form_container" width="auto">
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
                    <v-text-field v-model="emailReg" :rules="[emailValidation]" label="E-Mail"
                                  prepend-inner-icon="mdi-email"></v-text-field>
                  </v-col>
                  <v-col cols="12" sm="4">
                    <v-text-field v-model="phone" label="Phone Number" prepend-inner-icon="mdi-phone"></v-text-field>
                  </v-col>
                </v-row>
                <v-row>
                  <v-col cols="12" sm="4">
                    <v-text-field v-model="passwordReg" label="Password" prepend-inner-icon="mdi-lock"
                                  type="password"></v-text-field>
                  </v-col>
                  <v-col cols="12" sm="4">
                    <v-text-field v-model="passwordRegConfirm" :rules="[passwordValidation]"
                                  label="Confirm Password" prepend-inner-icon="mdi-lock-check"
                                  type="password"></v-text-field>
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
      <slot/>
    </v-layout>
  </v-card>
</template>

<style scoped>
.blur-effect {
  background: rgba(245, 245, 247, .72) !important;
  backdrop-filter: blur(20px) saturate(1.8);
  -webkit-backdrop-filter: blur(20px) saturate(85px);
  -webkit-backdrop-filter: blur(20px) saturate(85px);
  -webkit-backdrop-filter: blur(15px) saturate(86%);
  backdrop-filter: blur(20px) saturate(1.8);
  -webkit-backface-visibility: hidden;
  backface-visibility: hidden;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}

.form_container {
  background-color: rgba(0, 0, 0, 0);
}

.account_pane {
  background: rgba(255, 255, 255, .86) !important;
}

@media screen and (max-width: 600px) {
  .account_pane {
    background: rgba(242, 241, 244, 0.95) !important;

  }
}

</style>
