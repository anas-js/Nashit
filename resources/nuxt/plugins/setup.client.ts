import Cookies from "js-cookie";
export default defineNuxtPlugin(async (nuxt) => {

  // loading
  const {$anime} : {$anime : (params: anime.AnimeParams) => anime.AnimeInstance} = useNuxtApp();

  const loadingHtml = $help().toHtml(
    `
    <div class='loading-app'>
    <div>
<svg width="66" height="62" viewBox="0 0 66 62" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M43.7642 10.9412C43.7642 4.89854 38.8657 0 32.823 0C26.7804 0 21.8818 4.89854 21.8818 10.9412C21.8818 16.9839 26.7804 21.8824 32.823 21.8824C38.8657 21.8824 43.7642 16.9839 43.7642 10.9412Z" fill="#FA7878"/>
<path d="M32.561 62C22.5214 62 14.562 58.896 8.6829 52.69C2.8943 46.3808 0 37.9352 0 27.3528H16.9588C16.9588 32.9493 18.3608 37.3247 21.1646 40.4791C23.9685 43.6334 27.7673 45.2106 32.561 45.2106C37.3547 45.2106 41.1535 43.6843 43.9573 40.6317C46.7612 37.4773 48.1631 33.1019 48.1631 27.5055H65.122C65.122 38.1896 62.182 46.6352 56.303 52.842C50.5148 58.947 42.6006 62 32.561 62Z" fill="#FA7878"/>
</svg>


</svg>

</svg>


    </div>
  </div>
    `
  );

  const loadingStyle = $help().toHtml(
    `<style>
.loading-app {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 1000;
  background-color: #fff;
  color: #000;
  justify-content: center;
  align-items: center;
  display : inline-flex;
}

.dark-mode .loading-app {
 background-color: #0d0d27;
}


  
  

 </style>
`,
    "head > *"
  );

  let loading = false;

  nuxt.hook("page:loading:start", () => {
    if (!loading) {
      document.head.append(loadingStyle);
      document.body.appendChild(loadingHtml);
    }
  });

  nuxt.hook("page:loading:end", () => {
    if (!loading) {
      loadingHtml.remove();
      loadingStyle.remove();
      loading = true;
    }
  });

  nuxt.hook("app:error", () => {
    if (!loading) {
      loadingHtml.remove();
      loadingStyle.remove();
    }
  });

  // ============
  // Cookies.get('logout') && ( window.location.href = `${$app().FR_URL.juzr}/logout`)

  // ============
  //!!!
  // if (navigator.onLine) {
  //   await $api.get($app().BK_URL.csrf, {
  //     credentials: "include",
  //   });
  // }

  const systemStore = useSystemStore();

  // window.addEventListener("beforeinstallprompt", (e) => {
  //   e.preventDefault();
  //   console.log("g");
  //   // systemStore.PWA = e as unknown as { prompt: () => void };
  // });

  // route
  const router = useRouter();
  const cache = await caches.open("nashit-runtime-cache");
  router.beforeResolve(async (to) => {
    if (cache) {
      const match = await cache.match(to.fullPath, {
        ignoreVary: true,
        ignoreMethod: true,
        ignoreSearch: true,
      });
      if (!match) {
        if (!navigator.onLine) {
          $msg({
            text: $t("gl.msg.error.cache.open"),
            type: "error",
          });
          return false;
        } else {
          cache.add(to.fullPath);
        }
      }
    }
  });

 
});
