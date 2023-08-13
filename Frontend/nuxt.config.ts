// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
    devtools: {enabled: true},
    css: ["vuetify/styles/main.sass"],
    build: {
        transpile: ["vuetify"],
    },
    modules: ['@sidebase/nuxt-auth'],
    auth: {
        // The module is enabled. Change this to disable the module
        isEnabled: true,
        // The origin is set to the development origin. Change this when deploying to production by setting `origin` in this config before build-time or by exporting `AUTH_ORIGIN` by running `export AUTH_ORIGIN=...`
        origin: 'http://localhost:3000',
        // The base path to the authentication endpoints. Change this if you want to add your auth-endpoints at a non-default location
        basePath: '/api/auth',
        // Whether to periodically refresh the session. Change this to `true` for a refresh every seconds or set this to a number like `5000` for a refresh every 5000 milliseconds (aka: 5 seconds)
        enableSessionRefreshPeriodically: false,
        // Whether to refresh the session whenever a window focus event happens, i.e, when your user refocuses the window. Set this to `false` to turn this off
        enableSessionRefreshOnWindowFocus: true,
        // Whether to add a global authentication middleware that will protect all pages without exclusion
        globalAppMiddleware: true,
        // Select the default-provider to use when `signIn` is called. Setting this here will also effect the global middleware behavior: E.g., when you set it to `github` and the user is unauthorized, they will be directly forwarded to the Github OAuth page instead of seeing the app-login page
        defaultProvider: undefined,
        // Whether to automatically set the callback url to the page the user tried to visit when the middleware stopped them. This is useful to disable this when using the credentials provider, as it does not allow a `callbackUrl`. Setting this to a string-value will result in that being used as the callbackUrl path.
        addDefaultCallbackUrl: true,
        // Configuration of the global auth-middleware (only applies if you set `globalAppMiddleware: true` above!)
        globalMiddlewareOptions: {
            // Whether to allow access to 404 pages without authentication. Set this to `false` to force users to sign-in before seeing `404` pages. Setting this to false may lead to vue-router problems (as the target page does not exist)
            allow404WithoutAuth: true,
            // Whether to automatically set the callback url to the page the user tried to visit when the middleware stopped them. This is useful to disable this when using the credentials provider, as it does not allow a `callbackUrl`. Setting this to a string-value will result in that being used as the callbackUrl path. Note: You also need to set the global `addDefaultCallbackUrl` setting to `false` if you want to fully disable this for the global middleware.
            addDefaultCallbackUrl: true
        }
      },
      nitro: {
        routeRules: {
          '/proxy/': { proxy: 'http://127.0.0.1:8000' },
        },
        devProxy: {
            '/proxy/': {
                target: 'http://127.0.0.1:8000',
                changeOrigin: true
            }
        }
      }
});
