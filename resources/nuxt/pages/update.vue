<script setup lang="ts">
import pageLayout from "~/transition/page.layout";
const lang = "pages.update";
const updates = await $api
  .get<{
      version: string;
      date: string;
      name? : string;
      description: string;
      added: string[];
      removed: string[];
    }[]
  >(`${$app().BK_URL.nashit}/updates`)
  .catch(() => {
    throw createError({
      statusCode: 404,
      statusMessage: "Error",
      fatal: true,
    });
  });

 
const { $anime } = useNuxtApp();
const el = ref();

onMounted(() => {
  setTimeout(() => {
    el.value.querySelector(".open-box-btn")?.click();
  }, 100);
});

definePageMeta({
  name: "UpdatePage",
  pageTransition: pageLayout,
  layout: "page",
  offAuth: true,
});

function openBox() {
  const content = (
    event?.currentTarget as HTMLElement
  ).parentElement?.parentElement?.querySelector(".content-box") as HTMLElement;
  const box = (event?.currentTarget as HTMLElement).parentElement
    ?.parentElement as HTMLElement;
  const heightSet = box?.clientHeight + content?.clientHeight + 25;

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
}
</script>

<template>
  <div ref="el" class="update-page-global" data-width="medium">
    <h1>{{ $t(`${lang}.title`) }}</h1>
    <div class="app">
      <div class="boxs">
        <div v-for="(item, i) in updates" :key="i">
          <span v-if="i === 0" class="last-update">
            {{ $t(`${lang}.current`) }}
          </span>
          <div class="box">
            <div class="header">
              <h4 class="title">v{{ item.version }}</h4>
              <p v-if="item.name">{{ item.name }}</p>
              <p class="date">{{ item.date }}</p>
              <button class="open-box-btn" @click="openBox()">
                <i class="ri-arrow-down-s-line"></i>
              </button>
            </div>

            <div class="content-box">
              <p v-if="item.description">
                {{ item.description }}
              </p>
              <div v-if="item.added.length" class="added">
                <strong>{{ $t(`${lang}.added`) }}</strong>
                <ul>
                  <li
                    v-for="(added, index_added) in item.added"
                    :key="index_added + 'added'">
                    <i class="ri-add-circle-line"></i>
                    {{ added }}
                  </li>
                </ul>
              </div>
              <div v-if="item.removed.length" class="removed">
                <strong>{{ $t(`${lang}.removed`) }}</strong>
                <ul>
                  <li
                    v-for="(removed, index_removed) in item.removed"
                    :key="index_removed + 'added'">
                    <i class="ri-close-circle-line"></i>
                    {{ removed }}
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
        <p v-if="!updates.length">{{ $t(`${lang}.found`) }}</p>
      </div>
    </div>
  </div>
</template>
