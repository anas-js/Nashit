<script setup lang="ts">
const active = ref(false);
const large = ref(false);
const showInputTitle = ref(false);
const el = ref();
const emit = defineEmits([
  "onClose",
  "titleChange",
  "blurInputTitle",
  "onClickBtn",
]);
const props = defineProps({
  title: {
    type: String,
    required: true,
  },
  btn: {
    type: String,
    required: false,
    default: null,
  },
  btnIcon: {
    type: String,
    required: false,
    default: null,
  },
  canChangeTitle: {
    type: Boolean,
    required: false,
    default: false,
  },
});

// const router = useRouter();

onMounted(() => {
  //  console.dir(el.value);
  
  const doc = $help().findDOM(el.value, (e) => {
    return e.classList.contains("page-layout") || e.classList.contains("default-layout");
  });

  doc?.appendChild(el.value);

  if (el.value?.querySelector(".box")!.clientHeight > window.innerHeight) {
    large.value = true;
  }

  // console.log(doc);
});

// onNuxtReady(() => {
//   // debugger;
//   console.log(document.querySelector(".page-layout"));
// });

onBeforeUnmount(() => {
  el.value?.remove();
});

//!!!!!!!!!!

// const route = useRoute();

// watch(()=>route.path,() => {
//   el.value.remove();
// });

const input = ref();
function focusInput() {
  setTimeout(() => {
    input.value.focus();
  }, 100);
}

function show(): void {
  active.value = true;
}

function close(): void {
  active.value = false;
  emit("onClose");
}

function showTitle() {
  if (props.canChangeTitle) {
    showInputTitle.value = !showInputTitle.value;
    focusInput();
  }
}

function closeInput() {
  showInputTitle.value = false;
  emit("blurInputTitle", (event!.currentTarget as any)?.value);
}

defineExpose({
  show,
  close,
});
</script>
<template>
  <div ref="el" class="pop-up" :class="{ active: active, large: large }">
    <div class="box">
      <div class="title">
        <h1 ref="title" v-if="!showInputTitle" @click="showTitle">
          {{ title }}
        </h1>
        <input
          v-else
          ref="input"
          :value="title"
          type="text"
          @blur="closeInput"
          @input="$emit('titleChange', ($event.currentTarget as any)?.value)"
        />
        <button class="close" @click="close()">
          <i class="ri-close-line"></i>
        </button>
      </div>
      <div class="content">
        <slot></slot>
      </div>
      <div class="btns">
        <button class="btn-send" v-if="btn" @click="$emit('onClickBtn')">
          <i v-if="btnIcon" :class="btnIcon"></i> {{ btn }}
        </button>
        <button class="btn-close" @click="close()">
          <i class="ri-close-line"></i>{{ $t("gl.cancel") }}
        </button>
      </div>
    </div>
  </div>
</template>
