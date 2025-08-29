export const $sync = async () => {
  // return;
  const { $i18n } = useNuxtApp();
  const sync = useState("sync", () => false);
  const offline = $local.get("offline");
  const $auth = useAuth();
  const lang = "plugins.sync";
  const systemStore = useSystemStore();

  if (offline !== null && !navigator.onLine) {
    systemStore.offline = true;
  }

  if (offline !== null && !sync.value && $auth.loggedIn && navigator.onLine) {
    sync.value = true;

    const loading = $help().toHtml(`<div class="online full-loading">
      <span class="small-loading"><i class="ri-loader-4-line"></i></span>
      <p>${$i18n.t(`${lang}.syncData`)}</p>
    </div>`);

    document.body.append(loading);

    let done = true;
    let cacheExist = true;

    if (!("caches" in window) || !caches?.open) {
      cacheExist = false;
    }
    const cache = await caches.open("nashit-runtime-cache");
    if (!cache || !cache.match || !cache.put) {
      cacheExist = false;
    }

    let loop = 0;

    while (loop < 5) {
      if (cacheExist) {
        if (offline?.BoardPage) {
          const open: {
            url: string;
            result: (data: any) => any;
            ignor?: boolean | undefined;
          }[] = [];
          offline?.BoardPage.forEach((board) => {
            open.push({
              url: board,
              result: async (data) => {
                await $api
                  .post(`${$app().BK_URL.nashit}/list/update`, {
                    body: { lists: data.lists },
                  })
                  .then(async () => {
                    offline.BoardPage = offline.BoardPage.filter(
                      (e) => e != board
                    );
                    await $cache().kill(board);
                    $local.set("offline", offline);
                  })
                  .catch(() => {
                    done = false;
                  });
              },
              ignor: true,
            });
          });

          await $cache({
            done() {
              if (!offline?.BoardPage.length) {
                delete offline?.BoardPage;
                $local.set("offline", offline);
              }
            },
            err() {
              done = false;
            },
          }).open(open);
        }

        if (offline?.CoursesAllPage) {
          const open: {
            url: string;
            result: (data: any) => any;
            ignor?: boolean | undefined;
          }[] = [];
          offline?.CoursesAllPage.forEach((allPage) => {
            open.push({
              url: allPage,
              result: async (data) => {
                await $api
                  .post(`${$app().BK_URL.nashit}/course/update`, {
                    body: {
                      lessons: data.lessons.data.filter((e) => e.process),
                    },
                  })
                  .then( async() => {
                    offline.CoursesAllPage = offline.CoursesAllPage.filter(
                      (e) => e != allPage
                    );
                   await $cache().kill(allPage);
                    $local.set("offline", offline);
                  })
                  .catch(() => {
                    done = false;
                  });
              },
              ignor: true,
            });
          });

          await $cache({
            done() {
              if (!offline?.CoursesAllPage.length) {
                delete offline?.CoursesAllPage;
                $local.set("offline", offline);
              }
            },
            err() {
              done = false;
            },
          }).open(open);
        }

        if (offline?.CoursesTodayPage) {
          const open: {
            url: string;
            result: (data: any) => any;
            ignor?: boolean | undefined;
          }[] = [];
          offline?.CoursesTodayPage.forEach((todayPage) => {
            open.push({
              url: todayPage,
              result: async (lessons) => {
                await $api
                  .post(`${$app().BK_URL.nashit}/course/update`, {
                    body: { lessons: lessons.data.filter((e) => e.process) },
                  })
                  .then( async () => {
                    offline.CoursesTodayPage = offline.CoursesTodayPage.filter(
                      (e) => e != todayPage
                    );
                    await $cache().kill(todayPage);
                    $local.set("offline", offline);
                  })
                  .catch(() => {
                    done = false;
                  });
              },
              ignor: true,
            });
          });

          await $cache({
            done() {
              if (!offline?.CoursesTodayPage) {
                delete offline?.CoursesTodayPage;
                $local.set("offline", offline);
              }
            },
            err() {
              done = false;
            },
          }).open(open);
        }
      }
      if (done) {
        loop = 10;
        loading.remove();
        $local.delete("offline");
        systemStore.offline = false;
        window.location.reload();

        $msg({
          text: String($i18n.t(`${lang}.done`)),
          type: "ok",
        });
      } else {
        if(loop == 4) {
          loading.remove();
          $local.delete("offline");
          systemStore.offline = false;
        }
        loop++;
        // $msg({
        //   text: String($i18n.t(`${lang}.error`)),
        //   type: "error",
        //   time: 10000,
        // });

        // await $sleep(10000, () => {
        //   window.location.reload();
        // });
      }
    }
    sync.value = false;
  } else if (offline !== null && !sync.value && !$auth.loggedIn) {
    $local.delete("offline");
    systemStore.offline = false;
  }
};
