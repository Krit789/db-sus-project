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
  <v-footer class="d-flex flex-column">
    <h1>About Us</h1>
    <v-row no-gutters>
      <v-col :cols="credCols[0]">
        <v-card class="ma-2 pa-2">
          <template v-slot:title>
            <v-row>
              <v-col class="profile_img_left" cols="12" md="6">
                <div class="profile_img_container mx-auto">
                  <v-avatar class="profile_img" image="/aboutus/05_Mark.webp"></v-avatar>
                </div>
              </v-col>
              <v-col cols="12" md="6">
                <span class="font-weight-bold profile_text ">65070005 นายกนกพล โป้ยขำ</span>
                <div class="pt-0 profile_text profile_contact">Some contact infomation idk</div>
              </v-col>
            </v-row>
          </template>
        </v-card>
      </v-col>
      <v-col :cols="credCols[1]">
        <v-card class="ma-2 pa-2">
          <template v-slot:title>
            <v-row>
              <v-col class="profile_img_left" cols="12" md="6">
                <div class="profile_img_container mx-auto">
                  <v-avatar class="profile_img" image="/aboutus/28_Bess.webp"></v-avatar>
                </div>
              </v-col>
              <v-col cols="12" md="6">
                <span class="font-weight-bold profile_text ">65070028 นายคณิศร สมศรีอักษรแสง</span>
                <div class="pt-0 profile_text profile_contact">Some contact infomation idk</div>
              </v-col>
            </v-row>
          </template>
        </v-card>
      </v-col>
      <!-- <v-responsive width="100%"></v-responsive> -->
      <v-col :cols="credCols[2]">
        <v-card class="ma-2 pa-2">
          <template v-slot:title>
            <v-row>
              <v-col class="profile_img_left" cols="12" md="6">
                <div class="profile_img_container mx-auto">
                  <v-avatar class="profile_img" image="/aboutus/30_Krit.webp"></v-avatar>
                </div>
              </v-col>
              <v-col cols="12" md="6">
                <span class="font-weight-bold profile_text ">65070030 นายจารุกิตติ์ ศรีพาเพลิน</span>
                <div class="pt-0 profile_text profile_contact">Some contact infomation idk</div>
              </v-col>
            </v-row>
          </template>
        </v-card>
      </v-col>
      <v-col :cols="credCols[3]">
        <v-card class="ma-2 pa-2">
          <template v-slot:title>
            <v-row>
              <v-col class="profile_img_left" cols="12" md="6">
                <div class="profile_img_container mx-auto">
                  <v-avatar class="profile_img" image="/aboutus/35_Jai.webp"></v-avatar>
                </div>
              </v-col>
              <v-col cols="12" md="6">
                <span class="font-weight-bold profile_text ">65070035 นายจิรโชติ อินทรวงษ์โชติ</span>
                <div class="pt-0 profile_text profile_contact">Some contact infomation idk</div>
              </v-col>
            </v-row>
          </template>
        </v-card>
      </v-col>
      <!-- <v-responsive width="100%"></v-responsive> -->
      <v-col :cols="credCols[4]">
        <v-card class="ma-2 pa-2">
          <template v-slot:title>
            <v-row>
              <v-col class="profile_img_left" cols="12" md="6">
                <div class="profile_img_container mx-auto">
                  <v-avatar class="profile_img" image="/aboutus/64_Japan.webp"></v-avatar>
                </div>
              </v-col>
              <v-col cols="12" md="6">
                <span class="font-weight-bold profile_text ">65070064 นายณภัทร เวชพันธุ์</span>
                <div class="pt-0 profile_text profile_contact">Some contact infomation idk</div>
              </v-col>
            </v-row>
          </template>
        </v-card>
      </v-col>
      <v-col :cols="credCols[5]">
        <v-card class="ma-2 pa-2">
          <template v-slot:title>
            <v-row>
              <v-col class="profile_img_left" cols="12" md="6">
                <div class="profile_img_container mx-auto">
                  <v-avatar class="profile_img" image="/aboutus/89_Tae.webp"></v-avatar>
                </div>
              </v-col>
              <v-col cols="12" md="6">
                <span class="font-weight-bold profile_text ">65070085 นายธนกฤต ทรัพย์ประสิทธิ์์</span>
                <div class="pt-0 profile_text profile_contact">Some contact infomation idk</div>
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
  max-width: 100px;
}

.profile_text {
  font-family: "Sarabun", "Roboto", sans-serif;
}

.profile_contact {
  font-size: small;
}
</style>

<script lang="ts" setup>
</script>
