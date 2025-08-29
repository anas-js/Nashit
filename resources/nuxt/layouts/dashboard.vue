<script setup lang="ts">


const localePath = useLocalePath();
const lang = "layouts.dashboard";
</script>

<template>
  <div class="dashboard-layout">
    <div>
      <div class="rigth">
      <NuxtLink :to="localePath('/')"><ImageNashit  class="logo"></ImageNashit></NuxtLink>
      <ul>
        <li><NuxtLink :to="localePath('/dashboard/active')"><i class="ri-time-line"></i>{{ $t(`${lang}.list.active`) }}</NuxtLink></li>
        <li><NuxtLink :to="localePath('/dashboard/courses')"><i class="ri-apps-2-line"></i>{{ $t(`${lang}.list.courses`) }}</NuxtLink></li>
        <li><NuxtLink :to="localePath('/dashboard/boards')"><i class="ri-checkbox-circle-line"></i>{{ $t(`${lang}.list.boards`) }}</NuxtLink></li>
      </ul>
      <NuxtLink class="new-btn" :to="localePath('/start')"><i class="ri-add-circle-line"></i><span>{{ $t(`${lang}.btn`) }}</span></NuxtLink>
    </div>
    <div class="left">

    <div class="user">
    <PrefsBtns></PrefsBtns>
      <nav-user></nav-user>
    </div>
      

      <div>
        <h1>{{ $t(`${lang}.title`) }}</h1>
        <div class="content">
          <slot />
        </div>
      </div>
    </div>
    <div class="bottom">
      <ul>
        <li><NuxtLink :to="localePath('/dashboard/active')"><i class="ri-time-line"></i>{{ $t(`${lang}.list.active`) }}</NuxtLink></li>
        <li><NuxtLink :to="localePath('/dashboard/courses')"><i class="ri-apps-2-line"></i>{{ $t(`${lang}.list.courses`) }}</NuxtLink></li>
        <li><NuxtLink :to="localePath('/dashboard/boards')"><i class="ri-checkbox-circle-line"></i>{{ $t(`${lang}.list.boards`) }}</NuxtLink></li>
      </ul>
    </div>
    </div>
  </div>
</template>

