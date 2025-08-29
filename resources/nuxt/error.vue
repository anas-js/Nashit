<script setup lang="ts">
const lang = "layouts.error";
const localPath = useLocalePath();
</script>

<template>
  <div>
    <header-app></header-app>
    <div class="error-layout">
      <div class="container">
        <div class="box">
          <img src="~assets/images/find3.png" />
          <h1>404</h1>
          <p>{{ $t(`${lang}.text`) }}</p>
          <NuxtLink :to="localPath('/')" class="go-back"><i class="ri-home-5-line"></i>{{ $t(`${lang}.main`) }}</NuxtLink>
          <!-- <p v-if="error.message">{{ error.message }}</p> -->
        </div>
      </div>
    </div>
    <footer-app></footer-app>
  </div>
</template>
