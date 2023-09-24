<script lang="ts" setup>
    definePageMeta({ auth: false });
    const { status, data } = useAuth();
    useHead({
        title: "Seatify: Seat Reservation Service",
        meta: [
            {
                name: "Seatify: Seat Reservation Service",
                content: "Seatify, The Seat Reservation Service that makes the difference.",
            },
        ],
    });
    useSeoMeta({
        title: "Seatify",
        ogTitle: "Seatify",
        description: "Seatify, The Seat Reservation Service that makes the difference.",
        ogDescription: "Seatify, The Seat Reservation Service that makes the difference.",
        ogImage: "/og_image.jpg",
        twitterCard: "summary_large_image",
    });
</script>

<script lang="ts">
    import "~/assets/stylesheets/global.css";
    import "~/assets/stylesheets/index.css";
</script>

<template>
    <v-main class="mt-n16">
        <v-parallax src="images/home/banner.webp">
            <div class="text-center mt-16 first-box">
                <h1 class="text-h2 font-weight-bold text-white mt-16">Seatify</h1>
                <p class="text-h5 ma-5 text-white">Satisfying your seaty needs.</p>
                <v-btn prepend-icon="mdi-login-variant" rounded="lg" size="x-large" @click="$router.push('/reservation')">Reserve Now!</v-btn>
            </div>
        </v-parallax>
        <div class="bg-grey-lighten-4 mb-3">
            <Features class="mt-0" />
            <Credit class="bg-transparent" />
        </div>
    </v-main>
</template>

<style>
    @import "@/assets/stylesheets/global.css";
    @import "@/assets/stylesheets/index.css";
</style>
