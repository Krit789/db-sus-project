<script lang="ts" setup>

</script>

<template>
  <v-container class="feature_container" style="padding: 12px; margin-top: 12px;">
    <h1 class="feature_header blue-text text-center">Dinner is served!</h1>
    <v-row class="w-100 ma-0" justify="center">
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

.feature_header {
  font-size: 42px;
  margin-top: 32px;
  margin-left: 12px;
}

.blue-text {
  color: #377df7;
}

.feature_container {
  background-color: rgba(0, 0, 0, 0);
  min-height: 70vh;
  max-width: 100vw !important;
}

.feature_rounded {
  background-color: rgba(255, 255, 255);
  margin: 12px;
  padding: 12px 12px 0px 12px;
//min-height: 100vh; //border: 1px black solid; border-radius: 32px; background-color: white; //min-width: 40vw; min-height: 500px;
}

.title_header {
  padding: 30px 35px 12px 35px;
}

.title_body {
  padding: 30px 35px 30px 35px;
}

.inner_text {
  padding-top: 5px;
  text-align: justify;
}

.map_img {
  margin: 0px 0%;
  background-size: cover;
  top: 0%;
}

.icon_img {
  margin: 0px 10%;
  height: 100px;
}

.icon_text {
  margin: 0px 15px 0px 3%;
  font-size: 42px;
  z-index: 1;
  line-height: 130%;
}

.pin_location_body {
  align-self: center;
  box-sizing: border-box;
  display: flex;
  flex-direction: column;
  justify-content: center;
  height: 90%;
}

@media only screen and (max-width: 600px) {
  .pin_location_body {
    flex-direction: row-reverse;
  }

}
</style>


