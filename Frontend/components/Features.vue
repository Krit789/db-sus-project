<script lang="ts" setup>
const {status, data} = useAuth();
const route = useRoute();
</script>

<template>
  <v-container class="justify-center feature_container pb-2 mb-8">
    <h1 class="feature_header text-primary pl-4 mb-5">
      Dinner is ready to served!
    </h1>
    <!-- Row 1 -->
    <v-row class="feature_flex mx-0" justify="center">
      <v-col class="feature_rounded">
        <div class="title_header">
          <h4 class="text-primary text-h4">
            Find your favorite restaurant
          </h4>
          <p class="inner_text">
            หาร้านอาหารร้านโปรดที่คุณชื่นชอบได้ในพริบตาเดียว
            เพียงแค่คุณค้นหาชื่อของร้านเท่านั้น
          </p>
        </div>
        <div class="title_body">
          <v-img
              class="map_img"
              height="640"
              src="images/home/PChang_Apple_Map.webp"
          ></v-img>
        </div>
      </v-col>
      <v-col class="feature_rounded">
        <div class="title_header">
          <h2 class="text-primary text-h4">
            Just search what you are thinking
          </h2>
        </div>

        <div class="title_body pin_location_body">
          <v-img
              class="icon_img"
              max-height="350"
              src="images/home/Swensens.webp"
          ></v-img>
          <h1 class="icon_text">สำรวจร้านอาหารชั้นเลิศรอบตัวคุณ!</h1>
        </div>
      </v-col>
    </v-row>

    <!--Row 2 Create Branches Report   -->
    <!-- remember to remove debug condition -->
    <v-row
        v-if="(status == 'authenticated' && data.role == 'MANAGER') || 1"
        class="feature_rounded mx-3 mt-8 px-5 py-8"
        justify="center"
        no-gutters
    >
      <v-col class="" col="">
        <h2 class="text-primary text-h4">Create Branches Report</h2>
        <v-row align-content="center" class="h-100 pb-10">
          <h3 class="report_text report_header">
            สรุปยอดรวมและดูรายงานของสาขาที่คุณต้องการ
          </h3>
          <p class="report_text report_body">
            คุณสามารถดูรายงานผลลัพธ์ของสาขาได้ที่ปุ่มสร้างรายงายในหน้าสร้างรายงาน
          </p>
          <v-col
              class="w-100 justify-center"
              cols="auto"
              style="padding-left: 22px">
            <v-btn
                :variant="'elevated'"
                class="font-weight-bold"
                rounded="lg"
                @click="() => {$router.push('/report');}"
            >Create Report
            </v-btn>
          </v-col>
        </v-row>
      </v-col>
      <v-col align-self="end">
        <v-img
            class="report_macbook"
            src="images/home/macbook_air_temp.webp"
            width="670"
        ></v-img>
      </v-col>
    </v-row>

    <!--Row 3 Manage Branches and Manage Menu   -->
    <v-row
        v-if="(status == 'authenticated' && (data.role == 'MANAGER' || data.role == 'GOD')) || 1"
        class="mt-5 px-3"
        justify="center"
    >
      <v-col class="feature_rounded row_three_container">
        <h2 class="text-primary text-h4">Manage Branches</h2>
        <v-img
            class="report_macbook"
            height="180"
            src="https://cdn-icons-png.flaticon.com/512/2727/2727399.png"
            width="100%"
        ></v-img>
        <h3 class="report_text report_header">
          จัดการสาขาที่คุณดูแลอยู่
        </h3>
        <p class="report_text report_body mt--3">
          คุณสามารถดูแลการจองและจัดการร้านได้ตรงนี้
        </p>
        <v-row
            class="min-w-100 justify-center mt-0"
            cols="auto"
            style="padding-left: 22px"
        >
          <v-btn :variant="'outlined'" class="" rounded="lg"
                 @click="() => {$router.push('/management/branches');}"
          >Manage Branches
          </v-btn>
        </v-row>
      </v-col>

      <v-col class="feature_rounded row_three_container">
        <h2 class="text-primary text-h4">Manage Reservation</h2>
        <v-img
            class="report_macbook"
            height="180"
            src="https://cdn-icons-png.flaticon.com/512/886/886329.png"
            width="100%"
        ></v-img>
        <h3 class="report_text report_header">จัดการการจองของสาขา</h3>
        <p class="report_text report_body mt--3">
          คุณสามารถจัดการการจองของสาขาของคุณได้ตรงนี้
        </p>
        <v-row
            class="min-w-100 justify-center mt-0"
            cols="auto"
            style="padding-left: 22px"
        >
          <v-btn :variant="'outlined'" class="" rounded="lg"
                 @click="() => {$router.push('/management/reservation');}"
          >Manage Menu
          </v-btn>
        </v-row>
      </v-col>
    </v-row>
  </v-container>
</template>

<script lang="ts">
import "~/assets/stylesheets/features.css";
import "~/assets/stylesheets/global.css";
</script>
