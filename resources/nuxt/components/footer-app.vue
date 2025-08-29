<script setup lang="ts">
const lang = "components.footerApp";
const localePath = useLocalePath();
const systemStore = useSystemStore();
const dark = computed(() => systemStore.mode === "dark");
// console.log(dark.value);
</script>

<template>
  <footer class="footer-app">
    <div class="container">
      <div class="row">
        <div class="about-us col-lg-6">
          <div>
            <ImageNashit
              class="logo"
              :class="{ ai: $route.path === '/' }"
              anime-name="rigth-in"
              next-anime
              anime-before="500"></ImageNashit>
            <p
              :class="{ ai: $route.path === '/' }"
              anime-name="rigth-in"
              anime-delay="200"
              next-anime>
              {{ $t(`${lang}.about.text`) }}
            </p>
            <p
              class="from-juzr"
              :class="{ ai: $route.path === '/' }"
              anime-name="rigth-in"
              anime-delay="300"
              next-anime>
              {{ $t(`${lang}.about.from`) }}
              <NuxtLink :to="$app().FR_URL.juzr" :external="true">
                <ImageJuzr :dark="dark"></ImageJuzr>
              </NuxtLink>
            </p>
            <ul class="social">
              <li><NuxtLink to="https://www.instagram.com/juzr.official" :external="true"><i class="ri-instagram-line"></i></NuxtLink></li>
              <li><NuxtLink to="https://x.com/juzr_official" :external="true"><i class="ri-twitter-x-line"></i></NuxtLink></li>
            </ul>
            <b
              :class="{ ai: $route.path === '/' }"
              anime-name="rigth-in"
              anime-delay="400"
              next-anime>
              {{ new Date().getFullYear() }}
            </b>
          </div>
        </div>
        <div class="pages col-lg-3 list">
          <h3
            :class="{ ai: $route.path === '/' }"
            anime-name="rigth-in"
            anime-delay="500"
            next-anime>
            {{ $t(`${lang}.pages.title`) }}
          </h3>
          <ul>
            <li
              :class="{ ai: $route.path === '/' }"
              anime-name="rigth-in"
              anime-delay="600"
              next-anime>
              <NuxtLink :to="localePath(`${$app().FR_URL.juzr}/register`)">
                {{ $t(`${lang}.pages.signup`) }}
              </NuxtLink>
            </li>
            <li
              :class="{ ai: $route.path === '/' }"
              anime-name="rigth-in"
              anime-delay="700"
              next-anime>
              <NuxtLink :to="localePath(`${$app().FR_URL.juzr}/login`)">
                {{ $t(`${lang}.pages.login`) }}
              </NuxtLink>
            </li>
            <li
              :class="{ ai: $route.path === '/' }"
              anime-name="rigth-in"
              anime-delay="800"
              next-anime>
              <NuxtLink :to="localePath('/dashboard/active')">
                {{ $t(`${lang}.pages.dashboard`) }}
              </NuxtLink>
            </li>
            <!-- <li :class="{ai : $route.path === '/'}" anime-name="rigth-in" anime-delay="900" next-anime><NuxtLink :to="`${$app().FR_URL.juzr}/register`">{{ $t(`${lang}.pages.createAccount`) }}</NuxtLink></li> -->
            <li
              :class="{ ai: $route.path === '/' }"
              anime-name="rigth-in"
              anime-delay="1000"
              next-anime>
              <NuxtLink :to="localePath('/start')">
                {{ $t(`${lang}.pages.createCourse`) }}
              </NuxtLink>
            </li>
          </ul>
        </div>
        <!-- <div class="Important col-lg-2 list">
          <h3
            :class="{ ai: $route.path === '/' }"
            anime-name="rigth-in"
            anime-delay="1100"
            next-anime>
            {{ $t(`${lang}.imp.title`) }}
          </h3>
          <ul>

          </ul>
        </div> -->
        <div class="juzr col-lg-3 list">
          <h3
            :class="{ ai: $route.path === '/' }"
            anime-name="rigth-in"
            anime-delay="1200"
            next-anime>
            {{ $t(`${lang}.plat.title`) }}
          </h3>
          <ul>
            <li
              :class="{ ai: $route.path === '/' }"
              anime-name="rigth-in"
              anime-delay="1300"
              next-anime>
              <NuxtLink :to="localePath($app().FR_URL.juzr)">
                {{ $t(`${lang}.plat.website`) }}
              </NuxtLink>
            </li>
            <li
              :class="{ ai: $route.path === '/' }"
              anime-name="rigth-in"
              anime-delay="1400"
              next-anime>
              <NuxtLink :to="localePath(`${$app().FR_URL.juzr}/products`)">
                {{ $t(`${lang}.plat.apps`) }}
              </NuxtLink>
            </li>
            <li
              :class="{ ai: $route.path === '/' }"
              anime-name="rigth-in"
              anime-delay="1500"
              next-anime>
              <NuxtLink :to="localePath(`${$app().FR_URL.juzr}/pages/privacy`)">
                {{ $t(`${lang}.imp.pp`) }}
              </NuxtLink>
            </li>
            <li
              :class="{ ai: $route.path === '/' }"
              anime-name="rigth-in"
              anime-delay="1600"
              next-anime>
              <NuxtLink :to="localePath(`${$app().FR_URL.juzr}/pages/terms`)">
                {{ $t(`${lang}.imp.tc`) }}
              </NuxtLink>
            </li>

            <li
              :class="{ ai: $route.path === '/' }"
              anime-name="rigth-in"
              anime-delay="1700"
              next-anime>
              <NuxtLink :to="localePath(`${$app().FR_URL.juzr}/credits`)">
                {{ $t(`${lang}.imp.credits`) }}
              </NuxtLink>
            </li>
          </ul>
        </div>
      </div>
      <div class="row">
      <p class="anas col-lg-12">{{ $t(`${lang}.anas.0`) }} <NuxtLink to="https://instagram.com/anas.repo" target="_blank" :external="true">@{{ $t(`${lang}.anas.1`) }}</NuxtLink></p>
      </div>
    </div>
  </footer>
</template>
