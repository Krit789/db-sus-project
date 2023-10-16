<script lang="ts" setup>
import {useDisplay} from 'vuetify';
import '~/assets/stylesheets/navbar.css';
import '~/assets/stylesheets/global.css';

const {mobile} = useDisplay();
const {status, data, signIn, signOut} = useAuth();
const route = useRoute();
const mySignInHandler = async ({email, password}: {
  email: string;
  password: string
}) => {
  const {error} = await signIn('credentials', {
    email,
    password,
    redirect: false,
    callbackUrl: '/',
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
interface User {
  id: number;
  first_name: string;
  last_name: string;
  email: string;
  telephone: string | null;
  points: number;
}

export default {
  data: () => ({
    first_name: '',
    last_name: '',
    phone: '',
    email: '',
    emailReg: '',
    password: '',
    passwordReg: '',
    passwordRegConfirm: '',
    dialogIn: false,
    dialogRe: false,
    drawer: false,
    group: null,
    snackbar: false,
    NotiText: '',
    NotiColor: '',
    NotiIcon: '',
    timeout: 2000,
    isCardLoading: false,
    accountData: {
      id: 0,
      first_name: 'FN',
      last_name: 'LN',
      email: 'email',
      telephone: '',
      points: 0,
    } as User,
    items: [
      {
        title: 'Home',
        permitted: ['USER', 'MANAGER', 'GOD'],
        value: 'home',
        action: 'u-home',
        props: {
          prependIcon: 'mdi-home',
        },
      },
      {
        title: 'Reservation',
        permitted: ['USER'],
        value: 'booking',
        action: 'u-booking',
        props: {
          prependIcon: 'mdi-book-plus-multiple',
        },
      },
      {
        title: 'My Reservation',
        permitted: ['USER'],
        value: 'status',
        action: 'u-status',
        props: {
          prependIcon: 'mdi-clipboard-text-clock',
        },
      },
      {
        title: 'Report',
        permitted: ['MANAGER', 'GOD'],
        value: 'report',
        action: 'u-report',
        props: {
          prependIcon: 'mdi-chart-line',
        },
      },
    ],
    management: [
      {
        title: 'Branches',
        permitted: ['MANAGER', 'GOD'],
        value: 'mbranch',
        action: 'u-mbranch',
        props: {
          prependIcon: 'mdi-store-marker',
        },
      },
      {
        title: 'Reservations',
        permitted: ['MANAGER', 'GOD'],
        value: 'mbooking',
        action: 'u-mbooking',
        props: {
          prependIcon: 'mdi-book-multiple',
        },
      },
      {
        title: 'Menus',
        permitted: ['GOD'],
        value: 'mmenu',
        action: 'u-menu',
        props: {
          prependIcon: 'mdi-food',
        },
      },
      {
        title: 'Users',
        permitted: ['GOD'],
        value: 'muser',
        action: 'u-muser',
        props: {
          prependIcon: 'mdi-account',
        },
      },
    ],
  }),
  methods: {
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
          })
          .then((response) => {
            const {status, message} = response as {
              status: number;
              message: User;
            };
            this.accountData = message;
          });
    },
    passwordValidation(value: String) {
      if (this.passwordReg === value) return true;
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
    navActions: function (actions: String) {
      this.drawer = false;
      switch (actions) {
        case 'u-home':
          this.$router.push('/');
          break;
        case 'u-booking':
          this.$router.push('/reservation');
          break;
        case 'u-status':
          this.$router.push('/dashboard');
          break;
        case 'u-report':
          this.$router.push('/report');
          break;
        case 'u-mbooking':
          this.$router.push('/management/reservation');
          break;
        case 'u-mbranch':
          this.$router.push('/management/branches');
          break;
        case 'u-muser':
          this.$router.push('/management/users');
          break;
        case 'u-menu':
          this.$router.push('/management/menus');
          break;
      }
      this.drawer = false;
    },
    makeRegistration: async function () {
      this.isCardLoading = true;
      await $fetch('/proxy/api/account/create-user.php', {
        method: 'POST',
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
            const {message, status} = response as {
              status: number;
              message: any;
            };
            if (status === 1) {
              this.NotiText = 'Registration Successful. Login with your account to begin!';
              this.NotiColor = 'success';
              this.NotiIcon = 'mdi-check-circle-outline';
              this.snackbar = true;
              this.dialogRe = false;
            } else if (status === 2) {
              this.NotiText = 'Email already in use!';
              this.NotiColor = 'error';
              this.NotiIcon = 'mdi-alert';
              this.snackbar = true;
            } else {
              this.NotiText = message;
              this.NotiColor = 'error';
              this.NotiIcon = 'mdi-alert';
              this.snackbar = true;
            }
            this.isCardLoading = false;
          });
    },
  },
  computed: {
    isLoginValid: function () {
      return this.emailValidation(this.email) && this.password != '';
    },
    isRegisValid() {
      return this.emailValidation(this.emailReg) && this.passwordValidation(this.passwordRegConfirm) && this.first_name != '' && this.last_name != '' && this.emailReg != '' && this.passwordRegConfirm != '';
    },
  },
  watch: {
    group() {
      this.drawer = false;
    },
  },
  beforeMount() {
    this.loadAccountData();
  },
};
</script>

<template>
  <v-card>
    <v-layout>
      <v-app-bar class="blur-effect nav_bar" elevation="8" prominent>
        <v-snackbar v-model="snackbar" :color="NotiColor" :timeout="timeout" location="top">
          <v-icon :icon="NotiIcon" start></v-icon>
          {{ NotiText }}
        </v-snackbar>
        <v-app-bar-nav-icon variant="text" @click.stop="drawer = !drawer"></v-app-bar-nav-icon>
        <v-toolbar-title
            @click="
            () => {
              $router.push('/');
            }
          ">
          <NuxtLink :custom="true" to="/">Seatify | Seat Reservation Service</NuxtLink>
        </v-toolbar-title>
        <div v-if="status == 'unauthenticated' && !mobile">
          <v-btn
              color="blue"
              variant="text"
              @click="
              () => {
                dialogRe = true;
              }
            ">
            Register
          </v-btn>
          <v-btn
              background-color="#D9D9D9"
              @click="
              () => {
                dialogIn = true;
              }
            ">
            Login
          </v-btn>
        </div>
        <div v-else-if="status == 'authenticated' && !mobile">
          <NuxtLink :custom="true" to="/account">
            <v-btn
                variant="text"
                @click="
                () => {
                  $router.push('/account');
                }
              ">
              <v-icon class="mr-1" size="x-large">mdi-account-circle-outline</v-icon>
              <p>
                {{ `${accountData.first_name} ${accountData.last_name.charAt(0)}.` }}
              </p>
            </v-btn>
          </NuxtLink>
          <v-btn
              color="primary"
              variant="text"
              @click="
              signOut({ callbackUrl: '/', redirect: false }).then(() => {
                $router.push('/');
                NotiText = 'You have been logged out';
                NotiColor = 'info';
                NotiIcon = 'mdi-check-circle-outline';
                snackbar = true;
              })
            ">
            Logout
          </v-btn>
        </div>
      </v-app-bar>
      <v-navigation-drawer v-model="drawer" disable-resize-watcher disable-route-watcher temporary>
        <div v-if="status == 'authenticated'">
          <v-list>
            <v-list-item>
              <v-list-item-title>
                {{ accountData.first_name + ' ' + accountData.last_name }}
              </v-list-item-title>
              <v-list-item-subtitle>
                {{ accountData.email }}
              </v-list-item-subtitle>
              <template v-slot:append>
                <v-tooltip text="Account Settings">
                  <template v-slot:activator="{ props }">
                    <v-btn
                        color="grey"
                        icon="mdi-cog"
                        size="small"
                        v-bind="props"
                        variant="text"
                        @click="
                        () => {
                          $router.push('/account');
                        }
                      "></v-btn>
                  </template>
                </v-tooltip>
              </template>
            </v-list-item>
            <v-list-item v-if="data.role === 'USER'" height="auto">
              <v-list-item-subtitle>
                <v-icon>mdi-circle-multiple</v-icon>
                {{ accountData.points }} points
              </v-list-item-subtitle>
            </v-list-item>
          </v-list>
          <v-divider></v-divider>
          <v-list>
            <div v-for="(item, index) in items" :key="index">
              <v-list-item v-if="item.permitted.includes(data.role)" :prepend-icon="item.props.prependIcon" rounded="xl"
                           @click="navActions(item.action)">
                <v-list-item-title>{{ item.title }}</v-list-item-title>
              </v-list-item>
            </div>
            <v-list-group v-if="data.role == 'MANAGER' || data.role == 'GOD'">
              <template v-slot:activator="{ props }">
                <v-list-item color="primary" prepend-icon="mdi-tools" rounded="xl" v-bind="props">Management
                </v-list-item>
              </template>
              <template v-for="(item, index) in management" :key="index">
                <v-list-item v-if="item.permitted.includes(data.role)" :prepend-icon="item.props.prependIcon"
                             rounded="xl" @click="navActions(item.action)">
                  <v-list-item-title>{{ item.title }}</v-list-item-title>
                </v-list-item>
              </template>
            </v-list-group>
          </v-list>
          <v-divider></v-divider>
          <v-list>
            <v-list-item
                base-color="red"
                prepend-icon="mdi-logout"
                rounded="xl"
                title="Logout"
                value="signout"
                @click="
                signOut({
                  callbackUrl: '/',
                  redirect: false,
                }).then(() => {
                  $router.push('/');
                  NotiText = 'You have been logged out';
                  NotiColor = 'info';
                  NotiIcon = 'mdi-check-circle-outline';
                  snackbar = true;
                })
              "></v-list-item>
          </v-list>
        </div>
        <div v-else>
          <v-list>
            <v-list-item>
              <v-list-item-title>Guest</v-list-item-title>
              <v-list-item-subtitle class="pb-1">Sign In to Continue</v-list-item-subtitle>
            </v-list-item>
          </v-list>
          <v-divider></v-divider>
          <v-list>
            <v-list-item
                prepend-icon="mdi-login-variant"
                @click="
                () => {
                  dialogIn = true;
                }
              ">
              <v-list-item-title>Login</v-list-item-title>
            </v-list-item>
            <v-list-item
                prepend-icon="mdi-account-plus"
                @click="
                () => {
                  dialogRe = true;
                }
              ">
              <v-list-item-title>Register</v-list-item-title>
            </v-list-item>
          </v-list>
          <v-divider></v-divider>
        </div>
      </v-navigation-drawer>
      <div class="text-center">
        <v-dialog v-model="dialogIn" :fullscreen="mobile" :width="mobile ? '100%' : '700px'">
          <v-card :loading="isCardLoading ? 'blue' : undefined" :width="mobile ? '100%' : '700px'"
                  class="blur-effect account_pane">
            <v-form class="justify-center" fast-fail @submit.prevent>
              <v-card-title class="mt-4 ml-4 pb-3">
                <h1>Login</h1>
              </v-card-title>
              <v-card-subtitle class="ml-4 pb-1">
                <h4 class="font-weight-medium">The best reservation experience is just a click away!</h4>
              </v-card-subtitle>
              <v-card-text>
                <v-sheet class="mx-auto form_container bg-transparent" width="auto">
                  <v-text-field v-model="email" :rules="[emailValidation]" label="E-Mail"
                                prepend-inner-icon="mdi-email"></v-text-field>
                  <v-text-field v-model="password" label="Password" prepend-inner-icon="mdi-lock"
                                type="password"></v-text-field>
                  <p>
                    Don't have a account?
                    <a
                        class="like-a-link"
                        @click="
                        () => {
                          dialogIn = false;
                          dialogRe = true;
                        }
                      ">
                      Register Here
                    </a>
                  </p>
                </v-sheet>
              </v-card-text>
              <v-card-actions class="ml-3 mb-3">
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
                          dialogIn = false;
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
                  ">
                  Submit
                </v-btn>
                <v-btn :variant="'plain'" class="mt-2 cancel_button" color="primary" rounded="lg"
                       @click="dialogIn = false">Cancel
                </v-btn>
              </v-card-actions>
            </v-form>
          </v-card>
        </v-dialog>
      </div>
      <v-dialog v-model="dialogRe" :fullscreen="mobile" :width="mobile ? '100%' : '700px'" activator="#regisActivator">
        <v-card :loading="isCardLoading ? 'blue' : undefined" :width="mobile ? '100%' : '700px'"
                class="blur-effect account_pane">
          <v-form fast-fail @submit.prevent>
            <v-card-title class="mt-4 ml-4 pb-3">
              <h1>Register</h1>
            </v-card-title>
            <v-card-subtitle class="ml-4 pb-1">
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
                    <v-text-field v-model="passwordRegConfirm" :rules="[passwordValidation]" label="Confirm Password *"
                                  prepend-inner-icon="mdi-lock-check" type="password"></v-text-field>
                  </v-col>
                </v-row>
              </v-sheet>
              <p>
                Already have a account?
                <a
                    class="like-a-link"
                    @click="
                    () => {
                      dialogIn = true;
                      dialogRe = false;
                    }
                  ">
                  Login Here
                </a>
              </p>
            </v-card-text>
            <v-card-actions class="ml-3 mb-3">
              <v-btn :disabled="!isRegisValid" class="mt-2 bg-blue-darken-1 h-[22px] mw-50" rounded="lg" type="submit"
                     @click="makeRegistration">Submit
              </v-btn>
              <v-btn :variant="'plain'" class="mt-2 cancel_button" color="primary" rounded="lg"
                     @click="dialogRe = false">Cancel
              </v-btn>
            </v-card-actions>
          </v-form>
        </v-card>
      </v-dialog>
      <slot/>
    </v-layout>
  </v-card>
</template>
<style scoped>
.like-a-link {
  cursor: pointer;
}

.like-a-link:hover {
  cursor: pointer;
  text-decoration: underline;
  color: #0373de;
}
</style>
