<script lang="ts" setup>
const {
  status,
  data
} = useAuth()

</script>
<script lang="ts">
export default {
  data: () => ({
    DialogueCP: false,
    editMode: false,
    Old_password: "",
    New_password: "",
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
                :model-value="data.firstName" class="my-3 mx-15" label="First Name" readonly
                variant="underlined"></v-text-field>
            <v-text-field :model-value="data.lastName" class="my-3 mx-15" label="Last Name" readonly
                          variant="underlined"></v-text-field>
            <v-text-field :model-value="data.tel" class="my-3 mx-15" label="Telephone Number" readonly
                          variant="underlined"></v-text-field>
            <v-text-field :model-value="data.email" class="my-3 mx-15" label="Email" readonly
                          variant="underlined"></v-text-field>
          </div>
          <div v-if="editMode == true">
            <v-card-text class="text-h3 font-weight-bold my-6">Your Account</v-card-text>
            <v-text-field
                :model-value="data.firstName" class="my-3 mx-15" label="First Name" variant="underlined"></v-text-field>
            <v-text-field :model-value="data.lastName" class="my-3 mx-15" label="Last Name"
                          variant="underlined"></v-text-field>
            <v-text-field :model-value="data.tel" class="my-3 mx-15" label="Telephone Number"
                          variant="underlined"></v-text-field>
            <v-text-field :model-value="data.email" :rules="[emailValidation]" class="my-3 mx-15" label="Email"
                          variant="underlined"></v-text-field>
          </div>


          <v-btn
              v-if="editMode == true"
              class="ma-2"
              color="deep-purple-lighten-2"
              variant="outlined"
              @click="DialogueCP = true">
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
      <div class="text-center">
        <v-dialog v-model="DialogueCP" :fullscreen="mobile" activator="#loginActivator">
          <v-card class="blur-effect account_pane">
            <v-card-text>
              <h1 class="mb-3">Change Password</h1>
              <v-sheet class="mx-auto form_container" width="auto">
                <v-form fast-fail @submit.prevent>
                  <v-text-field v-model="Old_password" label="Old Password" prepend-inner-icon="mdi-lock"
                                type="password"></v-text-field>
                  <v-text-field v-model="New_password" label="New Password" prepend-inner-icon="mdi-lock"
                                type="password"></v-text-field>
                  <v-btn block class="mt-2 bg-blue-darken-1" type="submit" @click="">
                    Submit
                  </v-btn>
                </v-form>
              </v-sheet>
            </v-card-text>
            <v-card-actions>
              <v-btn block color="primary" @click="DialogueCP = false">Cancel</v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
      </div>
      </div>
      <!-- <v-btn v-on:click="() => $router.push({ name: 'index' })">
        Back to Index
      </v-btn> -->
      <Credit/>
    </v-main>
  </Navbar>
</template>
