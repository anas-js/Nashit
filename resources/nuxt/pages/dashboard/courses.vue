<script setup lang="ts">
definePageMeta({
  layout: "dashboard",
});

const route = useRoute();
const filter = reactive({
  order: typeof route.query.order === "string" ? route.query.order : undefined,
  type: [] as any[],
});
const router = useRouter();
// if (route.query.order) {
//   filter.order = route.query.order;
// }

if (route.query.type) {
  if (typeof route.query.type === "object") {
    filter.type = route.query.type as any[];
  } else {
    filter.type = [route.query.type];
  }
}
// console.log(filter.type);

const courses = reactive({
  value: null,
});

const page_res =  $api
  .get<any>(`${$app().BK_URL.nashit}/courses`, {
    params: {
      order: filter.order,
      "type[]": filter.type,
    },
  }).then(e => {
     e.data.map((e: any) => (e.loading = false));
    courses.value = e.data;

  })
  .catch((e) => {
    if (e.statusCode === 422) {
      // router.push();
      window.location.href = "/dashboard/courses";
    } else {
      throw createError({
        fatal: true,
        status: e.statusCode,
        message: e.data.message,
      });
    }
  });







const lang = "pages.dashboard.course";
const listFilterShow = ref(false);
const loadMoreDataScroll = ref();
const loading_page = ref(false);
const showBtnLoadMore = ref(false);
const getNewDataStatus = ref(false);
const el = ref();
const { $anime } = useNuxtApp();
const $auth = useAuth();

const localePath = useLocalePath();
let page = 1;

let end = !page_res.next_page_url;

onBeforeUnmount(() => {
  if (loadMoreDataScroll.value) {
    window.removeEventListener("scroll", loadMoreDataScroll.value);
  }
});
let mount = false;
// const images = ref();
onMounted(async () => {
  if (courses.value && !mount) {
    mount = true;
    $anime({
      targets: courses.value,
      ratio: [0, courses.value.ratio],
      delay(_: any, i: number) {
        return i * 300 + 1000;
      },
      round: 1,
      easing: "easeInOutCirc",
      duration: 1000,
    });

    const boxsCourses = el.value.querySelectorAll(".box-course");

    if (boxsCourses[0]?.getBoundingClientRect && courses.value.length) {
      const boxCoursesCoordinates =
        boxsCourses[boxsCourses.length - 1]?.getBoundingClientRect();

      const fromTop = boxCoursesCoordinates?.top + window.scrollY;

      if (!end && fromTop + 50 < window.innerHeight) {
        showBtnLoadMore.value = true;
      }

      ScrollToLoadData();
    } else if (courses.value.length) {
      showBtnLoadMore.value = true;
    }
    // getImages();
  }
});

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
//           `${$app().BK_URL.img}/course/${img.getAttribute("data-id")}`,
//           {}
//         )
//         .then((res) => {
//           Reader.readAsDataURL(res);
//           Reader.onload = function (e) {
//             img.src = e.target.result;
//             img.removeAttribute('data-id');
//           };
//         })
//         .catch(() => {});
//     });
// }

function course(item: any) {
  item.loading = true;
  return {
    setStatus(status: any) {
      $msg({
        text: `${$t(`${lang}.msg.status.sure.text.0`)} "${item.name}" ${$t(
          `${lang}.msg.status.sure.text.1`
        )} ${
          status
            ? $t(`${lang}.msg.status.sure.private`)
            : $t(`${lang}.msg.status.sure.public`)
        }`,
        type: "sure",
        btns: {
          t() {
            $api
              .post(`${$app().BK_URL.nashit}/course/${item.id}/settings`, {
                body: { private: status },
              })
              .then((_res) => {
                item.loading = false;
                item.private = status;

                $msg({
                  text: `${$t(`${lang}.msg.status.set.text`)} ${
                    status
                      ? $t(`${lang}.msg.status.set.private`)
                      : $t(`${lang}.msg.status.set.public`)
                  }`,
                  type: "ok",
                });
              })
              .catch((_) => {
                item.loading = false;

                $msg({
                  text: $t(`gl.msg.error.sendData`),
                  type: "error",
                });
              });
          },
          f() {
            item.loading = false;
          },
        },
      });
    },
    remove() {
      $msg({
        text: `${$t(`${lang}.msg.status.remove.sure.0`)} "${item.name}"  ${$t(
          `${lang}.msg.status.remove.sure.1`
        )}`,
        type: "sure",
        btns: {
          t() {
            $api
              .post(`${$app().BK_URL.nashit}/course/${item.id}/settings`, {
                body: {
                  delete: true,
                },
              })
              .then((_res) => {
                courses.value = courses.value.filter(
                  (e: any) => e.id !== item.id
                );
                $msg({
                  text: $t(`${lang}.msg.status.remove.set`),
                  type: "ok",
                });
              })
              .catch((_) => {
                item.loading = false;

                $msg({
                  text: $t(`gl.msg.error.sendData`),
                  type: "error",
                });
              });
          },
          f() {
            item.loading = false;
          },
        },
      });
    },
    sharing() {
      if (navigator.share) {
        navigator.share({
          title: item.name,
          url: `https://nashit.org/${$auth?.user?.username}/course/${item.id}/stats`,
        });
      } else if (navigator?.clipboard?.writeText) {
        navigator.clipboard.writeText(
          `https://nashit.org/${$auth?.user?.username}/course/${item.id}/stats`
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
      item.loading = false;
    },
  };
}

function closeList() {
  document
    .querySelector(".box-course .list ul.active")!
    .classList.remove("active");
}

function showList() {
  document
    .querySelectorAll(".box-course .list ul.active")
    .forEach(
      (e) =>
        e !== (event as any).currentTarget.nextElementSibling &&
        e.classList.remove("active")
    );

  (event as any).currentTarget.nextElementSibling.classList.toggle("active");
}

function goToCourse(item: any) {
  router.push(localePath(`/course/${item.id}/today`));
}

function ScrollToLoadData() {
  let boxsCourses = el.value.querySelectorAll(".box-course");
  let boxCoursesCoordinates =
    boxsCourses[boxsCourses.length - 1]?.getBoundingClientRect();
  let loadMoreStatus = true;

  if (showBtnLoadMore.value === false && !end && courses.value.length) {
    async function loadMoreDataScrollFun() {
      boxsCourses = el.value.querySelectorAll(".box-course");
      boxCoursesCoordinates =
        boxsCourses[boxsCourses.length - 1]?.getBoundingClientRect();

      if (
        boxCoursesCoordinates.bottom - window.innerHeight <= 100 &&
        loadMoreStatus &&
        !getNewDataStatus.value
      ) {
        loadMoreStatus = false;
        await loadMoreData();
        loadMoreStatus = true;
      }
    }
    window.addEventListener("scroll", loadMoreDataScrollFun);
    loadMoreDataScroll.value = loadMoreDataScrollFun;
  }
}

async function loadMoreData() {
  getNewDataStatus.value = true;

  await $api
    .get(`${$app().BK_URL.nashit}/courses`, {
      params: {
        // form: courses.value.length,
        order: filter.order === undefined ? undefined : String(filter.order),
        "type[]": filter.type,
        page: page,
      },
    })
    .then((res: any) => {
      end = !res.next_page_url;

      getNewDataStatus.value = false;
      courses.value = courses.value.concat(res.data);
      let boxsCourses = el.value.querySelectorAll(".box-course");

      if (end) {
        showBtnLoadMore.value = false;
        window.removeEventListener("scroll", loadMoreDataScroll.value);
      } else {
        page++;
      }

      if (
        showBtnLoadMore.value === true &&
        !end &&
        boxsCourses[0]?.getBoundingClientRect
      ) {
        const checkItem = setInterval(function () {
          boxsCourses = el.value.querySelectorAll(".box-course");

          if (boxsCourses.length === courses.value.length) {
            const boxCoursesCoordinates =
              boxsCourses[boxsCourses.length - 1]?.getBoundingClientRect();

            if (
              boxCoursesCoordinates.top + 50 + window.scrollY >
                window.innerHeight &&
              !end
            ) {
              showBtnLoadMore.value = false;
              ScrollToLoadData();
            }
            clearInterval(checkItem);
          }
        }, 100);
      }
      // getImages();
    })
    .catch((_) => {
      getNewDataStatus.value = false;
      window.scrollTo({ top: 0 });
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

function filterSelect() {
  loading_page.value = true;

  // !!! POST
  // axios.post('/api/courses-dashborad.json', this.filter).then(res=> {
  // this.courses = res.data.courses;
  //  this.filter = res.data.filter;
  // })

  $api
    .get(`${$app().BK_URL.nashit}/courses`, {
      query: {
        order: filter.order,
        "type[]": filter.type,
        // page: page_res.current_page,
      },
    })
    .then((res: any) => {
      page = 2;
      // console.log(res.data.courses);
      courses.value = res.data;
      // setTimeout(() => {
      //   courses.value = res.data;
      // }, 1);
      listFilterShow.value = false;
      loading_page.value = false;

      router.push({
        query: {
          order: filter.order === undefined ? undefined : String(filter.order),
          type: filter.type,
        },
      });
    })
    .catch((_) => {
      $msg({
        text: $t(`gl.msg.error.getData`),
        type: "error",
      });
    });
}

function addTypeFilter(type: string) {
  if (filter.type.includes(type)) {
    filter.type = filter.type.filter((e: any) => e !== type);
  } else {
    filter.type.push(type);
  }
}

function addOrderFilter(order: string) {
  if (filter.order === order) {
    filter.order = undefined;
  } else {
    filter.order = order;
  }
}

// async function getImage(id) {

//   return "11";
// }
</script>

<template>
  <div ref="el" class="courses-dashboard">
    <div>
      <h2>{{ $t(`${lang}.title`) }}</h2>
      <div v-if="courses.value" class="active-courses">
        <div
          v-for="(item, index) in courses.value"
          :key="item.id"
     
          class="box-course">
          <div class="list">
            <button class="btn-list" @click="showList()">
              <i class="ri-more-2-line"></i>
            </button>
            <ul @click="closeList()">
              <li>
                <button @click="course(item).setStatus(!item.private)">
                  <template v-if="item.private">
                    <i class="ri-earth-line space"></i>
                    {{ $t(`${lang}.list.public`) }}
                  </template>
                  <template v-else>
                    <i class="ri-lock-2-line space"></i>
                    {{ $t(`${lang}.list.private`) }}
                  </template>
                </button>
              </li>
              <li v-if="!item.private">
                <button @click="course(item).sharing()">
                  <i class="ri-share-line"></i>
                  {{ $t(`${lang}.list.share`) }}
                </button>
              </li>
              <li class="delete">
                <button @click="course(item).remove()">
                  <i class="ri-close-line"></i>
                  {{ $t(`${lang}.list.remove`) }}
                </button>
              </li>
            </ul>
          </div>
          <div class="background" @click="goToCourse(item)">
            <span class="private-flag">
              <template v-if="item.private">
                <i class="ri-lock-2-line"></i>
              </template>
              <template v-else>
                <i class="ri-earth-line space"></i>
              </template>
            </span>
            <!-- :data-id="item.id" @click="goToCourse(item)" -->
            <img
              @error="$event.target.remove()"
              :src="`${$app().BK_URL.img}/course/${item.id}`" />
          </div>
          <div class="text">
            <div class="rigth">
              <h2 @click="goToCourse(item)" class="name">{{ item.name }}</h2>
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
          <full-loading v-if="item.loading"></full-loading>
        </div>
        <!-- v-if="courses.value.length" -->
        <div class="filter">
          <button
            class="btn"
            :class="[
              { active: listFilterShow },
              { filterIsWork: filter.type.length || filter.order },
            ]"
            @click="listFilterShow = !listFilterShow">
            {{ $t(`${lang}.filter.title`) }}
            <i class="ri-arrow-down-s-line"></i>
          </button>
          <div
            class="list-filter"
            :class="{ active: listFilterShow }"
            @click="filterSelect()">
            <div class="order">
              <h3>{{ $t(`${lang}.filter.sort.title`) }}</h3>
              <ul>
                <li
                  :class="{ active: filter.order === 'old-new' }"
                  @click="addOrderFilter('old-new')">
                  <i class="ri-arrow-right-up-line"></i>
                  {{ $t(`${lang}.filter.sort.oldToNew`) }}
                </li>
                <li
                  :class="{ active: filter.order === 'new-old' }"
                  @click="addOrderFilter('new-old')">
                  <i class="ri-arrow-left-down-line"></i>
                  {{ $t(`${lang}.filter.sort.newToOld`) }}
                </li>
                <li
                  :class="{ active: filter.order === 'last-active' }"
                  @click="addOrderFilter('last-active')">
                  <i class="ri-time-line"></i>
                  {{ $t(`${lang}.filter.sort.lastActive`) }}
                </li>
                <li
                  :class="{ active: filter.order === 'ratio' }"
                  @click="addOrderFilter('ratio')">
                  <i class="ri-percent-line"></i>
                  {{ $t(`${lang}.filter.sort.rate`) }}
                </li>
              </ul>
            </div>
            <div class="type">
              <h3>{{ $t(`${lang}.filter.type.title`) }}</h3>
              <ul>
                <li
                  :class="{ active: filter.type.includes('done') }"
                  @click="addTypeFilter('done')">
                  <i class="ri-checkbox-circle-line"></i>
                  {{ $t(`${lang}.filter.type.done`) }}
                </li>
                <li
                  :class="{ active: filter.type.includes('not-done') }"
                  @click="addTypeFilter('not-done')">
                  <i class="ri-checkbox-blank-circle-line"></i>
                  {{ $t(`${lang}.filter.type.notDone`) }}
                </li>
              </ul>
            </div>
          </div>
        </div>
        <div v-if="!courses.value.length" class="no-contnet">
          <div>
            <img src="~/assets/images/index/floder.png" />
            <h3>
              {{ $t(`${lang}.notFound.title`) }}
              <template v-if="filter.type.length">
                {{ $t(`${lang}.filter.type.title`) }} =
                <template v-if="filter.type.includes('done')">
                  {{ $t(`${lang}.filter.type.done`) }}
                </template>
                <template v-if="filter.type.includes('not-done')">
                  {{ $t(`${lang}.filter.type.notDone`) }}
                </template>
              </template>
            </h3>
            <p>
              {{ $t(`${lang}.notFound.comment.0`) }}
              <NuxtLink :to="localePath(`/start`)">
                <i class="ri-add-circle-line"></i>
                {{ $t(`${lang}.notFound.comment.1`) }}
              </NuxtLink>
            </p>
          </div>
        </div>

        <button
          v-if="showBtnLoadMore"
          class="load-more"
          :disabled="getNewDataStatus"
          :class="{ load: getNewDataStatus }"
          @click="loadMoreData()">
          <i class="ri-loader-4-line"></i>
          {{ $t(`gl.loading.more`) }}
        </button>

        <small-loading
          v-if="getNewDataStatus && !showBtnLoadMore"></small-loading>
      </div>
      <div v-else class="box-loading">
        <div v-for="(item, index) in 6" :key="index" class="box-course">
          <div class="background"></div>
          <div class="text">
            <div class="rigth">
              <h2 class="name">دو زون</h2>
              <p class="date">2023-01-01</p>
              <p class="day-left">(0 يوم)</p>
            </div>
            <div class="left">
              <p class="rate">55%</p>
            </div>
          </div>
        </div>
        <!-- <p>يرجى الإنتظار...</p> -->
      </div>
    </div>
    <full-loading v-if="loading_page"></full-loading>
  </div>
</template>
