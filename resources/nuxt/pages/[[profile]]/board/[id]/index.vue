<script setup lang="ts">
import draggable from "vuedraggable";
import pageLayout from "~/transition/page.layout";

definePageMeta({
  name: "BaordIndexPage",
  layout: "page",
  pageTransition: pageLayout,
});

const route = useRoute();
const trans = useNuxtApp().$i18n.t;
const data = await $api
  .get<any>(`${$app().BK_URL.nashit}${route.params.profile ? '/profile/'+route.params.profile : ''}/board/${route.params.id}/lists`)
  .catch(() => {
    throw createError({
      statusCode: 404,
      statusMessage: "Board Not Found",
      fatal: true,
    });
  });
// console.log("run")
const lists = reactive({
  value: data.lists,
});

// const groupLists = ref([]) as Ref<string[]>;

lists.value.map((e: any) => {
  // if(navigator.onLine) {
  //   groupLists.value.push("list");
  // } else {
  //   groupLists.value.push(e.id);
  // }

  e.loading = false;
  e.smallLoading = [];

  e.tasks_loaded = navigator.onLine ? e.tasks_number <= 10 : true;

  e.loadingMore = false;
  e.tasks = e.tasks.map((t: any) => {
    t.loading = false;
    return t;
  });
  return e;
});

// console.log(lists.value);

const lang = "pages.board.index";
const { $i18n } = useNuxtApp();
const systemStore = useSystemStore();
const layoutDataStore = useLayoutDataStore();
const temps = reactive({
  list: {
    id: null,
    name: "",
    date: { create: null, lastUpdate: null },
    tasks: [],
  },
  task: {
    name: null,
    note: undefined,
    date: { done: null, create: null, lastUpdate: null },
  },
  taskIndex: null as null | number,
  listIndex: null as null | number,
});

const d_pop = reactive({
  todoList: {
    rename: {
      name: "",
      error: false,
      loading: false,
    },
    copy: {
      type: "with-tasks",
      loading: false,
    },
    create: {
      name: "",
      error: false,
      loading: false,
    },
  },
  task: {
    loading: false,
  },
});

// const dragOptions = computed(() => {
//   return {
//     animation: 200,
//   };
// });

const getLists = computed(() => {
  return lists.value.filter((e: any) => e.id !== temps.list.id);
});

const todoListOptions = ref();
const taskListOptions = ref();
const note = ref();
const rename_todo = ref();
const copyTodo = ref();
const detailsTodo = ref();
const editor = ref();
const moveTask = ref();
const detailsTask = ref();
const createTodo = ref();
const input_value = ref();

// onMounted(() => {
//   console.dir(createTodo);
// });



function m_show() {
  return {
    todoList() {
      return {
        options(index: number): void {
          todoListOptions.value.forEach(
            (e, i) => i !== index && e.classList.remove("active")
          );
          todoListOptions.value[index].classList.toggle("active");
        },
      };
    },
    task() {
      return {
        options() {
          taskListOptions.value.classList.toggle("active");
        },
        rename() {
          //!!!!!!!!!!!
          note.value.$refs?.title?.click();
        },
      };
    },
  };
}

function m_close() {
  return {
    todoList() {
      return {
        options(index: number): void {
          todoListOptions.value[index].classList.remove("active");
        },
      };
    },
    task() {
      return {
        options() {
          taskListOptions.value.classList.remove("active");
        },
      };
    },
  };
}

function m_pop({
  list,
  task,
  listIndex,
  taskIndex,
}: { list?: any; task?: any; listIndex?: any; taskIndex?: any } = {}) {
  return {
    show() {
      list && (temps.list = list);
      task && (temps.task = task);
      listIndex !== undefined && (temps.listIndex = listIndex);
      taskIndex !== undefined && (temps.taskIndex = taskIndex);

      return {
        todoList() {
          return {
            rename() {
              d_pop.todoList.rename.name = temps.list.name;
              rename_todo.value.show()!;
            },
            copy() {
              copyTodo.value.show()!;
            },
            details() {
              detailsTodo.value.show()!;
            },
          };
        },
        task() {
          return {
            note() {
              temps.task = {};

              if (!editor.value.write) {
                setTimeout(() => {
                  temps.task = task;
                  
                }, 10);

                // console.log(task.note);
                editor.value.setHtml(task.note);
                note.value.show();
              } else {
                $msg({
                  text: $t(`${lang}.msg.wait`),
                  type: "error",
                });
              }
              setTimeout(() => {
                document
                  .querySelectorAll(".pop-up.smallTitle .box h1")
                  .forEach((e) => {
                    e.scrollTo(0, 0);
                  });
              }, 10);
            },
            details() {
              detailsTask.value.show();
            },
            move() {
              moveTask.value.show();
            },
          };
        },
      };
    },
  };
}

const listsDOM = ref();
let events = [];
function todoList(list?: any) {
  !list && (list = temps.list);

  return {
    remove() {
      const lengthListAvailable = lists.value.filter(
        (e) => !e?.process?.remove
      ).length;

      if (lengthListAvailable === 1) {
        $msg({
          text: $t(`${lang}.msg.todoList.delete.one`),
          type: "error",
        });
        return;
      }

      function errorHandler(msg: string) {
        list.loading = false;

        $msg({
          text: msg,
          type: "error",
        });
      }

      $msg({
        text: `${$t(`${lang}.msg.todoList.delete.sure`)} "${list.name}"`,
        type: "sure",
        btns: {
          async t() {
            list.loading = true;

            if (!navigator.onLine) {
              errorHandler(String($t(`${lang}.msg.todoList.delete.error`)));
              // if (!(await isCachesSupport())) {
              //   errorHandler(String($t("gl.msg.error.cache.support")));
              //   return;
              // }
              // caches
              //   .open("nashit-runtime-cache")
              //   .then(function (cache) {
              //     cache
              //       .match("/api/board.json")
              //       .then(function (e) {
              //         if (!e) {
              //           errorHandler(String($t("gl.msg.error.cache.found")));
              //           return;
              //         }
              //         e?.json()
              //           .then(function (dataJson) {
              //             const listRemove = dataJson.lists.find(
              //               (e: any) => e.id === list.id
              //             );
              //             if (listRemove?.process?.create) {
              //               dataJson.lists = dataJson.lists.filter(
              //                 (e: any) => e.id !== list.id
              //               );
              //             } else {
              //               listRemove.process
              //                 ? (listRemove.process.remove = new Date()
              //                     .toISOString()
              //                     .replace("Z", "000Z"))
              //                 : (listRemove.process = {
              //                     remove: new Date()
              //                       .toISOString()
              //                       .replace("Z", "000Z"),
              //                   });
              //             }

              //             const newRes = new Response(
              //               JSON.stringify(dataJson),
              //               e
              //             );
              //             cache
              //               .put("/api/board.json", newRes)
              //               .then((_) => {
              //                 const listRemove = lists.value.find(
              //                   (e: any) => e.id === list.id
              //                 );
              //                 if (listRemove?.process?.create) {
              //                   lists.value = lists.value.filter(
              //                     (e: any) => e.id !== list.id
              //                   );
              //                 } else {
              //                   listRemove.process
              //                     ? (listRemove.process.remove = new Date()
              //                         .toISOString()
              //                         .replace("Z", "000Z"))
              //                     : (listRemove.process = {
              //                         remove: new Date()
              //                           .toISOString()
              //                           .replace("Z", "000Z"),
              //                       });
              //                 }

              //                 $msg({
              //                   text: $t(`${lang}.msg.todoList.delete.done`),
              //                   type: "ok",
              //                 });
              //                 list.loading = false;
              //               })
              //               .catch((_) => {
              //                 errorHandler(
              //                   String($t(`gl.msg.error.cache.put`))
              //                 );
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
                .post(`${$app().BK_URL.nashit}/list/${list.id}/remove`)
                .then(async (_) => {
                  events = events.filter((e) => e.id !== list.id);
                  lists.value = lists.value.filter(
                    (e: any) => e.id !== list.id
                  );
                  await $cache().edit(
                    [
                      {
                        req: `${$app().BK_URL.nashit}/board/${
                          route.params.id
                        }/lists`,
                        searchIn: (data) => data.lists,
                      },
                    ],
                    (lists) => {
                      lists = lists.filter((e: any) => e.id !== list.id);
                      return lists;
                    }
                  );

                  list.loading = false;
                })
                .catch((_) => {
                  errorHandler(String($t(`${lang}.msg.todoList.delete.error`)));
                });
            }
          },
        },
      });
    },
    async rename() {
      const pop = d_pop.todoList.rename;
      if (!$filters.length({ item: pop.name, max: 20, min: 1 })) {
        pop.error = true;
        return false;
      } else {
        pop.error = false;
      }

      pop.loading = true;

      function errorHandler(msg: string) {
        rename_todo.value.close();
        pop.loading = false;

        $msg({
          text: msg,
          type: "error",
        });
      }

      if (!navigator.onLine) {
        errorHandler(String($t(`${lang}.msg.todoList.cant`)));
        // if (!(await isCachesSupport())) {
        //   errorHandler(String($t("gl.msg.error.cache.support")));
        //   return;
        // }
        // caches
        //   .open("nashit-runtime-cache")
        //   .then(function (cache) {
        //     cache
        //       .match("/api/board.json")
        //       .then(function (e) {
        //         if (!e) {
        //           errorHandler(String($t("gl.msg.error.cache.found")));
        //           return;
        //         }
        //         e?.json()
        //           .then(function (dataJson) {
        //             dataJson.lists[temps.listIndex].name = pop.name.trim();
        //             dataJson.lists[temps.listIndex].date.lastUpdate =
        //               new Date().getTime();

        //             dataJson.lists[temps.listIndex].process
        //               ? (dataJson.lists[temps.listIndex].process.rename =
        //                   new Date().toISOString().replace("Z", "000Z"))
        //               : (dataJson.lists[temps.listIndex].process = {
        //                   rename: new Date().toISOString().replace("Z", "000Z"),
        //                 });

        //             const newRes = new Response(JSON.stringify(dataJson), e);
        //             cache
        //               .put("/api/board.json", newRes)
        //               .then((_) => {
        //                 setOffline();
        //                 temps.list.name = pop.name.trim();
        //                 temps.list.date.lastUpdate = new Date().getTime();

        //                 temps.list.process
        //                   ? (temps.list.process.rename = new Date()
        //                       .toISOString()
        //                       .replace("Z", "000Z"))
        //                   : (temps.list.process = {
        //                       rename: new Date()
        //                         .toISOString()
        //                         .replace("Z", "000Z"),
        //                     });

        //                 rename_todo.value.close();
        //                 pop.loading = false;
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
          .post(`${$app().BK_URL.nashit}/list/${temps.list.id}/rename`, {
            body: { name: pop.name },
          })
          .then((res) => {
            temps.list.date.lastUpdate = res.lastUpdate;
            temps.list.name = pop.name.trim();
            rename_todo.value.close();
            pop.loading = false;
          })
          .catch((_) => {
            errorHandler(String($t(`${lang}.msg.todoList.cant`)));
          });
      }
    },
    async copy() {
      const pop = d_pop.todoList.copy;

      const list = JSON.parse(JSON.stringify(temps.list));

      pop.loading = true;

      function errorHandler(msg: string) {
        copyTodo.value.close();
        pop.loading = false;

        $msg({
          text: msg,
          type: "error",
        });
      }
      if (!navigator.onLine) {
        errorHandler(String($t(`${lang}.msg.todoList.copy.error`)));
        // if (!(await isCachesSupport())) {
        //   errorHandler(String($t("gl.msg.error.cache.support")));
        //   return;
        // }
        // await caches
        //   .open("nashit-runtime-cache")
        //   .then(async function (cache) {
        //     await cache
        //       .match("/api/board.json")
        //       .then(async function (e) {
        //         if (!e) {
        //           errorHandler(String($t("gl.msg.error.cache.found")));
        //           return;
        //         }
        //         await e
        //           ?.json()
        //           .then(async function (dataJson) {
        //             list.date = {
        //               create: new Date().getTime(),
        //               lastUpdate: new Date().getTime(),
        //             };

        //             list.process
        //               ? (list.process.create = new Date()
        //                   .toISOString()
        //                   .replace("Z", "000Z"))
        //               : (list.process = {
        //                   create: new Date().toISOString().replace("Z", "000Z"),
        //                 });

        //             if (pop.type === "without-tasks") {
        //               list.tasks = [];
        //             }

        //             list.id = new Date().getTime();
        //             list.tasks.forEach((e, i) => {
        //               e.date = {
        //                 create: new Date().getTime() + i,
        //                 lastUpdate: new Date().getTime() + i,
        //                 done: e.date.done,
        //               };
        //             });

        //             const copyWord = String($t(`${lang}.copied`));
        //             if (
        //               !$filters.range({
        //                 item: list.name.length + copyWord.length,
        //                 max: 20,
        //                 min: 1,
        //               })
        //             ) {
        //               list.name =
        //                 list.name.substr(0, 20 - (copyWord.length + 3)) + "...";
        //             }
        //             list.name = list.name + copyWord;

        //             dataJson.lists.push(list);

        //             const newRes = new Response(JSON.stringify(dataJson), e);
        //             await cache
        //               .put("/api/board.json", newRes)
        //               .then((_) => {
        //                 setOffline();
        //                 copyTodo.value.close();

        //                 lists.value.push(list);

        //                 $msg({
        //                   text: String($t(`${lang}.msg.todoList.copy.done`)),
        //                   type: "ok",
        //                 });
        //                 pop.loading = false;
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
        await $api
          .post<any>(`${$app().BK_URL.nashit}/list/${list.id}/copy`, {
            body: { name: list.name, type: pop.type },
          })
          .then(async (res) => {
            copyTodo.value.close();

            await $cache().edit(
              [
                {
                  req: `${$app().BK_URL.nashit}/board/${route.params.id}/lists`,
                  searchIn: (data) => data.lists,
                },
              ],
              (lists) => {
                // item.tasks = item.tasks.concat(res.data);
                lists.push(res);
                // console.log(item);
                return lists;
              }
            );
            // list.id = res.id || "RES";
            // list.name = res.name || "RES";
            // if (pop.type === "without-tasks") {
            //   list.tasks = [];
            // }
            res.loading = false;
            res.smallLoading = [];
            res.tasks_loaded = res.tasks_number <= 10;
            res.loadingMore = false;

            res.tasks = res.tasks.map((t: any) => {
              t.loading = false;
              return t;
            });

            lists.value.push(res);
            setTimeout(() => {
              setScrollListsEvent(
                listsDOM.value[listsDOM.value.length - 1],
                listsDOM.value.length - 1
              );
            }, 1);

            $msg({
              text: $t(`${lang}.msg.todoList.copy.done`),
              type: "ok",
            });
            pop.loading = false;
          })
          .catch((_) => {
            errorHandler(String($t(`${lang}.msg.todoList.copy.error`)));
          });
      }

      pop.type = "with-tasks";
    },
    async cerate() {
      const pop = d_pop.todoList.create;
      if (!$filters.length({ item: pop.name, max: 20, min: 1 })) {
        pop.error = true;
        return false;
      } else {
        pop.error = false;
      }

      function errorHandler(msg: string) {
        createTodo.value.close();
        pop.loading = false;

        $msg({
          text: msg,
          type: "error",
        });
      }
      pop.loading = true;


      if(layoutDataStore.data.value.lists_limit < lists.value.length+1) {
      
        errorHandler($t(`${lang}.msg.todoList.create.limit`) as string + " " + layoutDataStore.data.value.lists_limit);
        return;
      }
      if (!navigator.onLine) {
        errorHandler(String($t(`${lang}.msg.todoList.create.cant`)));
        // if (!(await isCachesSupport())) {
        //   errorHandler(String($t("gl.msg.error.cache.support")));
        //   return;
        // }
        // caches
        //   .open("nashit-runtime-cache")
        //   .then(function (cache) {
        //     cache
        //       .match("/api/board.json")
        //       .then(function (e) {
        //         if (!e) {
        //           errorHandler(String($t("gl.msg.error.cache.found")));
        //           return;
        //         }
        //         e?.json()
        //           .then(function (dataJson) {
        //             const pushList = {
        //               id: new Date().getTime(),
        //               name: pop.name.trim(),
        //               tasks: [],
        //               date: {
        //                 create: new Date().getTime(),
        //                 lastUpdate: new Date().getTime(),
        //               },
        //               process: {
        //                 create: true,
        //               },
        //             };
        //             dataJson.lists.push(pushList);

        //             const newRes = new Response(JSON.stringify(dataJson), e);
        //             cache
        //               .put("/api/board.json", newRes)
        //               .then((_) => {
        //                 setOffline();
        //                 lists.value.push({
        //                   ...pushList,
        //                   loading: false,
        //                   smallLoading: [],
        //                 });
        //                 createTodo.value.close();
        //                 pop.loading = false;
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
          .post<any>(
            `${$app().BK_URL.nashit}/board/${route.params.id}/list/create`,
            { body: { name: pop.name } }
          )
          .then(async (res) => {
            await $cache().edit(
              [
                {
                  req: `${$app().BK_URL.nashit}/board/${route.params.id}/lists`,
                  searchIn: (data) => data.lists,
                },
              ],
              (lists) => {
                // item.tasks = item.tasks.concat(res.data);
                lists.push(res);
                // console.log(item);
                return lists;
              }
            );

            res.loading = false;
            res.smallLoading = [];
            res.tasks_loaded = true;
            res.tasks = res.tasks.map((t: any) => {
              t.loading = false;
              return t;
            });
            lists.value.push(res);
            createTodo.value.close();
            pop.loading = false;
          })
          .catch((_) => {
            errorHandler(String($t(`${lang}.msg.todoList.create.cant`)));
          });
      }
    },
  };
}

function task({
  taskIndex,
  listIndex,
}: { taskIndex?: any; listIndex?: any } = {}) {
  return {
    async create(list: any) {
      if (layoutDataStore.data.value.tasks_limit < list.tasks_number + 1) {
        $msg({
          text: `${$t(`${lang}.msg.task.create.limitLength`)} ${layoutDataStore.data.value.tasks_limit}!!`,
          type: "error",
        });
        return;
      }
      const input = input_value.value[listIndex];

      if (input.value.trim() === "") {
        return;
      }

      if (!$filters.length({ item: input.value, max: 50, min: 1 })) {
        $msg({
          text: $t(`${lang}.msg.task.create.limitLetters`),
          type: "error",
        });
        return false;
      }
      const task = {
        id: new Date().getTime(),
        name: input.value.trim(),
        done: false,
        note: "",
        order: 0,
        list_id: list.id,
        date: {
          create: $help().getDateISO(),
          lastUpdate: $help().getDateISO(),
          done: null,
        },
        loading: true,
      };

      if (list.tasks_loaded || !navigator.onLine) {
        list.tasks.push(task);
        // console.log("up");
        setTimeout(() => {
          boxTasks.scrollTo(0, boxTasks.scrollHeight);
        }, 1);
      }
      const boxTasks = Array.from(document.querySelectorAll(".tasks"))[
        listIndex
      ];

      list.smallLoading.push(task);

      input.value = "";

      // function errorHandler(msg: string) {
      //   list.tasks = list.tasks.filter((e: any) => e.id !== task.id);

      //   $msg({
      //     text: msg,
      //     type: "error",
      //   });
      // }

      if (!navigator.onLine) {
        await $cache({
          name: "BoardPage",
          done() {
            task.loading = false;
            list.tasks_number++;
            list.smallLoading.shift();
          },
          err() {
            list.tasks = list.tasks.filter((e: any) => e.id !== task.id);
            $msg({
              text: $t("gl.msg.error.cache.put"),
              type: "error",
            });
          },
        }).edit(
          [
            {
              req: `${$app().BK_URL.nashit}/board/${route.params.id}/lists`,
              searchIn: (data) => data.lists,
              target: (l) => l.id == list.id,
            },
          ],
          (list: any) => {
            list.tasks.push(task);
            $ofun(task).path({
              path: "process.create",
              value: $help().getDateISO(),
            });
            list.tasks_number++;
            return list;
          }
        );
      } else {
        $api
          .post<any>(`${$app().BK_URL.nashit}/list/${list.id}/task/create`, {
            body: { name: task.name },
          })
          .then(async (res) => {
            // res.loading = false;
            // task = res;

            task.date = res.date;
            task.name = res.name;
            task.id = res.id;
            task.order = res.order;
            list.tasks_number++;
            await $cache().edit(
              [
                {
                  req: `${$app().BK_URL.nashit}/board/${route.params.id}/lists`,
                  searchIn: (data) => data.lists,
                  target: (l) => l.id == list.id,
                },
              ],
              (item) => {
                item.tasks_number++;
                item.tasks.push(task);
                // console.log(item);
                return item;
              }
            );

            task.loading = false;
            list.smallLoading.shift();
          })
          .catch((_) => {
            list.smallLoading.shift();
            list.tasks = list.tasks.filter((e: any) => e.id !== task.id);
            $msg({
              text:  $t(`gl.msg.error.sendData`) as string,
              type: "error",
            });
          });
      }
    },
    async changeIndex(event: any) {
      function mybefore(e: Event) {
        e.preventDefault();
        e.returnValue = true;
      }

      window.addEventListener("beforeunload", mybefore);
      const spans = Array.from(document.querySelectorAll(".tasks"));

      const boradFrom = spans.findIndex((e) => e === event.from);
      const boradTo = spans.findIndex((e) => e === event.to);

      const boradFromData = lists.value[boradFrom];
      const boradToData = lists.value[boradTo];

      const task = boradToData.tasks[event.newIndex];

      const data = {
        // from: boradFromData.id,
        to: boradToData.id,
        // oldIndex: event.oldIndex,
        newIndex: event.newIndex,
        // task: task.id,
      };

      boradFromData.smallLoading.push(data);

      if (boradFromData.id !== boradToData.id) {
        boradToData.smallLoading.push(data);
      }

      function errorHandler(msg: string) {
        boradFromData.smallLoading.shift();

        if (boradFromData.id !== boradToData.id) {
          boradToData.smallLoading.shift();
        }
        window.removeEventListener("beforeunload", mybefore);

        $msg({
          text: msg,
          type: "error",
        });
      }

      
      if (
        layoutDataStore.data.value.tasks_limit <
        (boradToData.tasks_number + 1)
      ) {
        boradToData.tasks = boradToData.tasks.filter((e) => e.id !== task.id);
        boradFromData.tasks.splice(event.oldIndex, 0, task);

        errorHandler($t(`${lang}.msg.task.create.limitLength`) + " " + layoutDataStore.data.value.tasks_limit);

        return;
      }

      if (!navigator.onLine) {
        // console.log("offline", task.id);

        await $cache({
          name: "BoardPage",
          done() {
            boradFromData.smallLoading.shift();

            if (boradFromData.id !== boradToData.id) {
              boradToData.smallLoading.shift();
            }
            window.removeEventListener("beforeunload", mybefore);
            // $ofun(boradToData).path({
            //   path: "process.reindex",
            //   value: $help().getDateISO(),
            // });
            // $ofun(boradFromData).path({
            //   path: "process.reindex",
            //   value: $help().getDateISO(),
            // });
            // $ofun(task).path({
            //         path: "process.moveTo",
            //         value: { time: $help().getDateISO(), to: boradToData.id },
            //       });
          },
          err() {
            boradToData.tasks = boradToData.tasks.filter(
              (e) => e.id !== task.id
            );
            boradFromData.tasks.splice(event.oldIndex, 0, task);
            // boradFromData.tasks =
            errorHandler($t(`gl.msg.error.proces`) as string);
          },
        }).edit(
          [
            {
              req: `${$app().BK_URL.nashit}/board/${route.params.id}/lists`,
              searchIn: (data) => data.lists,
            },
          ],
          (list: any) => {
            const listTo = list.find((e) => e.id === boradToData.id);
            const listFrom = list.find((e) => e.id === boradFromData.id);
            // $ofun(listTo).path({
            //   path: "process.reindex",
            //   value: $help().getDateISO(),
            // });

            // $ofun(listFrom).path({
            //   path: "process.reindex",
            //   value: $help().getDateISO(),
            // });

            const cacheTask = listFrom.tasks.find((e) => e.id === task.id);
            if (cacheTask) {
              // if (cacheTask.list_id == listTo.id) {
              //   delete cacheTask?.process?.move;
              // } else if (!cacheTask?.process?.create) {
              //   $ofun(cacheTask).path({
              //     path: "process.move",
              //     value: $help().getDateISO(),
              //   });
              // }

              listFrom.tasks = listFrom.tasks.filter(
                (e) => e.id !== cacheTask.id
              );
              listTo.tasks.splice(data.newIndex, 0, cacheTask);
            }
            return list;
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
        //       .match("/api/board.json")
        //       .then(function (e) {
        //         if (!e) {
        //           errorHandler(String($t("gl.msg.error.cache.found")));
        //           return;
        //         }
        //         e?.json()
        //           .then(function (dataJson) {

        //             const taskOffline = dataJson.lists[boradFrom].tasks.find(
        //               (e) => e.id === task.id
        //             );
        //             // dataJson.lists[boradFrom] = boradFromData;

        //             // console.log(dataJson.lists[boradFrom]);
        //             if (boradFromData.id !== boradToData.id) {
        //               if (
        //                 !taskOffline?.process?.remove &&
        //                 !taskOffline.previous
        //               ) {
        //                 // console.log("set", taskOffline);
        //                 // delete taskOffline.process.remove;

        //                 taskOffline.process
        //                   ? (taskOffline.process.remove = new Date()
        //                       .toISOString()
        //                       .replace("Z", "000Z"))
        //                   : (taskOffline.process = {
        //                       remove: new Date()
        //                         .toISOString()
        //                         .replace("Z", "000Z"),
        //                     });

        //                 task.previous = boradFromData.id;
        //                 // taskOffline.previous = boradFromData.id;

        //                 task.process
        //                   ? (task.process.moveToBoard = new Date()
        //                       .toISOString()
        //                       .replace("Z", "000Z"))
        //                   : (task.process = {
        //                       moveToBoard: new Date()
        //                         .toISOString()
        //                         .replace("Z", "000Z"),
        //                     });
        //               }

        //               if (taskOffline?.process?.moveToBoard) {
        //                 dataJson.lists[boradFrom].tasks = dataJson.lists[
        //                   boradFrom
        //                 ].tasks.filter((e) => e.id !== taskOffline.id);
        //                 // delete task.previous;
        //                 // delete taskOffline.previous
        //               }

        //               if (boradToData.id === task.previous) {
        //                 delete task.process.moveToBoard;
        //                 delete task.previous;
        //               }

        //               // else {

        //               //     // dataJson.lists[boradFrom].tasks.push(taskOffline);
        //               // }

        //               // if(!dataJson.lists[boradFrom].tasks.find(
        //               //   (e) => e.id === task.id
        //               // )) {

        //               // }
        //             }

        //             // dataJson.lists[boradFrom] = boradFromData;
        //             dataJson.lists[boradTo] = boradToData;

        //             console.log(dataJson.lists[boradFrom], boradFromData);
        //             console.log(dataJson.lists[boradTo], boradToData);

        //             const newRes = new Response(JSON.stringify(dataJson), e);
        //             cache
        //               .put("/api/board.json", newRes)
        //               .then((_) => {
        //                 setOffline();
        //                 // storage.update('offline', offline);
        //                 boradFromData.smallLoading.shift();
        //                 if (boradFromData.id !== boradToData.id) {
        //                   boradToData.smallLoading.shift();
        //                 }
        //                 window.removeEventListener("beforeunload", mybefore);
        //               })
        //               .catch((_) => {
        //                 fromLastProcess
        //                   ? (boradFromData.process = fromLastProcess)
        //                   : delete boradFromData.process;
        //                 toLastProcess
        //                   ? (boradToData.process = toLastProcess)
        //                   : delete boradToData.process;

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
        await $api
          .post(`${$app().BK_URL.nashit}/task/${task.id}/change`, {
            body: data,
          })
          .then(async (res) => {
            await $cache().edit(
              [
                {
                  req: `${$app().BK_URL.nashit}/board/${route.params.id}/lists`,
                  searchIn: (data) => data.lists,
                },
              ],
              (lists: any) => {
                const listTo = lists.find((e) => e.id === boradToData.id);
                const listFrom = lists.find((e) => e.id === boradFromData.id);

                if (listTo.id !== listFrom.id) {
                  listTo.tasks_number++;
                  listFrom.tasks_number--;
                }

                const cacheTask = listFrom.tasks.find((e) => e.id === task.id);
                if (cacheTask) {
                  listFrom.tasks = listFrom.tasks.filter(
                    (e) => e.id !== cacheTask.id
                  );
                  listTo.tasks.splice(data.newIndex, 0, cacheTask);
                }
                return lists;
              }
            );

            task.date.lastUpdate = res.lastUpdate;
            task.order = data.newIndex + 1;
            boradFromData.smallLoading.shift();

            if (boradFromData.id !== boradToData.id) {
              boradToData.smallLoading.shift();
            }
            window.removeEventListener("beforeunload", mybefore);
          })
          .catch((_) => {
            boradToData.tasks = boradToData.tasks.filter(
              (e) => e.id !== task.id
            );
            boradFromData.tasks.splice(event.oldIndex, 0, task);
            // boradFromData.tasks =
            errorHandler(
              $t(`gl.msg.error.sendData`) as string
            );
            
          });
      }
    },
    async setDone(task: any) {
      task.loading = true;

      function errorHandler(msg: string) {
        task.loading = false;

        $msg({
          text: msg,
          type: "error",
        });
      }
      if (!navigator.onLine) {
        // console.log(lists.value[listIndex]);
        // const taskBeforeChanged = JSON.parse(JSON.stringify(task));
        await $cache({
          name: "BoardPage",
          done() {
            task.done = !task.done;
            task.date.lastUpdate = $help().getDateISO();

            if (task.done) {
              task.date.done = $help().getDateISO();
            } else {
              task.date.done = null;
            }
            task.loading = false;
          },
          err() {
            errorHandler(String($t(`gl.msg.error.cache.put`)));
          },
        }).edit(
          [
            {
              req: `${$app().BK_URL.nashit}/board/${route.params.id}/lists`,
              searchIn: (data) =>
                data.lists.find((e) => e.id === lists.value[listIndex].id)
                  ?.tasks,
              target: (t) => t.id == task.id,
            },
          ],
          (offlineTask: any) => {
            offlineTask.done = !task.done;
            offlineTask.date.lastUpdate = $help().getDateISO();

            if (offlineTask.done) {
              offlineTask.date.done = $help().getDateISO();
            } else {
              offlineTask.date.done = null;
            }
            $ofun(offlineTask).path({
              path: "process.done",
              value: $help().getDateISO(),
            });
            return offlineTask;
          }
        );
      } else {
        $api
          .post(`${$app().BK_URL.nashit}/task/${task.id}/set`, {
            body: {
              set: !task.done,
            },
          })
          .then((res) => {
            task.done = !task.done;
            task.loading = false;
            task.date = res;
            // task.data.done = res.done

            // if(res.lesson.last) {
            //   this.shows.addNewLesBtn = false;
            // }

            // if(res.course.end) {
            //   this.$router.push("/finsh");
            // }
          })
          .catch((_) => {
            errorHandler(
              $t(`gl.msg.error.sendData`) as string
            );
          });
      }
    },
    async copy() {
      const task = JSON.parse(JSON.stringify(temps.task));

      task.date = {
        create: $help().getDateISO(),
        lastUpdate: $help().getDateISO(),
      };
      const list = temps.list;
      // loading
      d_pop.task.loading = true;



      function errorHandler(msg: string) {
        d_pop.task.loading = false;

        $msg({
          text: msg,
          type: "error",
        });
        note.value.close();
      }

      if (
        layoutDataStore.data.value.tasks_limit <
        (list.tasks_number + 1)
      ) {
        errorHandler($t(`${lang}.msg.task.create.limitLength`) + " " + layoutDataStore.data.value.tasks_limit);
        return;
      }

      if (!navigator.onLine) {
        task.id = new Date().getTime();
        const copyWord = String($t(`${lang}.copied`));
        if (
          !$filters.range({
            item: task.name.length + copyWord.length,
            max: 50,
            min: 1,
          })
        ) {
          task.name = task.name.substr(0, 50 - (copyWord.length + 3)) + "...";
        }
        task.name = task.name + copyWord;

        $ofun(task).path({
          path: "process.create",
          value: $help().getDateISO(),
        });

        await $cache({
          name: "BoardPage",
          done() {
            list.tasks.push(task);
            list.tasks_number++;
            d_pop.task.loading = false;

            $msg({
              text: String($t(`${lang}.msg.task.copy.done`)),
              type: "ok",
            });
            note.value.close();
          },
          err() {
            errorHandler(String($t(`gl.msg.error.cache.put`)));
          },
        }).edit(
          [
            {
              req: `${$app().BK_URL.nashit}/board/${route.params.id}/lists`,
              searchIn: (data) => data.lists.find((e) => e.id === list.id),
            },
          ],
          (listOffilne: any) => {
            listOffilne.tasks_number++;
            listOffilne.tasks.push(task);
            return listOffilne;
          }
        );
      } else {
        $api
          .post<any>(`${$app().BK_URL.nashit}/task/${task.id}/copy`)
          .then(async (res) => {
            // task.date = res?.task?.date || task.date;
            // task.id = res?.task?.id || new Date().getTime();
            res.loading = false;
            list.tasks_number++;
            await $cache().edit(
              [
                {
                  req: `${$app().BK_URL.nashit}/board/${route.params.id}/lists`,
                  searchIn: (data) => data.lists,
                  target: (l) => l.id == list.id,
                },
              ],
              (item) => {
                item.tasks.push(res);
                item.tasks_number++;
                // console.log(item);
                return item;
              }
            );

            list.tasks.push(res);

            d_pop.task.loading = false;

            $msg({
              text: String($t(`${lang}.msg.task.copy.done`)),
              type: "ok",
            });

            note.value.close();
          })
          .catch((_) => {
            errorHandler(
              $t(`gl.msg.error.sendData`) as string
            );
          });
      }
    },
    async rename(newName: string) {
      if (!$filters.length({ item: newName, max: 50, min: 1 })) {
        $msg({
          text: $t(`${lang}.msg.task.rename.error`),
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

      const taskTemp = temps.task;

      if (newName === taskTemp.name) {
        return;
      }

      d_pop.task.loading = true;

      function errorHandler(msg: string) {
        d_pop.task.loading = false;

        $msg({
          text: msg,
          type: "error",
        });
      }

      if (!navigator.onLine) {
        await $cache({
          name: "BoardPage",
          done() {
            taskTemp.name = newName.trim();
            taskTemp.date.lastUpdate = $help().getDateISO();
            d_pop.task.loading = false;

            $msg({
              text: $t(`${lang}.msg.task.rename.done`),
              type: "ok",
            });
          },
          err() {
            errorHandler(String($t(`gl.msg.error.cache.put`)));
          },
        }).edit(
          [
            {
              req: `${$app().BK_URL.nashit}/board/${route.params.id}/lists`,
              searchIn: (data) =>
                data.lists.find((e) => e.id === temps.list.id)?.tasks,
              target: (t) => t.id == taskTemp.id,
            },
          ],
          (taksOffilne: any) => {
            // console.log(taksOffilne);
            taksOffilne.name = newName.trim();
            taksOffilne.date.lastUpdate = $help().getDateISO();
            $ofun(taksOffilne).path({
              path: "process.name",
              value: $help().getDateISO(),
            });
            return taksOffilne;
          }
        );
      } else {
        $api
          .post(`${$app().BK_URL.nashit}/task/${taskTemp.id}/rename`, {
            body: { name: newName },
          })
          .then((res) => {
            taskTemp.date.lastUpdate = res.lastUpdate;
            taskTemp.name = newName.trim();
            d_pop.task.loading = false;

            $msg({
              text: $t(`${lang}.msg.task.rename.done`),
              type: "ok",
            });
          })
          .catch((_) => {
            errorHandler(
              $t(`gl.msg.error.sendData`) as string
            );
          });
      }
    },
    remove() {
      const task = temps.task;
      const list = temps.list;

      // loading
      d_pop.task.loading = true;

      function errorHandler(msg: string) {
        $msg({
          text: msg,
          type: "error",
        });
        d_pop.task.loading = false;
      }

      $msg({
        text: $t(`${lang}.msg.task.delete.sure`),
        type: "sure",
        btns: {
          async t() {
            if (!navigator.onLine) {
              let isRremoveTask = false;
              await $cache({
                name: "BoardPage",
                done() {
                  list.tasks_number--;
                  if (isRremoveTask) {
                    list.tasks = list.tasks.filter((e) => e.id !== task.id);
                  } else {
                    $ofun(task).path({
                      path: "process.remove",
                      value: $help().getDateISO(),
                    });
                  }
                  $msg({
                    text: String($t(`${lang}.msg.task.delete.done`)),
                    type: "ok",
                  });
                  d_pop.task.loading = false;

                  note.value.close();
                },
                err() {
                  errorHandler(String($t(`gl.msg.error.cache.put`)));
                },
              }).edit(
                [
                  {
                    req: `${$app().BK_URL.nashit}/board/${
                      route.params.id
                    }/lists`,
                    searchIn: (data) => data.lists,
                    target: (lists) => lists.id == list.id,
                  },
                ],
                (listOffilne: any) => {
                  const taskOffline = listOffilne.tasks.find(
                    (e) => e.id == task.id
                  );
                  if (taskOffline?.process?.create) {
                    isRremoveTask = true;
                    listOffilne.tasks = listOffilne.tasks.filter(
                      (e) => e.id !== task.id
                    );
                  } else {
                    $ofun(taskOffline).path({
                      path: "process.remove",
                      value: $help().getDateISO(),
                    });
                  }

                  $ofun(listOffilne).path({
                    path: "process.reindex",
                    value: $help().getDateISO(),
                  });
                  listOffilne.tasks_number--;
                  return listOffilne;
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
              //       .match("/api/board.json")
              //       .then(function (e) {
              //         if (!e) {
              //           errorHandler(String($t("gl.msg.error.cache.found")));
              //           return;
              //         }
              //         e?.json()
              //           .then(function (dataJson) {

              //             const taskRemove =
              //               dataJson.lists[listIndex].tasks[taskIndex];
              //             if (taskRemove?.process?.create) {
              //               dataJson.lists[listIndex].tasks = dataJson.lists[
              //                 listIndex
              //               ].tasks.filter((e: any) => e.id !== task.id);
              //             } else {
              //               // taskRemove.remove = true;
              //               taskRemove.process
              //                 ? (taskRemove.process.remove = new Date()
              //                     .toISOString()
              //                     .replace("Z", "000Z"))
              //                 : (taskRemove.process = {
              //                     remove: new Date()
              //                       .toISOString()
              //                       .replace("Z", "000Z"),
              //                   });
              //             }

              //             const newRes = new Response(
              //               JSON.stringify(dataJson),
              //               e
              //             );
              //             cache
              //               .put("/api/board.json", newRes)
              //               .then((_) => {
              //                 setOffline();
              //                 const taskRemove = list.tasks[taskIndex];
              //                 if (taskRemove?.process?.create) {
              //                   list.tasks = list.tasks.filter(
              //                     (e: any) => e.id !== task.id
              //                   );
              //                 } else {
              //                   // taskRemove.remove = true;
              //                   taskRemove.process
              //                     ? (taskRemove.process.remove = new Date()
              //                         .toISOString()
              //                         .replace("Z", "000Z"))
              //                     : (taskRemove.process = {
              //                         remove: new Date()
              //                           .toISOString()
              //                           .replace("Z", "000Z"),
              //                       });
              //                 }

              //                 $msg({
              //                   text: String(
              //                     $t(`${lang}.msg.task.delete.done`)
              //                   ),
              //                   type: "ok",
              //                 });
              //                 d_pop.task.loading = false;

              //                 note.value.close();
              //               })
              //               .catch((_) => {
              //                 errorHandler(
              //                   String($t(`gl.msg.error.cache.put`))
              //                 );
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
                .post(`${$app().BK_URL.nashit}/task/${task.id}/remove`)
                .then(async (_) => {
                  // list.tasks = list.tasks.filter(
                  //   (e: any) => e.id !== task.id
                  // );
                  list.tasks = list.tasks.filter((e: any) => e.id !== task.id);
                  list.tasks_number--;

                  await $cache().edit(
                    [
                      {
                        req: `${$app().BK_URL.nashit}/board/${
                          route.params.id
                        }/lists`,
                        searchIn: (data) => data.lists,
                        target: (l) => l.id == list.id,
                      },
                    ],
                    (item) => {
                      item.tasks_number--;
                      item.tasks = item.tasks.filter(
                        (e: any) => e.id !== task.id
                      );
                      // console.log(item);
                      return item;
                    }
                  );

                  $msg({
                    text: $t(`${lang}.msg.task.delete.done`),
                    type: "ok",
                  });

                  d_pop.task.loading = false;

                  note.value.close();

                  // this.$refs.details_task.close();
                })
                .catch((_) => {
                  errorHandler(
                    $t(`gl.msg.error.sendData`) as string
                  );
                });
            }
          },
          f() {
            d_pop.task.loading = false;
          },
        },
      });
    },
    move(toList: any) {
      d_pop.task.loading = true;

      const task = temps.task;
      const list = temps.list;
      const listindex = temps.listIndex;

      function errorHandler(msg: string) {
        d_pop.task.loading = false;
        moveTask.value.close();
        note.value.close();

        $msg({
          text: $t(`${lang}.msg.task.move.error`),
          type: "error",
        });
      }

      $msg({
        text: `${$t(`${lang}.msg.task.move.sure.0`)} "${task.name}" ${$t(
          `${lang}.msg.task.move.sure.1`
        )} "${toList.name}"`,
        type: "sure",
        btns: {
          async t() {
            if (!navigator.onLine) {
              errorHandler(
                $t(`gl.msg.error.sendData`) as string
              );
              // await $cache({
              //   name: "BoardPage",
              //   done() {
              //     toList.tasks.push(task);
              //     list.tasks = list.tasks.filter((e: any) => e.id !== task.id);
              //     d_pop.task.loading = false;
              //     moveTask.value.close();
              //     note.value.close();
              //   },
              //   err() {
              //     errorHandler(String($t(`gl.msg.error.cache.put`)));
              //   },
              // }).edit(
              //   [
              //     {
              //       req: `${$app().BK_URL.nashit}/board/${
              //         route.params.id
              //       }/lists`,
              //       searchIn: (data) => data.lists,
              //     },
              //   ],
              //   (listsOffilne: any) => {
              //     const listTo = listsOffilne.find((e) => e.id === toList.id);
              //     const listFrom = listsOffilne.find((e) => e.id === list.id);
              //     $ofun(listTo).path({
              //       path: "process.reindex",
              //       value: $help().getDateISO(),
              //     });

              //     $ofun(listFrom).path({
              //       path: "process.reindex",
              //       value: $help().getDateISO(),
              //     });

              //     const cacheTask = listFrom.tasks.find(
              //       (e) => e.id === task.id
              //     );
              //     if (cacheTask) {
              //       // cacheTask.list_id = listTo.id;
              //       if (cacheTask.list_id == listTo.id) {
              //         delete cacheTask?.process?.move;
              //       } else if (!cacheTask?.process?.create){
              //         $ofun(cacheTask).path({
              //           path: "process.move",
              //           value: $help().getDateISO(),
              //         });
              //       }

              //       listFrom.tasks = listFrom.tasks.filter(
              //         (e) => e.id !== cacheTask.id
              //       );

              //       listTo.tasks.push(cacheTask);
              //     }
              //     return listsOffilne;
              //   }
              // );
            } else {
              $api
                .post(`${$app().BK_URL.nashit}/task/${task.id}/move`, {
                  body: { to: toList.id },
                })
                .then(async (res) => {
                  await $cache().edit(
                    [
                      {
                        req: `${$app().BK_URL.nashit}/board/${
                          route.params.id
                        }/lists`,
                        searchIn: (data) => data.lists,
                      },
                    ],
                    (lists: any) => {
                      const cacheToList = lists.find((e) => e.id == toList.id);
                      const cacheList = lists.find((e) => e.id == list.id);
                      const cacheTask = cacheList.find((e) => e.id === task.id);
                      cacheToList.tasks_number++;
                      cacheToList.push(cacheTask);

                      cacheList.tasks = cacheList.tasks.filter(
                        (e: any) => e.id !== task.id
                      );
                      cacheList.tasks_number--;

                      return lists;
                    }
                  );

                  task.order = toList.tasks.length + 1;
                  task.date.lastUpdate = res.lastUpdate;
                  toList.tasks_number++;
                  toList.tasks.push(task);

                  list.tasks = list.tasks.filter((e: any) => e.id !== task.id);
                  list.tasks_number--;
                  d_pop.task.loading = false;
                  moveTask.value.close();
                  note.value.close();
                })
                .catch(() => {
                  errorHandler(
                    $t(`gl.msg.error.sendData`) as string
                  );
                });
            }
          },
          f() {
            d_pop.task.loading = false;
          },
        },
      });
    },
    async note(dataNote: any) {
      const task = temps.task;
      const list = temps.list;
      const last = task.note;
      if (navigator.onLine) {
        $api
          .post(`${$app().BK_URL.nashit}/task/${task.id}/note`, {
            body: {
              // id: temps.task.id,
              note: dataNote,
              // returnSmallText: true,
            },
          })
          .then((res) => {
            task.note = dataNote;
            task.date.lastUpdate = res.lastUpdate;
          })
          .catch((_e) => {
            // const editor = this.$refs.editor!?.quill;
            // editor.blur();
            // console.log(temps.task.note);
            const last = task.note;
            task.note = "";

            setTimeout(() => {
              task.note = last;
            }, 10);

            $msg({
              text: $t(`${lang}.msg.task.cant`),
              type: "error",
            });
          });
      } else {
        const sizeText = new Blob([dataNote]).size;

        if (sizeText > layoutDataStore.data.value.note_limit) {
          $msg({
            text: trans(`${lang}.msg.task.note`,{
              limit : layoutDataStore.data.value.note_limit,
              size : sizeText
            }),
            type: "error",
          });
          return;
        }
        await $cache({
          name: "BoardPage",
          done() {
            task.note = dataNote;
            task.date.lastUpdate = $help().getDateISO();
          },
          err() {
            task.note = "";
            setTimeout(() => {
              task.note = last;
            }, 10);
            $msg({
              text: $t(`${lang}.msg.task.cant`),
              type: "error",
            });
          },
        }).edit(
          [
            {
              req: `${$app().BK_URL.nashit}/board/${route.params.id}/lists`,
              searchIn: (data) =>
                data.lists.find((e) => e.id === list.id)?.tasks,
              target: (t) => t.id == task.id,
            },
          ],
          (taksOffilne: any) => {
            taksOffilne.note = dataNote;
            taksOffilne.date.lastUpdate = $help().getDateISO();
            $ofun(taksOffilne).path({
              path: "process.note",
              value: $help().getDateISO(),
            });
            return taksOffilne;
          }
        );
      }
    },
  };
}

function clickEnter(list: any, index: any) {
  if ((event as any).key === "Enter") {
    task({ listIndex: index }).create(list);
  }
}

// const groupLists = computed( ? 'list' : Math.random());
// let listsName = [];

const groupLists = ref(navigator.onLine);

window.addEventListener("online", () => {
  groupLists.value = true;
});

window.addEventListener("offline", () => {
  groupLists.value = false;
});
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
//   $local.update("offline", { board: true });
//   // this.$store.commit("setSystem", { pro: "offlineData", value: true });
//   systemStore.offline = true;
// }

onMounted(() => {
  listsDOM.value.forEach((draggableObject: any, i: number) => {
    setScrollListsEvent(draggableObject, i);
  });
});

function setScrollListsEvent(draggableObject: any, i: number) {
  const list = lists.value[i];
  if (!list.tasks_loaded) {
    const domList = draggableObject.targetDomElement;
    let page = 2;
    function scrollEvent() {
      if (
        domList.offsetHeight + domList.scrollTop >= domList.scrollHeight - 40 &&
        !list.loadingMore
      ) {
        if (navigator.onLine) {
          list.loadingMore = true;
          $api
            .get(`${$app().BK_URL.nashit}${route.params.profile ? '/profile/'+route.params.profile : ''}/list/${list.id}/tasks`, {
              params: { page, cache: false },
            })
            .then(async (res) => {
              await $cache().edit(
                [
                  {
                    req: `${$app().BK_URL.nashit}${route.params.profile ? '/profile/'+route.params.profile : ''}/board/${
                      route.params.id
                    }/lists`,
                    searchIn: (data) => data.lists,
                    target: (l) => l.id == list.id,
                  },
                ],
                (item) => {
                  item.tasks = item.tasks.concat(res.data);
                  // console.log(item);
                  return item;
                }
              );

              // console.log("more");
              page++;
              list.tasks = list.tasks.concat(res.data);

              if (!res.next_page_url) {
                const event = events.find((e) => e.id === list.id)?.event;
                domList.removeEventListener("scroll", event);
                list.tasks_loaded = true;
              }
              list.loadingMore = false;
            });
        }
      }
    }
    events.push({
      id: list.id,
      event: scrollEvent,
    });
    domList.addEventListener("scroll", scrollEvent);
  }
}
</script>

<template>
  <div ref="el" class="todos-page-board" data-width="full" data-class="board">
    <!-- <div class="temp-box" ref="temp">

    </div> -->
    <div class="app">
      <div class="boxs-todos">
        <div
          v-for="(list, i) in lists.value"
          :key="i"
          class="todo-list"
          :class="{ remove: list?.process?.remove }"
        >
          <div class="header">
            <h2>{{ list.name }}</h2>
            <div class="left">
              <div v-if="list.smallLoading.length" class="box-loading">
                <small-loading></small-loading>
              </div>
              <button
                class="btn-list-options"
                @click="m_show().todoList().options(i)"
              >
                <i class="ri-more-2-line"></i>
              </button>
              <div ref="todoListOptions" class="list">
                <ul @click="m_close().todoList().options(i)">
                  <li>
                    <button
                      @click="m_pop({ list: list }).show().todoList().details()"
                    >
                      <i class="ri-information-line"></i
                      >{{ $t(`${lang}.options.details`) }}
                    </button>
                  </li>
                  <li>
                    <button
                      @click="
                        m_pop({ list: list, listIndex: i })
                          .show()
                          .todoList()
                          .rename()
                      "
                    >
                      <i class="ri-edit-2-line"></i
                      >{{ $t(`${lang}.options.rename`) }}
                    </button>
                  </li>
                  <li>
                    <button
                      @click="m_pop({ list: list }).show().todoList().copy()"
                    >
                      <i class="ri-file-copy-line"></i
                      >{{ $t(`${lang}.options.copy`) }}
                    </button>
                  </li>
                  <li class="delete">
                    <button @click="todoList(list).remove()">
                      <i class="ri-close-line"></i
                      >{{ $t(`${lang}.options.delete`) }}
                    </button>
                  </li>
                </ul>
              </div>
            </div>
          </div>

          <draggable
            ref="listsDOM"
            v-model="list.tasks"
            :disabled="list.smallLoading.length"
            class="tasks"
            :group="groupLists"
            item-key="id"
            :animation="200"
            @end="task().changeIndex($event)"
          >
            <template #item="{ element, index }">
              <div class="task" :class="{ remove: element?.process?.remove }">
                <span
                  class="icon"
                  :class="{ done: element.done }"
                  @click="
                    !$route.params.profile &&
                      task({ taskIndex: index, listIndex: i }).setDone(element)
                  "
                >
                  <template v-if="element.done">
                    <i class="ri-checkbox-circle-line"></i>
                  </template>
                  <template v-else>
                    <i class="ri-forbid-2-line"></i>
                  </template>
                </span>
                <p>{{ element.name }}</p>
                <button
                  class="note-btn"
                  @click="
                    m_pop({
                      list: list,
                      task: element,
                      listIndex: i,
                      taskIndex: index,
                    })
                      .show()
                      .task()
                      .note()
                  "
                  @touchend="m_pop({
                      list: list,
                      task: element,
                      listIndex: i,
                      taskIndex: index,
                    })
                      .show()
                      .task()
                      .note()
                  "
                >
                  <i class="ri-sticky-note-line"></i>
                </button>
                <full-loading v-if="element.loading"></full-loading>
              </div>
            </template>
            <template #footer>
              <!-- list.loadingMore -->
              <small-loading v-if="list.loadingMore"></small-loading>
            </template>
          </draggable>

          <!-- <draggable
            v-model="list.tasks"
            class="tasks"
            group="list"
            :animation="200"
            item-key="id"
            @end="task().changeIndex($event)"
          >
         
            <template #item="{ element, index }">
            <p>{{ element.name }}</p>


            </template>
          
          </draggable> -->

          <div class="box-added-task">
            <input
              type="text"
              :placeholder="String($t(`${lang}.tasksPlaceholder`))"
              ref="input_value"
              @keypress="clickEnter(list, i)"
            />
            <button class="push" @click="task({ listIndex: i }).create(list)">
              <i
                v-if="$i18n.localeProperties.value.dir === 'rtl'"
                class="ri-arrow-left-line"
              ></i>
              <i v-else class="ri-arrow-right-line"></i>
            </button>
          </div>
          <full-loading v-if="list.loading"></full-loading>
        </div>
      </div>

      <PopUp
        ref="detailsTodo"
        class="details-task-popup details-todoList-popup medium-box"
        :title="String($t(`${lang}.pops.todoList.details.title`))"
      >
        <div>
          <p>
            {{ $t(`${lang}.pops.todoList.details.name`) }} :
            <span>{{ temps.list?.name }}</span>
          </p>
          <p>
            {{ $t(`${lang}.pops.todoList.details.create`) }} :
            <span class="time">{{
              $help().formatDate(temps?.list?.date?.create)
            }}</span>
          </p>
          <p>
            {{ $t(`${lang}.pops.todoList.details.lastUpdate`) }} :
            <span class="time">{{
              $help().formatDate(temps?.list?.date?.lastUpdate)
            }}</span>
          </p>
        </div>
      </PopUp>
      <PopUp
        ref="createTodo"
        class="create-todo-popup"
        :title="String($t(`${lang}.pops.todoList.create.title`))"
        :btn="String($t(`${lang}.pops.todoList.create.title`))"
        btn-icon="ri-check-line"
        @onClickBtn="todoList().cerate()"
      >
      <button class="d-none showCreateTodo-btn" @click="$refs['createTodo'].show()"></button>
        <div class="inputs">
          <div>
            <label>{{ $t(`${lang}.pops.todoList.create.name`) }}</label>
            <input
              v-model="d_pop.todoList.create.name"
              type="text"
              :class="{ error: d_pop.todoList.create.error }"
            />
            <p v-if="d_pop.todoList.create.error" class="errMsg">
              {{ $t(`${lang}.pops.todoList.create.error`) }}
            </p>
          </div>
        </div>

        <full-loading v-if="d_pop.todoList.create.loading"></full-loading>
      </PopUp>
      <PopUp
        ref="rename_todo"
        class="rename-todo-popup"
        :title="String($t(`${lang}.pops.todoList.rename.title`))"
        :btn="String($t(`${lang}.pops.todoList.rename.title`))"
        btn-icon="ri-edit-2-line"
        @onClickBtn="todoList().rename()"
      >
        <div class="inputs">
          <div>
            <label>{{ $t(`${lang}.pops.todoList.rename.name`) }}</label>
            <input
              v-model="d_pop.todoList.rename.name"
              type="text"
              :class="{ error: d_pop.todoList.rename.error }"
            />
            <p v-if="d_pop.todoList.rename.error" class="errMsg">
              {{ $t(`${lang}.pops.todoList.rename.error`) }}
            </p>
          </div>
        </div>

        <full-loading v-if="d_pop.todoList.rename.loading"></full-loading>
      </PopUp>
      <PopUp
        ref="copyTodo"
        class="copy-todo-popup"
        :title="String($t(`${lang}.pops.todoList.copy.title`))"
        :btn="String($t(`${lang}.pops.todoList.copy.title`))"
        btn-icon="ri-file-copy-line"
        @onClickBtn="todoList().copy()"
      >
        <div>
          <ElementsSelect
            :select="d_pop.todoList.copy.type"
            :elements="[
              {
                title: $t(`${lang}.pops.todoList.copy.withTasks`),
                value: 'with-tasks',
              },
              {
                title: $t(`${lang}.pops.todoList.copy.withoutTasks`),
                value: 'without-tasks',
              },
            ]"
            :linear="true"
            @onSelect="d_pop.todoList.copy.type = $event.value"
          ></ElementsSelect>
        </div>

        <full-loading v-if="d_pop.todoList.copy.loading"></full-loading>
      </PopUp>

      <PopUp
        ref="note"
        class="note-popup notebook-task-popup smallTitle list"
        :title="temps.task.name || ''"
        :can-change-title="true"
        @blurInputTitle="task().rename($event)"
        @is-close-POP="$refs['editor'].doneNow()"
      >
        <!--  @onClose="$refs['editor'].close()" -->
        <div class="options-pop">
          <button class="btn-list-options" @click="m_show().task().options()">
            <i class="ri-more-2-line"></i>
          </button>
          <div ref="taskListOptions" class="list">
            <ul @click="m_close().task().options()">
              <li>
                <button @click="m_pop().show().task().details()">
                  <i class="ri-information-line"></i
                  >{{ $t(`${lang}.options.details`) }}
                </button>
              </li>
              <li>
                <button @click="m_show().task().rename()">
                  <i class="ri-edit-2-line"></i
                  >{{ $t(`${lang}.options.rename`) }}
                </button>
              </li>
              <li>
                <button @click="m_pop().show().task().move()">
                  <i class="ri-external-link-line"></i
                  >{{ $t(`${lang}.options.move`) }}
                </button>
              </li>
              <li>
                <button @click="task().copy()">
                  <i class="ri-file-copy-line"></i
                  >{{ $t(`${lang}.options.copy`) }}
                </button>
              </li>

              <li class="delete">
                <button @click="task().remove()">
                  <i class="ri-close-line"></i
                  >{{ $t(`${lang}.options.delete`) }}
                </button>
              </li>
            </ul>
          </div>
        </div>
        <!-- :item="temps.task"   @onOfflineSave="task().note($event)"-->
        <!--   :set-text="temps.task.note" -->
        <note-book
          ref="editor"
          :disabled="!!$route.params.profile"
          @done="task().note($event)"
        ></note-book>
        <full-loading v-if="d_pop.task.loading"></full-loading>
      </PopUp>
      <PopUp
        ref="detailsTask"
        class="details-task-popup medium-box"
        :title="$t(`${lang}.pops.task.details.title`)"
      >
        <div>
          <p>
            {{ $t(`${lang}.pops.task.details.name`) }} :
            <span>{{ temps.task?.name }}</span>
          </p>
          <p v-if="temps.task?.date?.done">
            {{ $t(`${lang}.pops.task.details.done`) }} :
            <span class="time">{{
              $help().formatDate(temps.task?.date?.done)
            }}</span>
          </p>
          <p>
            {{ $t(`${lang}.pops.task.details.create`) }} :
            <span class="time">{{
              $help().formatDate(temps.task?.date?.create)
            }}</span>
          </p>
          <p>
            {{ $t(`${lang}.pops.task.details.lastUpdate`) }} :
            <span class="time">{{
              $help().formatDate(temps.task?.date?.lastUpdate)
            }}</span>
          </p>
        </div>
        <!-- <full-loading v-if="d_pop.task.loading"></full-loading> -->
      </PopUp>
      <PopUp
        ref="moveTask"
        class="move-task-popup medium-box"
        :title="$t(`${lang}.pops.task.move.title`)"
      >
        <div>
          <template v-if="getLists.length">
            <p>
              {{ $t(`${lang}.pops.task.move.to.0`) }} "{{ temps.task.name }}"
              {{ $t(`${lang}.pops.task.move.to.1`) }} :
            </p>
            <ElementsList
              :list="getLists"
              @select="task().move($event)"
            ></ElementsList>
          </template>

          <p v-else>{{ $t(`${lang}.pops.task.move.noTodoLists`) }}</p>
        </div>
        <full-loading v-if="d_pop.task.loading"></full-loading>
        <!-- <full-loading v-if="loading.moveTask"></full-loading> -->
      </PopUp>
    </div>
  </div>
</template>
