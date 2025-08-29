<script setup lang="ts">
import pageLayout from "~/transition/page.layout";

definePageMeta({
  name: "CourseTodayPage",
  pageTransition: pageLayout,
  layout: "page",
});

const router = useRouter();
const route = router.currentRoute.value;
const trans = useNuxtApp().$i18n.t;
const page_res = await $api
  .get<any>(`${$app().BK_URL.nashit}${route.params.profile ? '/profile/'+route.params.profile : ''}/course/${route.params.id}/lessons/today`)
  .catch(() => {
    throw createError({
      statusCode: 404,
      statusMessage: "Error",
      fatal: true,
    });
  });
const localePath = useLocalePath();

if (page_res.end) {
  router.push(
    localePath({
      name: "CourseFinishPage",
      params: route.params,
    })
  );
}


// if (data.course.end) {
//   router.push(
//     localePath(
//       `${route.params.profile ? "/" + route.params.profile : ""}/course/${
//         route.params.id
//       }/finish`
//     )
//   );
// }
if (page_res.end) {
  router.push(
    localePath({
      name: "CourseFinishPage",
      params: route.params,
    })
  );
}

const lessons = reactive({
  value: page_res.data,
});

lessons.value = lessons.value.map((e: any) => {
  e.loading = false;
  return e;
});

const more = ref(!!page_res.next_page_url);
let page = 2;

// const course = reactive({
//   value : page_res.course
// });

const lang = "pages.course.today";

const shows = reactive({
  doneAllLessons: false,
  loadingAddNewLes: false,
  addNewLesBtn: true,
  loadingMoreLessons: false,
});

const item = reactive({
  value: {},
});

const temps = reactive({
  value: {
    lesson: {
      name: null,
    },
    index: null,
  },
});

const showNoLessons = computed(() => lessons.value.length === 0);

onBeforeMount(() => {
  isDoneAll();
});

const boxs = ref();
onMounted(() => {
  let listiner: EventListener;
  if (more.value) {
    function loadMore() {
      if (navigator.onLine) {
        if (
          boxs.value.offsetHeight + boxs.value.scrollTop >=
            boxs.value.scrollHeight - 40 &&
          !shows.loadingMoreLessons
        ) {
          shows.loadingMoreLessons = true;

          $api
            .get<any>(
              `${$app().BK_URL.nashit}${route.params.profile ? '/profile/'+route.params.profile : ''}/course/${route.params.id}/lessons/today`,
              {
                params: {
                  page: page,
                  cache: false,
                },
              }
            )
            .then(async (res) => {
              await $cache().edit(
                [
                  {
                    req: `${$app().BK_URL.nashit}${route.params.profile ? '/profile/'+route.params.profile : ''}/course/${
                      route.params.id
                    }/lessons/today`,
                    searchIn: (data) => data,
                    // target: (e, i) => i === indexLesson,
                  },
                ],
                (offlieData) => {
                  offlieData.data = offlieData.data.concat(res.data);
                  return offlieData;
                }
              );
              page++;
              let data = res.data;
              data = data.map((e: any) => {
                e.loading = false;
                return e;
              });

              lessons.value = lessons.value.concat(data);
              more.value = !!res.next_page_url;

              // course.value = data.course;
              shows.loadingMoreLessons = false;
              
              if (!more.value) {
                boxs.value.removeEventListener("scroll", listiner);
              }
            })
            .catch((_) => {
              shows.loadingMoreLessons = false;

              $msg({
                text: $t(`gl.msg.error.getData`),
                type: "error",
              });
            });
        }
      }
    }
    listiner = loadMore;
    boxs.value.addEventListener("scroll", loadMore);
  }
});

// export default Vue.extend({

function setNewLesToDay() {
  shows.loadingAddNewLes = true;
  $api
    .post<any>(`${$app().BK_URL.nashit}/course/${route.params.id}/extra`)
    .then((res) => {
      if (res.end) {
        router.push(
          localePath({
            name: "CourseFinishPage",
            params: route.params,
          })
        );
      }

      const lesson = res;
      lesson.new = true;
      lesson.loading = false;

      lessons.value.push(lesson);
      shows.loadingAddNewLes = false;
      isDoneAll();
    })
    .catch((_) => {
      shows.loadingAddNewLes = false;

      $msg({
        text: $t(`gl.msg.error.getData`),
        type: "error",
      });
    });
}

async function setDone(lesson: any, indexLesson: number) {
  lesson.loading = true;

  if (!navigator.onLine) {
    await $cache({
      name: "CoursesTodayPage",

      done: async () => {
        lesson.done = !lesson.done;
        $ofun(lesson).path({
          path: "process.set",
          value: $help().getDateISO(),
        });
        isDoneAll();
        // console.log('done');
        await $cache().edit(
          [
            {
              req: `${$app().BK_URL.nashit}/course/${route.params.id}/lessons`,
              searchIn: (data) => data.lessons.data,
              target: (e) => e.id === lesson.id,
            },
          ],
          (item) => {
            item.done = lesson.done;
            console.log(item.done);
            if (item.done) {
              item.date.done = $help().getDateISO();
            } else {
              item.date.done = null;
            }
            return item;
          }
        );
        lesson.loading = false;
      },
      err: () => {
        lesson.loading = false;

        $msg({
          text: $t('gl.msg.error.sendData'),
          type: "error",
        });
      },
    }).edit(
      [
        {
          req: `${$app().BK_URL.nashit}/course/${
            route.params.id
          }/lessons/today`,
          searchIn: (data) => data.data,
          target: (e, i) => i === indexLesson,
        },
      ],
      (item) => {
        item.done = !lesson.done;
        $ofun(item).path({
          path: "process.set",
          value: $help().getDateISO(),
        });
        return item;
      }
    );
  } else {
    $api
      .post<any>(`${$app().BK_URL.nashit}/lesson/${lesson.id}/set`, {
        body: {
          set: !lesson.done,
        },
      })
      .then((res) => {
        if (res.end) {
          router.push(
            localePath({
              name: "CourseFinishPage",
              params: route.params,
            })
          );
        }

        lesson.done = !lesson.done;
        isDoneAll();
        lesson.loading = false;

        $msg({
          text: $t(`gl.msg.save`),
          type: "ok",
        });

        if (res.last) {
          shows.addNewLesBtn = false;
        }
      })
      .catch((_) => {
        lesson.loading = false;

        $msg({
          text: $t('gl.msg.error.sendData'),
          type: "error",
        });
      });
  }
}

const editor = ref();
const noteRef = ref();

function isDoneAll() {
  const isDoneAll =
    !lessons.value.filter((e: any) => e.done !== true).length &&
    !showNoLessons.value;
  shows.doneAllLessons = isDoneAll;
}

function showNote(les: any, index: number) {
  temps.value = {
    lesson: les,
    index,
  };

  if (!editor.value.write) {
    item.value = {};

    setTimeout(() => {
      item.value = les;
    }, 10);

   
    editor.value.setHtml(les.note);
    noteRef.value.show();
  } else {
    $msg({
      text: $t(`${lang}.msg.wait`),
      type: "error",
    });
  }
}

const layoutDataStore = useLayoutDataStore();
async function note(dataNote: any) {
  function errorHandler(msg: string) {
    // editor.value.shows.loadingSave = false;

    // editor.value.send = null;

    // editor.value.funSend = null;

    // editor.value.canSend = false;

    // const quill = editor.value?.$refs?.editor?.quill;
    // quill.blur();

    // editor.value.contnet = dataNote.item.note;
    // setTimeout(() => {
    //   editor.value.canSend = true;
    // }, 100);

    // window.removeEventListener(
    //   "beforeunload",

    //   editor.value.eventList!
    // );

    $msg({
      text: msg,
      type: "error",
    });
  }

  const indexLesson = temps.value.index;
  const lesson = temps.value.lesson;

  if (navigator.onLine) {
    $api
      .post(`${$app().BK_URL.nashit}/lesson/${temps.value.lesson.id}/note`, {
        body: {
          note: dataNote,
        },
      })
      .then((_res) => {
        temps.value.lesson.note = dataNote;
        item.value.note = dataNote;
      })
      .catch((_e) => {
        // const editor = this.$refs.editor!?.quill;
        // editor.blur();
        // console.log(temps.value.task.note);
        const last = temps.value.lesson.note;

        temps.value.lesson.note = "";
        item.value.note = "";

        setTimeout(() => {
          temps.value.lesson.note = last;
          item.value.note = last;
        }, 10);

        $msg({
          text: $t(`pages.course.cant`),
          type: "error",
        });
      });
  } else {
    const sizeText = new Blob([dataNote]).size;

    if (sizeText > layoutDataStore.data.value.note_limit) {
      $msg({
        text: trans(`${lang}.msg.note`,{
              limit : layoutDataStore.data.value.note_limit,
              size : sizeText
            }),
        type: "error",
      });
      return;
    }
    const last = temps.value.lesson.note;
    await $cache({
      name: "CoursesTodayPage",
      done: async () => {
        temps.value.lesson.note = dataNote;
        item.value.note = dataNote;
        // lesson.note =
        $ofun(temps.value.lesson).path({
          path: "process.note",
          value: $help().getDateISO(),
        });
        $ofun(item.value).path({
          path: "process.note",
          value: $help().getDateISO(),
        });

        await $cache().edit(
          [
            {
              req: `${$app().BK_URL.nashit}/course/${route.params.id}/lessons`,
              searchIn: (data) => data.lessons.data,
              target: (e) => e.id === lesson.id,
            },
          ],
          (item) => {
            item.note = lesson.note;
            item.smallNote = $t(`gl.needInternet`);
            if (item.done) {
              item.date.done = $help().getDateISO();
            } else {
              item.date.done = null;
            }
            return item;
          }
        );
      },
      err: () => {
        temps.value.lesson.note = "";
        item.value.note = "";

        setTimeout(() => {
          temps.value.lesson.note = last;
          item.value.note = last;
        }, 10);

        $msg({
          text: $t('gl.msg.error.sendData'),
          type: "error",
        });
      },
    }).edit(
      [
        {
          req: `${$app().BK_URL.nashit}/course/${
            route.params.id
          }/lessons/today`,
          searchIn: (data) => data.data,
          target: (e, i) => i === indexLesson,
        },
      ],
      (item) => {
        item.note = dataNote;
        $ofun(item).path({
          path: "process.note",
          value: $help().getDateISO(),
        });

        return item;
      }
    );

    //                 temps.value.lesson.note = dataNote;

    //                 temps.value.lesson.process
    //                   ? (temps.value.lesson.process.note = new Date()
    //                       .toISOString()
    //                       .replace("Z", "000Z"))
    //                   : (temps.value.lesson.process = {
    //                       note: new Date().toISOString().replace("Z", "000Z"),
    //                     });

    //                 // cache 2
    //                 await cache
    //                   .match("/api/all-course.json")
    //                   .then(async function (e) {
    //                     // if (!e) {
    //                     //   done();
    //                     //   return;
    //                     // }
    //                     await e
    //                       ?.json()
    //                       .then(async function (dataJson) {
    //                         const lessonOffline = dataJson.lessons.find(
    //                           (e: any) => e.id === lesson.id
    //                         );

    //                         if (lessonOffline) {
    //                           lessonOffline.note = dataNote;
    //                           lessonOffline.smallNote = $t(`gl.needInternet`);

    //                           const newRes = new Response(
    //                             JSON.stringify(dataJson),
    //                             e
    //                           );

    //                           await cache
    //                             .put("/api/all-course.json", newRes)
    //                             .catch((_) => {
    //                               $msg({
    //                                 text: $t(`${lang}.cache.save`),
    //                                 type: "error",
    //                               });
    //                             });
    //                         }
    //                       })
    //                       .catch((_) => {
    //                         $msg({
    //                           text: $t(`${lang}.cache.open`),
    //                           type: "error",
    //                         });
    //                       });
    //                   })
    //                   .catch((_) => {
    //                     $msg({
    //                       text: $t(`${lang}.cache.match`),
    //                       type: "error",
    //                     });
    //                   });

    //                 // editor.value.shows.loadingSave = false;

    //                 // editor.value.send = null;

    //                 // editor.value.funSend = null;
    //                 // window.removeEventListener(
    //                 //   "beforeunload",

    //                 //   editor.value.eventList!
    //                 // );

    //                 // dataNote.item.value.date.lastUpdate = new Date().getTime();
    //               })
    //               .catch((_) => {
    //                 errorHandler(String($t(`gl.msg.error.cache.put`)));
    //               });
    //           })
    //           .catch((_) => {
    //             errorHandler(String($t(`gl.msg.error.cache.open`)));
    //           });
    //       })
    //       .catch((_) => {
    //         errorHandler(String($t(`gl.msg.error.cache.match`)));
    //       });
    //   })
    //   .catch((_) => {
    //     errorHandler(String($t(`gl.msg.error.cache.open`)));
    //   });
  }
}
</script>

<template>
  <div class="today-page-course" data-width="small">
    <h1>{{ $t(`${lang}.title`) }}</h1>
    <div class="app">
      <div class="todays-lessons">
        <div v-if="shows.doneAllLessons" class="done-all">
          <div class="box-top">
            <img src="~assets/images/shield.png" />
            <p>{{ $t(`${lang}.doneBox.title`) }}</p>
          </div>
          <button
            v-if="!$route.params.profile"
            class="btn-addNewLes"
            :disabled="shows.loadingAddNewLes"
            @click="setNewLesToDay()"
          >
            <i class="ri-add-circle-line"></i>
            {{ $t(`${lang}.doneBox.btn`) }}
            <full-loading v-if="shows.loadingAddNewLes"></full-loading>
          </button>
        </div>
        <div v-if="showNoLessons" class="no-lessons">
          <img src="~assets/images/index/hand-up.png" />
          <div class="box-text">
            <h2>{{ $t(`${lang}.noLessonsBox.title`) }}</h2>
            <p>
              {{ $t(`${lang}.noLessonsBox.comment.0`) }}
              <button
                class="btn-addNewLes"
                :disabled="shows.loadingAddNewLes"
                @click="setNewLesToDay()"
              >
                <i class="ri-add-circle-line"></i>
                {{ $t(`${lang}.noLessonsBox.comment.1`) }}
                <full-loading v-if="shows.loadingAddNewLes"></full-loading>
              </button>
            </p>
          </div>
        </div>
        <div ref="boxs" class="boxs">
          <div
            v-for="(les, index) in lessons.value"
            :key="index"
            :class="{ 'new-lesson': les.new, 'late-lesson': les.late }"
          >
            <span
              class="icon"
              :class="{ done: les.done }"
              @click="!$route.params.profile && setDone(les, index)"
            >
              <template v-if="les.done">
                <i class="ri-checkbox-circle-line"></i>
              </template>
              <template v-else>
                <i class="ri-forbid-2-line"></i>
              </template>
            </span>

            <span v-if="les.new" class="new-lesson-flag">
              {{ $t(`${lang}.newLes`) }}
            </span>

            <span v-if="les.late" class="late-lesson-flag">
              <i class="ri-heart-pulse-line"></i>
              {{ $t(`${lang}.lateLes`) }}
            </span>

            <p>{{ les.name }}</p>
            <button class="note-btn" @click="showNote(les, index)">
              <i class="ri-sticky-note-line"></i>
            </button>
            <full-loading v-if="les.loading"></full-loading>
          </div>

          <small-loading v-if="shows.loadingMoreLessons"></small-loading>
        </div>

        <PopUp
          ref="noteRef"
          class="note-popup smallTitle"
          :title="temps.value?.lesson?.name || ''"
          @onClose="$refs['editor'].doneNow()"
        >

        <!--    :set-text="item.value.note" -->
          <note-book
            ref="editor"
         
            @done="note($event)"
          ></note-book>
        </PopUp>
      </div>
    </div>
  </div>
</template>
