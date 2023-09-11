<script lang="ts" setup>

</script>

<template>
  <v-container class="feature_container" style="padding: 12px;">
    <v-row class="w-100 ma-0" justify="center">
      <v-col class="feature_rounded">
        <v-img
            class="map_img"
            height="600"
            src="Home/PChang_Apple_Map.webp"
        ></v-img>
      </v-col>
      <v-col class="feature_rounded">
      </v-col>

    </v-row>
  </v-container>
</template>

<style scoped>


.feature_container {
  background-color: rgba(0, 0, 0, 0);
  min-height: 80vh;
  max-width: 100vw !important;

}

.feature_rounded {
  background-color: rgba(255, 255, 255);
  margin: 12px;
  padding: 0px;
//border: 1px black solid; border-radius: 32px; background-color: white; //min-width: 40vw; min-height: 500px;
}

.map_img {
  margin: 0px 10%;
  background-size: cover;
  background-position: top center !important;
}
</style>


