<script setup lang="ts">
import Cropper from "cropperjs";
import "cropperjs/dist/cropper.css";
defineEmits(["cropImage","onClose"]);
const props = defineProps({
  minSize : {
    type : Object,
    required : true
  },
  aspectRatio : {
    type : Number,
    required : true
  }
});

let crop: Cropper;
const lang = "components.pop.imageCrop";
const imageResizeEle = ref();
const pop = ref();

let mount = false;
onMounted(() => {
  if (!mount) {
    crop = new Cropper(imageResizeEle.value, {
      aspectRatio: props.aspectRatio,
      dragMode: "move",
      // scalable : false,
      autoCropArea: 1,
      restore : false,
      // responsive : false,
      // zoomable : false,
     wheelZoomRatio : 0.2,
      // zoomOnTouch : false,
      // zoomOnWheel : false,
      minContainerWidth: 0,
      minContainerHeight: 0,
      viewMode: 3,
      // minCropBoxWidth: props.minSize.width,
      // minCropBoxHeight: props.minSize.height,
      cropmove(event) {
        const width = crop.getData().width < props.minSize.width;
        const height = crop.getData().height < props.minSize.height;
       
        // console.log(event.detail.originalEvent);
        // console.log(crop.getData());
        if (width || height) {
          // event.preventDefault();
       
    
          // event.preventDefault();
          crop.setData({
            width: width ? props.minSize.width : crop.getData().width,
            height: height ? props.minSize.height : crop.getData().height,
          });
          // crop.reset();
        }
      },
      // cropend() {
      //   if ((crop.getData().width < 92) || (crop.getData().height < 50)) {
      //     crop.reset();
      //   }
      // },
      zoom(event) {
        const width = crop.getData().width < props.minSize.width;
        const height = crop.getData().height < props.minSize.height;

        // console.log(crop.getData());
        
        if ((width || height) && (event.detail.oldRatio < event.detail.ratio)) {
          // console.log(crop.getData())
          event.preventDefault();
          crop.setData({
            width: width ? props.minSize.width : crop.getData().width,
            height: height ? props.minSize.height : crop.getData().height,
          });
        }
      },
    });
  }
  mount = true;
});

defineExpose({
  getCrop() {
    return crop;
  },
  pop,
});
</script>

<template>
  <PopUp
    ref="pop"
    class="imageCropPOP scaleable-box"
    :title="$t(`${lang}.customize`)"
    btn-icon="ri-checkbox-circle-line"
    :btn="$t(`${lang}.select`)"
    @on-close="$emit('onClose')"

    @on-click-btn="$emit('cropImage')">
    <div>
    <p class="upload-alert alert warning full liner"><i class="ri-alert-line"></i><span>{{ $t(`${lang}.alert`) }}</span></p>
      <div class="image-container">
        <img ref="imageResizeEle" />
      </div>
      <slot></slot>
    </div>
  </PopUp>
</template>
