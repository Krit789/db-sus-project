<script setup lang="ts">
definePageMeta({ auth: false })
const {
  status,
  data,
  signIn,
  signOut,
  getCsrfToken
} = useAuth()
// console.log(getProviders())

</script>

<script lang="ts">
export default {
  data: () => ({
    email: '',
    password: '',
    dialog: false,
    drawer: false,
    group: null,
    items: [
      {
        title: 'Koo',
        value: 'koo',
      },
      {
        title: 'Bar',
        value: 'bar',
      },
      {
        title: 'Fizz',
        value: 'fizz',
      },
      {
        title: 'Buzz',
        value: 'buzz',
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
        <v-app-bar-nav-icon variant="text" @click.stop="drawer = !drawer"></v-app-bar-nav-icon>

        <v-toolbar-title><a v-on:click="() => $router.push({ name: 'index' })">SUS App</a></v-toolbar-title>

        <v-spacer></v-spacer>

        <v-btn icon="mdi-magnify" variant="text"></v-btn>

        <v-btn icon="mdi-filter" variant="text"></v-btn>

        <v-btn icon="mdi-dots-vertical" variant="text"></v-btn>
        <div v-if="status == 'unauthenticated'">
          <v-btn color="blue" variant="text">Register</v-btn>
          <v-btn background-color="#D9D9D9" @click="dialog = true">Login</v-btn>
        </div>
        <div v-else-if="status == 'authenticated'">
          <v-btn variant="text">
            <p v-on:click="() => $router.push( {name: 'account'} )">{{ data.firstName }}</p>
          </v-btn>
          <v-btn color="blue" @click="signOut()" variant="text">Sign Out</v-btn>
        </div>
      </v-app-bar>

      <v-navigation-drawer v-model="drawer" location="left" temporary>
        <v-list :items="items"></v-list>
      </v-navigation-drawer>

      <v-main color="#D9D9D9" style="height: 600px;">
        <div class="text-center " style="color:red;">
          <v-btn color="Blue" rounded>
            Book Now!
          </v-btn>
          <div class="text-center">
            <v-dialog v-model="dialog" width="auto">
              <v-card>
                <v-card-text>
                  <h1>Sign In</h1>
                  <v-sheet width="300" class="mx-auto">
                    <v-form @submit.prevent>
                      <v-text-field v-model="email" label="E-Mail"></v-text-field>
                      <v-text-field v-model="password" type="password" label="Password"></v-text-field>
                      <v-btn type="submit" block class="mt-2" @click="signIn('credentials', { email: email, password: password, callbackUrl: '/' })">Submit</v-btn>
                    </v-form>
                  </v-sheet>
                </v-card-text>
                <v-card-actions>
                  <v-btn color="primary" block @click="dialog = false">Cancel</v-btn>
                </v-card-actions>
              </v-card>
            </v-dialog>
          </div>
        </div>
      </v-main>
    </v-layout>
  </v-card>
</template>
