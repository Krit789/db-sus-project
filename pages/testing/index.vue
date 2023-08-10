
<script>
  export default {
    data: () => ({
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
      group () {
        this.drawer = false
      },
    },
  }
</script>

<template>
  <v-card>
    <v-layout>
      <!-- <v-system-bar color="deep-purple darken-3"></v-system-bar> -->

      <v-app-bar
        color="#F1F1F1"
        prominent
        elevation="8"
      >
        <v-app-bar-nav-icon variant="text" @click.stop="drawer = !drawer"></v-app-bar-nav-icon>

        <v-toolbar-title v-on:click="() => $router.push({ name: 'index' })">SUS App</v-toolbar-title>

        <v-spacer></v-spacer>

        <v-btn variant="text" icon="mdi-magnify"></v-btn>

        <v-btn variant="text" icon="mdi-filter"></v-btn>

        <v-btn variant="text" icon="mdi-dots-vertical"></v-btn>

        <v-btn variant="text" color="blue" >Register</v-btn>
        <v-btn variant="rounded" background-color="#D9D9D9">Login</v-btn>
      </v-app-bar>

      <v-navigation-drawer
        v-model="drawer"
        location="left"
        temporary
      >
        <v-list
          :items="items"
        ></v-list>
      </v-navigation-drawer>

      <v-main style="height: 600px;" color="#D9D9D9">
        <div class="text-center ">
            <v-btn
            rounded
            color="#22A2FF"
            dark
            >
            Book Now!
            </v-btn>
        </div>
      </v-main>
      <!-- <v-main style="height: 500px;">
        <v-card-text>
          The navigation drawer will appear from the bottom on smaller size screens.
        </v-card-text>
        <div>
          <h1>Nuxt Routing set up successfully!</h1>
          <p>Current route: {{ $route.path }}</p>
          <v-btn v-on:click="() => $router.push({ name: 'account' })">
            Button
          </v-btn>
          <v-btn v-on:click="() => $router.push({ name: 'index' })">
            Button
          </v-btn>
          <a href="https://nuxt.com/docs/getting-started/routing" target="_blank">Learn more about Nuxt Routing</a>
        </div>
      </v-main> -->
    </v-layout>
  </v-card>

</template>
