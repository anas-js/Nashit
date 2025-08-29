<script setup lang="ts">
definePageMeta({
  layout: "dashboard",
});
const route = useRoute();
const lang = "pages.dashboard.boards";

const boards = reactive({
  value : null
});
let end = true;

const page_res = $api.get<any>(`${$app().BK_URL.nashit}/boards`, {
  params: route.query,
}).then(e=> {
  e.data.map((e: any) => (e.loading = false));
  boards.value = e.data;
  end = !e.next_page_url
}).catch((e) => {
    if (e.statusCode === 422) {
      window.location.href = "/dashboard/boards";
    } else {
      throw createError({
        fatal: true,
        status: e.statusCode,
        message: e.data.message,
      });
    }
  });

const $auth = useAuth();
const router = useRouter();
const localePath = useLocalePath();
const filter = reactive({
  order: undefined,
});
let page = 1;

if (typeof route.query.order === 'string') {
  filter.order = route.query.order;
}








const listFilterShow = ref(false);
const loadMoreDataScroll = ref();
const loading_page = ref(false);
const showBtnLoadMore = ref(false);
const getNewDataStatus = ref(false);

const el = ref();
const { $anime } = useNuxtApp();


onBeforeUnmount(() => {
  if (loadMoreDataScroll.value) {
    window.removeEventListener("scroll", loadMoreDataScroll.value);
  }
});

// const images = ref();
let mount = false;
onMounted(() => {
  if (boards.value && !mount) {
    mount = true;
    $anime({
      targets: boards.value,
      ratio: [0, boards.value.ratio],
      delay(_: any, i: number) {
        $anime({
          targets: boards.value[i].details,
          sub: [0, boards.value[i].details.sub],
          task: [0, boards.value[i].details.task],
          done: [0, boards.value[i].details.done],
          easing: "easeInOutCirc",
          round: 1,
          delay: 1000,
          duration: 1000,
        });
        return i * 300 + 1000;
      },
      easing: "easeInOutCirc",
      round: 1,
      duration: 1000,
    });

    const boxsCourses = el.value.querySelectorAll(".box-board");

    if (boxsCourses[0]?.getBoundingClientRect && boards.value.length) {
      const boxCoursesCoordinates =
        boxsCourses[boxsCourses.length - 1]?.getBoundingClientRect();

      const fromTop = boxCoursesCoordinates?.top + window.scrollY;

      if (!end && fromTop + 150 < window.innerHeight) {
        showBtnLoadMore.value = true;
      }

      ScrollToLoadData();
    } else if (boards.value.length) {
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
//           `${$app().BK_URL.img}/board/${img.getAttribute("data-id")}`,
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

function board(item: any) {
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
              .post(`${$app().BK_URL.nashit}/board/${item.id}/settings`, { body: { private: status } })
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
        text: `${$t(`${lang}.msg.remove.sure.0`)} "${item.name}" ${$t(
          `${lang}.msg.remove.sure.1`
        )}`,
        type: "sure",
        btns: {
          t() {
            $api
              .post(`${$app().BK_URL.nashit}/board/${item.id}/settings`, { body: { delete: true } })
              .then((_res) => {
              
                boards.value = boards.value.filter((e: any) => e.id !== item.id)
              
                $msg({
                  text: $t(`${lang}.msg.remove.set`),
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
          url: `https://nashit.org/${$auth?.user?.username}/board/${item.id}/stats`,
        });
      } else if (navigator?.clipboard?.writeText) {
        navigator.clipboard.writeText(
          `https://nashit.org/${t.$auth?.user?.username}/board/${item.id}/stats`
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
    .querySelector(".box-board .list ul.active")!
    .classList.remove("active");
}

function showList() {
  document
    .querySelectorAll(".box-board .list ul.active")
    .forEach(
      (e) =>
        e !== (event as any).currentTarget.nextElementSibling &&
        e.classList.remove("active")
    );

  (event as any).currentTarget.nextElementSibling.classList.toggle("active");
}

function goToBoard(item: any) {
  router.push(localePath(`/board/${item.id}`));
}

function ScrollToLoadData() {
  let boxsCourses = el.value.querySelectorAll(".box-board");
  let boxCoursesCoordinates =
    boxsCourses[boxsCourses.length - 1]?.getBoundingClientRect();
  let loadMoreStatus = true;

  if (showBtnLoadMore.value === false && !end && boards.value.length) {
    async function loadMoreDataScrollFun() {
      
      boxsCourses = el.value?.querySelectorAll(".box-board");
      
      boxCoursesCoordinates =
        boxsCourses[boxsCourses?.length - 1]?.getBoundingClientRect();

      if (
        boxCoursesCoordinates.bottom - window.innerHeight <= 100 &&
        loadMoreStatus &&
        !getNewDataStatus.value
      ) {
        loadMoreStatus = false;
        await loadMoreData();
        loadMoreStatus = true;
      }
    };

    window.addEventListener("scroll", loadMoreDataScrollFun);
    loadMoreDataScroll.value = loadMoreDataScrollFun;
  }
}

async function loadMoreData() {
  getNewDataStatus.value = true;

  await $api
    .get(`${$app().BK_URL.nashit}/boards`, {
      params: {
        // form: boards.value.length,
        order: filter.order === undefined ? undefined : String(filter.order),
        page : page
        // type: this.filter.type,
      },
    })
    .then((res: any) => {

     
      boards.value = boards.value.concat(res.data);
      end = !res.next_page_url;

      getNewDataStatus.value = false;

      let boxsCourses = el.value.querySelectorAll(".box-board");

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
          boxsCourses = el.value.querySelectorAll(".box-board");

          if (boxsCourses.length === boards.value.length) {
            const boxCoursesCoordinates =
              boxsCourses[boxsCourses.length - 1]?.getBoundingClientRect();

            if (
              boxCoursesCoordinates.top + 150 + window.scrollY >
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
  // axios.post('/api/boards-dashborad.json', this.filter).then(res=> {
  // this.boards = res.data.boards;
  //  this.filter = res.data.filter;
  // })

  $api
    .get(`${$app().BK_URL.nashit}/boards`, {
      params: filter,
    })
    .then((res: any) => {
      page = 2;
      boards.value = res.data;
      listFilterShow.value = false;
      loading_page.value = false;

      router.push({
        query: {
          order: filter.order === undefined ? undefined : String(filter.order),
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

function addOrderFilter(order: string) {
  if (filter.order === order) {
    filter.order = undefined;
  } else {
    filter.order = order;
  }
}
</script>
<template>
  <div ref="el" class="boards-dashboard" >
    <div>
      <h2>{{ $t(`${lang}.title`) }}</h2>
      <div v-if="boards.value" class="active-boards">
        <div v-for="(item, index) in boards.value" :key="item.id" class="box-board">
          <div class="list">
            <button class="btn-list" @click="showList()">
              <i class="ri-more-2-line"></i>
            </button>
            <ul @click="closeList()">
              <li>
                <button @click="board(item).setStatus(!item.private)">
                  <template v-if="item.private">
                    <i class="ri-earth-line space"></i
                    >{{ $t(`${lang}.list.public`) }}
                  </template>
                  <template v-else>
                    <i class="ri-lock-2-line space"></i
                    >{{ $t(`${lang}.list.private`) }}
                  </template>
                </button>
              </li>
              <li v-if="!item.private">
                <button @click="board(item).sharing()">
                  <i class="ri-share-line"></i> {{ $t(`${lang}.list.share`) }}
                </button>
              </li>
              <li class="delete">
                <button @click="board(item).remove()">
                  <i class="ri-close-line"></i>{{ $t(`${lang}.list.remove`) }}
                </button>
              </li>
            </ul>
          </div>
          <div  @click="goToBoard(item)" class="image">
            <img @error="$event.target.remove()" :src="`${$app().BK_URL.img}/board/${item.id}`"  />
            <span class="rate">{{ item.ratio }}%</span>
            <span class="private-flag">
              <template v-if="item.private">
                <i class="ri-lock-2-line"></i>
              </template>
              <template v-else>
                <i class="ri-earth-line space"></i>
              </template>
            </span>
          </div>
          <div class="text">
            <h2 @click="goToBoard(item)">{{ item.name }}</h2>
            <p class="date">{{ item.date }}</p>
            <ul>
              <li>
                <i class="ri-file-copy-fill"></i
                ><span>{{ item.details.sub }}</span> {{ $t(`${lang}.sub`) }}
              </li>
              <li>
                <i class="ri-check-line"></i
                ><span>{{ item.details.task }}</span> {{ $t(`${lang}.task`) }}
              </li>
              <li>
                <i class="ri-indeterminate-circle-line"></i
                ><span>{{  item.details.task - item.details.done }}</span>
                {{ $t(`${lang}.taskLeft`) }}
              </li>
            </ul>
          </div>
          <full-loading v-if="item.loading"></full-loading>
        </div>
        <div class="filter" v-if="boards.value.length">
          <button
            class="btn"
            :class="[
              { active: listFilterShow },
              { filterIsWork: filter.order },
            ]"
            @click="listFilterShow = !listFilterShow"
          >
            {{ $t(`${lang}.filter.title`) }}<i class="ri-arrow-down-s-line"></i>
          </button>
          <div
            class="list-filter"
            :class="{ active: listFilterShow }"
            @click="filterSelect()"
          >
            <div class="order">
              <h3>{{ $t(`${lang}.filter.sort.title`) }}</h3>
              <ul>
                <li
                  :class="{ active: filter.order === 'old-new' }"
                  @click="addOrderFilter('old-new')"
                >
                  <i class="ri-arrow-right-up-line"></i
                  >{{ $t(`${lang}.filter.sort.oldToNew`) }}
                </li>
                <li
                  :class="{ active: filter.order === 'new-old' }"
                  @click="addOrderFilter('new-old')"
                >
                  <i class="ri-arrow-left-down-line"></i
                  >{{ $t(`${lang}.filter.sort.newToOld`) }}
                </li>
                <li
                  :class="{ active: filter.order === 'last-active' }"
                  @click="addOrderFilter('last-active')"
                >
                  <i class="ri-time-line"></i
                  >{{ $t(`${lang}.filter.sort.lastActive`) }}
                </li>
                <li
                  :class="{ active: filter.order === 'ratio' }"
                  @click="addOrderFilter('ratio')"
                >
                  <i class="ri-percent-line"></i
                  >{{ $t(`${lang}.filter.sort.rate`) }}
                </li>
              </ul>
            </div>
            <!-- <div class="type">
              <h3>{{ $t(`${lang}.filter.type.title`) }}</h3>
              <ul>
                <li
                  :class="{ active: filter.type.includes('done') }"
                  @click="addTypeFilter('done')"
                >
                  <i class="ri-checkbox-circle-line"></i>{{ $t(`${lang}.filter.type.done`) }}
                </li>
                <li
                  :class="{ active: filter.type.includes('not-done') }"
                  @click="addTypeFilter('not-done')"
                >
                  <i class="ri-checkbox-blank-circle-line"></i>{{ $t(`${lang}.filter.type.notDone`) }}
                </li>
              </ul>
            </div> -->
          </div>
        </div>
        <div v-if="!boards.value.length" class="no-contnet">
          <div>
            <img src="~/assets/images/index/floder.png" />
            <h3>{{ $t(`${lang}.notFound.title`) }}</h3>
            <p>
              {{ $t(`${lang}.notFound.comment.0`)
              }}<NuxtLink :to="localePath(`/start`)"
                ><i class="ri-add-circle-line"></i
                >{{ $t(`${lang}.notFound.comment.1`) }}</NuxtLink
              >
            </p>
          </div>
        </div>

        <button
          v-if="showBtnLoadMore"
          class="load-more"
          :disabled="getNewDataStatus"
          :class="{ load: getNewDataStatus }"
          @click="loadMoreData()"
        >
          <i class="ri-loader-4-line"></i>{{ $t(`gl.loading.more`) }}
        </button>

        <small-loading
          v-if="getNewDataStatus && !showBtnLoadMore"
        ></small-loading>
      </div>
      <div v-else class="box-loading">
        <div v-for="(item, index) in 6" :key="index" class="box-board">
          <div class="image">
            <span class="rate">00%</span>
          </div>
          <div class="text">
            <h2>دو زون</h2>
            <p class="date"></p>
            <ul>
              <li>
                <i class="ri-file-copy-fill"></i>
                <p></p>
              </li>
              <li>
                <i class="ri-check-line"></i>
                <p></p>
              </li>
              <li>
                <i class="ri-indeterminate-circle-line"></i>
                <p></p>
              </li>
            </ul>
          </div>
        </div>
        <!-- <p>يرجى الإنتظار...</p> -->
      </div>
      <!-- <p v-else>يرجى الإنتظار...</p> -->
    </div>
    <full-loading v-if="loading_page"></full-loading>
  </div>
</template>
