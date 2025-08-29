export const $cache = ({
  name,
  done,
  err,
}: {
  name?: string;
  done?: () => void;
  err?: () => void;
} = {}) => {
  const systemStore = useSystemStore();

  let match = false;

  // Start function
  async function boot() {
    if (!("caches" in window) || !caches?.open || !caches?.delete) {
      if (err) {
        await err();
      }
      throw Error("Error Caches Undefind");
    }
    const cache = await caches.open("nashit-runtime-cache");
    if (!cache || !cache.match || !cache.put || !cache.delete) {
      if (err) {
        await err();
      }
      throw Error("Cache Funcion Undefind");
    }

    return cache;
  }

  // End function
  async function exit() {
    if (!match) {
      if (err) {
        await err();
      }
    }
  }

  return {
    async edit(
      sources: {
        req: string;
        searchIn: (data: any) => any;
        target?: ((e: any, i: number) => boolean) | string;
      }[],
      editItem: (item: any) => any
    ) {
      let cache: Cache;
      try {
        cache = await boot();
      } catch {
        // console.log("return");
        return;
      }

      for (let i = 0; i < sources.length; i++) {
        // ======== Handel Error =========
        const source = sources[i];
        const resArray = await cache.matchAll(source.req, {
          ignoreSearch: true,
          ignoreVary: true,
        });

        // console.log(resArray);
        // if (!resArray.length) {
        //   continue;
        // }

        // resArray.forEach((res) => {

        // });
        for (let h = 0; h < resArray.length; h++) {
          try {
            await handling(resArray[h], source, name);
          } catch {
            i = sources.length;
            h = resArray.length;
          }

          if (match) {
            i = sources.length;
            h = resArray.length;
          }
        }

        // ======== Handel Error =========
      }

      async function handling(
        res: Response,
        source: (typeof sources)[0],
        name?: string
      ) {
        let data: Promise<any> | "jsonError" | any = res.json();
        data.catch(() => {
          data = "jsonError";
        });
        if ((await data) === "jsonError") {
          if (err) {
            await err();
            return;
          }
        } else {
          data = await data;
        }
        // console.log(data);
        // console.log(data);
        // ======== Handel Code =========
        const searchIn = source.searchIn(data);
        if (!searchIn) {
          //  continue;
          return;
        }

        // if(source.target) {

        // }
        let item = searchIn;
        let indexItem;
        if (source.target) {
          if (typeof source.target !== "string") {
            indexItem = searchIn.findIndex(source.target);
          } else {
            indexItem = source.target;
          }

          try {
            item = searchIn[indexItem];
            match = true;
          } catch {
            // return;
            return;
          }
        } else {
          match = true;
        }

        const returnValue = await editItem(item);

        if (returnValue) {
          if (typeof returnValue === "object" && !returnValue?.push) {
            item = returnValue;
          } else {
            if (returnValue === "delete") {
              // console.log("up",indexItem);

              searchIn.splice(indexItem, 1);
              // console.log(searchIn);
            } else {
              searchIn[indexItem] = returnValue;
            }
          }

          // console.log(data);
          if (!data.url) {
            data.url = res.url;
          }

          // console.log();
          // Set New Data
          const newRes = new Response(JSON.stringify(data), res);

          let errorPut = false;
          // console.log(data.url);
          // console.log(data);
          await cache.put(data.url, newRes).catch(() => {
            errorPut = true;
          });
          // console.log("put",data);
          // console.log(errorPut);

          if (errorPut && err) {
            await err();
            throw Error("put Error");
          } else if (done) {
            await done();
            systemStore.offline = true;
          }

          if (name) {
            let lastSorcse;
            try {
              lastSorcse = $local.get("offline")[name];
              if (lastSorcse?.find((e) => e === data.url)) {
                return;
              }
            } catch {
              lastSorcse = null;
            }

            $local.update("offline", {
              [name]: lastSorcse ? [data.url].concat(lastSorcse) : [data.url],
            });
          }
        }
      }

      exit();
    },
    async open(
      sources: {
        url: string;
        result: (data: any) => any;
        ignor?: boolean;
      }[]
    ) {
      let cache: Cache;
      try {
        cache = await boot();
      } catch {
        return;
      }

      for (let i = 0; i < sources.length; i++) {
        // ======== Handel Error =========
        const source = sources[i];
        const res = await cache.match(source.url, {
          ignoreVary: true,
        });

        if (!res && source.ignor) {
          continue;
        } else if (!res) {
          if (err) {
            await err();
          }
          return;
        }

        try {
          await handling(res, source);
        } catch {
          return;
        }
      }

      async function handling(res: Response, source: (typeof sources)[0]) {
        let data: Promise<any> | "jsonError" | any = res.json();
        data.catch(() => {
          data = "jsonError";
        });
        if ((await data) === "jsonError") {
          if (err) {
            await err();
            throw Error("Error");
          }
        } else {
          data = await data;
        }

        // ======== Handel Code =========
        await source.result(data);
      }

      if (done) {
        await done();
      }
    },
    async kill(url: string) {
      let cache: Cache;

      let loop = 0;
      while (loop < 5) {
        try {
          cache = await boot();
        } catch {
          loop++;
          continue;
        }

        await cache
          .delete(url)
          .then(() => {
            loop = 10;
          })
          .catch(() => {
            loop++;
          });
      }
    },
    async clear() {
      let cache: Cache;
      // try {
      //   cache = await boot();
      // } catch {
      //   return;
      // }

      let loop = 0;
      while (loop < 5) {
        try {
          cache = await boot();
        } catch {
          loop++;
          continue;
        }

        await caches
          .delete("nashit-runtime-cache")
          .then(() => {
            loop = 10;
          })
          .catch(() => {
            loop++;
          });
      }
    },
  };
};
