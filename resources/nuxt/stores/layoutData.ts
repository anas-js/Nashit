import { defineStore } from "pinia";
import type { Reactive } from "vue";

export const useLayoutDataStore = defineStore("layoutData",() => {
    const title = ref("");
    interface objectTypeAny {[key: string]: any};
    const data = reactive({
        value : {}
    }) as Reactive<{value:any}>;
    const setData = ref(false);

    function setLayoutData(dataSet : {title : string, data : objectTypeAny}) {
       title.value = dataSet.title;
       data.value = dataSet.data;
       setData.value = true;
    }

    return {
        title,
        data,
        setLayoutData,
        setData
    }
})