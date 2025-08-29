<script setup lang="ts">
const props = defineProps({
  data: {
    type: Object,
    required: true,
  },
  colored: {
    type: Boolean,
    required: false,
    default: true,
  },
  labeled: {
    type: Boolean,
    required: false,
    default: true,
  },
});

const lang = "components.pageItems.rate";
const ratio = ref(0);
const day = reactive({
  val: 0,
  title: "***",
});
const el = ref();
const { $anime } = useNuxtApp();

const evalu = [
  {
    ratio: 100,
    level: 6,
    icon: "ri-zzz-line",
    text: $t("components.pageItems.rate.eval.5"),
    colors: {
      front: "#1B1C52",
      back: "#0d0d27",
    },
  },
  {
    ratio: 90,
    level: 5,
    icon: "ri-alert-line",
    text: $t("components.pageItems.rate.eval.4"),
    colors: {
      front: "#850000",
      back: "#630202",
    },
  },
  {
    ratio: 70,
    level: 4,
    icon: "ri-information-line",
    text: $t("components.pageItems.rate.eval.3"),
    colors: {
      front: "#C90303",
      back: "#8d0505",
    },
  },
  {
    ratio: 50,
    level: 3,
    icon: "ri-heart-pulse-line",
    text: $t("components.pageItems.rate.eval.2"),
    colors: {
      front: "#FF774C",
      back: "#c75d3b",
    },
  },
  {
    ratio: 20,
    level: 2,
    icon: "ri-seedling-line",
    text: $t("components.pageItems.rate.eval.1"),
    colors: {
      front: "#FFB84C",
      back: "#d79d43",
    },
  },
  {
    ratio: 10,
    level: 1,
    icon: "ri-user-smile-line",
    text: $t("components.pageItems.rate.eval.0"),
    colors: {
      front: "#03C988",
      back: "#039363",
    },
  },
].sort((a, b) => a.level - b.level);

const var_ratio = reactive({
  status: false,
  val: 0,
  text: "",
});

//!! false
// watchEffect(() => {
//   setTimeout(() => {
//     setData();
//   }, 1000);
// });

let timeout : any = null;
let mount = false;
onMounted(() => {
  if(!mount) {
    mount = true;
  el.value.querySelectorAll(".back").forEach((e) => {
    e.style.height = "";
  });

  timeout = setTimeout(() => {
    setData();
  }, 1000);
}
});

onBeforeUnmount(() => {
  clearTimeout(timeout);
});

// const layoutDataStore = useLayoutDataStore();
function setData() {
  const data = props.data;
  // if(layoutDataStore.title === "5") {
  //   data.ratio.exp = 80;
  //   data.ratio.todayExp = 5;
  //   data.ratio.curr = 30;
  //   // console.log("5");
  // } 

  // new set
  // if(!data?.ratio?.curr) {
  //   data.ratio.curr = data.ratio;
  //   data.ratio.exp = data.ratio;
  //   data.ratio.todayExp = data.ratio;
  // }

  // if(!data?.day?.curr) {
  //   data.day.curr = data.day;
  //   data.day.full = data.day;
  // }

  const late = data.ratio.exp - data.ratio.todayExp - data.ratio.curr;
  const finished = data?.finished || false;
  // console.log(data.ratio.exp,data.ratio.todayExp,data.ratio.curr);

  // ratio
  let selectedevalu: any = null;
  // (data.ratio.exp === 100) = true;
  if (finished) {
    selectedevalu = evalu.reduce((e1, e2) => (e1.level > e2.level ? e1 : e2));
  } else {
    evalu.forEach((item) => {
      if (late <= item.ratio) {
       
        if (
          (selectedevalu !== null ? selectedevalu.ratio : Infinity) >=
          item.ratio
        ) {
         
          selectedevalu = item;
        }
      }
    });

    // console.log(selectedevalu);
   

    if (props.labeled) {
      if (late > 0) {
        var_ratio.status = true;
        // this.var_ratio.val = late+data.ratio.todayExp;
        var_ratio.text = $t(`${lang}.evalRatio.late`);

        $anime({
          targets: var_ratio,
          // color: '#FFFFFF',
          val: late,
          round: 1,
          easing: "easeInOutCirc",
          duration: 2000,
        });

        $anime({
          targets: el.value.querySelectorAll(".variable-ratio > *"),
          color: "#F45050",
          easing: "easeInOutCirc",
          duration: 2000,
        });

        $anime({
          targets: el.value.querySelector(".variable-ratio"),
          translateX: [100, 0],
          opacity: [0, 1],
          easing: "easeInOutCirc",
          duration: 2000,
        });
      } else if (late + data.ratio.todayExp < 0) {
        var_ratio.status = true;
        // this.var_ratio.val = Math.abs(late+data.ratio.todayExp);
        var_ratio.text = $t(`${lang}.evalRatio.early`);

        // const var_ratioAnime = {
        //   val : this.var_ratio.val,
        // }

        $anime({
          targets: var_ratio,
          // color: '#FFFFFF',
          val: Math.abs(late + data.ratio.todayExp),
          round: 1,
          easing: "easeInOutCirc",
          duration: 2000,
        });

        $anime({
          targets: el.value.querySelector(".variable-ratio"),
          translateX: [100, 0],
          opacity: [0, 1],
          easing: "easeInOutCirc",
          duration: 2000,
        });
      }
    }
  }

  // Number Animation
  const raitoAnime = {
    r: ratio.value,
    d: 0,
  };
  // console.log(data.day.full-data.day.curr);

  $anime({
    targets: raitoAnime,
    easing: "easeInOutCirc",
    round: 1,
    r: data.ratio.curr,
    d: Math.abs(data.day.full-data.day.curr),
    duration: 2000,
    change: () => {
      ratio.value = raitoAnime.r;
      day.val = raitoAnime.d;
      day.title = $help().titleDay({ num: day.val });
    },
  });

  // text Color Animation

  const dayRatio =
    data.ratio.exp === 100
      ? 0
      : Math.round(100 - ((100 / data.day.full) * data.day.curr));

  // console.log(dayRatio);

  const textElements = el.value.querySelectorAll(".elements p");
  
  if (dayRatio <= 70) {
    // textElements[0].style.color
    // console.dir(textElements[0].style = "::before {background-color : #000}")
    // textElements[0].classList.add("shadow");
    $anime({
      targets: textElements[0],
      color: "#FA7878",
      easing: "easeInOutCirc",
      duration: 2000,
    });

    // anime({
    //   targets: textElements[0].nextElementSibling,
    //   color: '#3F3D56',
    //   easing: 'easeInOutCirc',
    //   duration: 2000,
    // });
  }

  if (data.ratio.curr >= 70) {
    
    textElements[1].style.textShadow = "none";
    $anime({
      targets: textElements[1],
      color: ["#03C988","#FFFFFF"],
      easing: "easeInOutCirc",
      duration: 2000,
    });

    // anime({
    //   targets: textElements[0].nextElementSibling,
    //   color: '#3F3D56',
    //   easing: 'easeInOutCirc',
    //   duration: 2000,
    // });
  } else if ((late > 10 || finished) && props.colored) {
    // console.log(selectedevalu.colors.front);
    $anime({
      targets: textElements[1],
      color: selectedevalu.colors.front,
      easing: "easeInOutCirc",
      duration: 2000,
    });
  }

  // Element Back Animation
  const elemets = el.value.querySelectorAll(".elements .back");
  // console.log(dayRatio);
  $anime({
    targets: elemets[0],
    easing: "easeInOutCirc",
    height: [0,`${dayRatio}%`],
    duration: 2000,
  });
  // (late > 20 || data.ratio.exp === 100) && 
  if (props.colored) {
    $anime({
      targets: [
        elemets[1].querySelectorAll(".waves svg")[1].querySelector("path"),
        elemets[1].querySelector(".fill"),
      ],
      easing: "easeInOutCirc",
      backgroundColor: ["#03C988",selectedevalu.colors.front],
      fill: ["#03C988",selectedevalu.colors.front],
      duration: 2000,
    });

    $anime({
      targets: elemets[1]
        .querySelectorAll(".waves svg")[0]
        .querySelector("path"),
      easing: "easeInOutCirc",
      fill: ["#039363",selectedevalu.colors.back],
      duration: 2000,
    });
  }

  $anime({
    targets: elemets[1],
    easing: "easeInOutCirc",
    height: [0,`${data.ratio.curr}%`],
    duration: 2000,
  });

  if ((late > 10 || finished) && props.labeled) {
    const textevalu = el.value.querySelectorAll(".eval-text > div");
    const to = -(textevalu[0].clientHeight + 19);

    $anime({
      targets: textevalu,
      translateY: to * (selectedevalu.level - 1),
      easing: "easeInOutCirc",
      duration: 2000,
    });
  }


}

</script>

<template>
  <div ref="el" class="rate-item-PageItems">
    <div class="elements">
      <div class="boxs">
        <div class="box-day">
          <svg
            viewBox="0 0 180 180"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path
              fill-rule="evenodd"
              clip-rule="evenodd"
              d="M180 0H0V180H180V0ZM14.3056 56.6944C-4.08859 75.0886 -4.0886 104.911 14.3056 123.306L56.6944 165.694C75.0886 184.089 104.911 184.089 123.306 165.694L165.694 123.306C184.089 104.911 184.089 75.0886 165.694 56.6944L123.306 14.3056C104.911 -4.08859 75.0886 -4.08859 56.6944 14.3056L14.3056 56.6944Z"
            />
          </svg>

          <div class="text">
            <p>{{ day.val }}</p>
            <span>{{ day.title }}</span>
          </div>
          <div class="back">
            <div class="waves">
              <svg
                viewBox="0 0 1034 88"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  d="M258.5 39.9993C89.3979 129.998 0 39.9993 0 39.9993V88H1034V39.9993C1034 39.9993 944.602 -49.9991 775.5 39.9993C606.398 129.998 517 39.9993 517 39.9993C517 39.9993 427.602 -49.9991 258.5 39.9993Z"
                  fill="#A6485B"
                />
              </svg>

              <svg
                viewBox="0 0 1034 88"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  d="M775.5 39.9993C944.602 129.998 1034 39.9993 1034 39.9993V88H0V39.9993C0 39.9993 89.3979 -49.9991 258.5 39.9993C427.602 129.998 517 39.9993 517 39.9993C517 39.9993 606.398 -49.9991 775.5 39.9993Z"
                  fill="#FA7878"
                />
              </svg>
            </div>
            <div class="fill"></div>
          </div>
        </div>
        <div class="box-rate">
          <svg
            viewBox="0 0 180 180"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path
              fill-rule="evenodd"
              clip-rule="evenodd"
              d="M180 0H0V180H180V0ZM14.3056 56.6944C-4.08859 75.0886 -4.0886 104.911 14.3056 123.306L56.6944 165.694C75.0886 184.089 104.911 184.089 123.306 165.694L165.694 123.306C184.089 104.911 184.089 75.0886 165.694 56.6944L123.306 14.3056C104.911 -4.08859 75.0886 -4.08859 56.6944 14.3056L14.3056 56.6944Z"
            />
          </svg>
          <p>{{ ratio }}%</p>

          <div
            v-if="labeled"
            :class="{ active: var_ratio.status }"
            class="variable-ratio"
          >
            <p>{{ var_ratio.val }}%</p>
            <span>{{ var_ratio.text }}</span>
          </div>

          <div class="back">
            <div class="waves">
              <svg
                viewBox="0 0 1034 88"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  d="M258.5 39.9993C89.3979 129.998 0 39.9993 0 39.9993V88H1034V39.9993C1034 39.9993 944.602 -49.9991 775.5 39.9993C606.398 129.998 517 39.9993 517 39.9993C517 39.9993 427.602 -49.9991 258.5 39.9993Z"
                  fill="#039363"
                />
              </svg>

              <svg
                viewBox="0 0 1034 88"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  d="M775.5 39.9993C944.602 129.998 1034 39.9993 1034 39.9993V88H0V39.9993C0 39.9993 89.3979 -49.9991 258.5 39.9993C427.602 129.998 517 39.9993 517 39.9993C517 39.9993 606.398 -49.9991 775.5 39.9993Z"
                  fill="#03C988"
                />
              </svg>
            </div>
            <div class="fill"></div>
          </div>
        </div>
      </div>
      <div v-if="labeled" class="eval-text">
        <div
          v-for="(item, index) in evalu"
          :key="index"
          :class="'level-' + item.level"
        >
          <span class="icon">
            <i :class="item.icon"></i>
          </span>
          <p>{{ item.text }}</p>
        </div>
      </div>
    </div>
  </div>
</template>
