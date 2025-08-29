/*eslint no-constant-condition: "off"*/
export const useAnimeScroll = () => {
  let distroy: any = null;
  let ease: any = null;
  const { $anime } = useNuxtApp();
  const route = useRoute();

  onBeforeUnmount(() => {
    if (distroy !== null) {
      window.removeEventListener("scroll", distroy);
    }
  });

  
  onMounted(() => {
    let ai = [...document.getElementsByClassName("ai")] as HTMLElement[];
    // console.log(ai);

    // onMounted(() => {
    //   console.log(document.getElementsByClassName("ai"));
    // });
    const beforeSet = 200;

    setTimeout(() => {
      for (let i = 0; i < ai.length; i++) {
        if ("getBoundingClientRect" in ai[i]) {
          
          const ele = ai[i].getBoundingClientRect();
         
          const after =
            ai[i].getAttribute("anime-after") != null
              ? ai[i].getAttribute("anime-after")!.split(",")
              : [0];
          const animeOff = ai[i].getAttribute("anime-off-auto-run") != null;
          // console.log(window.innerHeight,ele.y + window.scrollY , ai[i])
          if (
            animeOff &&
           (window.innerHeight + window.scrollY - beforeSet) -  Number(after[0]) >= ele.y + window.scrollY
          ) {
            let next = null;
            
            if (ai.length) {
              next = ai[i]?.getAttribute("next-anime") != null;
              i++;
            }

            while (true) {
              if (next && ai.length) {
                runAnime(ai[i]);
                next = ai[i].getAttribute("next-anime") != null;
                ai = ai.filter((_, ei) => ei !== i);
                
              } else {
                break;
              }
            }
          } else if (after[1] && window.innerWidth <= Number(after[1])) {
            if (
              window.innerHeight -  Number(after[0]) >=
              ele.y + window.scrollY
            ) {
              runAnime(ai[i]);
              ai = ai.filter((_, ei) => ei !== i);
              i = -1;
            }
          } else if (
            (window.innerHeight + window.scrollY - beforeSet) - Number(after[0]) >=
            ele.y + window.scrollY
          ) {
            runAnime(ai[i]);
            let next = null;
            if (ai.length) {
              next = ai[i].getAttribute("next-anime") != null;
            }

            ai = ai.filter((_, ei) => ei !== i);
            while (true) {
              if (next && ai.length) {
                runAnime(ai[i]);
                next = ai[i].getAttribute("next-anime") != null;
                ai = ai.filter((_, ei) => ei !== i);
              } else {
                break;
              }
            }
            i = -1;
          }
        } else {
          ai[i].classList.add("opacity-1");
          ai = ai.filter((_, ei) => ei !== i);
        }
      }

      window.addEventListener("scroll", function scrollPage() {
        distroy = scrollPage;
        for (let i = 0; i < ai.length; i++) {
          const ele = ai[i].getBoundingClientRect();
          const before =
            ai[i].getAttribute("anime-before") != null
              ? Number(ai[i].getAttribute("anime-before"))
              : 0;
          const animeOff = ai[i].getAttribute("anime-off-auto-run") != null;

          if ((window.innerHeight + window.scrollY - beforeSet)  + before >= ele.y + window.scrollY && !animeOff) {
         
            runAnime(ai[i]);

            let next = null;

            if (ai.length) {
              next = ai[i].getAttribute("next-anime") != null;
            }

            ai = ai.filter((_, ei) => ei !== i);

            while (true) {
              if (next && ai.length) {
                runAnime(ai[i]);
                next = ai[i].getAttribute("next-anime") != null;
                ai = ai.filter((_, ei) => ei !== i);
              } else {
                break;
              }
            }
            // }
          }
        }
      });
    }, 500);

    function runAnime(ele: Element) {
      const name = ele.getAttribute("anime-name");
      const delayTime = Number(ele.getAttribute("anime-delay"));
      const easing = ele.getAttribute("anime-type") || "easeOutElastic(1, .5)";
      const dir = Number(ele.getAttribute("anime-dir")) || 1000;

      // 'easeOutElastic(1, .5)'

      // if (route.path === "/" && $local.get("start") && !ease) {
      //   const easingNames = [
      //     "easeOutElastic(1, .5)",
      //     "steps(5)",
      //     "easeInQuad",
      //     "easeInCubic",
      //     "easeInQuart",
      //     "easeInQuint",
      //     "easeInSine",
      //     "easeInExpo",
      //     "easeInCirc",
      //     "easeInBack",
      //     "easeOutQuad",
      //     "easeOutCubic",
      //     "easeOutQuart",
      //     "easeOutQuint",
      //     "easeOutSine",
      //     "easeOutExpo",
      //     "easeOutCirc",
      //     "easeOutBack",
      //     "easeInBounce",
      //     "easeInOutQuad",
      //     "easeInOutCubic",
      //     "easeInOutQuart",
      //     "easeInOutQuint",
      //     "easeInOutSine",
      //     "easeInOutExpo",
      //     "easeInOutCirc",
      //     "easeInOutBack",
      //     "easeInOutBounce",
      //     "easeOutBounce",
      //     "easeOutInQuad",
      //     "easeOutInCubic",
      //     "easeOutInQuart",
      //     "easeOutInQuint",
      //     "easeOutInSine",
      //     "easeOutInExpo",
      //     "easeOutInCirc",
      //     "easeOutInBack",
      //     "easeOutInBounce",
      //   ];
      //   ease = easingNames[Math.floor(Math.random() * easingNames.length)];
      //   // easing  = this.ease;
      // } else if (route.path === "/" && !$local.get("start")) {
      //   $local.set("start", "true");
      //   ease = "easeOutElastic(1, .5)";
      // }

      // if (ease) {
      //   easing = ease;
      // }

      if (name) {
        switch (name) {
          case "rigth-in":
            ele.classList.add("visible");
            $anime({
              targets: ele,
              translateX: [250, 0],
              opacity: 1,
              delay: delayTime,
              duration: dir,
              easing,
            });
            break;
          case "left-in":
            ele.classList.add("visible");
            $anime({
              targets: ele,
              translateX: [-250, 0],
              opacity: 1,
              delay: delayTime,
              duration: dir,
              easing,
            });
            break;
          case "bottom-in":
            ele.classList.add("visible");
            $anime({
              targets: ele,
              translateY: [-250, 0],
              opacity: 1,
              delay: delayTime,
              duration: dir,
              easing,
            });
            break;
          case "top-in":
            ele.classList.add("visible");
            $anime({
              targets: ele,
              translateY: [250, 0],
              opacity: 1,
              delay: delayTime,
              duration: dir,
              easing,
            });
            break;
          case "left-in-center":
            ele.classList.add("visible");
            $anime({
              targets: ele,
              translateX: ["-65%", "-50%"],
              translateY: ["-50%", "-50%"],
              opacity: 1,
              delay: delayTime,
              duration: dir,
              easing,
            });
            break;
          case "bottom-in-center":
            ele.classList.add("visible");
            $anime({
              targets: ele,
              translateX: ["-50%", "-50%"],
              translateY: ["-65%", "-50%"],
              // translateY: [-250, 0],
              opacity: 1,
              delay: delayTime,
              easing,
              duration: dir,
            });
            break;
        }
      }
    }
  });
};
