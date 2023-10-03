export default defineNuxtRouteMiddleware((to, from) => {
  const { status, data } = useAuth();
  // const route = useRoute();
  if (!to.meta.meta.permitted.includes(data.value.role)) {
    abortNavigation("Permission Denied");
  }
});
