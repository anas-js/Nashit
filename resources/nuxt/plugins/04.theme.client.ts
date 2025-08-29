import Cookies from "js-cookie";

export default defineNuxtPlugin((_) => {
  let usetSelect = Cookies.get("mode") || "light";
  const $auth = useAuth();
  const systemStore = useSystemStore();

  if ($auth.loggedIn) {
    if ($auth.user?.settings?.mode !== usetSelect) {
      if ($auth.user?.settings?.mode !== "auto") {
        usetSelect = $auth.user!.settings.mode;
        Cookies.set("mode", usetSelect,{
          domain: $app().domain.juzr,
          expires: 30
        });
      }
    }
  }



  document.body?.classList?.forEach((e) => {
    if (e.includes("mode") && e !== usetSelect) {
      document.body.classList.remove(e);
    }
  });

  if (!document.body.classList.contains(usetSelect)) {
    document.body.classList.add(usetSelect + "-mode");
  }

  if (systemStore.mode !== usetSelect) {
    systemStore.mode = usetSelect;
  }
});
