export const $ofun = (obj : any) => {
    return {
      path({ path, value } : {path : string,value:any}) {
        const pathSplit = path.split('.');
        for (let i = 0; i < pathSplit.length; i++) {
          const ele = pathSplit[i];
          if (pathSplit.length > 1) {
            obj[ele] = obj[ele] || {};
            obj = obj[ele];
            pathSplit.shift();
            i--;
          } else {
            obj[ele] = value;
          }
        }
      },
    };
  }