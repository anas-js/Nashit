<script setup lang="ts">
// import { NuxtLink } from '#build/components';
import Cookies from "js-cookie";
const lang = "components.headerApp";
const $auth = useAuth();
// const { $i18n } = useNuxtApp();
const localePath = useLocalePath();

// const langList = ref();
// const loadingLang = ref(false);
// const loadingMode = ref(false);
// const storeSystem = useSystemStore();
// const dark = computed(()=> storeSystem.mode === "dark");

// async function updateLang(e : any) {

//   langList.value.close();

//   if(e.code === $i18n.locale.value) {
//     return;
//   }

//   loadingLang.value = true;

//   if ($auth.loggedIn) {
//     await $api.post(`${$app().BK_URL.juzr}/user/update`, {
//       body: {
//         lang: e.code,
//       },
//     }).then(_=> {
//       $auth.update((user) => {
//         user.settings.lang = e.code;
//         return user;
//       })
//     });
//   }

//   Cookies.set("lang", e.code, {
//     domain: $app().domain.juzr,
//     expires: 30,
//   });

//   $i18n.setLocale(e.code);

//   if (e.dir === "ltr") {
//     document.body.classList.add("ltr");
//   } else {
//     document.body.classList.remove("ltr");
//   }
//   loadingLang.value = false;

// }

// async function updateMode() {
//   loadingMode.value = true

//   if ($auth.loggedIn) {
//     await $api.post(`${$app().BK_URL.juzr}/prefs/set`, {
//       body: {
//         mode: dark.value ? "light" : "dark",
//       },
//     }).then(_=> {
//       $auth.update((user) => {
//         user.settings.mode = dark.value ? "light" : "dark";
//         return user;
//       })
//     });
//   }

//   if (dark.value) {
//     Cookies.remove("mode");
//     storeSystem.mode = "light";
//     document.body.classList.remove("dark-mode");

//   } else {
//     Cookies.set("mode", "dark", {
//       domain: $app().domain.juzr,
//       expires: 30
//     });
//     storeSystem.mode = "dark";
//     document.body.classList.add("dark-mode");
//   }

//   loadingMode.value = false
// }
// const el = ref();
// function showBtns() {
//   el.value.querySelectorAll(".buttons a").forEach((ele) => {
//     if (ele.classList.contains("d-block")) {
//       ele.classList.remove("d-block");
//     } else {
//       ele.classList.add("d-block");
//     }
//   });
// }

async function to(to: string) {
  Cookies.set(
    "redirect_to",
    JSON.stringify({
      path: '/',
      from: "nashit",
    }),
    {
      domain: $app().domain.juzr,
      expires: 1,
      // secure : true,
      // sameSite : "strict",
    }
  );

  return navigateTo($app().FR_URL.juzr+localePath(to), {
    external: true,
  });
}
</script>
<template>
  <div ref="el" class="container header-app">
    <NuxtLink :to="localePath('/')">
      <!-- <img
        
        src="~/assets/images/full-logo.svg" /> -->
      <ImageNashit class="logo"></ImageNashit>
    </NuxtLink>

    <div class="left">
      <PrefsBtns></PrefsBtns>
      <!-- <div class="change-lang">
        <button class="small-btn lang" :disabled="loadingLang" @click="$refs['langList'].toggle()">
        <SmallLoading v-if="loadingLang"></SmallLoading>
          <i v-else class="ri-earth-line"></i>
        </button>
        <ElementsList
          ref="langList"
          :hiddin="true"
          :list="$i18n.locales.value"
          @select="updateLang($event)">
          </ElementsList>
      </div>

      <button class="small-btn mode" :disabled="loadingMode" @click="updateMode">
        <SmallLoading v-if="loadingMode"></SmallLoading>
        <template v-else>
          <i v-if="!dark" class="ri-moon-clear-line"></i>
          <i v-else class="ri-sun-line"></i>
        
        </template>
        </button> -->

      <div v-if="!$auth.loggedIn" class="buttons">
        <a class="login" @click="to(`/login`)">
          {{ $t(`${lang}.buttons.login`) }}
        </a>
        <a class="register" @click="to(`/register`)">
          {{ $t(`${lang}.buttons.signup`) }}
        </a>
      </div>
      <nav-user v-else></nav-user>
    </div>
  </div>
</template>
