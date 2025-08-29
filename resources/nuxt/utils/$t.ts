
export const $t = (str : string) : string | string[] => {
  const i18n = useNuxtApp().$i18n;
  const getTrans = i18n?.tm(str);
  let returnString;
  if('length' in getTrans) {
    returnString = [];
    for(let i=0;i<getTrans.length;i++) {
      returnString.push(i18n.rt(getTrans[i]));
    }
  } else {
    try {
      returnString = i18n?.rt(getTrans);
    } catch {
      console.warn(`$t(${str}) Not Found`);
    }
  }
 
  return returnString as  string | string[] ;
};