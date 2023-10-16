<script lang="ts" setup>
import '~/assets/stylesheets/global.css';
import '~/assets/stylesheets/index.css';
import {DateTime} from 'luxon';

definePageMeta({auth: false});
const {status, data} = useAuth();
useHead({
  title: 'Seatify: Seat Reservation Service',
  meta: [
    {
      name: 'Seatify: Seat Reservation Service',
      content: 'Seatify, The Seat Reservation Service that makes the difference.',
    },
  ],
});
useSeoMeta({
  title: 'Seatify',
  ogTitle: 'Seatify',
  description: 'Seatify, The Seat Reservation Service that makes the difference.',
  ogDescription: 'Seatify, The Seat Reservation Service that makes the difference.',
  ogImage: '/og_image.jpg',
  twitterCard: 'summary_large_image',
});
</script>
<script lang="ts">
export default {
  methods: {
    scrollTo(id: string) {
      const position = document.getElementById(id).offsetTop;
      window.scrollTo({top: position - 80, behavior: 'smooth'});
    },
  },
};
</script>
<template>
  <v-main class="mt-n16">
    <v-parallax
        :src="
        DateTime.now() >= DateTime.now().set({ hour: 0, minute: 0 }) && DateTime.now() < DateTime.now().set({ hour: 6, minute: 0 })
          ? '/images/home/banner-night.webp'
          : DateTime.now() >= DateTime.now().set({ hour: 6, minute: 0 }) && DateTime.now() < DateTime.now().set({ hour: 12, minute: 0 })
          ? '/images/home/banner-morning.webp'
          : DateTime.now() >= DateTime.now().set({ hour: 12, minute: 0 }) && DateTime.now() < DateTime.now().set({ hour: 18, minute: 0 })
          ? '/images/home/banner.webp'
          : DateTime.now() >= DateTime.now().set({ hour: 18, minute: 0 }) && DateTime.now() <= DateTime.now().set({ hour: 23, minute: 59, second: 59 })
          ? '/images/home/banner-evening.webp'
          : '/images/home/banner.webp'
      "
        class="index_parallax">
      <div class="text-center mt-16 first-box">
        <h1 class="text-h2 font-weight-bold text-white mt-16">Seatify</h1>
        <p class="text-h5 mb-5 text-white index_text_shadow">Satisfying your seaty needs.</p>
        <v-btn v-if="status === 'unauthenticated'" prepend-icon="mdi-login-variant" rounded="lg" size="x-large"
               @click="$router.push('/login')">Login Now!
        </v-btn>
        <v-btn v-else prepend-icon="mdi-login-variant" rounded="lg" size="x-large"
               @click="data.role === 'USER' ? $router.push('/reservation') : scrollTo('management_features')">
          {{ data.role === 'USER' ? 'Reserve Now!' : 'Manage Now!' }}
        </v-btn>
      </div>
    </v-parallax>
    <div class="bg-grey-lighten-4 mb-3">
      <Features id="features" class="mt-0"/>
      <Credit id="about_us" class="bg-transparent"/>
    </div>
  </v-main>
</template>

<style>
@import '@/assets/stylesheets/global.css';
@import '@/assets/stylesheets/index.css';
</style>
