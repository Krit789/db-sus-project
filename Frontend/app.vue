<template>
  <NuxtLayout>
    <NuxtLoadingIndicator :height="4" color="#0373DE" />
    <NuxtPage />
  </NuxtLayout>
</template>
<style>
  .page-enter-active,
  .page-leave-active {
    transition: all 0.4s;
  }

  .page-enter-from,
  .page-leave-to {
    opacity: 0;
    filter: blur(1rem);
  }

  .table-hover:hover {
    background-color: #f0f0f093 !important;
    cursor: pointer;
  }

  .table-hover:hover .td-hover {
    background-color: transparent !important; /* or #000 */
  }
</style>
