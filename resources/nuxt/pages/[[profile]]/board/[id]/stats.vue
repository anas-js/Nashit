<script setup lang="ts">
import pageLayout from "~/transition/page.layout";

definePageMeta({
  name: "BaordStatsPage",
  pageTransition: pageLayout,
  layout: "page",
});

const route = useRoute();

const res = await $api
  .get<any>(`${$app().BK_URL.nashit}${route.params.profile ? '/profile/'+route.params.profile : ''}/board/${route.params.id}/stats`)
  .catch(() => {
    throw createError({
      statusCode: 404,
      statusMessage: "Error",
      fatal: true,
    });
  });

const data = {
  ratio: {
    curr: res.ratio,
    exp: res.ratio,
    todayExp: res.ratio,
  },
  day: {
    full: res.day,
    curr: res.day,
  },
};
</script>

<template>
  <div class="stats-page-course" data-width="full">
    <h1>{{ $t("pages.board.stats.title") }}</h1>
    <div class="app">
      <PageItemsRate
        :colored="false"
        :labeled="false"
        :data="data"
      ></PageItemsRate>
    </div>
  </div>
</template>
