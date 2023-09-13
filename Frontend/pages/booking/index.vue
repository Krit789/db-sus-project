<!--bro where's the template of pages?-->
<script lang="ts" setup>
const {
  status,
  data
} = useAuth()
async function test(){
    const locations: any = await fetch(
        "http://localhost:3000/proxy/api/booking/Booking/Booking-viewalllocation-user.php",
    {
        method: "POST",
        body: {
        },
    }
    ).catch((error) => error).then(() => {
      
    });
}
</script>

<template>
  <Navbar>

    <Credit/>
  </Navbar>
</template>
