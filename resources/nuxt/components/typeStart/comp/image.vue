<script setup lang="ts">
import "cropperjs/dist/cropper.css";
const props = defineProps({
  minSize: {
    type: Object,
    required: true,
  },
  aspectRatio: {
    type: Number,
    required: true,
  },
  size: {
    type: String,
    required: true,
  },
});
const inputFile = ref();
const image = ref();
const errorImage = ref(false);
const setImage = ref(false);
const loadingImage = ref(false);
const lang = "pages.start.typeStart.comp.image";

const popImageCrop = ref();

function openImage() {
  const file = inputFile.value.files[0];

  if(!file) {
    return;
  }

  if (
    file.size / 1024 / 1024 >= 10 ||
    !["image/png", "image/jpeg"].includes(file.type)
  ) {
    errorImage.value = true;
    return;
  }
  errorImage.value = false;
  loadingImage.value = true;
  const reader = new FileReader();
  reader.readAsDataURL(file);
  reader.onload = function (e) {
    const image = new Image();
    image.src = e.target?.result as string;
    image.onload = function () {
      if (image.height > 5000 || image.width > 5000) {
        $msg({
          text: $t(`${lang}.error.big`),
          type: "error",
        });
      } else if (
        image.height < props.minSize.height ||
        image.width < props.minSize.width
      ) {
        $msg({
          text: $t(`${lang}.error.small`),
          type: "error",
        });
      } else {
        popImageCrop.value.getCrop().replace(e.target?.result as string);

        popImageCrop.value.pop.show();
      }
      loadingImage.value = false;
    };
  };
}

function saveImage() {
  setImage.value = true;
  image.value.src = popImageCrop.value
    .getCrop()
    .getCroppedCanvas()
    .toDataURL("image/png");

    // console.log(popImageCrop.value.getCrop().getData());
  popImageCrop.value.pop.close();
}

function claerImage() {
  image.value.src = null;
  setImage.value = false;
  inputFile.value.value = null;
  popImageCrop.value.getCrop().replace("null");
}

// onMounted(() => {

// });
defineExpose({
  setImage,
  inputFile,
  getDataCrop() {
    return popImageCrop.value.getCrop().getData();
  },
});
</script>

<template>
  <div class="imageComp">
    <!-- <FullLoading class="abs"></FullLoading> -->
    <div class="image-file" :class="{ error: errorImage, set: setImage }">
      <button class="remove-image" v-if="setImage" @click="claerImage">
        <i class="ri-close-line"></i>
      </button>

      <div @click="$refs['inputFile'].click()">
        <img ref="image" :class="{ 'd-block': setImage }" />
        <FullLoading v-if="loadingImage"></FullLoading>
        <div v-if="!setImage">
          <i class="ri-image-line"></i>
          <p v-if="!errorImage">{{$t(`${lang}.unselect`)}}</p>
          <p v-else>{{$t(`${lang}.error.type`)}}</p>
          <p class="small">{{$t(`${lang}.size`)}}</p>
          <p class="small">{{ size }}</p>
          <p class="small">png, jpeg, jpg</p>
        </div>
      </div>
    </div>

    <input
      ref="inputFile"
      accept="image/png, image/jpeg"
      class="input-file"
      type="file"
      @change="openImage" />

    <PopImageCrop
      ref="popImageCrop"
      :min-size="minSize"
      :aspect-ratio="aspectRatio"
      @crop-image="saveImage"></PopImageCrop>
  </div>
</template>
