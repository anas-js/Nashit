<script setup lang="ts">
import Cookies from "js-cookie";
const $auth = useAuth();
const { $i18n } = useNuxtApp();
const localePath = useLocalePath();

const langList = ref();
const loadingLang = ref(false);
const loadingMode = ref(false);
const storeSystem = useSystemStore();
const dark = computed(() => storeSystem.mode === "dark");

async function updateLang(e: any) {
  langList.value.close();

  if (e.code === $i18n.locale.value) {
    return;
  }

  loadingLang.value = true;

  if ($auth.loggedIn) {
    await $api
      .post(`${$app().BK_URL.juzr}/user/update`, {
        body: {
          lang: e.code,
        },
      })
      .then((_) => {
        $auth.update((user) => {
          user.settings.lang = e.code;
          return user;
        });
      });
  }

  Cookies.set("lang", e.code, {
    domain: $app().domain.juzr,
    expires: 30,
  });

  $i18n.setLocale(e.code);

  $i18n.locales.value.forEach((l) => {
    document.body.classList.remove(l.code);
  });

  document.body.classList.add(e.code);
  // document.body.classList.forEach((e)=> {

  // });

  if (e.dir === "ltr") {
    document.body.classList.add("ltr");
  } else {
    document.body.classList.remove("ltr");
  }
  loadingLang.value = false;
}

async function updateMode() {
  loadingMode.value = true;

  if ($auth.loggedIn) {
    await $api
      .post(`${$app().BK_URL.juzr}/prefs/set`, {
        body: {
          mode: dark.value ? "light" : "dark",
        },
      })
      .then((_) => {
        $auth.update((user) => {
          user.settings.mode = dark.value ? "light" : "dark";
          return user;
        });
      });
  }

  if (dark.value) {
    Cookies.remove("mode", {
      domain: $app().domain.juzr,
    });
    storeSystem.mode = "light";
    document.body.classList.remove("dark-mode");
  } else {
    Cookies.set("mode", "dark", {
      domain: $app().domain.juzr,
      expires: 30,
    });
    storeSystem.mode = "dark";
    document.body.classList.add("dark-mode");
  }

  loadingMode.value = false;
}
</script>
<template>
  <div class="prefs-btns">
    <div class="change-lang">
      <button
        class="small-btn lang"
        :disabled="loadingLang"
        @click="$refs['langList'].toggle()">
        <SmallLoading v-if="loadingLang"></SmallLoading>
        <i v-else class="ri-earth-line"></i>
      </button>
      <ElementsList
        ref="langList"
        :hiddin="true"
        :list="$i18n.locales.value"
        @select="updateLang($event)"></ElementsList>
    </div>

    <button class="small-btn mode" :disabled="loadingMode" @click="updateMode">
      <SmallLoading v-if="loadingMode"></SmallLoading>
      <template v-else>
        <i v-if="!dark" class="ri-moon-clear-line"></i>
        <i v-else class="ri-sun-line"></i>
      </template>
    </button>
  </div>
</template>
