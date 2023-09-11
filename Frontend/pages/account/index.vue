<script lang="ts" setup>
const {
  status,
  data
} = useAuth()

</script>
<script lang="ts">
export default {
  data: () => ({
    DialogueEdt: false,
    editMode: false,
  }),
  methods: {
    
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
  }
}
</script>

<template>
  <Navbar>
    <v-main>
      <div class="text-center ma-lg-16 ma-sm-8 ma-xs-0 my-8" sv-if="status == 'authenticated'">
        <v-card>
          <div v-if="editMode == false">
            <v-card-text class="text-h3 font-weight-bold my-6">Your Account</v-card-text>
            <v-text-field
            class="my-3 mx-15" readonly label="First Name" variant="underlined" :model-value="data.firstName"></v-text-field>
            <v-text-field class="my-3 mx-15" readonly label="Last Name" variant="underlined" :model-value="data.lastName"></v-text-field>
            <v-text-field class="my-3 mx-15" readonly label="Telephone Number" variant="underlined" :model-value="data.tel"></v-text-field>
            <v-text-field class="my-3 mx-15" readonly label="Email" variant="underlined" :model-value="data.email"></v-text-field>
          </div>
          <div v-if="editMode == true">
            <v-card-text class="text-h3 font-weight-bold my-6">Your Account</v-card-text>
            <v-text-field
            class="my-3 mx-15" label="First Name" variant="underlined" :model-value="data.firstName"></v-text-field>
            <v-text-field class="my-3 mx-15" label="Last Name" variant="underlined" :model-value="data.lastName"></v-text-field>
            <v-text-field class="my-3 mx-15" label="Telephone Number" variant="underlined" :model-value="data.tel"></v-text-field>
            <v-text-field class="my-3 mx-15" :rules="[emailValidation]" label="Email" variant="underlined" :model-value="data.email"></v-text-field>
          </div>


          <v-btn
            v-if="editMode == true"
            class="ma-2"
            color="deep-purple-lighten-2"
            variant="outlined"
            @click="editMode = true">
            Change Password
          </v-btn>


          <v-divider class="border-opacity-0"></v-divider>


          <v-btn
            v-if="editMode == false"
            class="ma-2"
            color="deep-purple-lighten-2"
            variant="outlined"
            @click="editMode = true">
            Edit
          </v-btn>

          <!-- vv only appear on edit mode vv -->
          <v-btn
            v-if="editMode == true"
            class="ma-2"
            color="deep-purple-lighten-2"
            variant="outlined"
            @click="">
            Save
          </v-btn>
          
          <v-btn
            v-if="editMode == true"
            class="ma-2"
            color="deep-purple-lighten-2"
            variant="outlined"
            @click="editMode = false">
            Cancle
          </v-btn>
          <!-- ^^ only appear on edit mode ^^ -->

      </v-card>
      </div>
      <!-- <v-btn v-on:click="() => $router.push({ name: 'index' })">
        Back to Index
      </v-btn> -->
      <Credit/>
    </v-main>
  </Navbar>
</template>
