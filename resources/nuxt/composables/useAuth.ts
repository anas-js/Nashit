import cookie from "js-cookie";

// console.log(cookie.get());

export const useAuth = () => {
  interface User {
    id: number;
    name: string;
    username: string;
    email: string;
    settings: {
      lang: string;
      mode: string;
    };
    notifs : {
      ads : boolean,
      apps:boolean
    },
    image_src: string;
    registered: boolean;
  }

  const user = useState("user", () => null as null | User);
  const loggedIn = useState("isLoggedIn", () => false);
  const localPath = useLocalePath();
  // const lang = "plugins.logout";
  // const { $i18n } = useNuxtApp();

  const setUser = (userSet: User) => {
    user.value = userSet;
  };

  const setStatus = (status: boolean) => {
    loggedIn.value = status;
  };

  const platformLink = async () => {
    await $api
      .post(`${$app().BK_URL.nashit}/registration`, {
        // body: {
        //   with : "nashit"
        // },
      })
      .then(() => {
        user.value!.registered = true;
      })
      .catch(() => {
        throw createError("Error");
      });
  };

  // const login = () => {
  //   loggedIn.value = true;
  //   cookie.set("token","12345678");
  //   // chick login here and sedn Req
  //   // user.value = {
  //   //   name: "أنس العنزي",
  //   //   email: "anas@anas.sa",
  //   //   username: "anas",
  //   //   settings: {
  //   //     lang: "ar",
  //   //     theme: "white",
  //   //   },
  //   // };
  // };

  const login = (user: User) => {
    $local.set("user", user.id);
    setUser(user);
    setStatus(true);
  };

  const logout = async (
    redirectTo: string = `${$app().FR_URL.juzr}${localPath("/logout")}`
  ) => {
    user.value = null;
    loggedIn.value = false;
    $local.delete("user");
    $local.delete("offline");
    await $cache().clear();

    let logoutCookie = null;

    try {
      logoutCookie = JSON.parse(cookie.get("logout") || "[]");
    } catch {
      logoutCookie = [];
    }
    
    if (
      !logoutCookie ||
      !logoutCookie.length ||
      typeof logoutCookie !== "object"
    ) {
      logoutCookie = [];
    }

    logoutCookie.push("nashit");


    
    cookie.set("logout", JSON.stringify(logoutCookie), {
      domain: $app().domain.juzr,
      expires: 7
    });


    await navigateTo(redirectTo, {
      external: true,
    });

    // await $api
    //   .post(`${$app().FR_URL.juzr}/logout?cache=true`)
    //   .then(async () => {

    //   })
    //   .catch(() => {
    //     $msg({
    //       text: $t(`plugins.logout.error`),
    //       type: "error",
    //     });
    //   });
  };

  const update = (callback : (user : User) => User) => {
    setUser(callback(user.value!));
    // console.log(user.value?.timezone);
  };


  return readonly({
    user,
    loggedIn,
    setUser,
    platformLink,
    login,
    logout,
    setStatus,
    update
  });
};

// export const $auth() = readonly({
//    user,
//   loggedIn,
//   setUser,
//   login,
//   logout,
// });
