<script setup lang="ts">
import pageLayout from "~/transition/page.layout";

const lang = "pages.install";
const { $anime } = useNuxtApp();

definePageMeta({
    name: "InstallPage",
  layout: "page",
  pageTransition: pageLayout,
  offAuth : true 
});



function openBox() {
  const eventTarget = event?.currentTarget as HTMLElement;
  const content = eventTarget.parentElement?.parentElement?.querySelector(
    ".content-box"
  ) as HTMLElement;
  const box = eventTarget?.parentElement?.parentElement;
  const heightSet = (box?.clientHeight || 0) + content?.clientHeight + 25;

  if (!box) {
    return;
  }

  if (box.getAttribute("anime")) {
    return;
  }

  box.setAttribute("anime", "true");
  if (box.classList.contains("active")) {
    box.classList.remove("active");
    $anime({
      targets: box,
      height: Number(box.getAttribute("data-height")),
      duration: 500,
      easing: "easeInOutCirc",
      complete() {
        content.style.position = "";
        content.style.padding = "0 15px";
        box.removeAttribute("anime");
      },
    });

    $anime({
      targets: box.querySelector(".open-box-btn"),
      rotateZ: "0deg",
      duration: 500,
      easing: "easeInOutCirc",
    });

    box.removeAttribute("data-height");
  } else {
    box.classList.add("active");
    box.setAttribute("data-height", String(box.clientHeight!));

    $anime({
      targets: box,
      height: heightSet,
      duration: 500,
      easing: "easeInOutCirc",
      complete() {
        $anime.set(content, {
          position: "static",
          height: 0,
        });
        content.style.height = "";
        content.style.padding = "0";
        box.removeAttribute("anime");
      },
    });

    $anime({
      targets: box.querySelector(".open-box-btn"),
      rotateZ: "180deg",
      duration: 500,
      easing: "easeInOutCirc",
    });
  }

  // setTimeout(() => {

  // }, 450);
}


</script>

<template>
  <div class="install-page-global" data-width="medium">
    <h1>{{ $t(`${lang}.title`) }}</h1>
    <div class="app">
      <div class="boxs">
        <div class="box">
          <div class="header">
            <h4 class="title">{{ $t(`${lang}.pc.title`) }}</h4>
            <button class="open-box-btn" @click="openBox()">
              <i class="ri-arrow-down-s-line"></i>
            </button>
          </div>

          <div class="content-box">
            <p>{{ $t(`${lang}.pc.text.0`) }}</p>
            <img src="~assets/images/install/SearchFailed.svg" />
            <p>{{ $t(`${lang}.pc.text.1`) }}</p>
            <p>{{ $t(`${lang}.pc.text.2`) }}</p>
            <img src="~assets/images/install/popupInstall.svg" />
          </div>
        </div>

        <div class="box">
          <div class="header">
            <h4 class="title">{{ $t(`${lang}.android.title`) }}</h4>
            <button class="open-box-btn" @click="openBox()">
              <i class="ri-arrow-down-s-line"></i>
            </button>
          </div>

          <div class="content-box">
            <p>
              {{ $t(`${lang}.android.text.0`) }}
              <i class="ri-more-2-line icon"></i>
              {{ $t(`${lang}.android.text.1`) }}
            </p>
            <img src="~assets/images/install/android/1.svg" />
            <img src="~assets/images/install/android/2.svg" />
            <img src="~assets/images/install/android/3.svg" />
            <p>{{ $t(`${lang}.android.text.2`) }}</p>
          </div>
        </div>

        <div class="box">
          <div class="header">
            <h4 class="title">{{ $t(`${lang}.iphone.title`) }}</h4>
            <button class="open-box-btn" @click="openBox()">
              <i class="ri-arrow-down-s-line"></i>
            </button>
          </div>

          <div class="content-box">
            <p>{{ $t(`${lang}.iphone.text.0`) }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
