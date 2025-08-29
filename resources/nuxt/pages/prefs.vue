<script setup lang="ts">
import pageLayout from "~/transition/page.layout";
import Cookies from "js-cookie";

definePageMeta({
  name: "PrefsPage",
  pageTransition: pageLayout,
  layout: "page",
  offAuth: true,
});

const i18n = useI18n();
const lang = "pages.prefs";
const storeSystem = useSystemStore();
const $auth = useAuth();
const langName = ref(i18n.localeProperties.value.name as string);

const settings = reactive({
  lang: i18n.localeProperties.value,
  mode: storeSystem.mode,
});

// const langBrowser = navigator?.language.split("-")[0] || "ar";

const shows = reactive({
  lang: false,
  mode: false,
});

const loading = reactive({
  lang: false,
  mode: false,
});

function setLangName() {
  // const langObject = settings.lang;
  const text = ` - ${$t(`${lang}.autoLang`)}`;

  // if($auth.user.lang) {

  // }
  // langObject.code === langBrowser &&
  if (!Cookies.get("lang") && !langName.value.includes(text)) {
    langName.value += text;
  } else if (langName.value.includes(text) && Cookies.get("lang")) {
    langName.value = langName.value.substr(
      0,
      langName.value.length - text.length
    );
  }
}

onMounted(() => {
  setLangName();
});

async function setDarkMode(stats: boolean) {

    await sendSettings("mode");

  if (stats) {
    Cookies.set("mode", "dark", {
      domain: $app().domain.juzr,
      expires: 30
    });
    storeSystem.mode = "dark";
    document.body.classList.add("dark-mode");
  } else {
    Cookies.remove("mode");
    storeSystem.mode = "light";
    document.body.classList.remove("dark-mode");
  }
}

async function changeLang(lang: any) {
  // lang = JSON.parse(JSON.stringify(lang));
  let langBrowser = navigator?.language.split("-")[0] || "ar";
  const user = { ...$auth.user } as any;
  // const lang = lang.value;

  if (lang.code === "auto") {
    await sendSettings("lang", "auto");
   
    Cookies.remove("lang");
    const languages = i18n.availableLocales;
    const isSupport = languages?.includes(langBrowser);
    if (isSupport) {
      const langFind: any = i18n.locales?.value.find(
        (e: any) => e.code === langBrowser
      );

      settings.lang = langFind;
    } else {
      langBrowser = "ar";
      settings.lang = i18n.locales?.value.find(
        (e: any) => e.code === "ar"
      ) as any;
    }

    if (settings.lang.dir === "ltr") {
      document.body.classList.add("ltr");
    } else {
      document.body.classList.remove("ltr");
    }

    setLangName();
    
    if ($auth.loggedIn && user.ussername) {
      user.settings.lang = "auto";
      $auth.setUser(user);
    }

    i18n.setLocale(langBrowser);
  
  } else {
    await sendSettings("lang", lang.code);


    Cookies.set("lang", lang.code,{
      domain: $app().domain.juzr,
      expires: 30
    });
    settings.lang = lang as typeof settings.lang;

    if ($auth.loggedIn && user.ussername) {
      user.settings.lang = lang.code;
      $auth.setUser(user);
    }

    i18n.setLocale(lang.code);

    
    // console.log(settings.lang);
    setLangName();
    if (lang.dir === "ltr") {
      document.body.classList.add("ltr");
    } else {
      document.body.classList.remove("ltr");
    }
  }
}

async function sendSettings(name: keyof typeof settings, value?: any) {
  const data = arguments.length === 2 ? value : settings[name];
  if($auth.loggedIn) {
    loading[name] = true;

    await $api
    .post(`${$app().BK_URL.juzr}/prefs/set`, { body: { [name]: data } })
    .then((_) => {
      shows[name] = false;

      loading[name] = false;

      $msg({
        text: $t("gl.msg.save"),
        type: "ok",
      });
    })
    .catch((_) => {
    
      shows[name] = false;

      loading[name] = false;

      $msg({
        text: $t(`gl.msg.error.sendData`),
        type: "error",
      });

      throw createError("Error");
    });
  }
  shows[name] = false;
}
</script>
<template>
  <div class="settings-page-course" data-width="small">
    <h1>{{ $t(`${lang}.title`) }}</h1>
    <!-- <NuxtLink :to="switchLocalePath('en')">English</NuxtLink> -->
    <div class="app">
      <div class="boxs">
        <div>
          <div class="ctr">
            <div class="rigth">
              <h3>{{ $t(`${lang}.boxs.lang`) }}</h3>
              <p v-if="!shows.lang">{{ langName }}</p>
            </div>
            <div class="left">
              <button
                v-if="!shows.lang"
                class="icon"
                @click="shows.lang = true"
              >
                <i class="ri-pencil-line"></i>
              </button>
              <button
                v-else
                class="icon"
                @click="
                  (shows.lang = false), (settings.lang = $i18n.localeProperties)
                "
              >
                <i class="ri-close-line"></i>
              </button>
            </div>
          </div>

          <ElementsList
            v-if="shows.lang"
            :list="[
              ...$i18n.locales,
              { name: $t(`${lang}.autoLang`), code: 'auto' },
            ]"
            @select="changeLang($event)"
          />

          <full-loading v-if="loading.lang"></full-loading>
        </div>
        <div>
          <div class="ctr">
            <div class="rigth">
              <h3>{{ $t(`${lang}.boxs.dark`) }}</h3>
            </div>
            <div class="left">
              <ElementsToogleButton
                :set="settings.mode === 'dark'"
                @toggle="
                  (settings.mode = $event
                    ? (settings.mode = 'dark')
                    : (settings.mode = 'light')),
                    setDarkMode($event)
                "
              ></ElementsToogleButton>
            </div>
          </div>
          <full-loading v-if="loading.mode"></full-loading>
        </div>
      </div>
    </div>
  </div>
</template>
