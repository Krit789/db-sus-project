<script lang="ts" setup>

</script>

<template>
  <v-container class="feature_container" style="padding: 12px; margin-top: 12px;">
    <h1 class="feature_header blue-text pl-12">Dinner is ready to served!</h1>
    <v-row class="feature_flex" justify="center">
      <v-col class="feature_rounded">
        <div class="title_header">
          <h2 class="blue-text">Find your favorite restaurant</h2>
          <p class="inner_text">หาร้านอาหารร้านโปรดที่คุณชื่นชอบได้ในพริบตาเดียว เพียงแค่คุณค้นหาชื่อของร้านเท่านั้น</p>
        </div>
        <div class="title_body">
          <v-img
              class="map_img"
              height="640"
              src="Home/PChang_Apple_Map.webp"
          ></v-img>
        </div>
      </v-col>
      <v-col class="feature_rounded">
        <div class="title_header">
          <h2 class="blue-text">Just search what you are thinking</h2>
        </div>

        <div class="title_body pin_location_body">
          <v-img
              class="icon_img"
              max-height="350"
              src="Home/Swensens.webp"
          ></v-img>
          <h1 class="icon_text">สำรวจร้านอาหารชั้นเลิศรอบตัวคุณ!</h1>
        </div>
      </v-col>
    </v-row>

    <!--Row 2-->
    <v-row class="feature_flex" justify="center">
      <v-col class="feature_rounded row_two_container">
        <div class="title_header"></div>
      </v-col>

    </v-row>
  </v-container>
</template>

<style scoped>
@import "@/components/stylesheets/features.css";
@import "@/components/stylesheets/global.css";

</style>


