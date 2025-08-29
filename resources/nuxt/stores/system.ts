import { defineStore } from "pinia";

export const useSystemStore = defineStore("system",() => {
    const path = reactive({
        full :  "",
        split : [] as Array<string>,
      });
    const mode = ref(null as string | null);
    const redirectPath = ref("/");
    const PWA = ref(null as null | {prompt:() => void});
    const sync = ref(false);
    const loggin = ref(null);
    const loading = ref(true);
    const offline = ref(false);
    return {
        path,
        mode,
        redirectPath,
        PWA,
        sync,
        loggin,
        offline,
        loading
    }
})

