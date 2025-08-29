<script setup lang="ts">
const $auth = useAuth();
const router = useRouter();

const loading = ref(false);
const lang = "pages.registration";

async function link() {
  loading.value = true;
  await $auth.platformLink().catch(() => {
    throw createError({
      statusCode: 404,
      statusMessage: "Error",
      fatal: true,
    });
  });
  router.push("/start");
}

definePageMeta({
  name: "RegistrationPage",
  registration: true,
  middleware: () => {
    const $auth = useAuth();
    if ($auth.user?.registered) {
      return navigateTo("/dashboard/active");
    }
  },
});
</script>
<template>
  <div class="registration-page container">
    <div class="box">
      <!-- <img class="logo" src="@/assets/images/full-logo.svg" /> -->
      <ImageNashit  class="logo"></ImageNashit>
      <p class="text">{{ $t(`${lang}.text`,{name : $auth.user?.name}) }}</p>
      <button :disabled="loading" class="start" @click="link"><SmallLoading v-if="loading"></SmallLoading><i v-else class="ri-arrow-right-line"></i><span>{{ $t(`${lang}.go`)}}</span></button>
      
    </div>
  </div>
</template>
