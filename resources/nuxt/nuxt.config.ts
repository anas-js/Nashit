// https://nuxt.com/docs/api/configuration/nuxt-config
import fs from "node:fs";
const path = "C:\\laragon\\www\\nashit.juzr\\";

const config = {
  logout : ['juzr','nashit']
};

export default defineNuxtConfig({
  experimental: {
    scanPageMeta: true,
  },
  $production: {
    appConfig:  process.env.APP_ENV === 'production' ? {
      BK_URL: {
        nashit: "https://nashit.juzr.sa/api",
        img: "https://nashit.juzr.sa/images",
        juzrImg: "https://juzr.sa/images",
        juzr: "https://juzr.sa/api",
        csrf: `https://juzr.sa/sanctum/csrf-cookie`,
      },
      FR_URL: {
        nashit: "https://nashit.juzr.sa",
        juzr: "https://juzr.sa",
      },
      domain: {
        juzr: "juzr.sa",
        nashit: "nashit.juzr.sa",
      },
      ...config
    } : {
      BK_URL: {
        nashit: "https://nashit.juzr.app/api",
        img: "https://nashit.juzr.app/images",
        juzrImg: "https://juzr.app/images",
        juzr: "https://juzr.app/api",
        csrf: `https://juzr.app/sanctum/csrf-cookie`,
      },
      FR_URL: {
        nashit: "https://nashit.juzr.app",
        juzr: "https://juzr.app",
      },
      domain: {
        juzr: "juzr.app",
        nashit: "nashit.juzr.app",
      },
      ...config
    },
  },

  appConfig: {
    BK_URL: {
      nashit: "https://nashit.juzr.app/api",
      img: "https://nashit.juzr.app/images",
      juzrImg: "https://juzr.app/images",
      juzr: "https://juzr.app/api",
      csrf: `https://juzr.app/sanctum/csrf-cookie`,
    },
    FR_URL: {
      nashit: "https://nashit.juzr.app:8080",
      juzr: "https://juzr.app:3000",
    },
    domain: {
      juzr: "juzr.app",
      nashit: "nashit.juzr.app",
    },
    ...config
  },

  hooks: {
    "pages:extend"(pages) {
      function dir(file: string) {
        return __dirname + file;
      }
      const paths = ["board", "course"];

      paths.forEach((e) => {
        pages.push({
          name: `${e}PrefsPage`,
          path: `/:profile?/${e}/:id/prefs`,
          file: dir("/pages/prefs.vue"),
          meta: pages.find((e) => e.name == "PrefsPage")!.meta,
        });

        pages.push({
          name: `${e}ReadPage`,
          path: `/:profile?/${e}/:id/read`,
          file: dir("/pages/read.vue"),
          meta: pages.find((e) => e.name == "ReadPage")!.meta,
        });

        pages.push({
          name: `${e}InstallPage`,
          path: `/:profile?/${e}/:id/install`,
          file: dir("/pages/install.vue"),
          meta: pages.find((e) => e.name == "InstallPage")!.meta,
        });

        pages.push({
          name: `${e}UpdatePage`,
          path: `/:profile?/${e}/:id/update`,
          file: dir("/pages/update.vue"),
          meta: pages.find((e) => e.name == "UpdatePage")!.meta,
        });

        pages.push({
          name: `${e}AboutPage`,
          path: `/:profile?/${e}/:id/about`,
          file: dir("/pages/about.vue"),
          meta: pages.find((e) => e.name == "AboutPage")!.meta,
        });
      });

      // paths.forEach((e) => {
      //   pages.push({
      //     name: `${e}PrefsPage`,
      //     path: `/:profile/${e}/:id/prefs`,
      //     file: dir("/pages/prefs.vue"),
      //     meta: pages.find((e) => e.name == "PrefsPage")!.meta,
      //   });

      //   pages.push({
      //     name: `${e}ReadPage`,
      //     path: `/:profile/${e}/:id/read`,
      //     file: dir("/pages/read.vue"),
      //     meta: pages.find((e) => e.name == "ReadPage")!.meta,
      //   });

      //   pages.push({
      //     name: `${e}InstallPage`,
      //     path: `/:profile/${e}/:id/install`,
      //     file: dir("/pages/install.vue"),
      //     meta: pages.find((e) => e.name == "InstallPage")!.meta,
      //   });

      //   pages.push({
      //     name: `${e}UpdatePage`,
      //     path: `/:profile/${e}/:id/update`,
      //     file: dir("/pages/update.vue"),
      //     meta: pages.find((e) => e.name == "UpdatePage")!.meta,
      //   });

      //   pages.push({
      //     name: `${e}AboutPage`,
      //     path: `/:profile/${e}/:id/about`,
      //     file: dir("/pages/about.vue"),
      //     meta: pages.find((e) => e.name == "AboutPage")!.meta,
      //   });
      // });

      // pages.push({
      //   name: "profileCourseAll",
      //   path: "/:profile/course/:id/all",
      //   file: dir("/pages/course/[id]/all.vue"),
      //   meta : {
      //     layout : 'page'
      //   }
      // });

      // pages.push({
      //   name: "profileCourseStats",
      //   path: "/:profile/course/:id/stats",
      //   file: dir("/pages/course/[id]/stats.vue"),
      //   meta : {
      //     layout : 'page'
      //   }
      // });

      // pages.push({
      //   name: "profileCourseToday",
      //   path: "/:profile/course/:id/today",
      //   file: dir("/pages/course/[id]/today.vue"),
      //   meta : {
      //     layout : 'page'
      //   }
      // });

      // pages.push({
      //   name: "profileCourseFinish",
      //   path: "/:profile/course/:id/finish",
      //   file: dir("/pages/course/[id]/finish.vue"),
      //   meta : {
      //     layout : 'page'
      //   }
      // });

      // pages.push({
      //   name: "profileBoardStats",
      //   path: "/:profile/board/:id/stats",
      //   file: dir("/pages/board/[id]/stats.vue"),
      //   meta : {
      //     layout : 'page'
      //   }
      // });

      // pages.push({
      //   name: "profileBoard",
      //   path: "/:profile/board/:id/",
      //   file: dir("/pages/board/[id]/index.vue"),
      //   meta : {
      //     layout : 'page'
      //   }
      // });

      //=================================

      // pages.push({
      //   name: `testExtend`,
      //   path: `/testExtend`,
      //   file:  "~/pages/test.vue",
      //   meta : {
      //     layout : 'page'
      //   }
      // });

      // pages.push({
      //   name: `prefs`,
      //   path: `/prefs`,
      //   file: dir("/pages/prefs.vue"),
      //   alias: paths.map((e) => `/${e}/:id/prefs`),
      // });

      // pages.push({
      //   name: `read`,
      //   path: `/read`,
      //   file: dir("/pages/read.vue"),
      //   alias: paths.map((e) => `/${e}/:id/read`),
      // });

      // pages.push({
      //   name: `install`,
      //   path: `/install`,
      //   file: dir("/pages/install.vue"),
      //   alias: paths.map((e) => `/${e}/:id/install`),
      // });

      // pages.push({
      //   name: `update`,
      //   path: `/update`,
      //   file: dir("/pages/update.vue"),
      //   alias: paths.map((e) => `/${e}/:id/update`),
      // });

      // pages.push({
      //   name: `/about`,
      //   path: `/about`,
      //   file: dir("/pages/about.vue"),
      //   alias: paths.map((e) => `/${e}/:id/about`),
      // });

      // const course = ["all", "stats", "today", "finish"];

      // course.forEach((e) => {
      //   pages.push({
      //     name: "Course" + e.toUpperCase(),
      //     path: `/course/:id/${e}`,
      //     file: dir(`/pages/course/[id]/${e}.vue`),
      //     alias: [`/:profile/course/:id/${e}`],
      //   });
      // });

      // const baord = ["stats", "index"];

      // baord.forEach((e) => {
      //   pages.push({
      //     name: "Board" + e.toUpperCase(),
      //     path: `/board/:id/${e == "index" ? "" : e}`,
      //     file: dir(`/pages/board/[id]/${e}.vue`),
      //   });
      // });
      // console.log(JSON.stringify(pages));
    },
    "build:before"() {
      fs.chmod(path + "public", "755",()=>{});



      // remove  _nuxt

      fs.rm(path + "public\\_nuxt",{force:true,recursive:true},(err) => {
        // console.log(err);
      });
      fs.rm(path + "public\\images",{force:true,recursive:true},(err) => {
        // console.log(err);
      });
      console.log('Remove Done');
    },
    close() {

      fs.chmod(path + "public", "755",()=>{});
      fs.rm(path + "resources\\views\\nuxt\\200.html",{force:true},() => {});
      // _nuxt
      fs.rename(path + "resources\\views\\nuxt\\_nuxt", path + "public\\_nuxt",() => {});

      // images
      fs.rename(
        path + "resources\\views\\nuxt\\images",
        path + "public\\images",
        () => {}
      );

      //
      fs.rename(
        path + "resources\\views\\nuxt\\index.js",
        path + "public\\index.js",
        () => {}
      );
      //
      fs.rename(
        path + "resources\\views\\nuxt\\manifest.webmanifest",
        path + "public\\manifest.webmanifest",
        () => {}
      );

      // index
      fs.rename(
        path + "resources\\views\\nuxt\\index.html",
        path + "resources\\views\\nuxt\\index.blade.php",
        () => {}
      );

      // 404
      fs.rename(
        path + "resources\\views\\nuxt\\404.html",
        path + "resources\\views\\errors\\404.blade.php",
        () => {}
      );
    },
  },

  ssr: false,
  devtools: { enabled: false },

  nitro: {
    // static: true,
    preset: "node-server",
    output: {
      publicDir: "C:\\laragon\\www\\nashit.juzr\\resources\\views\\nuxt",
    },
  },

  vite: {
    build: {
      minify: "terser",
      terserOptions: {
        format: {
          comments: "all",
        },
      },
    },
  },

  css: [
    "~/assets/styles/main.scss",
    "remixicon/fonts/remixicon.css",
    "bootstrap/dist/css/bootstrap-grid.min.css",
  ],

  pwa: {
    registerType: "autoUpdate",
    strategies: "injectManifest",
    srcDir: "sw",
    filename: "index.ts",
    manifest: {
      name: "نشِط | جزيرة الإنجاز",
      short_name: "نشِط",
      icons: [
        {
          src: "/images/pwa/pwa-64x64.png",
          sizes: "64x64",
          type: "image/png",
          purpose: "any maskable",
        },
        {
          src: "/images/pwa/pwa-144x144.png",
          sizes: "144x144",
          type: "image/png",
          purpose: "any maskable",
        },
        {
          src: "/images/pwa/pwa-192x192.png",
          sizes: "192x192",
          type: "image/png",
          purpose: "any maskable",
        },
        {
          src: "/images/pwa/pwa-512x512.png",
          sizes: "512x512",
          type: "image/png",
          purpose: "any maskable",
        },
        {
          src: "/images/pwa/maskable-icon-512x512.png",
          sizes: "512x512",
          type: "image/png",
          purpose: "any maskable",
        },
      ],
      start_url: "/",
      display: "standalone",
      background_color: "#FA7070",
      theme_color: "#FA7070",
      lang: "ar",
      dir: "rtl",
    },

    workbox: {
      disableDevLogs: true,

      //  globPatterns : ["**/*"],
      // cleanupOutdatedCaches: true,
      // runtimeCaching: [
      //   // {
      //   //   urlPattern: /^https:\/\/fonts\.googleapis\.com\/.*/i,
      //   //   handler: 'CacheFirst',
      //   //   options: {
      //   //     cacheName: 'google-fonts-cache',
      //   //     expiration: {
      //   //       maxEntries: 10,
      //   //       maxAgeSeconds: 60 * 60 * 24 * 365 // <== 365 days
      //   //     },
      //   //     cacheableResponse: {
      //   //       statuses: [0, 200]
      //   //     }
      //   //   }
      //   // },
      //   {
      //     // ({request,event,sameOrigin,url}) => {
      //     //   console.log(request);
      //     //   return false;
      //     // }
      //     urlPattern: /.*posts.*/ig,
      //     handler: "NetworkOnly",
      //     options : {
      //       cacheName : "test"
      //     }
      //   },
      // ],
    },
    injectManifest: {},
    client: {
      installPrompt: false,
    },

    devOptions: {
      enabled: true,
      suppressWarnings: true,
      navigateFallback: "/",
      // navigateFallbackAllowlist: [/^\/$/],
      type: "module",
    },
  },

  // sourcemap: {
  //   server: true,
  //   client: true,
  // },
  // "app/spa-loading-template.html"
  spaLoadingTemplate: false,

  // devServer : {
  //   loadingTemplate : () => {
  //     return "~/app/laoding.html"
  //   },
  // },
  app: {
    baseURL: "/",
    layoutTransition: {
      name: "layout",
      mode: "out-in",
      appear: true,
    },

    pageTransition: {
      name: "page-trans",
      mode: "out-in",
      appear: true,
    },

    head: {
      title: "نشِط | جزيرة الإنجاز",
      meta: [
        { name: "twitter:card", content: "summary_large_image" },
        { property: "og:title", content: "نشِط | جزيرة الإنجاز" },
        {
          property: "og:image",
          content: `https://nashit.juzr.sa/images/og.png`,
        },
      ],
      link: [
        { rel: "apple-touch-icon", href: `/pwa/apple-touch-icon-180x180.png` },
        { rel: "icon", type: "image/svg+xml", href: "/images/icon.svg" },
        { rel: "icon", type: "image/png", href: "/images/icon.png" },
        {
          rel: "icon",
          type: "image/x-icon",
          href: "/images/pwa/favicon.ico",
          sizes: "any",
        },
        { rel: "manifest", href: "/manifest.webmanifest" },
        {
          rel: "stylesheet",
          href: "https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&family=Poppins:wght@400;500;700;800;900&family=JetBrains+Mono:wght@100;200;300;400;500;600;700;800&family=Mirza:wght@400;500;600;700&display=swap",
        },
      ],
      script: [
        {
          innerHTML: `window.addEventListener("beforeinstallprompt", (e) => {
    e.preventDefault();
    window.pwa = e;
  });
  `,
        },
        {
          async: true,
          src: "https://www.googletagmanager.com/gtag/js?id=",
        },
        {
          innerHTML: `<!-- Google tag (gtag.js) -->

  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', '');
`,
        },
      ],
    },
  },

  // plugins: [
  //   {src : "@/plugins/sync.ts"},
  //    {src : "@/plugins/logoutHelper.ts"}
  // ],

  modules: [
    "@nuxtjs/i18n",
    "@pinia/nuxt",
    "@nuxt/eslint",
    "@hypernym/nuxt-anime",
    "@vite-pwa/nuxt",
  ],

  eslint: {
    // config: {
    //   stylistic: true // <---
    // }
  },

  // nashit.juzr.app
  devServer: {
    host: "nashit.juzr.app",
    port: 8080,
    https: {
      cert: "C:\\laragon\\etc\\ssl\\laragon.crt",
      key: "C:\\laragon\\etc\\ssl\\laragon.key",
    },
  },

  i18n: {
    // customRoutes: 'config',
    // pages : {},
    // customRoutes: 'page',
    // pages: {
    //   "profile-course": {
    //     en: "/en/:profile/course/:id/",
    //   },
    // },
    locales: [
      { code: "en", file: "en.js", dir: "ltr", name: "English" },
      { code: "ar", file: "ar.js", dir: "rtl", name: "عربي" },
    ],
    defaultLocale: "ar",
    defaultDirection: "rtl",
    detectBrowserLanguage: false,
    langDir: "lang",
    vueI18n: "./i18n.config.ts",
  },

  compatibilityDate: "2024-08-04",
});
