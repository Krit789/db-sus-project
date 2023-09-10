<script lang="ts">
export default {
  computed: {
    credCols() {
      const {lgAndUp, md, sm, xs} = this.$vuetify.display
      return lgAndUp ? [4, 4, 4, 4, 4, 4] : md ? [4, 4, 4, 4, 4, 4] : sm ? [6, 6, 6, 6, 6, 6] : xs ? [12, 12, 12, 12, 12, 12] : [4, 4, 4, 4, 4, 4]
    },
  },
}
</script>

<template>
  <v-footer class="d-flex flex-column pt-5" style="background: #ecfef666">
    <h1>About Us</h1>
    <v-img alt="Image Description" class="w-100 mt-1 mb-5"
           height="auto"
           max-width="8%"
           min-width="100px"
           src="/rainbow-line.webp"></v-img>
    <v-row no-gutters>
      <v-col :cols="credCols[0]">
        <v-card class="ma-2 pa-2 contact_card_box">
          <template v-slot:title>
            <v-row class="profile_row_card">
              <v-col class="profile_img_left" cols="12" md="6">
                <div class="profile_img_container mx-auto">
                  <v-avatar class="profile_img" image="/aboutus/05_Mark.webp"></v-avatar>
                </div>
              </v-col>
              <v-col class="profile_contact_container" cols="12" md="6">
                <span class="font-weight-bold profile_text ">นายกนกพล โป้ยขำ<br>65070005</span>
                <div class="pt-0 profile_text profile_contact">Backend Developer</div>
              </v-col>
            </v-row>
          </template>
        </v-card>
      </v-col>
      <v-col :cols="credCols[1]">
        <v-card class="ma-2 pa-2 contact_card_box">
          <template v-slot:title>
            <v-row class="profile_row_card">
              <v-col class="profile_img_left" cols="12" md="6">
                <div class="profile_img_container mx-auto">
                  <v-avatar class="profile_img" image="/aboutus/28_Bess.webp"></v-avatar>
                </div>
              </v-col>
              <v-col cols="12" md="6">
                <span class="font-weight-bold profile_text ">นายคณิศร สมศรีอักษรแสง<br>65070028</span>
                <div class="pt-0 profile_text profile_contact">Backend Developer</div>
              </v-col>
            </v-row>
          </template>
        </v-card>
      </v-col>
      <!-- <v-responsive width="100%"></v-responsive> -->
      <v-col :cols="credCols[2]">
        <v-card class="ma-2 pa-2 contact_card_box">
          <template v-slot:title>
            <v-row class="profile_row_card">
              <v-col class="profile_img_left" cols="12" md="6">
                <div class="profile_img_container mx-auto">
                  <v-avatar class="profile_img" image="/aboutus/30_Krit.webp"></v-avatar>
                </div>
              </v-col>
              <v-col cols="12" md="6">
                <span class="font-weight-bold profile_text ">นายจารุกิตติ์ ศรีพาเพลิน<br>65070030</span>
                <div class="pt-0 profile_text profile_contact">Backend Developer</div>
              </v-col>
            </v-row>
          </template>
        </v-card>
      </v-col>
      <v-col :cols="credCols[3]">
        <v-card class="ma-2 pa-2 contact_card_box">
          <template v-slot:title>
            <v-row class="profile_row_card">
              <v-col class="profile_img_left" cols="12" md="6">
                <div class="profile_img_container mx-auto">
                  <v-avatar class="profile_img" image="/aboutus/35_Jai.webp"></v-avatar>
                </div>
              </v-col>
              <v-col cols="12" md="6">
                <span class="font-weight-bold profile_text ">นายจิรโชติ อินทรวงษ์โชติ<br>65070035</span>
                <div class="pt-0 profile_text profile_contact">Frontend Developer</div>
              </v-col>
            </v-row>
          </template>
        </v-card>
      </v-col>
      <!-- <v-responsive width="100%"></v-responsive> -->
      <v-col :cols="credCols[4]">
        <v-card class="ma-2 pa-2 contact_card_box">
          <template v-slot:title>
            <v-row class="profile_row_card">
              <v-col class="profile_img_left" cols="12" md="6">
                <div class="profile_img_container mx-auto">
                  <v-avatar class="profile_img" image="/aboutus/64_Japan.webp"></v-avatar>
                </div>
              </v-col>
              <v-col cols="12" md="6">
                <span class="font-weight-bold profile_text ">นายณภัทร เวชพันธุ์<br>65070064</span>
                <div class="pt-0 profile_text profile_contact">Frontend Developer</div>
              </v-col>
            </v-row>
          </template>
        </v-card>
      </v-col>
      <v-col :cols="credCols[5]">
        <v-card class="ma-2 pa-2 contact_card_box">
          <template v-slot:title>
            <v-row class="profile_row_card">
              <v-col class="profile_img_left" cols="12" md="6">
                <div class="profile_img_container mx-auto">
                  <v-avatar class="profile_img" image="/aboutus/89_Tae.webp"></v-avatar>
                </div>
              </v-col>
              <v-col cols="12" md="6">
                <span class="font-weight-bold profile_text ">นายธนกฤต ทรัพย์ประสิทธิ์<br>65070089</span>
                <div class="pt-0 profile_text profile_contact">Frontend Developer</div>
              </v-col>
            </v-row>
          </template>
        </v-card>
      </v-col>
    </v-row>
  </v-footer>
</template>

<style>
.no-opacity {
  opacity: 1 !important;
}

.profile_img_container {
  position: relative;
  width: 100%;
  padding-bottom: 100%;
  margin: auto;

}

.profile_img {
  position: absolute !important;
  width: 100% !important;;
  height: 100% !important;;
  object-fit: cover !important;;
  left: 0;
  right: 0;
  top: 0;
  bottom: 0;
}

.profile_img_left {
  max-width: 120px;
}

.profile_text {
  font-family: "Sarabun", "Roboto", sans-serif;
}

.profile_contact {
  font-size: small;
}

@media only screen and (max-width: 960px) {
  .profile_row_card {
    flex-direction: row;
    flex-wrap: nowrap;
    align-content: center;
  }

  .profile_img_left {
    min-width: 15vh;
    height: 100px;
  }

  .profile_contact_container {
    width: 50%;
  }

  .profile_contact {
    font-size: large;
  }

  .profile_text {
    font-size: large;
    text-wrap: normal;
  }

  .profile_contact {
    font-weight: 200;
    font-size: medium;
  }
}

</style>

<script lang="ts" setup>
</script>
