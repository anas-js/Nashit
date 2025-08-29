import type { TransitionProps } from "vue";

export default {
  name: "page-layout-trans-slots",
  mode: "out-in",
  appear  :true,
  onEnter(el,done) {
   
    const {$anime} = useNuxtApp();
    // const docEle = document?.querySelector(".slots-content");
    const docEle = el.parentElement;


    const allClass = Array.from(docEle?.classList || []);

    allClass.forEach((e) => {
      if (e?.includes("Box") || e?.includes("Class")) {
        docEle?.classList?.remove(e);
      }
    });

    const widthContent = docEle
      ?.querySelector("div")
      ?.getAttribute("data-width");
    const classContent = docEle
      ?.querySelector("div")
      ?.getAttribute("data-class");
    docEle?.classList?.add(`${widthContent}Box`);

    
    if (classContent) {
      docEle?.classList?.add(`${classContent}Class`);
    }

    $anime({
      targets: docEle,
      opacity: 1,
      duration: 300,
      translateY: 0,
      easing: "easeInOutCirc",
      filter: "blur(0px)",
      complete: () => {
        done();
      },
    });
  },
  onLeave(el,done) {
    const {$anime} = useNuxtApp();
    // const docEle = document?.querySelector(".slots-content");
    const docEle = el.parentElement;

    $anime({
      targets: docEle,
      opacity: 0,
      translateY: 40,
      duration: 300,
      easing: "easeInOutCirc",
      filter: "blur(15px)",
      complete: () => {
        done();
      },
    });
  },
} as TransitionProps;
