<script setup lang="ts">
import pageLayout from "~/transition/page.layout";

definePageMeta({
  name: "ReadPage",
  pageTransition: pageLayout,
  layout: "page",
  offAuth: true,
});

const lang = "pages.read";
const { articles } = await $api
  .get<{ articles: [{ name: string; description: string; url: string }] }>(
    `${$app().BK_URL.juzr}/reads`
  )
  .catch(() => {
    throw createError({
      statusCode: 404,
      statusMessage: "Error",
      fatal: true,
    });
  });
</script>

<template>
  <div class="read-page-global" data-width="small">
    <h1>{{ $t(`${lang}.title`) }}</h1>
    <div class="app">
      <div class="boxs">
        <div  v-for="(item, index) in articles" :key="index">
          <div class="ctr">
            <div class="rigth">
              <h3>{{ item.name }}</h3>
              <p v-if="item.description">{{ item.description }}</p>
            </div>
            <div class="left">
              <a :href="item.url" target="_blank" class="icon">
                <i
                  v-if="$i18n.localeProperties.dir === 'rtl'"
                  class="ri-arrow-left-up-line"
                ></i>
                <i v-else class="ri-arrow-right-up-line"></i>
              </a>
            </div>
          </div>
        </div>
        <p v-if="!articles.length">{{ $t(`${lang}.found`) }}</p>
      </div>
    </div>
  </div>
</template>
