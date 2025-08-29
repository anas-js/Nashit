<script setup lang="ts">
import pageLayout from "~/transition/page.layout";

definePageMeta({
  name: "CourseStatsPage",
  pageTransition: pageLayout,
  layout: "page",
});
const router = useRouter();
const route = router.currentRoute.value;


const data = await $api
  .get<any>(`${$app().BK_URL.nashit}${route.params.profile ? '/profile/'+route.params.profile : ''}/course/${route.params.id}/stats`)
  .catch(() => {
    throw createError({
      statusCode: 404,
      statusMessage: "Error",
      fatal: true,
    });
  });

const localePath = useLocalePath();

if (data.end) {
  router.push(
    localePath({
      name: "CourseFinishPage",
      params: route.params,
    })
  );
}
</script>
<template>
  <div class="stats-page-course" data-width="full">
    <h1>{{ $t("pages.course.stats.title") }}</h1>
    <div class="app">
      <PageItemsRate :data="data"></PageItemsRate>
    </div>
  </div>
</template>
