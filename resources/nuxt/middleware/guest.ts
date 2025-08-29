export default defineNuxtRouteMiddleware(async () => {
    const $auth = useAuth();
  
    if($auth.loggedIn) {
      return navigateTo("/");
    }
  });