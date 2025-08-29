export const $help = () => {
  return {
    getDate({ day = 0, number = false }: { day?: number; number?: boolean }) {
      const date = new Date();
      date.setDate(date.getDate() + day);

      if (number) {
        return date.getDay();
      } else {
        const data = `${date.getFullYear()}-${
          date.getMonth() + 1
        }-${date.getDate()}`;
        return data;
      }
    },
    titleDay({ num, word }: { num: number; word?: string[] }): string {
      num = Math.abs(num);
      if (!word) {
        word = $t(`gl.days`) as unknown as string[];
      }

      if (num === 1 || num > 10) {
        return word[0]!;
      } else if (num === 2) {
        return word[1]!;
      } else if (num >= 3 && num <= 10) {
        return word[2]!;
      } else {
        return word[2]!;
      }
    },
    toHtml(strHtml: string, selector: string = "body > *") {
      return new DOMParser()
        .parseFromString(strHtml, "text/html")
        .querySelector(selector) as HTMLElement;
    },
    formatDate(date?: string) {
      const d = date ? new Date(date) : new Date();
      const {$i18n} = useNuxtApp();
      if($i18n.locale !== "en") {
        return d.toLocaleString('en-US', {
          year: 'numeric',
          month: '2-digit',
          day: '2-digit',
          hour: '2-digit',
          minute: '2-digit',
          second: '2-digit',
          hour12: true,
        }).replace("PM",$t('gl.time.pm') as string).replace("AM",$t('gl.time.am') as string);
      }
      return d.toLocaleString('en-US', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: true,
      });
      
      // return `${d.getFullYear()}-${(d.getMonth() + 1)
      //   .toString()
      //   .padStart(2, "0")}-${d.getDate().toString().padStart(2, "0")}
      //          ${d.getHours()}:${d.getMinutes().toString().padStart(2, "0")}:${d
      //   .getSeconds()
      //   .toString()
      //   .padStart(2, "0")}
      //         `;
    },
    getDateISO() {
    return new Date().toISOString().replace("Z", "000Z");
    },
    findDOM(startElement : HTMLElement,callback : (element : HTMLElement) => boolean) {
      let loop = true;
      while(loop) {
        if(callback(startElement as HTMLElement)) {
          loop = false;
          return startElement;
        } else if (startElement.nodeName === 'HTML') {
          loop = false;
          return null;
        } else {
          startElement = startElement.parentElement as HTMLElement;
        }
      }
    },
    getPathURL(path : string) {
      const i18n = useNuxtApp().$i18n;
      const array = path.split("/").filter((e) => e.trim() !== "");
      const haveCodeInURL = i18n.locale.value != i18n.defaultLocale;
      if (haveCodeInURL) {
        array.shift();
      }
    
      path = array.join("/");
      return {array,string : path};
    }
  };
};
