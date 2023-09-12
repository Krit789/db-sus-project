<script lang="ts" setup>

</script>

<template>
  <v-container class="feature_container" style="padding: 12px; margin-top: 12px;">
    <h1 class="feature_header blue-text text-center">Dinner is served!</h1>
    <v-row class="w-100 ma-0 feature_flex" justify="center">
      <v-col class="feature_rounded">
        <div class="title_header">
          <h2 class="blue-text">Find your favorite restaurant</h2>
          <p class="inner_text">สวัสดีครับ ในเลคเชอร์นี้นะครับเราก็จะมา ทำความรู้จักกับระบบคอมพิวเตอร์เบื้องต้นนะครับ
            ซึ่งระบบคอมพิวเตอร์ก็ถือว่าเป็นระบบดิจิตอลครับ ในเลคเชอร์นี้นะครับหลักๆเลยก็จะ เรียนรู้ความแตกต่างระหว่าง
            ระบบดิจิตอลนะครับ กับระบบอนาล็อกว่าระบบดิจิตอลคืออะไรนะครับ มันทำงานยังไง แล้วมันแตกต่างจากระบบอนาล็อกยังไง
            นะครับ เมื่อนักศึกษาดู
            เลคเชอร์นี้นะครับหรือว่าศึกษาเลคเชอร์นี้จนจบแล้วนะครับก็หวังว่านักศึกษาจะมีความสามารถที่จะอธิบายให้เพื่อนฟังได้ครับว่าระบบดิจิตอลระบบอนาล็อกมันต่างกันยังไงนะครับ</p>
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
  </v-container>
</template>

<style scoped>
@import "@/components/stylesheets/features.css";
@import "@/components/stylesheets/global.css";

</style>


