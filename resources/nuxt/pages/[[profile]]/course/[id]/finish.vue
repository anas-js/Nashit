<script setup lang="ts">
import pageLayout from "~/transition/page.layout";

definePageMeta({
  name: "CourseFinishPage",
  pageTransition: pageLayout,
  layout: "page",
});

const router = useRouter();
const route = router.currentRoute.value;
const {$i18n} = useNuxtApp();
const rtl = computed(() => $i18n.localeProperties.value.dir === 'rtl');

const data = await $api
  .get<any>(`${$app().BK_URL.nashit}${route.params.profile ? '/profile/'+route.params.profile : ''}/course/${route.params.id}/finish`)
  .catch(() => {
    throw createError({
      statusCode: 404,
      statusMessage: "Error",
      fatal: true,
    });
  });

const localePath = useLocalePath();
const layoutDataStore = useLayoutDataStore();

if (data.end === false) {
  router.push(localePath("/dashboard/courses"));
}

const course = reactive(data.course);
const end = ref(data.end);

const lang = "pages.course.finish";
const shareText = ref($t(`pages.course.finish.box.shareText`));

function share() {
  if (navigator?.share) {
    navigator.share({
      url: $app().FR_URL.nashit,
      title: $t(`${lang}.box.share.navigator`) as string,
      text: getText(),
    });
  } else {
    $msg({
      text: $t(`${lang}.box.share.error`),
      type: "error",
    });
  }
}

function copy() {
  if (navigator?.clipboard?.writeText) {
    navigator.clipboard.writeText(getText());

    $msg({
      text: $t(`${lang}.box.copy`),
      type: "ok",
    });
  }
  // console.log(navigator);
}

function getText() {
  return replaceData(
    layoutDataStore.title,
    rtl.value ? course.startDate.split("-").reverse().join("-") : course.startDate,
    rtl.value ? course.endDate.date.split("-").reverse().join("-") : course.endDate.date,
    course.dayLeft.value,
    course.lessons,
    `"${$t(`config.names.nashit`)}"`,
    $t(`config.names.juzr`),
    $app().domain.juzr
  );
}

function getShareText() {
  return encodeURI(
    replaceData(
      layoutDataStore.title,
      rtl.value ? course.startDate.split("-").reverse().join("-") : course.startDate,
      rtl.value ? course.endDate.date.split("-").reverse().join("-") : course.endDate.date,
      course.dayLeft.value,
      course.lessons,
      `"${$t(`config.names.nashit`)}"`,
      $t(`config.names.juzr`),
      $app().domain.juzr
    )
  );
}

function replaceData(...args: any) {
  let text = shareText.value as string;
  args.forEach((element: any) => {
    text = text.replace("%%%", element);
  });

  return text;
}

// onMounted(() => {
//   console.log()
// })
</script>

<template>
  <div class="finish-page-course" data-width="medium">
    <h1>{{ $t(`${lang}.title`) }}</h1>
    <div class="app">
      <img class="flag" src="~assets/images/finsh-flag.png" />
      <div class="finish-box">
        <div class="top">
          <p>{{ $t(`${lang}.box.text.0`) }}</p>
          <h2>{{ layoutDataStore.title }}</h2>
          <p>
            {{ $t(`${lang}.box.text.1`) }}
          </p>
        </div>
        <div class="stats" @click="getShareText()">
          <div>
            <p>{{ $t(`${lang}.box.stats.start`) }}</p>
            <div class="pack">
              <p>{{ course.startDate }}</p>
            </div>
          </div>
          <div class="m-top">
            <p>{{ $t(`${lang}.box.stats.end`) }}</p>
            <div class="pack">
              <p>{{ course.endDate.date }}</p>
              <span v-if="course.endDate.exp" class="comment">
                {{ $t(`${lang}.box.stats.exp`) }} <span class="dir-rtl">{{ course.endDate.exp }}</span>
              </span>
            </div>
            <span v-if="course.endDate.early" class="notice">
              {{ $t(`${lang}.box.stats.earlyWith`) }}
              <b>{{ course.endDate.early }}%</b>
            </span>
          </div>
          <div>
            <p>{{ $t(`${lang}.box.stats.left`) }}</p>
            <div class="pack">
              <span v-if="course.dayLeft.notice" class="notice">
                <template v-if="course.dayLeft.notice.type === '+'">
                  {{ $t(`${lang}.box.stats.early`) }}
                </template>
                <template v-else>{{ $t(`${lang}.box.stats.late`) }}</template>
                <b>
                  <template v-if="course.dayLeft.notice.type === '-'">
                    -
                  </template>
                  {{ course.dayLeft.notice.value }}
                </b>
                {{ $help().titleDay({ num: course.dayLeft.notice.value }) }}
              </span>
              <p>{{ course.dayLeft.value }}</p>
            </div>
          </div>
          <div>
            <p>{{ $t(`${lang}.box.stats.lessons`) }}</p>
            <div class="pack">
              <p>{{ course.lessons }}</p>
            </div>
          </div>
        </div>
        <div class="share">
          <h2>{{ $t(`${lang}.box.share.title`) }}</h2>
          <div class="buttons">
            <a
              class="whatsapp"
              target="_blank"
              :href="`https://wa.me/?text=${getShareText()}`"
            >
              <i class="ri-whatsapp-line"></i>
              {{ $t(`${lang}.box.share.whatsapp`) }}
            </a>
            <a
              class="twitter"
              target="_blank"
              :href="`https://twitter.com/share?text=${getShareText()}%0A%0A%0A&hashtags=nashit_students,juzr`"
            >
              <i class="ri-twitter-line"></i>
              {{ $t(`${lang}.box.share.twitter`) }}
            </a>

            <a
              class="telegram"
              target="_blank"
              :href="`https://t.me/share/url?url=https://nashit.org&text=${getShareText()}`"
            >
              <i class="ri-telegram-line"></i>
              {{ $t(`${lang}.box.share.telegram`) }}
            </a>
            <a
              class="facebook"
              target="_blank"
              href="https://www.facebook.com/sharer/sharer.php?u=https://nashit.org"
            >
              <i class="ri-facebook-circle-line"></i>
              {{ $t(`${lang}.box.share.facebook`) }}
            </a>
            <button class="more" @click="share()">
              <i class="ri-share-line"></i>
              {{ $t(`${lang}.box.share.more`) }}
            </button>
            <button class="copy" @click="copy()">
              <i class="ri-clipboard-line"></i>
              {{ $t(`${lang}.box.share.copy`) }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
