<script lang="ts" setup>

const {status, data, signIn, signOut} = useAuth();
</script>

<script lang="ts">
import "~/assets/stylesheets/features.css";
import "~/assets/stylesheets/global.css";

</script>

<template>
  <v-container class="justify-center feature_container" style="padding: 12px; margin-top: 12px;">
    <h1 class="feature_header blue-text pl-12">Dinner is ready to served!</h1>

    <!-- Row 1 -->
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

    <!--Row 2 Create Branches Report   -->
    <v-row v-if="status == 'authenticated' && data.role == 'MANAGER' || 1"
           class="feature_rounded row_two_container mt-8"
           justify="center" no-gutters>
      <v-col class="" col="">
        <h2 class="blue-text ">Create Branches Report</h2>
        <v-row align-content="center" class="h-100 pb-10">
          <h3 class="report_text report_header">สรุปยอดรวมและดูรายงานของสาขาที่คุณต้องการ</h3>
          <p class="report_text report_body">คุณสามารถดูรายงานผลลัพธ์ของสาขาได้ที่ปุ่มสร้างรายงายในหน้าสร้างรายงาน</p>
          <v-col class="w-100 justify-center" cols="auto" style="padding-left: 22px;">
            <v-btn :variant="'elevated'" class="blue_button " rounded="lg">Create Report</v-btn>
          </v-col>
          <p></p>
        </v-row>
      </v-col>
      <v-col align-self="end">
        <v-img
            class="report_macbook"
            src="Home/macbook_air_temp.webp"
            width="670"
        ></v-img>
      </v-col>
    </v-row>

    <!--Row 3 Manage   -->
    <v-row v-if="status == 'authenticated' && data.role == 'MANAGER' || 1"
           class="mt-8 px-3" justify="center">
      <v-col class="feature_rounded row_three_container">
        <h2 class="blue-text ">Manage Branches</h2>
        <v-img
            class="report_macbook"
            height="180"
            src="https://ejudge.it.kmitl.ac.th/assets/img/avatar280.png"
            width="100%"
        ></v-img>
        <h3 class="report_text report_header">จัดการสาขาที่คุณดูแลอยู่</h3>
        <p class="report_text report_body mt--3">คกด้่หเหกด้เ่า้หกด่าเ้หกด่าเ้าก่หด้เาหกด่้เ่าสหกด้าส</p>
        <v-row class="min-w-100 justify-center mt-0" cols="auto" style="padding-left: 22px;">
          <v-btn :variant="'outlined'" color="#0373DE" class="" rounded="lg">Manage Branches</v-btn>
        </v-row>
      </v-col>

      <v-col class="feature_rounded row_three_container">
        <h2 class="blue-text ">Manage Menu</h2>
        <v-img
            class="report_macbook"
            height="180"
            src="https://ejudge.it.kmitl.ac.th/assets/img/avatar280.png"
            width="100%"
        ></v-img>
        <h3 class="report_text report_header">จัดการเมนูของสาขา</h3>
        <p class="report_text report_body mt--3">คกด้่หเหกด้เ่า้หกด่าเ้หกด่าเ้าก่หด้เาหกด่้เ่าสหกด้าส</p>
        <v-row class="min-w-100 justify-center mt-0" cols="auto" style="padding-left: 22px;">
          <v-btn :variant="'outlined'" color="#0373DE" class="" rounded="lg">Manage Menu</v-btn>
        </v-row>
      </v-col>
    </v-row>


  </v-container>
</template>




