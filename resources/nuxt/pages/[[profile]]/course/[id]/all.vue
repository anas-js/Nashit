<script setup lang="ts">
// import Vue from 'vue';
// import axios from 'axios';
// import transition from '~/mixins/pageTransition';
// import Msg from '~/classes/msg';
// import filters from '~/classes/filters';
// import { local } from '~/classes/storage';
// import listItem from '~/components/elements/listItems.vue';

import pageLayout from "~/transition/page.layout";

definePageMeta({
  name: "CourseAllPage",
  pageTransition: pageLayout,
  layout: "page",
  // course : true
});

const route = useRoute();
const trans = useNuxtApp().$i18n.t;
const page_res = await $api
  .get<any>(
    `${$app().BK_URL.nashit}${
      route.params.profile ? "/profile/" + route.params.profile : ""
    }/course/${route.params.id}/lessons`
  )
  .catch(() => {
    throw createError({
      statusCode: 404,
      statusMessage: "Error",
      fatal: true,
    });
  });
let page = 2;

const lessons = reactive({
  value: page_res.lessons.data,
});

lessons.value = lessons.value.map((e: any) => {
  e.loading = false;
  return e;
});

// if(data.course.end) {
//   redirect(`/course/${route.params.id}/finish`);
// }

const systemStore = useSystemStore();
const layoutDataStore = useLayoutDataStore();

const lang = "pages.course.all";
const shows = reactive({
  loadingMoreLessons: false,
});

const item = reactive({
  value: {},
});
const addNewLesPop = reactive({
  data: {
    lessons: [],
  },
});
const pop = reactive({
  lesson: {
    loading: false,
    add: {
      data: {
        lessons: [
          {
            name: "",
            error: false,
          },
        ],
        num: 1,
        word: $t(`gl.lesson`),
      },
      err: {
        num: "",
        word: "",
        filter: false,
      },
      loading: false,
    },
    move: {
      data: {
        type: "before",
        title: $t(`pages.course.all.pop.lesson.move.before`),
      },
      showListType: false,
      loadingMore: false,
    },
  },
});

const temps = reactive({
  value: {
    lesson: {
      name: null,
      done : null,
      date: {
        done: null,
      },
    },
    index: null,
  },
});

const more = ref(!!page_res.lessons.next_page_url);
let course = reactive({
  value: page_res.course,
});

const lessonsFilterToMove = computed(() =>
  lessons.value.filter((e: any) => e.id !== temps.value.lesson.id)
);

const boxs = ref();
onMounted(() => {
  setScrollEvents();
  pop.lesson.add.data.lessons[0].name = `${$t(`gl.lesson`)} (${
    course.value.lessons + 1
  })`;
});

let removeEvent = true;

function setScrollEvents() {
  const lessonsListMove = document.querySelector(
    ".lessons-list .list"
  ) as HTMLElement;

  if (more.value && removeEvent) {
    removeEvent = false;
    let listinerBox: EventListener;
    let listinerLessonsListMove: EventListener;

    function loadMore() {
      if (navigator.onLine) {
        if (
          boxs.value.offsetHeight + boxs.value.scrollTop >=
            boxs.value.scrollHeight - 40 &&
          !shows.loadingMoreLessons &&
          !pop.lesson.move.loadingMore
        ) {
          shows.loadingMoreLessons = true;

          $api
            .get<any>(
              `${$app().BK_URL.nashit}${
                route.params.profile ? "/profile/" + route.params.profile : ""
              }/course/${route.params.id}/lessons`,
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
                    req: `${$app().BK_URL.nashit}${
                      route.params.profile
                        ? "/profile/" + route.params.profile
                        : ""
                    }/course/${route.params.id}/lessons`,
                    searchIn: (data) => data,
                    // target: (e, i) => i === indexLesson,
                  },
                ],
                (data) => {
                  data.lessons.data = data.lessons.data.concat(
                    res.lessons.data
                  );
                  return data;
                }
              );

              page++;
              const data = res.lessons.data;
              // data.lessons = data.lessons.map((e: any) => {
              //   e.loading = false;
              //   return e;
              // });

              lessons.value = lessons.value.concat(data);

              more.value = !!res.lessons.next_page_url;
              course.value = res.course;
              shows.loadingMoreLessons = false;

              if (!more.value) {
                boxs.value.removeEventListener("scroll", listinerBox);
                lessonsListMove.removeEventListener(
                  "scroll",
                  listinerLessonsListMove
                );
                removeEvent = true;
              }
            })
            .catch((_) => {
              boxs.value.scrollTo({ top: 0 });
              lessonsListMove.scrollTo({ top: 0 });
              shows.loadingMoreLessons = false;
              $msg({
                text: $t(`gl.msg.error.getData`),
                type: "error",
              });
            });
        }
      }
    }

    listinerBox = loadMore;
    boxs.value.addEventListener("scroll", loadMore);

    function loadMoreTow() {
      if (
        lessonsListMove.offsetHeight + lessonsListMove.scrollTop >=
          lessonsListMove.scrollHeight - 5 &&
        !shows.loadingMoreLessons &&
        !pop.lesson.move.loadingMore
      ) {
        pop.lesson.move.loadingMore = true;

        $api
          .get<any>(
            `${$app().BK_URL.nashit}${
              route.params.profile ? "/profile/" + route.params.profile : ""
            }/course/${route.params.id}/lessons`,
            {
              params: {
                page: page,
              },
            }
          )
          .then((res) => {
            page++;
            const data = res.lessons.data;
            // data.lessons = data.lessons.map((e: any) => {
            //   e.loading = false;
            //   return e;
            // });

            lessons.value = lessons.value.concat(data);

            more.value = !!res.lessons.next_page_url;

            course.value = res.course;
            pop.lesson.move.loadingMore = false;

            if (!more.value) {
              lessonsListMove.removeEventListener(
                "scroll",
                listinerLessonsListMove
              );
              boxs.value.removeEventListener("scroll", listinerBox);
              removeEvent = true;
            }
          })
          .catch((_) => {
            pop.lesson.move.loadingMore = false;
            boxs.value.scrollTo({ top: 0 });
            lessonsListMove.scrollTo({ top: 0 });
            $msg({
              text: $t(`gl.msg.error.getData`),
              type: "error",
            });
          });
      }
    }

    listinerLessonsListMove = loadMoreTow;

    lessonsListMove?.addEventListener("scroll", loadMoreTow);
  }
}

function checkHeightPop() {
  const box = document.querySelector(".add-new-les-popup .box");
  const intrvalGetHeight = setInterval(() => {
    const lengthLes = addNewLesPop.data.lessons.length;
    const LengthBoxs = box?.querySelectorAll(".items  > div").length;

    if (lengthLes === LengthBoxs) {
      clearInterval(intrvalGetHeight);
      console.log(box!.clientHeight);
      if (box!.clientHeight > window.innerHeight) {
        box?.parentElement?.classList.add("large");
      }
    }
  }, 1);
}

const note = ref();
const moveRef = ref();
const editor = ref();
const addLessons = ref();
const lessonListOptions = ref();
function lesson() {
  return {
    fun() {
      return {
        async rename(newName: string) {
          if (!$filters.length({ item: newName.trim(), max: 50, min: 1 })) {
            $msg({
              text: $t(`${lang}.msg.rename.error`),
              type: "error",
              btns: {
                t() {
                  navigator.clipboard.writeText(newName);
                },
              },
              time: 5000,
            });
            return false;
          }

          const lesson = temps.value.lesson;

          if (newName === lesson.name) {
            return;
          }

          pop.lesson.loading = true;
          function errorHandler(msg: string) {
            pop.lesson.loading = false;

            $msg({
              text: msg,
              type: "error",
            });
          }

          if (!navigator.onLine) {
            await $cache({
              name: "CoursesAllPage",
              done: async () => {
                await $cache().edit(
                  [
                    {
                      req: `${$app().BK_URL.nashit}/course/${
                        route.params.id
                      }/lessons/today`,
                      searchIn: (data) => data.data,
                      target: (e) => e.id === lesson.id,
                    },
                  ],
                  (item) => {
                    item.name = newName.trim();
                    return item;
                  }
                );
                lesson.name = newName.trim();
                pop.lesson.loading = false;
                $msg({
                  text: $t(`${lang}.msg.rename.done`),
                  type: "ok",
                });
              },
              err: () => {
                errorHandler("Error");
              },
            }).edit(
              [
                {
                  req: `${$app().BK_URL.nashit}/course/${
                    route.params.id
                  }/lessons`,
                  searchIn: (data) => data.lessons.data,
                  target: (e, i) => i == temps.value.index,
                },
              ],
              (item) => {
                item.name = newName.trim();
                $ofun(item).path({
                  path: "process.name",
                  value: $help().getDateISO(),
                });
                return item;
              }
            );
            // if (!(await isCachesSupport())) {
            //   errorHandler(String($t("gl.msg.error.cache.support")));
            //   return;
            // }

            // caches
            //   .open("nashit-runtime-cache")
            //   .then(function (cache) {
            //     cache
            //       .match("/api/all-course.json")
            //       .then(function (e) {
            //         if (!e) {
            //           errorHandler(String($t("gl.msg.error.cache.found")));
            //           return;
            //         }
            //         e?.json()
            //           .then(function (dataJson) {

            //             const newRes = new Response(
            //               JSON.stringify(dataJson),
            //               e
            //             );
            //             cache
            //               .put("/api/all-course.json", newRes)
            //               .then(async (_) => {
            //                 setOffline();
            //                 lesson.name = newName.trim();
            //                 lesson.process
            //                   ? (lesson.process.rename = new Date()
            //                       .toISOString()
            //                       .replace("Z", "000Z"))
            //                   : (lesson.process = {
            //                       rename: new Date()
            //                         .toISOString()
            //                         .replace("Z", "000Z"),
            //                     });

            //                 // cache 2
            //                 await cache
            //                   .match("/api/today.json")
            //                   .then(async function (e) {
            //                     // if (!e) {
            //                     //   t.pop.lesson.loading = false;

            //                     //   return;
            //                     // }

            //                     await e
            //                       ?.json()
            //                       .then(async function (dataJson) {
            //                         const lessonOffline = dataJson.lessons.find(
            //                           (e: any) => e.id === lesson.id
            //                         );

            //                         if (lessonOffline) {
            //                           lessonOffline.name = newName.trim();

            //                           const newRes = new Response(
            //                             JSON.stringify(dataJson),
            //                             e
            //                           );
            //                           await cache
            //                             .put("/api/today.json", newRes)
            //                             .catch((_) => {
            //                               errorHandler(
            //                                 String($t(`${lang}.cache.save`))
            //                               );
            //                             });
            //                         } else {
            //                           $msg({
            //                             text: $t(
            //                               `${lang}.msg.task.rename.done`
            //                             ),
            //                             type: "ok",
            //                           });
            //                         }
            //                       })
            //                       .catch((_) => {
            //                         errorHandler(
            //                           String($t(`${lang}.cache.open`))
            //                         );
            //                       });
            //                   })
            //                   .catch((_) => {
            //                     errorHandler(
            //                       String($t(`${t.lang}.cache.match`))
            //                     );
            //                   });

            //                 pop.lesson.loading = false;

            //                 $msg({
            //                   text: String($t(`${lang}.msg.task.rename.done`)),
            //                   type: "ok",
            //                 });
            //                 // dataNote.item.date.lastUpdate = new Date().getTime();
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
          } else {
            $api
              .post(`${$app().BK_URL.nashit}/lesson/${lesson.id}/rename`, {
                body: { name: newName },
              })
              .then((_res) => {
                lesson.name = newName.trim();
                pop.lesson.loading = false;

                $msg({
                  text: $t(`${lang}.msg.rename.done`),
                  type: "ok",
                });
              })
              .catch((_) => {
                errorHandler(String($t(`gl.msg.error.sendData`)));
              });
          }
        },
        remove() {
          const lesson = temps.value.lesson;
          pop.lesson.loading = true;

          function errorHandler(msg: string) {
            note.value.close();
            pop.lesson.loading = false;

            $msg({
              text: msg,
              type: "error",
            });
          }

          $msg({
            text: `${$t(`${lang}.msg.remove.sure.0`)} "${lesson.name}"${$t(
              `${lang}.msg.remove.sure.2`
            )}"`,
            type: "sure",
            btns: {
              async t() {
                if (!navigator.onLine) {
                  errorHandler(String($t(`gl.msg.error.sendData`)));
                  // if (!(await isCachesSupport())) {
                  //   errorHandler(String($t("gl.msg.error.cache.support")));
                  //   return;
                  // }
                  // caches
                  //   .open("nashit-runtime-cache")
                  //   .then(function (cache) {
                  //     cache
                  //       .match("/api/all-course.json")
                  //       .then(function (e) {
                  //         if (!e) {
                  //           errorHandler(String($t("gl.msg.error.cache.found")));
                  //           return;
                  //         }
                  //         e?.json()
                  //           .then(function (dataJson) {
                  //             const lessonOffline =
                  //               dataJson.lessons[temps.value.index];
                  //             lessonOffline.process
                  //               ? (lessonOffline.process.remove = true)
                  //               : (lessonOffline.process = {
                  //                   remvoe: true,
                  //                 });

                  //             const newRes = new Response(
                  //               JSON.stringify(dataJson),
                  //               e
                  //             );
                  //             cache
                  //               .put("/api/all-course.json", newRes)
                  //               .then(async (_) => {
                  //                 setOffline();

                  //                 lesson.process
                  //                   ? (lesson.process.remove = true)
                  //                   : (lesson.process = {
                  //                       remove: true,
                  //                     });

                  //                 // cache 2
                  //                 await cache
                  //                   .match("/api/today.json")
                  //                   .then(async function (e) {
                  //                     // if (!e) {
                  //                     //   (t.$refs as any).note.close();
                  //                     //   t.pop.lesson.loading = false;
                  //                     //   return;
                  //                     // }
                  //                     await e
                  //                       ?.json()
                  //                       .then(async function (dataJson) {
                  //                         dataJson.lessons =
                  //                           dataJson.lessons.filter(
                  //                             (e: any) => e.id !== lesson.id
                  //                           );

                  //                         const newRes = new Response(
                  //                           JSON.stringify(dataJson),
                  //                           e
                  //                         );
                  //                         await cache
                  //                           .put("/api/today.json", newRes)
                  //                           .catch((_) => {
                  //                             errorHandler(
                  //                               String(
                  //                                 $t(`${t.lang}.cache.save`)
                  //                               )
                  //                             );
                  //                           });
                  //                       })
                  //                       .catch((_) => {
                  //                         errorHandler(
                  //                           String($t(`${t.lang}.cache.open`))
                  //                         );
                  //                       });
                  //                   })
                  //                   .catch((_) => {
                  //                     errorHandler(
                  //                       String($t(`${t.lang}.cache.match`))
                  //                     );
                  //                   });

                  //                 // dataNote.item.date.lastUpdate = new Date().getTime();
                  //                 note.value.close();
                  //                 pop.lesson.loading = false;

                  //                 $msg({
                  //                   text: $t(`${t.lang}.remove.done`),
                  //                   type: "ok",
                  //                 });
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
                } else {
                  $api
                    .post(`${$app().BK_URL.nashit}/lesson/${lesson.id}/remove`)
                    .then(async (_) => {
                      // lessons.value = lessons.value.filter(
                      //   (e: any) => e.id !== lesson.id
                      // );
                      const lessonsAfterAdd = await $api.get<any>(
                        `${$app().BK_URL.nashit}/course/${
                          route.params.id
                        }/lessons`
                      );

                      lessons.value = lessonsAfterAdd.lessons.data;
                      lessons.value = lessons.value.map((e: any) => {
                        e.loading = false;
                        return e;
                      });
                      course.value = lessonsAfterAdd.course;

                      layoutDataStore.data.value.lessons = course.value.lessons;

                      page = 2;
                      more.value = true;
                      setScrollEvents();
                      note.value.close();
                      pop.lesson.loading = false;

                      $msg({
                        text: $t(`${lang}.msg.remove.done`),
                        type: "ok",
                      });
                    })
                    .catch((_) => {
                      $msg({
                        text: $t(`gl.msg.error.sendData`),
                        type: "error",
                      });
                      pop.lesson.loading = false;
                    });
                }
              },
              f() {
                pop.lesson.loading = false;
              },
            },
          });
        },
        move(to: any) {
          pop.lesson.loading = true;
          const move = pop.lesson.move.data;
          const lesson = temps.value.lesson;

          function errorHandler(msg: string | string[]) {
            moveRef.value.close();
            note.value.close();
            pop.lesson.loading = false;

            $msg({
              text: msg,
              type: "error",
            });
          }

          if (lesson.done) {
            try {
              const findIndex =
                lessons.value.findIndex((e) => e.id == to.id) - 1;
              if (
                move.type == "before" &&
                new Date(lessons.value[findIndex].date.required + " 00:00:00") >
                  new Date()
              ) {
                errorHandler($t(`${lang}.pop.lesson.move.error`));
                return;
              }

              if (
                move.type == "after" &&
                new Date(to.date.required + " 00:00:00") > new Date()
              ) {
                errorHandler($t(`${lang}.pop.lesson.move.error`));
                return;
              }
            } catch {
              errorHandler($t(`gl.msg.error.proces`));
              return;
            }
          }

          $msg({
            text: `${$t(`${lang}.msg.move.sure.0`)} "${lesson.name}" ${
              move.title
            }  "${to.name} ${$t(`${lang}.msg.move.sure.1`)}"`,
            type: "sure",
            btns: {
              async t() {
                if (!navigator.onLine) {
                  errorHandler(String($t(`gl.msg.error.sendData`)));
                  // if (!(await isCachesSupport())) {
                  //   errorHandler(String($t("gl.msg.error.cache.support")));
                  //   return;
                  // }
                  // caches
                  //   .open("nashit-runtime-cache")
                  //   .then(function (cache) {
                  //     cache
                  //       .match("/api/all-course.json")
                  //       .then(function (e) {
                  //         if (!e) {
                  //           errorHandler(String($t("gl.msg.error.cache.found")));
                  //           return;
                  //         }
                  //         e?.json()
                  //           .then(function (dataJson) {
                  //             const lessonsOffline = JSON.parse(
                  //               JSON.stringify(lessons)
                  //             );

                  //             Object.assign(lessons,lessons.filter((e: any) => e.id !== lesson.id));

                  //             const toIndex = lessons.findIndex(
                  //               (e: any) => e.id === to.id
                  //             );
                  //             const arraySplitOne = lessons.slice(0, toIndex);
                  //             const arraySplitTow = lessons.slice(
                  //               toIndex + 1,
                  //               lessons.length
                  //             );

                  //             // lesson.date.done = 'بحاجة للإنترنت!';

                  //             if (move.type === "before") {

                  //               Object.assign(lessons,[
                  //                 ...arraySplitOne,
                  //                 lesson,
                  //                 to,
                  //                 ...arraySplitTow,
                  //               ]);

                  //             } else {
                  //               Object.assign(lessons,[
                  //                 ...arraySplitOne,
                  //                 to,
                  //                 lesson,
                  //                 ...arraySplitTow,
                  //               ]);

                  //             }

                  //             lesson.process
                  //               ? (lesson.process.move = new Date().getTime())
                  //               : (lesson.process = {
                  //                   move: new Date().getTime(),
                  //                 });

                  //             dataJson.lessons = lessons;
                  //             const newRes = new Response(
                  //               JSON.stringify(dataJson),
                  //               e
                  //             );
                  //             cache
                  //               .put("/api/all-course.json", newRes)
                  //               .then((_) => {
                  //                 setOffline();
                  //                 moveRef.value.close();
                  //                 note.value.close();
                  //                 pop.lesson.loading = false;

                  //                 $msg({
                  //                   text: $t(`${lang}.move.done`),
                  //                   type: "ok",
                  //                 });
                  //               })
                  //               .catch((_) => {

                  //                 Object.assign(lessons,lessonsOffline);
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
                } else {
                  const data = {} as any;
                  data[move.type] = to.id;
                  $api
                    .post(`${$app().BK_URL.nashit}/lesson/${lesson.id}/move`, {
                      body: data,
                    })
                    .then(async (_) => {
                      const lessonsAfterAdd = await $api.get<any>(
                        `${$app().BK_URL.nashit}/course/${
                          route.params.id
                        }/lessons`
                      );

                      lessons.value = lessonsAfterAdd.lessons.data;
                      lessons.value = lessons.value.map((e: any) => {
                        e.loading = false;
                        return e;
                      });
                      course.value = lessonsAfterAdd.course;

                      page = 2;
                      more.value = true;
                      setScrollEvents();
                      // lessons.value = lessons.value.filter(
                      //   (e: any) => e.id !== lesson.id
                      // );

                      // const toIndex = lessons.value.findIndex(
                      //   (e: any) => e.id === to.id
                      // );
                      // const arraySplitOne = lessons.value.slice(0, toIndex);
                      // const arraySplitTow = lessons.value.slice(
                      //   toIndex + 1,
                      //   lessons.value.length
                      // );

                      // // console.log(JSON.parse(JSON.stringify(arraySplitOne)),JSON.parse(JSON.stringify(arraySplitTow)));

                      // if (data.before !== undefined) {
                      //   // arraySplitTow.unshift(lesson);

                      //   lessons.value = [
                      //     ...arraySplitOne,
                      //     lesson,
                      //     to,
                      //     ...arraySplitTow,
                      //   ];
                      // } else {
                      //   lessons.value = [
                      //     ...arraySplitOne,
                      //     to,
                      //     lesson,
                      //     ...arraySplitTow,
                      //   ];

                      //   // arraySplitOne.push(lesson);
                      // }

                      moveRef.value.close();
                      note.value.close();
                      pop.lesson.loading = false;

                      $msg({
                        text: $t(`${lang}.msg.move.done`),
                        type: "ok",
                      });
                    })
                    .catch((e) => {
                      if (e?.data?.code == 2) {
                        errorHandler($t(`${lang}.pop.lesson.move.error`));
                      } else {
                        errorHandler(String($t(`gl.msg.error.sendData`)));
                      }
                    });
                }
              },
              f() {
                pop.lesson.loading = false;
              },
            },
          });
        },
        copy() {
          const lesson = JSON.parse(JSON.stringify(temps.value.lesson));
          pop.lesson.loading = true;

          function errorHandler(msg: string) {
            note.value.close();
            pop.lesson.loading = false;

            $msg({
              text: msg,
              type: "error",
            });
          }

          $msg({
            text: `${$t(`${lang}.msg.copy.sure.0`)} "${lesson.name}"${$t(
              `${lang}.msg.copy.sure.1`
            )}"`,
            type: "sure",
            btns: {
              async t() {
                if (!navigator.onLine) {
                  errorHandler(String($t(`gl.msg.error.sendData`)));
                  // if (!(await isCachesSupport())) {
                  //   errorHandler(String($t("gl.msg.error.cache.support")));
                  //   return;
                  // }
                  // caches
                  //   .open("nashit-runtime-cache")
                  //   .then(function (cache) {
                  //     cache
                  //       .match("/api/all-course.json")
                  //       .then(function (e) {
                  //         if (!e) {
                  //           errorHandler(String($t("gl.msg.error.cache.found")));
                  //           return;
                  //         }
                  //         e?.json()
                  //           .then(function (dataJson) {
                  //             const lessonsOfflineReset = JSON.parse(
                  //               JSON.stringify(lessons)
                  //             );
                  //             const lessonIndex = temps.value.index;
                  //             const arraySplitOne = lessons.slice(
                  //               0,
                  //               lessonIndex + 1
                  //             );
                  //             const arraySplitTow = t.lessons.slice(
                  //               lessonIndex + 1,
                  //               lessons.length
                  //             );

                  //             lesson.id = new Date().getTime();
                  //             lesson.date.done = $t(`gl.needInternet`);
                  //             lesson.date.required = $t(`gl.needInternet`);
                  //             lesson.done = false;

                  //             const copyWord = String(t.$t(`${lang}.copyWord`));
                  //             if (
                  //               !$filters.range({
                  //                 item: lesson.name.length + copyWord.length,
                  //                 max: 50,
                  //                 min: 1,
                  //               })
                  //             ) {
                  //               lesson.name =
                  //                 lesson.name.substr(
                  //                   0,
                  //                   50 - (copyWord.length + 3)
                  //                 ) + "...";
                  //             }
                  //             lesson.name = lesson.name + copyWord;

                  //             lesson.process
                  //               ? (lesson.process.create = true)
                  //               : (lesson.process = {
                  //                   create: true,
                  //                 });

                  //             arraySplitOne.push(lesson);

                  //             Object.assign(lessons,[
                  //               ...arraySplitOne,
                  //               ...arraySplitTow,
                  //             ]);
                  //             dataJson.lessons = lessons;

                  //             const newRes = new Response(
                  //               JSON.stringify(dataJson),
                  //               e
                  //             );
                  //             cache
                  //               .put("/api/all-course.json", newRes)
                  //               .then((_) => {
                  //                 setOffline();
                  //                 note.value.close();
                  //                 pop.lesson.loading = false;

                  //                 $msg({
                  //                   text: $t(`${lang}.msg.copy.done`),
                  //                   type: "ok",
                  //                 });
                  //               })
                  //               .catch((_) => {

                  //                 Object.assign(lessons,lessonsOfflineReset);
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
                } else {
                  $api
                    .post<any>(
                      `${$app().BK_URL.nashit}/lesson/${lesson.id}/copy`
                    )
                    .then(async (_) => {
                      const lessonsAfterAdd = await $api.get<any>(
                        `${$app().BK_URL.nashit}/course/${
                          route.params.id
                        }/lessons`
                      );

                      lessons.value = lessonsAfterAdd.lessons.data;
                      lessons.value = lessons.value.map((e: any) => {
                        e.loading = false;
                        return e;
                      });
                      course.value = lessonsAfterAdd.course;

                      page = 2;
                      more.value = true;
                      setScrollEvents();
                      // const lessonPush = res.lesson;
                      // const lessonIndex = temps.value.index;
                      // const arraySplitOne = lessons.value.slice(0, lessonIndex + 1);
                      // const arraySplitTow = lessons.value.slice(
                      //   lessonIndex + 1,
                      //   lessons.value.length
                      // );

                      // lessonPush.done = false;
                      // // lessonPush.id = res.data.lesson.id;
                      // // lessonPush.name = res.data.lesson.id;
                      // // lessonPush.date = res.data.lesson.id;
                      // arraySplitOne.push(lessonPush);

                      // lessons.value = [...arraySplitOne, ...arraySplitTow];
                      // // if(t.isLastPackage) {
                      // //   t.lessons.push(JSON.parse(JSON.stringify(lesson)));
                      // // }

                      note.value.close();
                      pop.lesson.loading = false;

                      $msg({
                        text: $t(`${lang}.msg.copy.done`),
                        type: "ok",
                      });
                    })
                    .catch((_) => {
                      $msg({
                        text: $t(`gl.msg.error.sendData`),
                        type: "error",
                      });
                      pop.lesson.loading = false;
                    });
                }
              },
              f() {
                pop.lesson.loading = false;
              },
            },
          });
        },
        async note(dataNote: any) {
          const indexLesson = temps.value.index;
          // function errorHandler(msg: string) {
          //   // const quill = editor.value.quill;
          //   // quill.blur();
          const last = temps.value.lesson.note;
          // }
          if (navigator.onLine) {
            $api
              .post<any>(
                `${$app().BK_URL.nashit}/lesson/${temps.value.lesson.id}/note`,
                {
                  body: {
                    note: dataNote,
                    returnSmallNote: true,
                  },
                }
              )
              .then((res) => {
                temps.value.lesson.note = dataNote;
                temps.value.lesson.smallNote = res.smallNote;
                item.value.note = dataNote;
                item.value.smallNote = res.smallNote;
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
                text: trans(`${lang}.msg.note`, {
                  size: sizeText,
                  limit: layoutDataStore.data.value.note_limit,
                }),
                type: "error",
              });
              return;
            }
            // console.log("h1");
            await $cache({
              name: "CoursesAllPage",
              done: async () => {
                // setOffline();
                const lesson = temps.value.lesson;
                lesson.note = dataNote;
                // temps.value.note = dataNote;
                lesson.smallNote = $t(`gl.needInternet`);

                $ofun(temps.value).path({
                  path: "process.note",
                  value: $help().getDateISO(),
                });

                await $cache().edit(
                  [
                    {
                      req: `${$app().BK_URL.nashit}/course/${
                        route.params.id
                      }/lessons/today`,
                      searchIn: (data) => data.data,
                      target: (e) => e.id === lesson.id,
                    },
                  ],
                  (item) => {
                    item.note = dataNote;
                    return item;
                  }
                );

                // $msg({
                //   text: $t(`${lang}.cache.save`),
                //   type: "error",
                // });
              },
              err: () => {
                temps.value.lesson.note = "";
                item.value.note = "";

                setTimeout(() => {
                  temps.value.lesson.note = last;
                  item.value.note = last;
                }, 10);

                $msg({
                  text: $t("gl.msg.error.sendData"),
                  type: "error",
                });
              },
            }).edit(
              [
                {
                  req: `${$app().BK_URL.nashit}/course/${
                    route.params.id
                  }/lessons`,
                  searchIn: (data) => data.lessons.data,
                  target: (e, i) => i == indexLesson,
                },
              ],
              (item) => {
                // console.log(item);
                item.note = dataNote;
                item.smallNote = $t(`gl.needInternet`);
                $ofun(item).path({
                  path: "process.note",
                  value: $help().getDateISO(),
                });
                return item;
              }
            );
          }
        },
      };
    },
    pop() {
      return {
        show() {
          return {
            rename() {
              note.value?.$refs?.title?.click();
            },
            move() {
              moveRef.value?.show();
            },
            // copy() {
            //   (t.$refs?.copy as any)?.show();
            // },
            note(les: any, index: number) {
              // temps = reactive();
              temps.value = {
                lesson: les,
                index,
              };

              if (!editor.value.write) {
                item.value = les;
                // item = reactive(les);
                // setTimeout(() => {
                //   item = reactive(les);
                // }, 10);
                editor.value.setHtml(les.note);
                note.value.show();
              } else {
                $msg({
                  text: $t(`${lang}.msg.wait`),
                  type: "error",
                });
              }
            },
          };
        },
        fun() {
          return {
            add() {
              const popLesson = pop.lesson.add;
              return {
                createInput() {
                  if (
                    popLesson.data.num <= 0 ||
                    popLesson.data.num +
                      (course.value.lessons + popLesson.data.lessons.length) >
                      layoutDataStore.data.value.lessons_limit
                  ) {
                    if (
                      course.value.lessons + popLesson.data.lessons.length >=
                      layoutDataStore.data.value.lessons_limit
                    ) {
                      popLesson.err.num = trans(
                        `${lang}.pop.lesson.add.errors.limit`,
                        {
                          limit: `${
                            layoutDataStore.data.value.lessons_limit
                          } ${$help().titleDay({
                            num: layoutDataStore.data.value.lessons_limit,
                            word: $t("gl.lessons") as [],
                          })}`,
                        }
                      );
                    } else {
                      // popLesson.err.num = `${$t(
                      //   `${lang}.pop.lesson.add.errors.numberLes.0`
                      // )}  ${
                      //   layoutDataStore.data.value.lessons_limit -
                      //   (course.value.lessons + popLesson.data.lessons.length)
                      // } , ${$t(`${lang}.pop.lesson.add.errors.numberLes.1`)}`;

                      popLesson.err.num = `${$t(
                        `${lang}.pop.lesson.add.errors.numberLes.0`
                      )} ${
                        layoutDataStore.data.value.lessons_limit
                      } ${$help().titleDay({
                        num: layoutDataStore.data.value.lessons_limit,
                        word: $t("gl.lessons") as [],
                      })} ${$t(`${lang}.pop.lesson.add.errors.numberLes.1`)}  ${
                        layoutDataStore.data.value.lessons_limit -
                        (course.value.lessons + popLesson.data.lessons.length)
                      } ${$help().titleDay({
                        num:
                          layoutDataStore.data.value.lessons_limit -
                          (course.value.lessons +
                            popLesson.data.lessons.length),
                        word: $t("gl.lessons") as [],
                      })}.`;
                    }

                    checkHeightPop();
                    return;
                  } else {
                    popLesson.err.num = "";
                  }

                  if (popLesson.data.word.length > 40) {
                    popLesson.err.word = $t(
                      `${lang}.pop.lesson.add.errors.word`
                    );
                    checkHeightPop();
                    return;
                  } else {
                    popLesson.err.word = "";
                  }

                  // const lesLength = this.lessons.length;

                  for (let i = 0; i < popLesson.data.num; i++) {
                    popLesson.data.lessons.push({
                      name: `${popLesson.data.word} (${
                        popLesson.data.lessons.length + course.value.lessons + 1
                      })`,
                      error: false,
                    });
                  }

                  checkHeightPop();
                },
                removeInput(index: number) {
                  popLesson.data.lessons = popLesson.data.lessons.filter(
                    (_: any, i: number) => i !== index
                  );
                },
                putInput(e: ClipboardEvent, index: number) {
                  e.preventDefault();
                  const text = e.clipboardData?.getData("text");
                  const array = text
                    ?.split(/\n/)
                    .map((e) => {
                      e = e.replace(/\r/, "").trim();
                      return e;
                    })
                    .filter((e) => e.trim() !== "");

                  if (
                    course.value.lessons + array?.length >
                    layoutDataStore.data.value.lessons_limit
                  ) {
                    if (
                      course.value.lessons + array?.length >=
                      layoutDataStore.data.value.lessons_limit
                    ) {
                      // `تم الوصول الى الحد ${layoutDataStore.data.value.lessons_limit} درس, لا يمكنك اضافة دروس اضافية`
                      $msg({
                        text: trans(`${lang}.pop.lesson.add.errors.limit`, {
                          limit: `${
                            layoutDataStore.data.value.lessons_limit
                          } ${$help().titleDay({
                            num: layoutDataStore.data.value.lessons_limit,
                            word: $t("gl.lessons") as [],
                          })}`,
                        }),
                        type: "error",
                      });
                    } else {
                      $msg({
                        text: `${$t(
                          `${lang}.pop.lesson.add.errors.numberLes.0`
                        )} ${
                          layoutDataStore.data.value.lessons_limit
                        } ${$help().titleDay({
                          num: layoutDataStore.data.value.lessons_limit,
                          word: $t("gl.lessons") as [],
                        })} ${$t(
                          `${lang}.pop.lesson.add.errors.numberLes.1`
                        )}  ${
                          layoutDataStore.data.value.lessons_limit -
                          (course.value.lessons + popLesson.data.lessons.length)
                        } ${$help().titleDay({
                          num:
                            layoutDataStore.data.value.lessons_limit -
                            (course.value.lessons +
                              popLesson.data.lessons.length),
                          word: $t("gl.lessons") as [],
                        })}.`,
                        type: "error",
                      });
                    }

                    return;
                  }

                  const splitTop = popLesson.data.lessons.slice(0, index + 1);
                  const splitBottom = popLesson.data.lessons.slice(
                    index + 1,
                    popLesson.data.lessons.length
                  );

                  for (let i = 0; i < array?.length; i++) {
                    if (i === 0) {
                      splitTop[splitTop.length - 1].name += array![i];
                    } else {
                      splitTop.push({
                        name: `${array![i]}`,
                        error: false,
                      });
                    }
                  }

                  popLesson.data.lessons = [...splitTop, ...splitBottom];
                  checkHeightPop();
                },
                async done() {
                  if (
                    course.value.lessons + popLesson.data.lessons.length >
                    layoutDataStore.data.value.lessons_limit
                  ) {
                    if (
                      course.value.lessons ==
                      layoutDataStore.data.value.lessons_limit
                    ) {
                      $msg({
                        text: trans(`${lang}.pop.lesson.add.errors.limit`, {
                          limit: `${
                            layoutDataStore.data.value.lessons_limit
                          } ${$help().titleDay({
                            num: layoutDataStore.data.value.lessons_limit,
                            word: $t("gl.lessons") as [],
                          })}`,
                        }),
                        type: "error",
                      });
                    } else {
                      $msg({
                        text: `${$t(
                          `${lang}.pop.lesson.add.errors.numberLes.0`
                        )}  ${
                          layoutDataStore.data.value.lessons_limit -
                          (course.value.lessons + popLesson.data.lessons.length)
                        } , ${$t(`${lang}.pop.lesson.add.errors.numberLes.1`)}`,
                        type: "error",
                      });
                    }

                    return;
                  }

                  popLesson.loading = true;
                  let status = true;
                  popLesson.data.lessons.forEach((e: any) => {
                    if (
                      !$filters.length({ item: e.name.trim(), max: 50, min: 1 })
                    ) {
                      e.error = true;
                      // this.$el.querySelectorAll(".lessons .items div")[index].classList.add("error");
                      status = false;
                      popLesson.err.filter = true;
                    } else {
                      e.error = false;
                      // this.$el.querySelectorAll(".lessons .items div")[index].classList.remove("error");
                    }
                  });

                  if (status) {
                    popLesson.err.filter = false;
                    await $api
                      .post(
                        `${$app().BK_URL.nashit}/course/${
                          route.params.id
                        }/lessons/add`,
                        {
                          body: {
                            // course : "",
                            lessons: popLesson.data.lessons,
                            // form: !more.value ? lessons.value.length : false,
                          },
                        }
                      )
                      .then(async (_) => {
                        const lessonsAfterAdd = await $api
                          .get<any>(
                            `${$app().BK_URL.nashit}/course/${
                              route.params.id
                            }/lessons`
                          )
                          .catch(() => {
                            throw createError("Error");
                          });

                        lessons.value = lessonsAfterAdd.lessons.data;
                        lessons.value = lessons.value.map((e: any) => {
                          e.loading = false;
                          return e;
                        });

                        course.value = lessonsAfterAdd.course;

                        layoutDataStore.data.value.lessons =
                          course.value.lessons;

                        page = 2;

                        more.value = true;

                        await $sleep(100, setScrollEvents);

                        $msg({
                          text: $t(`${lang}.msg.add`),
                          type: "ok",
                        });

                        // t.course.lessons = res.data.course.lessons
                        // t.pop.lesson.add.data.lessons = [{
                        //   name : `${pop.data.word} (${res.data.course.lessons+1})`,
                        //    error : false
                        // }]

                        // TODO: Ckick HERE
                        // pop.data.lessons = [{
                        //   name : `${pop.data.word} (${res.data.course.lessons+1})`,
                        //
                        // }];

                        addLessons.value.close();
                      })
                      .catch((_) => {
                        $msg({
                          text: $t(`gl.msg.error.sendData`),
                          type: "error",
                        });
                      });

                    popLesson.loading = false;
                  }
                },
              };
            },
          };
        },
      };
    },
    options() {
      return {
        show() {
          lessonListOptions.value.classList.toggle("active");
        },
        close() {
          lessonListOptions.value.classList.remove("active");
        },
      };
    },
  };
}

// async function isCachesSupport() {
//   let support = true;
//   if (caches?.open) {
//     await caches
//       .open("nashit-runtime-cache")
//       .then((cache) => {
//         if (!cache || !cache?.match || !cache?.put) {
//           support = false;
//         }
//       })
//       .catch((_) => {
//         support = false;
//       });
//   } else {
//     support = false;
//   }

//   return support;
// }

// function setOffline() {
//   $local.update("offline", { "course.all": true });
//   // this.$store.commit("setSystem",{pro : "offlineData",value : true});
//   systemStore.offline = true;
// }
</script>

<template>
  <div class="all-page-course" data-width="medium">
    <h1>{{ $t(`${lang}.title`) }}</h1>
    <div class="app">
      <button
        v-if="!$route.params.profile"
        class="btn-addNewLes"
        @click="$refs['addLessons'].show()">
        <i class="ri-add-circle-line"></i>
        {{ $t(`${lang}.btn`) }}
      </button>
      <div class="all-lessons">
        <div ref="boxs" class="boxs">
          <div
            v-for="(les, index) in lessons.value"
            :key="index"
            :class="{
              'no-note': !les.smallNote,
              remove: les?.process?.remove,
            }">
            <div class="rigth">
              <span class="icon" :class="{ done: les.done }">
                <template v-if="les.done">
                  <i class="ri-checkbox-circle-line"></i>
                </template>
                <template v-else>
                  <i class="ri-forbid-2-line"></i>
                </template>
              </span>

              <div class="box-text">
                <p>{{ les.name }}</p>
                <span class="date">{{ les.date.required }}</span>
              </div>
            </div>

            <div class="left box-note">
              <button
                class="note-btn"
                @click="lesson().pop().show().note(les, index)">
                <i class="ri-sticky-note-line"></i>
              </button>
              <p v-if="les.smallNote">
                {{ les.smallNote }}
              </p>
            </div>
          </div>

          <small-loading v-if="shows.loadingMoreLessons"></small-loading>
        </div>
        <PopUp
          ref="note"
          class="note-popup all-note-popup list smallTitle"
          :class="{ done: temps.value?.lesson?.date.done }"
          :title="temps.value.lesson.name || ''"
          :can-change-title="true"
          @blurInputTitle="lesson().fun().rename($event)"
          @onClose="$refs['editor'].doneNow()">
          <div class="options-pop">
            <button class="btn-list-options" @click="lesson().options().show()">
              <i class="ri-more-2-line"></i>
            </button>
            <div ref="lessonListOptions" class="list">
              <ul @click="lesson().options().close()">
                <li>
                  <button @click="lesson().pop().show().rename()">
                    <i class="ri-edit-2-line"></i>
                    {{ $t(`${lang}.pop.lesson.note.options.rename`) }}
                  </button>
                </li>
                <li>
                  <button @click="lesson().pop().show().move()">
                    <i class="ri-external-link-line"></i>
                    {{ $t(`${lang}.pop.lesson.note.options.move`) }}
                  </button>
                </li>
                <li>
                  <button @click="lesson().fun().copy()">
                    <i class="ri-file-copy-line"></i>
                    {{ $t(`${lang}.pop.lesson.note.options.copy`) }}
                  </button>
                </li>

                <li class="delete">
                  <button @click="lesson().fun().remove()">
                    <i class="ri-close-line"></i>
                    {{ $t(`${lang}.pop.lesson.note.options.remove`) }}
                  </button>
                </li>
              </ul>
            </div>
          </div>
          <!--   @onOfflineSave="lesson().fun().noteOffline($event)" -->
          <note-book
            ref="editor"
            :set-text="item.value.note"
            :disabled="!!$route.params.profile"
            @done="lesson().fun().note($event)"></note-book>
          <p v-if="temps.value?.lesson?.date?.done" class="isDoneLesson">
            <i class="ri-checkbox-circle-line"></i>
            {{ $t(`${lang}.pop.lesson.note.doneIn`) }}
            {{ $help().formatDate(temps.value?.lesson?.date?.done) }}
          </p>
          <full-loading v-if="pop.lesson.loading"></full-loading>
        </PopUp>
      </div>
      <PopUp
        ref="addLessons"
        class="add-new-les-popup"
        :title="$t(`${lang}.pop.lesson.add.title`)"
        :btn="$t(`gl.done`)"
        btn-icon="ri-check-line"
        @onClickBtn="lesson().pop().fun().add().done()">
        <div class="inputs">
          <div>
            <label>{{ $t(`${lang}.pop.lesson.add.numberLes.label`) }}</label>
            <input
              v-model.number="pop.lesson.add.data.num"
              type="number"
              :class="{ error: pop.lesson.add.err.num }" />
            <p v-if="pop.lesson.add.err.num" class="errMsg">
              {{ pop.lesson.add.err.num }}
            </p>
            <p class="comment">
              {{ $t(`${lang}.pop.lesson.add.numberLes.comment`) }}
            </p>
          </div>
          <div>
            <label>{{ $t(`${lang}.pop.lesson.add.customWord.label`) }}</label>
            <input
              v-model="pop.lesson.add.data.word"
              type="text"
              :class="{ error: pop.lesson.add.err.word }" />
            <p v-if="pop.lesson.add.err.word" class="errMsg">
              {{ pop.lesson.add.err.word }}
            </p>
            <p class="comment">
              {{ $t(`${lang}.pop.lesson.add.customWord.comment`) }}
            </p>
          </div>
          <button
            @click="lesson().pop().fun().add().createInput()"
            class="add-new-les-btn">
            <i class="ri-add-circle-line"></i>
            {{ $t(`${lang}.pop.lesson.add.btn`) }}
          </button>
          <div class="addedLessons">
            <label>{{ $t(`${lang}.pop.lesson.add.addedLes.title`) }}</label>

            <div class="items">
              <div
                v-for="(item, index) in pop.lesson.add.data.lessons"
                :key="index + course.value.lessons"
                class="box-item"
                :class="{ error: item.error }">
                <input
                  v-model="item.name"
                  type="text"
                  :placeholder="
                    String($t(`${lang}.pop.lesson.add.addedLes.placeholder`))
                  "
                  @paste="lesson().pop().fun().add().putInput($event, index)" />
                <button
                  v-if="index !== 0"
                  @click="lesson().pop().fun().add().removeInput(index)">
                  <i class="ri-delete-bin-2-line"></i>
                </button>
              </div>
            </div>
            <p
              :class="{ visible: pop.lesson.add.err.filter }"
              class="errMsg v-hiddin">
              {{ $t(`${lang}.pop.lesson.add.errors.addedLes`) }}
            </p>

            <p class="comment">
              {{ $t(`${lang}.pop.lesson.add.addedLes.comment`) }}
              <code>
                <span class="btn-style">CTRL</span>
                +
                <span class="btn-style">V</span>
              </code>
            </p>
          </div>
        </div>
        <full-loading v-if="pop.lesson.add.loading"></full-loading>
      </PopUp>

      <PopUp
        ref="moveRef"
        class="move-lesson-popup medium-box"
        :title="$t(`${lang}.pop.lesson.move.title`)">
        <div>
          <template v-if="lessons.value.length > 1">
            <span>
              {{ $t(`${lang}.pop.lesson.move.title`) }} "{{
                temps.value?.lesson?.name
              }}"
            </span>
            <div
              class="select"
              :class="{ active: pop.lesson.move.showListType }"
              @click="
                pop.lesson.move.showListType = !pop.lesson.move.showListType
              ">
              <p>
                {{ pop.lesson.move.data.title }}
                <i class="ri-arrow-drop-down-line"></i>
              </p>
              <ElementsList
                :list="[
                  {
                    title: $t(`${lang}.pop.lesson.move.before`),
                    type: 'before',
                  },
                  { title: $t(`${lang}.pop.lesson.move.after`), type: 'after' },
                ]"
                @select="pop.lesson.move.data = $event"></ElementsList>
            </div>
            <span>{{ $t(`gl.lesson`) }} :</span>
            <ElementsList
              class="lessons-list"
              :list="lessonsFilterToMove"
              @select="lesson().fun().move($event)"
              :loading="pop.lesson.move.loadingMore"></ElementsList>
          </template>

          <p v-else>{{ $t(`${lang}.pop.lesson.move.oneLesson`) }}</p>
        </div>
        <full-loading v-if="pop.lesson.loading"></full-loading>
        <!-- <full-loading v-if="loading.moveTask"></full-loading> -->
      </PopUp>
    </div>
  </div>
</template>
