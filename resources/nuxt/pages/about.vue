<script setup lang="ts">
import pageLayout from "~/transition/page.layout";

const lang = "pages.about";

definePageMeta({
  name : "AboutPage",
  pageTransition: pageLayout,
  layout: "page",
  offAuth: true,
});
</script>

<template>
  <div class="about-page-global" data-width="medium">
    <h1>{{ $t(`${lang}.title`) }}</h1>
    <div class="app">
      <div class="content-box">
     
        <ImageNashit  class="logo"></ImageNashit>
        <p>{{ $t(`${lang}.description`) }}</p>
        <br />
        <br />
        <div class="share">
          <p>{{ $t(`${lang}.share`) }}</p>
          <ul>
            <li>
              <a target="_blank" href="https://www.instagram.com/juzr.official"><i class="ri-instagram-line"></i></a>
            </li>
            <li>
              <a target="_blank" href="https://x.com/juzr_official"><i class="ri-twitter-line"></i></a>
            </li>
          </ul>
        </div>

        <br />
        <p>{{ $t(`${lang}.report`) }}</p>
        <!-- <a target="_blank" href="https://www.instagram.com/juzr.official/">support@juzr.org</a> -->
      </div>
    </div>
  </div>
</template>
