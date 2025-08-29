import Cookies from "js-cookie";

export default defineNuxtPlugin(async (_) => {
  const $auth = useAuth();
  const nuxt = useNuxtApp();
  const localePath = useLocalePath();
  const i18n = nuxt.$i18n;
  const route = useRoute();

  let usetSelect = Cookies.get("lang");

  if ($auth.loggedIn) {
    if ($auth.user?.settings?.lang !== usetSelect) {
      if ($auth.user?.settings?.lang !== "auto") {
        usetSelect = $auth.user?.settings?.lang;
        if (usetSelect) {
          Cookies.set("lang", usetSelect, {
            domain: $app().domain.juzr,
            expires: 30,
          });
        } else {
          Cookies.remove("lang");
        }
      }
    }
  }

  if (!usetSelect) {
    usetSelect = navigator?.language.split("-")[0];
  }

  const languages = i18n.localeCodes.value;
  const isSupport = languages?.includes(usetSelect);

  if (isSupport && i18n.locale.value !== usetSelect) {
    i18n.setLocale(usetSelect);
    // window.location.href = localePath('/'+$help().getPathURL(route.fullPath).string, usetSelect);

    await navigateTo(
      localePath("/" + $help().getPathURL(route.fullPath).string, usetSelect),
      {
        external: true,
      }
    );
  }

  const langInfo: any = i18n.localeProperties.value;

  if (langInfo.dir === "ltr") {
    document.body.classList.add("ltr");
  } else {
    document.body.classList.remove("ltr");
  }

  document.body?.classList?.forEach((e) => {
    if (languages?.includes(e) && e !== langInfo.code) {
      document.body.classList.remove(e);
    }
  });

  if (!document.body?.classList?.contains(langInfo.code)) {
    document.body.classList.add(langInfo.code);
  }

  useHead({
    title: $t("config.title") as string,
  });

  nuxt.hook("i18n:localeSwitched", () => {
    useHead({
      title: $t("config.title") as string,
    });
  });
});
