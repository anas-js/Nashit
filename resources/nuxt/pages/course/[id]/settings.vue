<script setup lang="ts">
import pageLayout from "~/transition/page.layout";

definePageMeta({
  name: "CourseSettingsPage",
  pageTransition: pageLayout,
  layout: "page",
});

const lang = "pages.course.settings";
const layoutDataStore = useLayoutDataStore();
const router = useRouter();
const localePath = useLocalePath();
const route = router.currentRoute.value;
const days = ref($t(`gl.week`));
const inSpace = layoutDataStore.data.value.inSpace;

const settings = reactive({
  name: layoutDataStore.title,
  notifs: layoutDataStore.data.value.notifs,
  delete: false,
  private: layoutDataStore.data.value.private,
  done_days: layoutDataStore.data.value.days,
  weekend: [...layoutDataStore.data.value.weekend].sort((a, b) => a - b),
});

// console.log(layoutDataStore.data.value);

const shows = reactive({
  name: false,
  delete: false,
  private: false,
  notifs: false,
  done_days: false,
  weekend: false,
  redist: false,
  image: false,
});

const loading = reactive({
  name: false,
  notifs: false,
  delete: false,
  private: false,
  done_days: false,
  weekend: false,
  redist: false,
  image: false,
});

const course = layoutDataStore.data.value.id;

function sendSettings(name: keyof typeof settings) {
  const data = settings[name];

  if (name === "done_days") {
    if (layoutDataStore.data.value.days_done_limit < data) {
      $msg({
        text: `${$t(`${lang}.done_days.limit`)} ${
          layoutDataStore.data.value.days_done_limit
        } ${$help().titleDay({
          num: layoutDataStore.data.value.days_done_limit,
        })}`,
        type: "error",
      });
      settings.done_days = layoutDataStore.data.value.days;
      return;
    }
  }

  if (name === "name") {
    if (!$filters.length({ item: data, max: 20, min: 1 })) {
      $msg({
        text: $t(`${lang}.nameCourse.error`),
        type: "error",
      });
      return;
    }
  }

  loading[name] = true;

  if (name === "delete") {
    $msg({
      text: $t(`${lang}.deleteCourse.sure`),
      type: "sure",
      btns: {
        t() {
          $api
            .post(
              `${$app().BK_URL.nashit}/course/${route.params.id}/settings`,
              { body: { [name]: !data } }
            )
            .then((_) => {
              router.push(localePath("/dashboard/courses"));
            })
            .catch((_) => {
              loading[name] = false;
              shows[name] = false;

              $msg({
                text: $t(`gl.msg.error.sendData`),
                type: "error",
              });
            });
        },
        f() {
          loading[name] = false;
          shows[name] = false;
        },
      },
    });

    return;
  }

  if (name === "private") {
    $msg({
      text: String(
        `${$t(`${lang}.private.sure`)} ${$t(
          `${lang}.private.${!data ? "private" : "public"}`
        )} ${$t(
          `${lang}.private.${!data ? "privateComment" : "publicComment"}`
        )}`
      ),
      type: "sure",
      btns: {
        t() {
          $api
            .post(
              `${$app().BK_URL.nashit}/course/${route.params.id}/settings`,
              { body: { [name]: !data } }
            )
            .then((_) => {
              settings[name] = !data;
              layoutDataStore.data.value.private = !data;
              $msg({
                text: $t("gl.msg.save"),
                type: "ok",
              });
            })
            .catch((_) => {
              $msg({
                text: $t(`gl.msg.error.sendData`),
                type: "error",
              });
            });
          loading[name] = false;
          shows[name] = false;
        },
        f() {
          loading[name] = false;
          shows[name] = false;
        },
      },
    });

    return;
  }

  $api
    .post(`${$app().BK_URL.nashit}/course/${route.params.id}/settings`, {
      body: { [name]: data },
    })
    .then((_) => {
      shows[name] = false;

      loading[name] = false;

      if (name === "name") {
        layoutDataStore.title = data.trim();

        settings[name] = settings[name].trim();
      } else if (name === "done_days") {
        layoutDataStore.data.value.days = data;
      } else {
        layoutDataStore.data.value[name] = data;
      }

      $msg({
        text: $t("gl.msg.save"),
        type: "ok",
      });
    })
    .catch((e) => {
      if (name === "name") {
        settings[name] = layoutDataStore.title;
      } else if (name === "notifs") {
        settings[name] = !settings[name];
        console.log(settings.notifs);
      } else {
        settings[name] = layoutDataStore.data.value[name];
      }

      shows[name] = false;

      loading[name] = false;

      if (e.data.code === 1) {
        $msg({
          text: $t(`${lang}.notifs.error`),
          type: "error",
        });
      } else if (e.data.code === 3) {
        $msg({
          text: $t(`${lang}.weekend.error`),
          type: "error",
        });
      } else if (e.data.code === 4) {
        $msg({
          text: `${$t(`${lang}.done_days.limit`)} ${
          layoutDataStore.data.value.days_done_limit
        } ${$help().titleDay({
          num: layoutDataStore.data.value.days_done_limit,
        })}`,
          type: "error",
        });
      } else {
        $msg({
          text: $t(`gl.msg.error.sendData`),
          type: "error",
        });
      }
    });
}

function sendWeekendDays() {
  if (settings.weekend.length > 6) {
    $msg({
      text: $t(`${lang}.weekend.limit`),
      type: "error",
    });
    return;
  }

  sendSettings("weekend");
}

function sendRedist() {
  loading.redist = true;
  shows.redist = true;
  $msg({
    text: $t(`${lang}.redist.sure`),
    type: "sure",
    btns: {
      t() {
        $api
          .post(
            `${$app().BK_URL.nashit}/course/${route.params.id}/lessons/redist`
          )
          .then((_) => {
            loading.redist = false;
            shows.redist = false;
            $msg({
              text: $t(`${lang}.redist.done`),
              type: "ok",
            });
          })
          .catch((_) => {
            loading.redist = false;
            shows.redist = false;

            $msg({
              text: $t(`gl.msg.error.sendData`),
              type: "error",
            });
          });
      },
      f() {
        loading.redist = false;
        shows.redist = false;
      },
    },
  });
}

const inputFile = ref();
const popImageCrop = ref();

async function uploadImage() {
  popImageCrop.value.pop.close();
  loading.image = true;
  const fromData = new FormData();
  fromData.append("image", inputFile.value.files[0]);
  const data = popImageCrop.value.getCrop().getData();
  Object.keys(data).forEach((key) => {
    fromData.append(key, data[key]);
  });

 await $api
    .post(`${$app().BK_URL.nashit}/course/${course}/upload`, {
      body: fromData,
    })
    .then(() => {
      $msg({
        text: $t(`${lang}.image.done`),
        type: "ok",
      });
    })
    .catch(() => {
      $msg({
        text: $t(`${lang}.image.error.cant`),
        type: "error",
      });
    });
  loading.image = false;
}

function openImage() {
  const file = inputFile.value.files[0];

  if (!file) {
    return;
  }

  if (
    file.size / 1024 / 1024 >= 10 ||
    !["image/png", "image/jpeg"].includes(file.type)
  ) {

    $msg({
      text: $t(`${lang}.image.error.type`),
      type: "error",
    });
  
    return;
  }
 
  loading.image = true;

  const reader = new FileReader();
  reader.readAsDataURL(file);
  reader.onload = function (e) {
    const image = new Image();
    image.src = e.target?.result as string;
    image.onload = function () {
      if (image.height > 5000 || image.width > 5000) {
        $msg({
          text: $t(`${lang}.image.error.big`),
          type: "error",
        });
        loading.image = false;
      } else if (image.height < 50 || image.width < 92) {
        $msg({
          text: $t(`${lang}.image.error.small`),
          type: "error",
        });
        loading.image = false;
      } else {
        popImageCrop.value.getCrop().replace(e.target?.result as string);

        popImageCrop.value.pop.show();
      }
    };
  };
}
</script>

<template>
  <div class="settings-page-course" data-width="small">
    <h1>{{ $t(`${lang}.title`) }}</h1>
    <div class="app">
      <div class="boxs">
        <div class="change-name">
          <div class="ctr">
            <div class="rigth">
              <h3>{{ $t(`${lang}.nameCourse.title`) }}</h3>
              <p v-if="!shows.name">{{ settings.name }}</p>
            </div>
            <div class="left">
              <button
                v-if="!shows.name"
                class="icon"
                @click="shows.name = true">
                <i class="ri-pencil-line"></i>
              </button>
              <div v-else class="btns-action">
                <button
                  @click="
                    (shows.name = false),
                      (settings.name = layoutDataStore.title)
                  ">
                  <i class="ri-close-line"></i>
                </button>

                <button @click="sendSettings('name')">
                  <i class="ri-check-line"></i>
                </button>
              </div>
            </div>
          </div>

          <input v-model="settings.name" v-if="shows.name" type="text" />
          <full-loading v-if="loading.name"></full-loading>
        </div>
        <div>
          <div class="ctr">
            <div class="rigth">
              <h3>{{ $t(`${lang}.image.title`) }}</h3>
              <p class="comment">
                {{ $t(`${lang}.image.info`) }} (png, jpeg, jpg)
              </p>
            </div>
            <div class="left">
              <button class="icon" @click="$refs['inputFile'].click()">
                <i class="ri-image-line"></i>
              </button>
              <input
                ref="inputFile"
                accept="image/png, image/jpeg"
                class="d-none"
                type="file"
                @change="openImage" />

              <PopImageCrop
                ref="popImageCrop"
                :min-size="{ width: 92, height: 50 }"
                :aspect-ratio="1.84"
                @on-close="loading.image = false"
                @crop-image="uploadImage"></PopImageCrop>
            </div>
          </div>
          <full-loading v-if="loading.image"></full-loading>
        </div>
        <div class="change-days">
          <div class="ctr">
            <div class="rigth">
              <h3>
                {{
                  $t(
                    `${lang}.done_days.title.${
                      inSpace ? "inSpace" : "outSpace"
                    }`
                  )
                }}
              </h3>
              <p v-if="!shows.done_days">{{ settings.done_days }}</p>
            </div>
            <div class="left">
              <button
                v-if="!shows.done_days"
                class="icon"
                @click="shows.done_days = true">
                <i class="ri-pencil-line"></i>
              </button>
              <div v-else class="btns-action">
                <button
                  @click="
                    (shows.done_days = false),
                      (settings.done_days = layoutDataStore.data.value.days)
                  ">
                  <i class="ri-close-line"></i>
                </button>

                <button @click="sendSettings('done_days')">
                  <i class="ri-check-line"></i>
                </button>
              </div>
            </div>
          </div>
          <div v-if="shows.done_days">
          <p class="comment" v-if="!inSpace">{{ $t(`${lang}.done_days.description`) }}</p> 
            <input v-model.number="settings.done_days" type="number" />
          </div>

          <full-loading v-if="loading.done_days"></full-loading>
        </div>
        <div>
          <div class="ctr">
            <div class="rigth">
              <h3>{{ $t(`${lang}.weekend.title`) }}</h3>
              <p v-if="!shows.weekend">
                {{
                  settings.weekend.map((e) => days[e]).join(` ${$t("gl.and")} `)
                }}
              </p>
            </div>
            <div class="left">
              <button
                v-if="!shows.weekend"
                class="icon"
                @click="shows.weekend = true">
                <i class="ri-pencil-line"></i>
              </button>
              <div v-else class="btns-action">
                <button
                  @click="
                    (shows.weekend = false),
                      (settings.weekend = [
                        ...layoutDataStore.data.value.weekend,
                      ].sort((a, b) => a - b))
                  ">
                  <i class="ri-close-line"></i>
                </button>

                <button @click="sendWeekendDays()">
                  <i class="ri-check-line"></i>
                </button>
              </div>
            </div>
          </div>

          <ElementsSelect
            v-if="shows.weekend"
            :select="settings.weekend"
            :multi="true"
            :elements="
              days.map((e, i) => {
                return { title: e, value: i };
              })
            "
            @on-select="settings.weekend = $event.sort((a, b) => a - b)" />

          <!-- <ElementsList
            v-if="shows.lang"
            :list="[
              ...$i18n.locales,
              { name: $t(`${lang}.autoLang`), code: 'auto' },
            ]"
            @select="changeLang($event)"
          /> -->

          <full-loading v-if="loading.weekend"></full-loading>
        </div>
        <div>
          <div class="ctr">
            <div class="rigth">
              <h3>{{ $t(`${lang}.notifs.title`) }}</h3>
            </div>
            <div class="left">
              <ElementsToogleButton
                :watch="true"
                :set="settings.notifs"
                @toggle="
                  (settings.notifs = $event), sendSettings('notifs')
                "></ElementsToogleButton>
            </div>
          </div>
          <full-loading v-if="loading.notifs"></full-loading>
        </div>
        <div>
          <div class="ctr">
            <div class="rigth">
              <h3 v-if="settings.private">
                {{ $t(`${lang}.private.privateCourse`) }}
              </h3>
              <h3 v-else>{{ $t(`${lang}.private.publicCourse`) }}</h3>
            </div>
            <div class="left">
              <button
                v-if="!shows.private"
                class="icon"
                @click="shows.private = true">
                <i class="ri-pencil-line"></i>
              </button>
              <div v-else class="btns-action">
                <button @click="shows.private = false">
                  <i class="ri-close-line"></i>
                </button>

                <button class="auto" @click="sendSettings('private')">
                  <!-- <i class="ri-check-line"></i> -->
                  <template v-if="settings.private">
                    <i class="ri-earth-line space"></i>
                    {{ $t(`${lang}.private.public`) }}
                  </template>
                  <template v-else>
                    <i class="ri-lock-2-line space"></i>
                    {{ $t(`${lang}.private.private`) }}
                  </template>
                </button>
              </div>
            </div>
          </div>
          <full-loading v-if="loading.private"></full-loading>
        </div>
        <div>
          <div class="ctr">
            <div class="rigth">
              <h3>{{ $t(`${lang}.redist.title`) }}</h3>
              <p>{{ $t(`${lang}.redist.description`) }}</p>
            </div>
            <div class="left">
              <button
                v-if="!shows.redist"
                class="icon"
                @click="shows.redist = true">
                <i class="ri-repeat-line"></i>
              </button>
              <div v-else class="btns-action">
                <button @click="shows.redist = false">
                  <i class="ri-close-line"></i>
                </button>

                <button @click="sendRedist()">
                  <i class="ri-check-line"></i>
                </button>
              </div>
            </div>
          </div>
          <full-loading v-if="loading.redist"></full-loading>
        </div>
        <div>
          <div class="ctr">
            <div class="rigth">
              <h3>{{ $t(`${lang}.deleteCourse.title`) }}</h3>
            </div>
            <div class="left">
              <button
                v-if="!shows.delete"
                class="icon"
                @click="shows.delete = true">
                <i class="ri-delete-bin-2-line"></i>
              </button>
              <div v-else class="btns-action">
                <button @click="shows.delete = false">
                  <i class="ri-close-line"></i>
                </button>

                <button @click="sendSettings('delete')">
                  <i class="ri-check-line"></i>
                </button>
              </div>
            </div>
          </div>
          <full-loading v-if="loading.delete"></full-loading>
        </div>
      </div>
    </div>
  </div>
</template>
