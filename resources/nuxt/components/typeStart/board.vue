<script setup lang="ts">
const el = ref();
const lang = "pages.start.typeStart.board";
const name = defineModel("name",{default : ""}) as Ref<string>;

 
const errMsgs = reactive({
  name: false,
});

const props = defineProps({
  stage: {
    type: Number,
    required: true,
  },
});

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
  }

  return true;
};

const getSteps = () => {
  return el.value.querySelectorAll(".type-element > div").length;
};

const imageComp = ref();

const sendData = async () => {
  // POST /createNewCourse.json
  try {
    const board = await $api
      .post(`${$app().BK_URL.nashit}/start/board`, {
        body: {
          name: name.value,
        },
      });
      if (imageComp.value.setImage) {
      const fromData = new FormData();
      fromData.append("image", imageComp.value.inputFile.files[0]);
      const data = imageComp.value.getDataCrop();
      Object.keys(data).forEach((key) => {
        fromData.append(key, data[key]);
      });

      await $api.post(`${$app().BK_URL.nashit}/board/${board.id}/upload`, {
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

defineExpose({
  check,
  getSteps,
  sendData
})
const $tr = $t;
</script>
<template>
  <div ref="el" class="type-element board">
    <div class="name-board">
      <div>
        <h2>{{ $t(`${lang}.nameBoard.title`) }}</h2>
        <input
          v-model="name"
          :class="{ error: errMsgs.name }"
          :placeholder="String($t(`${lang}.nameBoard.input`))"
        />
        <p :class="{ visible: errMsgs.name }" class="errMsg v-hiddin">
          {{ $tr(`${lang}.nameBoard.error`) }}
        </p>
      </div>
    </div>
    <div class="image">
      <div>
        <h2>{{ $t("gl.image") }}</h2>
        <TypeStartCompImage ref="imageComp" :min-size="{width : 115,height:50}" :aspect-ratio="2.3" size="230x100" />
      </div>
    </div>
  </div>
</template>
<!-- @click="pop('add-new-lessons')" -->
