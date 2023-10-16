<script lang="ts">
import '~/assets/stylesheets/credit.css';
import '~/assets/stylesheets/global.css';

export default {
  computed: {
    credCols: function () {
      const {lgAndUp, md, sm, xs} = this.$vuetify.display;
      return lgAndUp ? [4, 4, 4, 4, 4, 4] : md ? [4, 4, 4, 4, 4, 4] : sm ? [6, 6, 6, 6, 6, 6] : xs ? [12, 12, 12, 12, 12, 12] : [4, 4, 4, 4, 4, 4];
    },
  },
};
</script>

<template>
  <v-footer class="d-flex flex-column pt-5 credit_container">
    <h1 style="font-size: 42px">About Us</h1>
    <v-img alt="Image Description" class="w-100 mt-1 mb-5" height="auto" max-width="8%" min-width="100px"
           src="/images/rainbow-line.webp"></v-img>
    <v-row no-gutters>
      <v-col :cols="credCols[0]">
        <v-card class="ma-2 pa-2 contact_card_box">
          <template v-slot:title>
            <v-row class="profile_row_card">
              <v-col class="profile_img_left" cols="12" md="6">
                <div class="profile_img_container mx-auto">
                  <v-avatar class="profile_img" image="/images/credit/05_Mark.webp"></v-avatar>
                </div>
              </v-col>
              <v-col class="profile_contact_container" cols="12" md="6">
                <span class="font-weight-bold profile_text">
                  นายกนกพล โป้ยขำ
                  <br/>
                  65070005
                </span>
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
                  <v-avatar class="profile_img" image="/images/credit/28_Bess.webp"></v-avatar>
                </div>
              </v-col>
              <v-col cols="12" md="6">
                <span class="font-weight-bold profile_text">
                  นายคณิศร สมศรีอักษรแสง
                  <br/>
                  65070028
                </span>
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
                  <v-avatar class="profile_img" image="/images/credit/30_Krit.webp"></v-avatar>
                </div>
              </v-col>
              <v-col cols="12" md="6">
                <span class="font-weight-bold profile_text">
                  นายจารุกิตติ์ ศรีพาเพลิน
                  <br/>
                  65070030
                </span>
                <div class="pt-0 profile_text profile_contact">Fullstack Developer</div>
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
                  <v-avatar class="profile_img" image="/images/credit/35_Jai.webp"></v-avatar>
                </div>
              </v-col>
              <v-col cols="12" md="6">
                <span class="font-weight-bold profile_text">
                  นายจิรโชติ อินทรวงษ์โชติ
                  <br/>
                  65070035
                </span>
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
                  <v-avatar class="profile_img" image="/images/credit/64_Japan.webp"></v-avatar>
                </div>
              </v-col>
              <v-col cols="12" md="6">
                <span class="font-weight-bold profile_text">
                  นายณภัทร เวชพันธุ์
                  <br/>
                  65070064
                </span>
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
                  <v-avatar class="profile_img" image="/images/credit/89_Tae.webp"></v-avatar>
                </div>
              </v-col>
              <v-col cols="12" md="6">
                <span class="font-weight-bold profile_text">
                  นายธนกฤต ทรัพย์ประสิทธิ์
                  <br/>
                  65070089
                </span>
                <div class="pt-0 profile_text profile_contact">Frontend Developer</div>
              </v-col>
            </v-row>
          </template>
        </v-card>
      </v-col>
    </v-row>
  </v-footer>
</template>
