import Cookies from "js-cookie";

export default defineNuxtPlugin(async (nuxt) => {

  // window.addEventListener("beforeinstallprompt", (e) => {
  //   e.preventDefault();
  //   console.log("g");
  //   // systemStore.PWA = e as unknown as { prompt: () => void };
  // });

  

  const $auth = useAuth();
  // const token = Cookies.get("juzr_token");
  // const session = Cookies.get("juzr_session");

  // console.log("token");
  // const { $i18n } = useNuxtApp();

  // if ($local.get("caches")) {
  //   let loop = 0;
  //   while(loop < 5) {
  //     try {
  //       await $cache().clear();
  //       loop = 10;
  //     } catch {
  //       loop++;
  //     }
  //   }
  //   $local.delete("caches");
  //   // await navigateTo(`${$app().FR_URL.juzr}/logout`, {
  //   //   external: true,
  //   // });
  //   // if (await $auth.logout()) {
  //   //   $msg({
  //   //     text: $i18n.t(`plugins.logout.deletePrivateData`),
  //   //     type: "ok",
  //   //   });
  //   // }
  // }

  if (Cookies.get("logout") && !nuxt._route.meta.logout) {
    await navigateTo(`${$app().FR_URL.juzr}/logout`, {
      external: true,
    });
    return;
  }

  // if (session) {

  const lastUser = $local.get("user");

  await $api
    .get<any>(`${$app().BK_URL.juzr}/user`, {
      params: {
        platform: "nashit",
      },
    })
    .then(async (res) => {
      if (lastUser && lastUser != res.id) {
        await $cache().clear();
      }
      $auth.login(res);
    })
    .catch(async () => {

      if (lastUser) {
        await $cache().clear();
        $local.delete("user");
      }

      Cookies.remove("juzr_session", {
        domain:  $app().domain.juzr,
      });
    });
  // }
});
