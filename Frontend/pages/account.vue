<script lang="ts" setup>
import {useDisplay} from "vuetify";
import "~/assets/stylesheets/global.css";
import "~/assets/stylesheets/index.css";
import "~/assets/stylesheets/reservation.css";

const {status, data} = useAuth();
const {mobile} = useDisplay();
</script>

<script lang="ts">
export default {
  data: () => ({
    DialogueCP: false,
    editMode: false,
    Old_password: "",
    New_password: "",
    confirm_new_password: "",
  }),
  methods: {
    passwordValidation(value: String) {
      if (this.New_password === value) return true;
      return "Both passwords must be similar.";
    },
    emailValidation(value: String) {
      if (
          String(value)
              .toLowerCase()
              .match(
                  /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|.(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/,
              )
      )
        return true;

      return "E-Mail must be in correct format.";
    },
  },
};

function toTitleCase(str) {
  return str.replace(/\w\S*/g, function (txt) {
    return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
  });
}
</script>

<template>
  <v-main class="bg-grey-lighten-4 justify-center windows_bg">
    <div class="main_container mx-auto">
      <v-row class="justify-center mt-8 px-3">
        <v-col class="user_rounded">
          <div class="justify-center">
            <v-img
                class="mt-5 bg-white ma-2 user_image"
                cover=""
                src="ejudge_avatar280.png"
                width="170"
            ></v-img>
          </div>
          <!--            <h2>Welcome Back!</h2>-->
          <h2 class="mt-6">{{ data.firstName }} {{ data.lastName }}</h2>
          <h3>{{ data.email }}</h3>
          <h3 class="mt-12">You are {{ toTitleCase(data.role) }}</h3>
        </v-col>
        <v-col class="user_rounded">
          May be user role and role benefits
        </v-col>
      </v-row>
      <div class="text-center mt-10 ma-auto your_account user_rounded pa-4 mx-3">
        <v-card-text class="text-h3 font-weight-bold my-6">
          Your Account
        </v-card-text>
        <div class="mx-md-16 mx-sm-8 mx-xs-8">
          <v-text-field
              :model-value="data.firstName"
              :readonly="!editMode"
              label="First Name"
              variant="underlined"
          ></v-text-field>
          <v-text-field
              :model-value="data.lastName"
              :readonly="!editMode"
              label="Last Name"
              variant="underlined"
          ></v-text-field>
          <v-text-field
              :model-value="data.tel"
              :readonly="!editMode"
              label="Telephone Number"
              variant="underlined"
          ></v-text-field>
          <v-text-field
              :model-value="data.email"
              :readonly="!editMode"
              label="Email"
              variant="underlined"
          ></v-text-field>
        </div>

        <v-btn v-if="editMode == true" class="ma-2" color="#0373DE" rounded="lg" variant="outlined"
               @click="DialogueCP = true">
          Change Password
        </v-btn>
        <v-divider class="border-opacity-0"></v-divider>
        <v-btn v-if="editMode == false" class="ma-2" color="#0373DE" rounded="lg" variant="outlined"
               @click.stop="editMode = true">
          Edit
        </v-btn>

        <!-- vv only appear on edit mode vv -->
        <v-btn v-if="editMode == true" class="ma-2" color="#0373DE" rounded="lg" variant="outlined" @click.stop="">
          Save
        </v-btn>

        <v-btn v-if="editMode == true" class="ma-2" color="#0373DE" rounded="lg" variant="outlined"
               @click.stop="editMode = false">
          Cancel
        </v-btn>
        <!-- ^^ only appear on edit mode ^^ -->

        <div class="text-center">
          <v-dialog v-model="DialogueCP" :fullscreen="mobile">
            <v-card class="blur-effect account_pane">
              <v-card-text>
                <h1 class="mb-3">Change Password</h1>
                <v-sheet class="mx-auto form_container" width="auto">
                  <v-form fast-fail @submit.prevent>
                    <v-text-field
                        v-model="Old_password"
                        label="Old Password"
                        prepend-inner-icon="mdi-lock"
                        type="password"
                    ></v-text-field>
                    <v-text-field
                        v-model="New_password"
                        label="New Password"
                        prepend-inner-icon="mdi-lock"
                        type="password"
                    ></v-text-field>
                    <v-text-field
                        v-model="confirm_new_password"
                        :rules="[passwordValidation]"
                        label="Confirm New Password"
                        prepend-inner-icon="mdi-lock-check"
                        type="password"
                    ></v-text-field>
                    <v-btn
                        block=""
                        class="mt-2 bg-blue-darken-1"
                        type="submit"
                        @click=""
                    >
                      Submit
                    </v-btn>
                  </v-form>
                </v-sheet>
              </v-card-text>
              <v-card-actions>
                <v-btn block="" color="primary" @click="DialogueCP = false"
                >Cancel
                </v-btn
                >
              </v-card-actions>
            </v-card>
          </v-dialog>
        </div>
      </div>

    </div>
    <!--      <Credit class="user_rounded mt-7 my-0 mx-3 blur-effect mb-5"/>-->
  </v-main>
</template>
