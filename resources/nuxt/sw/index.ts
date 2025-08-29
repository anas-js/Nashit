import { precacheAndRoute } from 'workbox-precaching'
import {  registerRoute } from 'workbox-routing'
import { CacheFirst,NetworkFirst } from 'workbox-strategies'
import { ExpirationPlugin } from 'workbox-expiration';
declare let self: ServiceWorkerGlobalScope

// self.__WB_MANIFEST is default injection point

self.__WB_DISABLE_DEV_LOGS = true

precacheAndRoute(self.__WB_MANIFEST);
// /.*/
registerRoute((req) => {
  if(req.url.searchParams.get("cache")) {
    return false;
  }
return true;
}, new NetworkFirst({
  cacheName : 'nashit-runtime-cache',
  plugins : [
    new ExpirationPlugin({
      maxAgeSeconds : 60 * 60 * 24 * 7,
    })
  ]
}));


// console.log();


// to allow work offline
// registerRoute(new NavigationRoute(
//   createHandlerBoundToURL('/'),
//   { denylist: [/^\/backoffice/] },
// ))