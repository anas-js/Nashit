<script setup lang="ts">
// import Cookies from "js-cookie";
const lang = "components.navUser";
const $auth = useAuth();
const localePath = useLocalePath();
// const { $i18n } = useNuxtApp();

const logoutLoading = ref(false);
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
//     await $api.post(`${$app().BK_URL.juzr}/user/update`, {
//       body: {
//         mode: dark.value ? "light" : "dark",
//       },
//     }).then(_=> {
//       $auth.update((user) => {
//         user.settings.mode =  dark.value ? "light" : "dark";
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
</script>

<template>
  <div class="nav-user">
    <div class="top">
      <img  @error="$event.target.src = `${$app().FR_URL.juzr}/images/user.png`" :src="`${$app().BK_URL.juzrImg}/user/${$auth.user?.image_src}`"/>
      <p>{{ $auth.user?.name }}</p>
    </div>

    <div class="show">
      <div class="bottom">
      <ul>
        <li>
          <NuxtLink :to="localePath(`/${$auth.user?.username}`)">
            <i class="ri-user-line"></i>
            {{ $t(`${lang}.list.profile`) }}
          </NuxtLink>
        </li>
        <li>
          <NuxtLink :to="localePath('/dashboard/active')">
            <i class="ri-dashboard-2-line"></i>
            {{ $t(`${lang}.list.dashboard`) }}
          </NuxtLink>
        </li>
        <hr />
        <li class="juzr">
          <NuxtLink :to="localePath($app().FR_URL.juzr + '/account')" :external="true">
            <i class="ri-arrow-right-up-line"></i>
            {{ $t(`${lang}.list.myAccount`) }}
          </NuxtLink>
        </li>
      </ul>
      <button  class="logout" @click="logoutLoading = true,$auth.logout()">
        <i class="ri-logout-circle-r-line"></i>
        {{ $t(`${lang}.list.logout`) }}
      </button>
      <FullLoading v-if="logoutLoading"></FullLoading>
    </div>
    <div class="btns">
      <PrefsBtns></PrefsBtns>
      <!-- <div class="change-lang">
        <button class="small-btn lang mobile" :disabled="loadingLang" @click="$refs['langList'].toggle()">
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

      <button class="small-btn mode mobile" :disabled="loadingMode" @click="updateMode">
        <SmallLoading v-if="loadingMode"></SmallLoading>
        <template v-else>
          <i v-if="!dark" class="ri-moon-clear-line"></i>
          <i v-else class="ri-sun-line"></i>
        
        </template>
        </button> -->
        <!-- <button class="small-btn mode">
        <i class="ri-sun-line"></i>
      </button> -->
    </div>
    </div>

  </div>
</template>
