export default defineNuxtRouteMiddleware((to, from) => {
  const { data } = useAuth();
  if (!to?.meta?.meta?.permitted.includes(data?.value?.role)) {
    throw createError({ statusCode: 403, statusMessage: 'You don\'t have privilege to access this page' })
  }
});
