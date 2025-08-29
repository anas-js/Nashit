// import { $app } from "~/utils/$app";
import Cookies from "js-cookie";

export default defineNuxtRouteMiddleware(async (to) => {
  // const systemStore = useSystemStore();

  // const disablePagesNames = ["index", "install","profile","login"];
  // if (!to.name || disablePagesNames.includes(to.name.toString())) {
  //   return;
  // }

  if(!to.name) {
    return;
  }

  if (to.meta?.offAuth || to.params.profile) {
    Cookies.remove("redirect_to",{
      domain : $app().domain.juzr
    });
    return;
  }
 
  const $auth = useAuth();

  if (!$auth.loggedIn) {
    // systemStore.redirectPath = to.fullPath;
    Cookies.set("redirect_to",JSON.stringify({
      path : to.fullPath,
      from : "nashit"
    }),{
      domain : $app().domain.juzr,
      expires : 1,
      // secure : true,
      // sameSite : "strict",
    })
    return navigateTo(`${$app().FR_URL.juzr}/login`,{
      external : true
    });
  } else if (!$auth.user?.registered && !to.meta.registration) {
  
    return navigateTo('/registration',{external:true});
  }
});

