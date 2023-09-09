<script lang="ts">
  export default {
    computed: {
      credCols () {
        const { lgAndUp, md, sm, xs } = this.$vuetify.display
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
          <template v-slot:subtitle>
            <v-avatar image="/aboutus/05_Mark.webp"></v-avatar>
            <span class="font-weight-bold"> 65070005 นายกนกพล โป้ยขำ</span><div class="pt-1"><br />Some contact infomation idk</div>
          </template>
        </v-card>
      </v-col>
      <v-col :cols="credCols[1]">
        <v-card class="ma-2 pa-2">
          <template v-slot:subtitle>
            <v-avatar class="profile_img" image="/aboutus/28_Bess.webp"></v-avatar>
            <span class="font-weight-bold"> 65070028 นายคณิศร สมศรีอักษรแสง </span><div class="pt-1"><br />Some contact infomation idk</div>
          </template>
        </v-card>
      </v-col>
      <!-- <v-responsive width="100%"></v-responsive> -->
      <v-col :cols="credCols[2]">
        <v-card class="ma-2 pa-2">
          <template v-slot:subtitle>
            <v-avatar image="/aboutus/30_Krit.webp"></v-avatar>
            <span class="font-weight-bold"> 65070030 นายจารุกิตติ์ ศรีพาเพลิน</span><div class="pt-1"><br />Some contact infomation idk</div>
          </template>
        </v-card>
      </v-col>
      <v-col :cols="credCols[3]">
        <v-card class="ma-2 pa-2">
          <template v-slot:subtitle>
            <v-avatar image="/aboutus/35_Jai.webp"></v-avatar>
            <span class="font-weight-bold"> 65070035 นายจิรโชติ อินทรวงษ์โชติ</span><div class="pt-1"><br />Some contact infomation idk</div>
          </template>
        </v-card>
      </v-col>
      <!-- <v-responsive width="100%"></v-responsive> -->
      <v-col :cols="credCols[4]">
        <v-card class="ma-2 pa-2">
          <template v-slot:subtitle>
            <v-avatar image="/aboutus/64_Japan.webp"></v-avatar>
            <span class="font-weight-bold"> 65070064 นายณภัทร เวชพันธุ์</span><div class="pt-1"><br />Some contact infomation idk</div>
          </template>
        </v-card>
      </v-col>
      <v-col :cols="credCols[5]">
        <v-card class="ma-2 pa-2">
          <template v-slot:subtitle>
            <v-avatar image="/aboutus/89_Tae.webp"></v-avatar>
            <span class="font-weight-bold"> 65070085 นายธนกฤต ทรัพย์ประสิทธิ์</span><div class="pt-1"><br />Some contact infomation idk</div>
          </template>
        </v-card>
      </v-col>
    </v-row>
  </v-footer>
</template>

<style scoped>

</style>

<script lang="ts" setup>
</script>