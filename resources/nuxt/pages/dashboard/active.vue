<script setup lang="ts">
definePageMeta({
  layout: "dashboard",
});

// const { courses, boards } = await $api.get<{ [key: string]: any }>(
//   "/api/acitve-items-dashboard.json"
// );

const courses = reactive({
  value : null
});

 $api
  .get<{ [key: string]: any }>(`${$app().BK_URL.nashit}/courses`, {
    params: {
      order: "last-active",
    },
  }).then(e=> {
    courses.value = e.data;
  })
  .catch((e) => {
    throw createError({
      fatal: true,
      status: e.statusCode,
      message: e.data.message,
    });
  });

const boards = reactive({
  value : null
});

 $api
  .get<{ [key: string]: any }>(`${$app().BK_URL.nashit}/boards`, {
    params: {
      order: "last-active",
    },
  }).then(e=> {
    boards.value = e.data;
  })
  .catch((e) => {
    throw createError({
      fatal: true,
      status: e.statusCode,
      message: e.data.message,
    });
  });




const { $anime } = useNuxtApp();
const router = useRouter();
const localePath = useLocalePath();
const lang = "pages.dashboard.active";

let mount = false;

onMounted(() => {
  if(mount) {
    return;
  }
  mount = true;
  if (courses.value) {
    $anime({
      targets: courses.value,
      ratio: [0, courses.value.ratio],
      delay(_: any, i: number) {
        return i * 300 + 1000;
      },
      round: 1,
      duration: 1000,
      easing: "easeInOutCirc"
    });
  }

  if (boards.value) {
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
  }
  // getImages();
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
//           `${$app().BK_URL.img}/${img.getAttribute("data-type")}/${img.getAttribute("data-id")}`,
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

function goToCourse(item: any) {
  router.push(localePath(`/course/${item.id}/today`));
}

function goToBoard(item: any) {
  router.push(localePath(`/board/${item.id}`));
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
</script>

<template>
  <div class="acitve-items-dashboard">
    <div>
      <h2>{{ $t(`${lang}.course.title`) }}</h2>
      <div v-if="courses.value" class="active-courses">
        <div
          v-for="(item, index) in courses.value"
          :key="index"
          class="box-course"
         
        >
          <div  @click="goToCourse(item)" class="background">
          <img @error="$event.target.remove()" :src="`${$app().BK_URL.img}/course/${item.id}`"  />
          </div>
          <div class="text">
            <div class="rigth">
              <h2  @click="goToCourse(item)" class="name">{{ item.name }}</h2>
              <p class="date">{{ item.date_finish }}</p>
              <p class="day-left">
                ({{ dayLeft(item.date_finish) }}
                {{
                  typeof dayLeft(item.date_finish) === "number"
                    ? $help().titleDay({ num: Number(dayLeft(item.date_finish)) })
                    : undefined
                }})
              </p>
            </div>
            <div class="left">
              <p class="rate">{{ item.ratio }}%</p>
            </div>
          </div>
        </div>
        <div v-if="!courses.value.length" class="no-contnet">
          <div>
            <img src="~/assets/images/index/floder.png" />
            <h3>{{ $t(`${lang}.course.notFound.title`) }}</h3>
            <p>
              {{ $t(`${lang}.course.notFound.comment.0`)
              }}<NuxtLink :to="localePath(`/start`)"
                ><i class="ri-add-circle-line"></i
                >{{ $t(`${lang}.course.notFound.comment.1`) }}</NuxtLink
              >
            </p>
          </div>
        </div>
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
    <div>
      <h2>{{ $t(`${lang}.board.title`) }}</h2>
      <div v-if="boards.value" class="active-board">
        <div
          v-for="(item, index) in boards.value"
          :key="index"
          class="box-board"
        
        >
          <div class="image" @click="goToBoard(item)" @error="$event.target.remove()" :src="`${$app().BK_URL.img}/board/${item.id}`">
            <img   />
            <span class="rate">{{ item.ratio }}%</span>
          </div>
          <div class="text">
            <h2   @click="goToBoard(item)">{{ item.name }}</h2>
            <p class="date">{{ item.date }}</p>
            <ul>
              <li>
                <i class="ri-file-copy-fill"></i
                ><span>{{ item.details.sub }}</span>
                {{ $t(`${lang}.board.sub`) }}
              </li>
              <li>
                <i class="ri-check-line"></i
                ><span>{{ item.details.task }}</span>
                {{ $t(`${lang}.board.task`) }}
              </li>
              <li>
                <i class="ri-indeterminate-circle-line"></i
                ><span>{{ item.details.task - item.details.done }}</span>
                {{ $t(`${lang}.board.taskLeft`) }}
              </li>
            </ul>
          </div>
        </div>
        <div v-if="!boards.value.length" class="no-contnet">
          <div>
            <img src="~/assets/images/index/floder.png" />
            <h3>{{ $t(`${lang}.board.notFound.title`) }}</h3>
            <p>
              {{ $t(`${lang}.board.notFound.comment.0`)
              }}<NuxtLink :to="localePath(`/start`)"
                ><i class="ri-add-circle-line"></i
                >{{ $t(`${lang}.board.notFound.comment.1`) }}</NuxtLink
              >
            </p>
          </div>
        </div>
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
    </div>
  </div>
</template>
