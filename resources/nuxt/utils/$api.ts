import cookie from "js-cookie";
const option = {
  headers: {
    Accept: "application/json",
  },
  async onRequest({ request, options }) {
    const host = new URL(request).hostname;
    if (Object.values($app().domain).includes(host)) {
      // if (options.method == "POST") {
      options.credentials = "include";
      // }

      options.headers = new Headers(options.headers || {});
      options.headers.set("X-XSRF-TOKEN", String(cookie.get("XSRF-TOKEN")));
      // options.headers.set(
      //   "Authorization",
      //   String("Bearer " + cookie.get("juzr_token"))
      // );

      options.headers.set("language", cookie.get('lang') || navigator?.language?.split("-")[0]);
      options.headers.set("timezone", getTimeZoneName());
    }
  },
  async onResponseError({ request, options, response }) {
    const $auth = useAuth();
    if (response.status === 419) {
      await $api.get($app().BK_URL.csrf, {
        credentials: "include",
      });
    }
    if (response.status === 429) {
      $msg({
        text: $t(`utils.$api.many`),
        type: "error",
      });
    }

    if (response.status === 401 && $auth.loggedIn) {
      await $cache().clear();
      $local.delete("offline");
      $local.delete("user");
      window.location.reload();
    }
    
    if (response.status === 503) {
      await navigateTo("/503",{
         external : true
       });
     }
  },
};

function getTimeZoneName() {
  try {
    return Intl?.DateTimeFormat()?.resolvedOptions()?.timeZone || "Asia/Riyadh";
  } catch {
    return "Asia/Riyadh";
  }
}

export const $api = {
  get: $fetch.create({
    ...option,
    method: "GET",
  }),
  post: $fetch.create({
    ...option,
    method: "POST",
    // credentials: "include",
  }),
};
