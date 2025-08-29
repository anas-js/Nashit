<script setup lang="ts">
import pageLayout from "~/transition/page.layout";
const storeLayoutData = useLayoutDataStore();
const router = useRouter();
const localePath = useLocalePath();
const route = router.currentRoute.value;

definePageMeta({
  name : "BaordSettingsPage",
  pageTransition: pageLayout,
  layout: "page",
});

const lang = "pages.board.settings";

const settings = reactive({
  nameBoard: storeLayoutData.title,
  notifs: storeLayoutData.data.value.notifs,
  deleteBoard: false,
  private: storeLayoutData.data.value.private,
});

const shows = reactive({
  nameBoard: false,
  deleteBoard: false,
  private: false,
  notifs: false,
  image: false,
});

const loading = reactive({
  nameBoard: false,
  notifs: false,
  deleteBoard: false,
  private: false,
  image: false,
});
const inputFile = ref();
const popImageCrop = ref();
const board = storeLayoutData.data.value.id;


 async function uploadImage() {
  popImageCrop.value.pop.close();
  loading.image = true;
  const fromData = new FormData();
  fromData.append("image", inputFile.value.files[0]);
  const data = popImageCrop.value.getCrop().getData();
  Object.keys(data).forEach((key) => {
    fromData.append(key, data[key]);
  });

  await $api.post(`${$app().BK_URL.nashit}/board/${board}/upload`, {
    body: fromData,
  }).then(() => {
    $msg({
      text : $t(`${lang}.image.done`),
      type : "ok"
    })
  }).catch(()=> {
    $msg({
      text : $t(`${lang}.image.error.cant`),
      type : "error"
    })
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
      } else if (image.height < 50 || image.width < 115) {
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

function sendSettings(name: keyof typeof settings) {
  const data = settings[name];

  if (name === "nameBoard") {
    if (!$filters.length({ item: data, max: 20, min: 1 })) {
      $msg({
        text: $t(`${lang}.nameBoard.error`),
        type: "error",
      });
      return;
    }
  }

  loading[name] = true;

  if (name === "deleteBoard") {
    $msg({
      text: $t(`${lang}.deleteBoard.sure`),
      type: "sure",
      btns: {
        t() {
          $api
            .post(`${$app().BK_URL.nashit}/board/${route.params.id}/settings`, { body: { [name]: true } })
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
        async t() {
          await $api
            .post(`${$app().BK_URL.nashit}/board/${route.params.id}/settings`, { body: { [name]: !data } })
            .then((_) => {
              settings[name] = !data;

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
    .post(`${$app().BK_URL.nashit}/board/${route.params.id}/settings`, { body: { [name]: data } })
    .then((_) => {
      shows[name] = false;

      loading[name] = false;

      if (name === "nameBoard") {
        storeLayoutData.title = data.trim();

        settings[name] = settings[name].trim();
      }

      $msg({
        text: $t("gl.msg.save"),
        type: "ok",
      });
    })
    .catch((e) => {
      if (name === "nameBoard") {
        settings[name] = storeLayoutData.title;
      }
      if (name === "notifs") {
        settings[name] = !settings[name];
      }

      shows[name] = false;

      loading[name] = false;


      if(e.data?.code === 1) {
        $msg({
        text: $t(`${lang}.notifs.error`),
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
</script>

<template>
  <div class="settings-page-course" data-width="small">
    <h1>{{ $t(`${lang}.title`) }}</h1>
    <div class="app">
      <div class="boxs">
        <div class="change-name">
          <div class="ctr">
            <div class="rigth">
              <h3>{{ $t(`${lang}.nameBoard.title`) }}</h3>
              <p v-if="!shows.nameBoard">{{ settings.nameBoard }}</p>
            </div>
            <div class="left">
              <button
                v-if="!shows.nameBoard"
                class="icon"
                @click="shows.nameBoard = true"
              >
                <i class="ri-pencil-line"></i>
              </button>
              <div v-else class="btns-action">
                <button
                  @click="
                    (shows.nameBoard = false),
                      (settings.nameBoard = storeLayoutData.title)
                  "
                >
                  <i class="ri-close-line"></i>
                </button>

                <button @click="sendSettings('nameBoard')">
                  <i class="ri-check-line"></i>
                </button>
              </div>
            </div>
          </div>

          <input
            v-if="shows.nameBoard"
            v-model="settings.nameBoard"
            type="text"
          />
          <full-loading v-if="loading.nameBoard"></full-loading>
        </div>
        <div>
          <div class="ctr">
            <div class="rigth">
              <h3>{{$t(`${lang}.image.title`)}}</h3>
              <p class="comment">{{$t(`${lang}.image.info`)}} (png, jpeg, jpg)</p>
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
                :min-size="{ width: 115, height: 50 }"
                :aspect-ratio="2.3"
                @on-close="loading.image = false"
                @crop-image="uploadImage"></PopImageCrop>
            </div>
          </div>
          <full-loading v-if="loading.image"></full-loading>
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
                @toggle="(settings.notifs = $event), sendSettings('notifs')"
              ></ElementsToogleButton>
            </div>
          </div>
          <full-loading v-if="loading.notifs"></full-loading>
        </div>
        <div>
          <div class="ctr">
            <div class="rigth">
              <h3 v-if="settings.private">
                {{ $t(`${lang}.private.privateBoard`) }}
              </h3>
              <h3 v-else>{{ $t(`${lang}.private.publicBoard`) }}</h3>
            </div>
            <div class="left">
              <button
                v-if="!shows.private"
                class="icon"
                @click="shows.private = true"
              >
                <i class="ri-pencil-line"></i>
              </button>
              <div v-else class="btns-action">
                <button @click="shows.private = false">
                  <i class="ri-close-line"></i>
                </button>

                <button class="auto" @click="sendSettings('private')">
                  <!-- <i class="ri-check-line"></i> -->
                  <template v-if="settings.private">
                    <i class="ri-earth-line space"></i
                    >{{ $t(`${lang}.private.public`) }}
                  </template>
                  <template v-else>
                    <i class="ri-lock-2-line space"></i
                    >{{ $t(`${lang}.private.private`) }}
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
              <h3>{{ $t(`${lang}.deleteBoard.title`) }}</h3>
            </div>
            <div class="left">
              <button
                v-if="!shows.deleteBoard"
                class="icon"
                @click="shows.deleteBoard = true"
              >
                <i class="ri-delete-bin-2-line"></i>
              </button>
              <div v-else class="btns-action">
                <button @click="shows.deleteBoard = false">
                  <i class="ri-close-line"></i>
                </button>

                <button @click="sendSettings('deleteBoard')">
                  <i class="ri-check-line"></i>
                </button>
              </div>
            </div>
          </div>
          <full-loading v-if="loading.deleteBoard"></full-loading>
        </div>
      </div>
    </div>
  </div>
</template>
