export default defineNuxtRouteMiddleware(async (to) => {
  // const nuxt = useNuxtApp();
  // const i18n = nuxt.$i18n;
  const systemStore = useSystemStore();

  // set URL without lang code
  // let path = to.path;
  // const pathSplit = path.split("/").filter((e) => e.trim() !== "");
  // const haveCodeInURL = i18n.locale.value != i18n.defaultLocale;
  // if (haveCodeInURL) {
  //   pathSplit.shift();
  // }

  // path = pathSplit.join("/");

  const path = $help().getPathURL(to.path);
  
  systemStore.$patch({
    path: {
      split: path.array,
      full: path.string,
    },
  });



  // console.log('split');



});
