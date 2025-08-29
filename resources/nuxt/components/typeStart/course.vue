<script setup lang="ts">
const props = defineProps({
  stage: {
    type: Number,
    required: true,
  },
  limits: {
    type: Object,
    required: true,
  },
});
const emit = defineEmits(["setHeight"]);
const el = ref();

const lang = "pages.start.typeStart.course";
const name = defineModel("name", { default: "" }) as Ref<string>;
const lessons = reactive({
  value: [
    {
      name: `${$t(`gl.lesson`)} (1)`,
      error: false,
    },
  ],
});
const addNewLesPop = reactive({
  num: 1,
  word: $t(`gl.lesson`),
});

const addNewLesPopErr = reactive({
  num: "",
  word: "",
});

const days = ref($t(`gl.week`));

const weekend = reactive({
  value: [] as number[],
});

const day = ref<number>(1);
const result = reactive({
  value: {
    lessons: {
      value: 0,
      word: $t(`gl.lessons.0`),
    },
    days: {
      value: 0,
      word: $t(`gl.days.0`),
    },
  },
});

const errMsgs = reactive({
  name: false,
  lessons: false,
  day: false,
});

const isExtraDaysWeekend = ref(true);

onMounted(() => {
  clacDays();
});

const clacDays = () => {
  //  const day = Number(day.value);
  const les = lessons.value.length;
  let days = day.value;

  if (!isExtraDaysWeekend.value) {
    if (calcLearnDays(weekend.value) === 0) {
      weekend.value = [];
    } else {
      days = calcLearnDays(weekend.value);
      // console.log(calcLearnDays(weekend.value));
    }

    // console.log(dayStudyValue);
  }

  if (days > props.limits.days_done_limit || days < 1 || isNaN(day.value)) {
    result.value = {
      lessons: {
        value: 0,

        word: $t(`gl.lessons.0`),
      },
      days: {
        value: 0,
        word: $t(`gl.days.0`),
      },
    };

    if (day.value !== "") {
      $msg({
        text: `${$t(`${lang}.done.days.error`)} ${
          props.limits.days_done_limit
        } ${$help().titleDay({ num: props.limits.days_done_limit })}.`,
        type: "error",
      });
    }

    return;
  }

  if (days <= les) {
    const minDay = Math.floor(les / days);
    const maxDay = Math.ceil(les / days);

    if (minDay !== maxDay) {
      result.value.lessons.value = `${minDay} ${$t(`gl.or`)} ${maxDay}`;
      result.value.lessons.word = $t(`gl.lessons.2`);
    } else {
      result.value.lessons.value = maxDay;
      result.value.lessons.word = $help().titleDay({
        num: minDay,
        word: $t(`gl.lessons`) as unknown as string[],
      });
    }
  } else {
    result.value.lessons.value = 1;
    result.value.lessons.word = `${$help().titleDay({
      num: 1,
      word: $t(`gl.lessons`) as unknown as string[],
    })}, ${$t(`${lang}.done.result.text.3`)}`;
  }

  let dayLeft = day.value;

  if (isExtraDaysWeekend.value) {
    for (let i = 0; i < dayLeft; i++) {
      if (weekend.value.includes($help().getDate({ day: i, number: true }))) {
        dayLeft++;
      }
    }
  }

  if (dayLeft - 1 === 0) {
    result.value.days.value = $t(`${lang}.done.result.text.4`);
    result.value.days.word = "";
  } else {
    result.value.days.value = dayLeft - 1;
    result.value.days.word = $help().titleDay({
      num: dayLeft - 1,
    });
  }

  if (props.stage === 4) {
    document.querySelector<HTMLElement>(".content > .elements")!.style.height =
      "";
  }
};
const trans = useNuxtApp().$i18n.t;
const addWeekend = (index: number) => {
  if (weekend.value.find((e) => e === index) !== undefined) {
    weekend.value = weekend.value.filter((e) => e !== index);
  } else {
    if (weekend.value.length === 6) {
      $msg({
        text: $t(`${lang}.done.weekend.error.full`),
        type: "error",
      });
      return;
    } else if (!isExtraDaysWeekend.value) {
      // console.log(calcLearnDays(weekend.value.concat([index])));
      if (calcLearnDays(weekend.value.concat([index])) === 0) {
        $msg({
          text: trans(`${lang}.done.weekend.error.short`,{
            days : day.value,
            weekend : weekend.value.length+1
          }),
          type: "error",
        });
      }

      // 3 in wekened
    }

    weekend.value.push(index);
  }

  clacDays();
};

function calcLearnDays(weekend: number[]) {
  // console.log("run")
  // console.log("weekend",weekend);
  let LearnDay = day.value;
  for (let i = 0; i < day.value; i++) {
    const dayNow = $help().getDate({ day: i, number: true });
    // console.log("dayNow",dayNow,weekend.find((e) => e === dayNow))
    if (weekend.find((e) => e === dayNow) !== undefined) {
      LearnDay--;
      if (LearnDay == 0) {
        return 0;
      }
    }
  }

  // console.log("LearnDay",LearnDay)
  return LearnDay;
}

const putLes = (e: ClipboardEvent, index: number) => {
  e.preventDefault();
  const text = e.clipboardData?.getData("text");
  const array = text
    ?.split(/\n/)
    .map((e) => {
      e = e.replace(/\r/, "").trim();
      return e;
    })
    .filter((e) => e.trim() !== "");

  if (
    array?.length <= 0 ||
    array?.length + lessons.value.length > props.limits.lessons_limit
  ) {
    if (
      lessons.value.length >=
      props.limits.lessons_limit
    ) {
  
      $msg({
      text:  trans(`${lang}.lessons.popup.errors.limit`,{
        limit : props.limits.lessons_limit
      }),
      type: "error",
    });
    } else {
      $msg({
      text: `${$t(`${lang}.lessons.popup.errors.numberLes.0`)} ${
        props.limits.lessons_limit
      } ${$help().titleDay({
        num: props.limits.lessons_limit,
        word: $t("gl.lessons") as [],
      })} ${$t(`${lang}.lessons.popup.errors.numberLes.1`)}  ${
        props.limits.lessons_limit - lessons.value.length
      } ${$help().titleDay({
        num: props.limits.lessons_limit - lessons.value.length,
        word: $t("gl.lessons") as [],
      })}.`,
      type: "error",
    });
    }


    return;
  }

  const splitTop = lessons.value.slice(0, index + 1);
  const splitBottom = lessons.value.slice(index + 1, lessons.value.length);

  for (let i = 0; i < (array?.length || 0); i++) {
    if (i === 0) {
      splitTop[splitTop.length - 1].name += array![i];
    } else {
      splitTop.push({
        name: `${array![i]}`,
        error: false,
      });
    }
  }
  const lesLength = lessons.value.length;
  lessons.value = [...splitTop, ...splitBottom];

  if ((array?.length || 0) > 1) {
    // const Items  =$el.querySelector(".lessons .items") as HTMLElement;
    // const heightItems = Number(Items?.clientHeight)+10;

    const boxHeight =
      (Number(
        el.value.querySelector(".lessons .items .box-item")?.clientHeight
      ) +
        10) *
      (lessons.value.slice(0, 3).length - lesLength);
    // console.log((array!?.length <= 2 ? array!?.length-1 : 2));

    if (lesLength < 3) {
      emit("setHeight", { h: boxHeight, type: "+" });
    }
  }
  //check();
};

const addNewLessonsPopup = ref();
const addNewLessons = () => {
  if (
    addNewLesPop.num <= 0 ||
    addNewLesPop.num + lessons.value.length > props.limits.lessons_limit
  ) {
    if (
      lessons.value.length >=
      props.limits.lessons_limit
    ) {
      addNewLesPopErr.num = trans(`${lang}.lessons.popup.errors.limit`,{
        limit : props.limits.lessons_limit
      });
    } else {
      addNewLesPopErr.num = `${$t(
        `${lang}.lessons.popup.errors.numberLes.0`
      )} ${props.limits.lessons_limit} ${$help().titleDay({
        num: props.limits.lessons_limit,
        word: $t("gl.lessons") as [],
      })} ${$t(`${lang}.lessons.popup.errors.numberLes.1`)}  ${
        props.limits.lessons_limit - lessons.value.length
      } ${$help().titleDay({
        num: props.limits.lessons_limit - lessons.value.length,
        word: $t("gl.lessons") as [],
      })}.`;
    }
    return;
  } else {
    addNewLesPopErr.num = "";
  }

  if (addNewLesPop.word.length > 40) {
    addNewLesPopErr.word = $t(`${lang}.lessons.popup.errors.word`);
    return;
  } else {
    addNewLesPopErr.word = "";
  }

  const lesLength = lessons.value.length;

  for (let i = 0; i < addNewLesPop.num; i++) {
    lessons.value.push({
      name: `${addNewLesPop.word} (${lessons.value.length + 1})`,
      error: false,
    });
  }

  addNewLessonsPopup.value.close();
  clacDays();

  const boxHeight =
    (Number(el.value.querySelector(".lessons .items .box-item")?.clientHeight) +
      10) *
    (lessons.value.slice(0, 3).length - lesLength);

  if (lesLength < 3) {
    emit("setHeight", { h: boxHeight, type: "+" });
  }
};

const deleteLesson = (index: number) => {
  lessons.value = lessons.value.filter((_, i) => i !== index);

  // const Items  =$el.querySelector(".lessons .items") as HTMLElement;
  // const heightItems = Number(Items?.clientHeight);

  // console.log(heightItems);
  if (lessons.value.length < 3) {
    emit("setHeight", {
      h:
        Number(
          el.value.querySelector(".lessons .items .box-item")?.clientHeight
        ) + 10,
      type: "-",
    });
  }
};

const check = () => {
  switch (props.stage) {
    case 2: {
      if (!$filters.length({ item: name.value, max: 20, min: 1 })) {
        errMsgs.name = true;
        return false;
      } else {
        errMsgs.name = false;
      }
      break;
    }
    case 3: {
      let status = true;
      lessons.value.forEach((e) => {
        if (!$filters.length({ item: e.name, max: 50, min: 1 })) {
          e.error = true;

          status = false;
          errMsgs.lessons = true;
        } else {
          e.error = false;
        }
      });

      if (status) {
        errMsgs.lessons = false;
      }
      return status;
    }
    case 4: {
      if (
        !$filters.range({
          item: day.value,
          min: 1,
          max: props.limits.days_done_limit,
        })
      ) {
        errMsgs.day = true;
        return false;
      } else {
        errMsgs.day = false;
      }
    }
  }

  return true;
};

const getSteps = () => {
  return el.value.querySelectorAll(".type-element > div").length;
};

const imageComp = ref();
// new FormData();
const sendData = async () => {
  // POST /createNewCourse.json

  // dataImage: setImage.value ? crop.getCropBoxData() : null,
  // fromData.append(
  //   "fileImage",
  //   setImage.value ? inputFile.value.files[0] : null
  // );

  try {
   const course = await $api.post(`${$app().BK_URL.nashit}/start/course`, {
      body: {
        name: name.value,
        lessons: lessons.value,
        done_days: day.value,
        weekend: weekend.value,
        inSpace: !isExtraDaysWeekend.value,
      },
    });

    if (imageComp.value.setImage) {
      const fromData = new FormData();
      fromData.append("image", imageComp.value.inputFile.files[0]);
      const data = imageComp.value.getDataCrop();
      Object.keys(data).forEach((key) => {
        fromData.append(key, data[key]);
      });

      await $api.post(`${$app().BK_URL.nashit}/course/${course.id}/upload`, {
        body: fromData,
      });
    }

    $msg({
      text: $t(`${lang}.createMsg`),
      type: "ok",
    });
  } catch {
    $msg({
      text: $t(`gl.msg.error.sendData`),
      type: "error",
    });
  }
};

function extraDaysWeekendToggle(e) {
  isExtraDaysWeekend.value = e;
  clacDays();
}

defineExpose({
  check,
  getSteps,
  sendData,
});

const $tr = $t;
</script>

<template>
  <div ref="el" class="type-element course">
    <div class="name-course">
      <div>
        <h2>{{ $t(`${lang}.nameCourse.title`) }}</h2>
        <input
          v-model="name"
          :class="{ error: errMsgs.name }"
          :placeholder="String($t(`${lang}.nameCourse.input`))" />
        <p :class="{ visible: errMsgs.name }" class="errMsg v-hiddin">
          {{ $t(`${lang}.nameCourse.error`) }}
        </p>
      </div>
    </div>
    <div class="lessons">
      <div>
        <h2>{{ $t(`${lang}.lessons.title`) }}</h2>
        <div class="items">
          <div
            v-for="(item, index) in lessons.value"
            :key="index"
            class="box-item"
            :class="{ error: item.error }">
            <input
              v-model="item.name"
              type="text"
              :placeholder="String($t(`${lang}.lessons.input`))"
              @paste="putLes($event, index)" />
            <button v-if="index !== 0" @click="deleteLesson(index)">
              <i class="ri-delete-bin-2-line"></i>
            </button>
          </div>
        </div>
        <p :class="{ visible: errMsgs.lessons }" class="errMsg v-hiddin">
          {{ $t(`${lang}.lessons.error`) }}
        </p>
        <p class="comment">
          {{ $t(`${lang}.lessons.comment.0`) }}
          <button
            class="add-new-lessons"
            @click="($refs['addNewLessonsPopup'] as any).show()">
            <i class="ri-folder-add-line"></i>
          </button>
          {{ $t(`${lang}.lessons.comment.1`) }}
          <code>
            <span class="btn-style">CTRL</span>
            +
            <span class="btn-style">V</span>
          </code>
        </p>
      </div>
      <PopUp
        ref="addNewLessonsPopup"
        class="add-new-les-popup"
        :title="$t(`${lang}.lessons.popup.title`)"
        :btn="$t(`gl.add`)"
        btn-icon="ri-add-circle-line"
        @on-click-btn="addNewLessons()">
        <div class="inputs">
          <div>
            <label>{{ $t(`${lang}.lessons.popup.numberLes.label`) }}</label>
            <input
              v-model.number="addNewLesPop.num"
              type="number"
              :class="{ error: addNewLesPopErr.num }" />
            <p v-if="addNewLesPopErr.num" class="errMsg">
              {{ addNewLesPopErr.num }}
            </p>
            <p class="comment">
              {{ $t(`${lang}.lessons.popup.numberLes.comment`) }}
            </p>
          </div>
          <div>
            <label>{{ $t(`${lang}.lessons.popup.customWord.label`) }}</label>
            <input
              v-model="addNewLesPop.word"
              type="text"
              :class="{ error: addNewLesPopErr.word }" />
            <p v-if="addNewLesPopErr.word" class="errMsg">
              {{ addNewLesPopErr.word }}
            </p>
            <p class="comment">
              {{ $t(`${lang}.lessons.popup.customWord.comment`) }}
            </p>
          </div>
        </div>
      </PopUp>
    </div>
    <div class="done">
      <div>
        <h2>{{ $t(`${lang}.done.title`) }}</h2>
        <div class="data">
          <div class="done-day">
            <label>{{ $t(`${lang}.done.days.label`) }}</label>
            <input
              v-model.number="day"
              type="number"
              placeholder="15"
              @input="clacDays()" />
            <p :class="{ visible: errMsgs.day }" class="errMsg v-hiddin">
              {{
                `${$t(`${lang}.done.days.error`)} ${
                  props.limits.days_done_limit
                } ${$help().titleDay({ num: props.limits.days_done_limit })}.`
              }}
            </p>
          </div>
          <div class="type-doing">
            <label>{{ $t(`${lang}.done.weekend.added`) }}</label>
            <ElementsToogleButton
              @toggle="extraDaysWeekendToggle($event)"
              :set="isExtraDaysWeekend" />
          </div>
          <div class="weekend">
            <label>{{ $t(`${lang}.done.weekend.label`) }}</label>
            <div class="days">
              <div v-for="(d, index) in days" :key="index">
                <span
                  :class="{
                    active: weekend.value.find((e) => e == index) !== undefined,
                  }"
                  @click="addWeekend(index)">
                  {{ d }}
                </span>
              </div>
            </div>
          </div>

          <div class="result">
            <div class="lessons">
              {{ $t(`${lang}.done.result.text.0`) }}
              <span>
                {{
                  result.value.lessons.value === 0
                    ? "X"
                    : result.value.lessons.value
                }}
              </span>
              {{ result.value.lessons.word }}
            </div>
            <div class="days">
              {{ $t(`${lang}.done.result.text.1`) }}
              <span>
                {{
                  result.value.days.value === 0 ? "X" : result.value.days.value
                }}
              </span>
              {{ result.value.days.word }}
              {{ $t(`${lang}.done.result.text.2`) }}
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="image">
      <div>
        <h2>{{ $t("gl.image") }}</h2>
        <TypeStartCompImage
          ref="imageComp"
          :min-size="{ width: 92, height: 50 }"
          :aspect-ratio="1.84"
          size="230x125" />
      </div>
    </div>
    <div class="summary">
      <div>
        <h2>{{ $t(`${lang}.summary.title`) }}</h2>
        <p>
          {{ $t(`${lang}.summary.text.0`) }}
          <span class="name-course">{{ String(name).trim() }}</span>
          , {{ $t(`${lang}.summary.text.1`) }}
          <span>
            {{
              `${lessons.value.length} ${$help().titleDay({
                num: lessons.value.length,
                word: $tr(`gl.lessons`) as unknown as string[],
              })}`
            }}
          </span>
          {{ $t(`${lang}.summary.text.2`) }}
          <span>
            {{ result.value.lessons.value }} {{ result.value.lessons.word }}
          </span>
          <span v-if="weekend.value.length" class="if">
            {{ " " + $t(`${lang}.summary.text.3`) }}
            <span>
              {{
                weekend.value
                  .map((e) => days[e])
                  .join(` ${$t(`${lang}.summary.and`)} `)
              }}
            </span>
          </span>
          {{ $t(`${lang}.summary.text.4`) }}
          <span>
            {{ result.value.days.value }} {{ result.value.days.word }}
          </span>
          {{ $t(`${lang}.summary.text.5`) }}
          <span class="dir-rtl">
            {{
              $help().getDate({
                day:
                  typeof result.value.days.value === "string"
                    ? 0
                    : result.value.days.value,
              })
            }}
          </span>
          {{ $t(`${lang}.summary.text.6`) }}
        </p>
      </div>
    </div>
  </div>
</template>
<!-- @click="pop('add-new-lessons')" -->
