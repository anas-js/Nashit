

<script setup>
const props = defineProps({
  set: {
    type: Boolean,
    required: false,
    default: false,
  },
  watch : {
    type: Boolean,
    required: false,
    default: false,
  }
});

const active = ref(props.set);

if(props.watch) {
  watch(()=>props.set,function (newv) {
    active.value = newv;
  });
}
defineExpose({
  active,
});
defineEmits(["toggle"]);
</script>

<template>
  <div
    class="btn-toggle"
    :class="{ active: active }"
    @click="(active = !active), $emit('toggle', active)"
  >
    <span></span>
  </div>
</template>



