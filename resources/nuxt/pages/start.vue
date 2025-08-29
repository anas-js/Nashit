<script setup lang="ts">
const stage = ref(1);
const steps = ref(0);
const type = ref();
const loading = ref(false);
const lang = "pages.start";
const el = ref();
const { $anime, $i18n } = useNuxtApp();
const router = useRouter();
const localePath = useLocalePath();
const limits = await $api.get<any>(`${$app().BK_URL.nashit}/limits`).catch(() => {
    throw createError({
      statusCode: 404,
      statusMessage: "Error",
      fatal: true,
    });
  });;

let moving = false;

const btn_next = ref();
const btn_back = ref();
const barStepsEle = ref();

// types
const courseComp = ref();
const boardComp = ref();
const selectType = ref();
const trans = useNuxtApp().$i18n.t;

watch(type, (to) => {
  if (to) {
    switch (to) {
      case "course": {
        selectType.value = courseComp.value;
        break;
      }
      case "board": {
        selectType.value = boardComp.value;
        break;
      }
    }
    steps.value = selectType.value.getSteps() + 1;

    next();
  }
});

onMounted(() => {
  const heightEle = el.value.querySelector(".content .elements")?.clientHeight;
  $anime.set(".content .elements", {
    height: heightEle,
  });
});

function valid() {
  if (type.value) {
    return selectType.value.check();
  } else {
    return false;
  }
}

function setHeight(heightObject: { h: number; type: string }) {
  switch (heightObject.type) {
    case "+":
      $anime({
        targets: el.value.querySelector(".content > .elements"),
        height: `+=${heightObject.h}`,
        easing: "easeInOutCubic",
        duration: 500,
      });
      break;
    case "-":
      $anime({
        targets: el.value.querySelector(".content > .elements"),
        height: `-=${heightObject.h}`,
        easing: "easeInOutCubic",
        duration: 500,
      });
      break;
  }
}

async function next() {
  if (!valid() || moving) {
    return;
  }

  moving = true;
  // console.log(stage.value,steps.value);
  if (stage.value !== steps.value) {
    
    if (stage.value === 1) {
      if (
        (limits[`${type.value}s_number`] + 1) >
        limits.limits[`${type.value}s_limit`]
      ) {
        moving = false;
        // `لا يمكنك انشاء ${$t(`${lang}.elements.catg.${type.value}`)} لقد تم تعدي الحد وهو  ${limits.limits[`${type.value}s_limit`]} لإنشاء قائمة جديد , قم بحذف ${$t(`${lang}.elements.catg.${type.value}`)} لا تحتاجها.`
        $msg({
          text : trans(`${lang}.error.limit`,{
            type: $t(`${lang}.elements.catg.${type.value}`),
            limit : limits.limits[`${type.value}s_limit`]
          }),
          type : "sure",
          time : 15000
        });
        type.value = null;
        return;
      }
      // console.log(type);
      // await $api(`${$app().BK_URL.nashit}/limit`)
      btn_next.value.classList.add("visible");
      $anime({
        targets: btn_next.value,
        translateY: [50, 0],
        translateX: ["-50%", "-50%"],
        opacity: [0, 1],
        easing: "easeInOutCubic",
      });

      btn_back.value.classList.add("visible");
      $anime({
        targets: btn_back.value,
        translateX: [50, 0],
        opacity: [0, 1],
        easing: "easeInOutCubic",
      });
    }

    const toLeft = el.value.querySelector(".content > div > div")?.clientWidth;

    const nextElement = el.value.querySelectorAll(
      `.type-element.${type.value} > div`
    )[stage.value - 1];

    let heightSize = nextElement?.clientHeight;
    // let widthSize = nextElement?.clientWidth;

    if (heightSize === 0) {
      await new Promise((resolve, _reject) => {
        const getHeight = setInterval(() => {
          heightSize = nextElement?.clientHeight;

          if (heightSize !== 0) {
            clearInterval(getHeight);
            resolve(null);
          }
        }, 100);
      });
    }

    $anime({
      targets: el.value.querySelector(".content > .elements"),
      translateX: `${
        $i18n.localeProperties.value.dir === "rtl" ? "+" : "-"
      }=${toLeft}`,
      height: heightSize,
      easing: "easeInOutCubic",
      duration: 500,
      complete() {
        moving = false;
      },
    });

    stage.value++;
    barSteps();
  } else {
    loading.value = true;

    await selectType.value.sendData();

    // await new Promise((resolve) => {
    //   setTimeout(() => {
    //     resolve(true);
    //   }, 2000);
    // });

    if (type.value) {
      switch (type.value) {
        case "course": {
          router.push(localePath("/dashboard/courses"));

          break;
        }
        case "board": {
          router.push(localePath("/dashboard/boards"));
        }
      }
    }
  }
}

async function back() {
  if (moving) {
    return;
  }

  moving = true;
  const toLeft = el.value.querySelector(".content > div > div")?.clientWidth;

  let backElement = el.value.querySelectorAll(
    `.type-element.${type.value} > div`
  )[stage.value - 3];

  if (stage.value === 2) {
    backElement = el.value.querySelector(".choose-task > div") as HTMLElement;

    $anime({
      targets: btn_next.value,
      translateY: [0, 50],
      translateX: ["-50%", "-50%"],
      opacity: [1, 0],
      // duration : 1000,
      duration: 300,
      easing: "easeInOutCubic",
      complete() {
        btn_next.value.classList.remove("visible");
      },
    });

    $anime({
      targets: btn_back.value,
      // translateY: [50, 0],
      translateX: [0, 50],
      opacity: [1, 0],
      duration: 300,
      easing: "easeInOutCubic",
      complete() {
        btn_back.value.classList.remove("visible");
      },
    });
  }

  let heightSize = backElement?.clientHeight;

  if (heightSize === 0) {
    await new Promise((resolve, _reject) => {
      const getHeight = setInterval(() => {
        heightSize = backElement?.clientHeight;

        if (heightSize !== 0) {
          clearInterval(getHeight);
          resolve(null);
        }
      }, 100);
    });
  }

  $anime({
    targets: el.value.querySelector(".content > .elements"),
    translateX: `${
      $i18n.localeProperties.value.dir === "rtl" ? "-" : "+"
    }=${toLeft}`,
    height: heightSize,
    easing: "easeInOutCubic",
    duration: 500,
    complete() {
      if (stage.value === 1) {
        type.value = null;
      }
      moving = false;
    },
  });

  stage.value--;
  barSteps(stage.value === 1 ? 0 : undefined);
}

function barSteps(width?: number) {
  let barSize = (100 / steps.value) * stage.value;
  if (width !== undefined) {
    barSize = width;
  }

  $anime({
    targets: barStepsEle.value,
    easing: "easeInOutCirc",
    width: `${barSize}%`,
  });
}
</script>

<template>
  <div ref="el" class="start-page">
    <div class="container">
      <div class="main">
        <h1>{{ $t(`${lang}.title`) }}</h1>
        <div class="content">
          <FullLoading v-if="loading"></FullLoading>
          <div ref="barStepsEle" class="bar-steps"></div>
          <div class="elements">
            <div class="choose-task">
              <div>
                <h2>{{ $t(`${lang}.elements.title`) }}</h2>
                <div class="boxs">
                  <div
                    :class="{ active: type === 'course' }"
                    class="course"
                    @click="type = 'course'"
                  >
                    <img src="~/assets/images/index/rocket.png" />
                    <p>{{ $t(`${lang}.elements.catg.course`) }}</p>
                  </div>
                  <div
                    :class="{ active: type === 'board' }"
                    class="board"
                    @click="type = 'board'"
                  >
                    <img src="~/assets/images/index/done.png" />
                    <p>{{ $t(`${lang}.elements.catg.board`) }}</p>
                  </div>
                </div>
              </div>
            </div>
            <TypeStartCourse
              v-show="type === 'course'"
              ref="courseComp"
              :stage="stage"
              :limits="limits.limits"
              @setHeight="setHeight($event)"
            ></TypeStartCourse>

            <TypeStartBoard
              v-show="type === 'board'"
              ref="boardComp"
              :stage="stage"
            ></TypeStartBoard>
          </div>
          <button ref="btn_next" class="btn-next hiddin" @click="next()">
            <span v-if="stage !== steps">
              {{ $t("gl.next")
              }}<template v-if="$i18n.localeProperties.value.dir === 'rtl'">
                <i class="ri-arrow-left-line"></i>
              </template>
              <template v-else>
                <i class="ri-arrow-right-line"></i>
              </template>
            </span>
            <span v-else>
              {{ $t("gl.done") }}<i class="ri-check-line"></i>
            </span>
          </button>
          <button ref="btn_back" class="btn_back hiddin" @click="back()">
            <template v-if="$i18n.localeProperties.value.dir === 'ltr'">
              <i class="ri-arrow-left-line"></i>
            </template>
            <template v-else> <i class="ri-arrow-right-line"></i> </template
            >{{ $t("gl.back") }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
