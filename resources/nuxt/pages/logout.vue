<script setup lang="ts">
import cookie from "js-cookie";
definePageMeta({
  logout: true,
  offAuth: true,
});

const fromJuzr = cookie.get("fromJuzr");
const logout = cookie.get("logout");
const $auth = useAuth();
const localPath = useLocalePath();

if (fromJuzr) {
  $auth.logout();
} else if (logout) {
  await navigateTo(`${$app().FR_URL.juzr}/logout`, {
    external: true,
  });
} else {
  await navigateTo(localPath('/'));
}
</script>
<template>
  <FullLoading></FullLoading>
</template>
