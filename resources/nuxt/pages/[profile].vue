<script setup lang="ts">
const lang = "pages.profile";
useAnimeScroll();
const router = useRouter();
const route = router.currentRoute.value;
const user = reactive({
  ...(await $api
    .get<{ username: string; name: string; description: string ,image_src:string}>(
      `${$app().BK_URL.juzr}/profile/${route.params.profile}`,
      {
        params: {
          platform: "nashit",
        },
      }
    )
    .catch(() => {
      throw createError({
        statusCode: 404,
        statusMessage: "User Not Found",
        fatal: true,
      });
    })),
  courses: null as any,
  boards: null as any,
});




// let user = [];

// $api.get('https://google.com/user').then(data => {
//   user = data;
// }).catch(e=> {
//   alert("TRy fsdf")
// });







const show = reactive({
  loadMoreCourse: false,
  loadMoreBoard: false,
});

const status = reactive({
  getNewDataCourse: false,
  getNewDataBoard: false,
});

// if (!user.username) {

// }

const localePath = useLocalePath();

definePageMeta({});

// const images = ref();
let mount = false;
onMounted(() => {
  if (mount) {
    return;
  }
  mount = true;
  $api
    .get<any>(`${$app().BK_URL.nashit}/profile/${route.params.profile}/courses`)
    .then((res) => {
      user.courses = res.data;
      show.loadMoreCourse = !!res.next_page_url;
      // setTimeout(() => {
      //   // getImages();
      // }, 100);
    })
    .catch((_) => {
      $msg({
        text: $t(`gl.msg.error.getData`),
        type: "error",
      });
    });

  $api
    .get<any>(`${$app().BK_URL.nashit}/profile/${route.params.profile}/boards`)
    .then((res) => {
      user.boards = res.data;
      show.loadMoreBoard = !!res.next_page_url;

      // setTimeout(() => {
      //   getImages();
      // }, 100);
    })
    .catch((_) => {
      $msg({
        text: $t(`gl.msg.error.getData`),
        type: "error",
      });
    });
});

// watch([()=>user.boards, ()=>user.courses],function () {

// })

// function getImages() {
//   if (!images.value) {
//     return;
//   }
//   const Reader = new FileReader();

//   images.value
//     .filter((e) => e.getAttribute("data-id"))
//     .forEach((img) => {
//       $api
//         .get<Blob>(
//           `${$app().BK_URL.img}/${img.getAttribute(
//             "data-type"
//           )}/${img.getAttribute("data-id")}`,
//           {}
//         )
//         .then((res) => {
//           Reader.readAsDataURL(res);
//           Reader.onload = function (e) {
//             img.src = e.target.result;
//             img.removeAttribute("data-id");
//           };
//         })
//         .catch(() => {});
//     });
// }
function sharing(item: any, type: any) {
  if (navigator.share) {
    navigator.share({
      title: item.name,
      url: `${$app().FR_URL.nashit}/${user.username}/${type}/${item.id}/stats`,
    });
  } else if (navigator?.clipboard?.writeText) {
    navigator.clipboard.writeText(
      `${$app().FR_URL.nashit}/${user.username}/${type}/${item.id}/stats`
    );

    $msg({
      text: $t(`gl.msg.share.linkCopied`),
      type: "ok",
    });
  } else {
    $msg({
      text: $t(`gl.msg.error.share`),
      type: "error",
    });
  }
}

let courses_page = 2;
let boards_page = 2;
async function loadMoreDataCourse() {
  status.getNewDataCourse = true;

  await $api
    .get<any>(
      `${$app().BK_URL.nashit}/profile/${route.params.profile}/courses`,
      {
        params: {
          page: courses_page,
        },
      }
    )
    .then((res) => {
      courses_page++;
      user.courses = user.courses.concat(res.data);
      if (!res.next_page_url) {
        show.loadMoreCourse = false;
      }
      // getImages();
      status.getNewDataCourse = false;
    })
    .catch((_) => {
      status.getNewDataCourse = false;

      $msg({
        text: $t(`gl.msg.error.getData`),
        type: "error",
      });
    });
}

async function loadMoreDataBoard() {
  status.getNewDataBoard = true;

  await $api
    .get<any>(
      `${$app().BK_URL.nashit}/profile/${route.params.profile}/boards`,
      {
        params: {
          page: boards_page,
        },
      }
    )
    .then((res) => {
      boards_page++;
      user.boards = user.boards.concat(res.data);
      if (!res.next_page_url) {
        show.loadMoreBoard = false;
      }
      // getImages();
      status.getNewDataBoard = false;
    })
    .catch((_) => {
      status.getNewDataBoard = false;

      $msg({
        text: $t(`gl.msg.error.getData`),
        type: "error",
      });
    });
}

function dayLeft(date: string) {
  const dayleft: number = Math.ceil(
    (Number(new Date(date)) - Number(new Date())) / 1000 / 60 / 60 / 24
  );

  if (dayleft < -9999) {
    return $t(`${lang}.daysLeft.late`);
  } else if (dayleft > 9999) {
    return $t(`${lang}.daysLeft.early`);
  } else {
    return dayleft;
  }
}

function goToCourse(item: any) {
  router.push(localePath(`/${user.username}/course/${item.id}/stats`));
}

function goToBoard(item: any) {
  router.push(localePath(`/${user.username}/board/${item.id}/stats`));
}
// auth: false,
</script>

<template>
  <div class="profile-page">
    <div class="background">
      <img src="~assets/images/background-profile.png" />
    </div>
    <div class="box-profile">
      <div class="box-info ai" anime-name="rigth-in" next-anime>
        <div class="image ai" anime-name="rigth-in" anime-delay="200" next-anime>
          <img @error="$event.target.src = `${$app().FR_URL.juzr}/images/user.png`" :src="`${$app().BK_URL.juzrImg}/user/${user.image_src}`"  />
        </div>
        <div class="text">
          <h1 class="ai" anime-name="rigth-in" anime-delay="400" next-anime>
            {{ user.name }}
          </h1>
          <p class="username ai" anime-name="rigth-in" anime-delay="600" next-anime>
            {{ user.username }}
          </p>
          <p class="description ai" anime-name="rigth-in" anime-delay="700">
            {{ user.description }}
          </p>
        </div>
      </div>

      <div class="box-courses box ai" anime-name="rigth-in">
        <h2>
          {{ $t(`${lang}.courses`) }}
          <small-loading v-if="!user.courses"></small-loading>
        </h2>
        <template v-if="user.courses">
          <div v-if="user.courses.length">
            <div
              v-for="(item, index) in user.courses"
             :key="item.id"
              class="course">
              <div @click="goToCourse(item)" class="background">
                <span v-if="!item.private" class="sharing-flag" @click="sharing(item, 'course')">
                  <i class="ri-share-line"></i>
                </span>
                <img
                  @error="$event.target.remove()"
                  :src="`${$app().BK_URL.img}/course/${item.id}`" />
              </div>
              <div class="text">
                <div class="rigth">
                  <h2 class="name" @click="goToCourse(item)">
                    {{ item.name }}
                  </h2>
                  <p class="date">{{ item.date_finish }}</p>
                  <p class="day-left">
                    ({{ dayLeft(item.date_finish) }}
                    {{
                      typeof dayLeft(item.date_finish) === "number"
                        ? $help().titleDay({
                            num: Number(dayLeft(item.date_finish)),
                          })
                        : undefined
                    }})
                  </p>
                </div>
                <div class="left">
                  <p class="rate">{{ item.ratio }}%</p>
                </div>
              </div>
            </div>
            <button
              v-if="show.loadMoreCourse"
              class="load-more"
              :disabled="status.getNewDataCourse"
              :class="{ load: status.getNewDataCourse }"
              @click="loadMoreDataCourse()">
              <i class="ri-loader-4-line"></i>
              {{ $t(`gl.loading.more`) }}
            </button>
          </div>
          <div v-else>
            <div class="no-contnet">
              <div>
                <img src="~/assets/images/index/floder.png" />
                <h3>{{ $t(`${lang}.private.course.title`) }}</h3>
                <p>{{ $t(`${lang}.private.course.comment`) }}</p>
              </div>
            </div>
          </div>
        </template>
      </div>

      <div class="box-boards box ai" anime-name="rigth-in">
        <h2>
          {{ $t(`${lang}.boards.title`) }}
          <small-loading v-if="!user.boards"></small-loading>
        </h2>
        <template v-if="user.boards">
          <div v-if="user.boards.length">
            <div
              v-for="(item, index) in user.boards"
              :key="item.id"
              class="board">
              <!-- @click="goToBoard(item)" -->
              <div @click="goToBoard(item)" class="image">
                <span v-if="!item.private" class="sharing-flag" @click="sharing(item, 'board')">
                  <i class="ri-share-line"></i>
                </span>
                <img
                  @error="$event.target.remove()"
                  :src="`${$app().BK_URL.img}/board/${item.id}`" />
                <span class="rate">{{ item.ratio }}%</span>
              </div>
              <div class="text">
                <h2 @click="goToBoard(item)">{{ item.name }}</h2>
                <p class="date">{{ item.date }}</p>
                <ul>
                  <li>
                    <i class="ri-file-copy-fill"></i>
                    <span>{{ item.details.sub }}</span>
                    {{ $t(`${lang}.boards.sub`) }}
                  </li>
                  <li>
                    <i class="ri-check-line"></i>
                    <span>{{ item.details.task }}</span>
                    {{ $t(`${lang}.boards.task`) }}
                  </li>
                  <li>
                    <i class="ri-indeterminate-circle-line"></i>
                    <span>{{ item.details.task - item.details.done }}</span>
                    {{ $t(`${lang}.boards.taskLeft`) }}
                  </li>
                </ul>
              </div>
            </div>
            <button
              v-if="show.loadMoreBoard"
              class="load-more"
              :disabled="status.getNewDataBoard"
              :class="{ load: status.getNewDataBoard }"
              @click="loadMoreDataBoard()">
              <i class="ri-loader-4-line"></i>
              {{ $t(`gl.loading.more`) }}
            </button>
          </div>
          <div v-else>
            <div class="no-contnet">
              <div>
                <img src="~/assets/images/index/floder.png" />
                <h3>{{ $t(`${lang}.private.boards.title`) }}</h3>
                <p>{{ $t(`${lang}.private.boards.comment`) }}</p>
              </div>
            </div>
          </div>
        </template>
      </div>
    </div>
  </div>
</template>
