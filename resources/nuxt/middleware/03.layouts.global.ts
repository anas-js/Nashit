export default defineNuxtRouteMiddleware(async (to) => {
 

  if (to.meta.layout === "page") {
    const systemStore = useSystemStore();
    const layoutDataStore = useLayoutDataStore();
   
    const $auth = useAuth();
    const localePath = useLocalePath();


    const path = ref(
      to.params.profile ? systemStore.path.split[1] : systemStore.path.split[0]
    );
   
   
    if (to.params.id) {
      let getDataErr = false;
      if (!layoutDataStore.setData) {
        await $api.get<any>(`${$app().BK_URL.nashit}${to.params.profile ? '/profile/'+to.params.profile : ''}/${path.value}/${to.params.id}`).then((res) => {
          layoutDataStore.setLayoutData(res);
      
        }).catch(() => {
          getDataErr = true;
         
        });
        if(getDataErr) {
          return abortNavigation();
        }
      }
    }

    const firstPath = systemStore.path.split[systemStore.path.split.length - 1];


    
    if (to.params.profile) {
      if ($auth.user?.username === to.params.profile) {
        if (to.params.id === firstPath) {
        return  navigateTo(localePath(`/${path.value}/${to.params.id}/`));
        } else {
        return navigateTo(localePath(`/${path.value}/${to.params.id}/${firstPath}`));
        }
      }
    }


  }
});
