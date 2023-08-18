<script lang="ts" setup>
const {
  status,
  data,
  signIn,
  signOut,
} = useAuth()
</script>

<script lang="ts">
export default {
  data: () => ({
    first_name: '',
    last_name: '',
    email: '',
    password: '',
    confirm_password: '',
    dialogIn: false,
    dialogRe: false,
    drawer: false,
    group: null,
    items: [
      {
        title: 'Home',
        value: 'home',
      },
      {
        title: 'Booking',
        value: 'booking',
      },
      {
        title: 'Status',
        value: 'status',
      },
      {
        title: 'Setting',
        value: 'setting',
      },
    ],
  }),

  watch: {
    group() {
      this.drawer = false
    },
  },
}
</script>

<template>
  <v-card>
    <v-layout>
      <v-app-bar color="#F1F1F1" elevation="8" prominent>
        <v-app-bar-nav-icon v-if="status == 'authenticated'" variant="text" @click.stop="drawer = !drawer"></v-app-bar-nav-icon>

        <v-toolbar-title><a v-on:click="() => $router.push({ name: 'index' })">Seatify | Seat Reservation Service</a></v-toolbar-title>

        <v-spacer></v-spacer>
        <!-- 
        <v-btn icon="mdi-magnify" variant="text"></v-btn>

        <v-btn icon="mdi-filter" variant="text"></v-btn>

        <v-btn icon="mdi-dots-vertical" variant="text"></v-btn> -->
        <div v-if="status == 'unauthenticated'">
          <v-btn color="blue" variant="text" @click="dialogRe = true">Register</v-btn>
          <v-btn background-color="#D9D9D9" @click="dialogIn = true">Login</v-btn>
        </div>
        <div v-else-if="status == 'authenticated'">
          <v-btn variant="text">
            <p v-on:click="() => $router.push({ name: 'account' })">{{ data.firstName }}</p>
          </v-btn>
          <v-btn color="blue" variant="text" @click="signOut()">Sign Out</v-btn>
        </div>
      </v-app-bar>

      <v-navigation-drawer v-model="drawer" location="left" temporary>
        <v-list :items="items"></v-list>
      </v-navigation-drawer>

      <!-- <v-main color="#D9D9D9" style="height: 1100px;"> under construction-->
        <div class="text-center">
          <v-dialog v-model="dialogIn" width="auto">
            <v-card>
              <v-card-text>
                <h1>Sign In</h1>
                <v-sheet class="mx-auto" width="300">
                  <v-form @submit.prevent>
                    <v-text-field v-model="email" label="E-Mail"></v-text-field>
                    <v-text-field v-model="password" label="Password" type="password"></v-text-field>
                    <v-btn block class="mt-2 bg-blue-darken-1" type="submit"
                           @click="signIn('credentials', { email: email, password: password, callbackUrl: '/' })">Submit
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
        <v-dialog v-model="dialogRe" width="auto">
            <v-card>
              <v-card-text>
                <h1>Register</h1>
                <v-sheet class="mx-auto" width="600">
                  <v-form @submit.prevent>
                    <v-row>
                      <v-col>
                        <v-text-field v-model="first_name" label="First Name"></v-text-field>
                      </v-col>
                      <v-col>
                        <v-text-field v-model="last_name" label="Last Name"></v-text-field>
                      </v-col>
                      <v-responsive width="100%"></v-responsive>
                      <v-col>
                        <v-text-field v-model="email" label="Email"></v-text-field>
                      </v-col>
                      <v-responsive width="100%"></v-responsive>
                      <v-col>
                        <v-text-field v-model="password" label="Password" type="password"></v-text-field>
                      </v-col>
                      <v-col>
                        <v-text-field v-model="confirm_password" label="Confirm Password" type="password"></v-text-field>
                      </v-col>
                  </v-row>
                    <!-- <v-btn block class="mt-2 bg-blue-darken-1" type="submit"
                           @click="signIn('credentials', { email: email, password: password, callbackUrl: '/' })">Submit -->
                    <v-btn block class="mt-2 bg-blue-darken-1">
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
      <!-- </v-main> -->
      
      <slot/>
    </v-layout>
  </v-card>
</template>
