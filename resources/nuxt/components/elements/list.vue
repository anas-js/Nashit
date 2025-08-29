<script setup lang="ts">
// interface prpos [
// ]

const props =  defineProps({
      list: {
      required: true,
      type: Array as any,
    },
    loading : {
      type : Boolean,
      required : false,
      default : false
    },
    hiddin : {
      type : Boolean,
      required : false,
      default : false
    }
});

const show = ref(!props.hiddin);
const {$i18n} = useNuxtApp();

// interface gg {
//   name : Array<any>
// }

defineEmits(['select']);

const isRtl = computed(()=> $i18n.localeProperties.value.dir === 'rtl');

defineExpose({
  show() {
    show.value = true;
  },
  close() {
    show.value = false;
  },
  toggle() {
    show.value = !show.value;
  }
})
</script>

<template>
  <div class="list-items" :class="{hiddin : !show,absolute : hiddin}">
    <div class="list">
      <span v-for="(item, i) in list" :key="i" @click="$emit('select', item)">
        <template v-if="isRtl"
          ><i class="ri-corner-down-left-line"></i></template
        ><template v-else><i class="ri-corner-down-right-line"></i></template
        >{{ item.name || item.title }}
      </span>
      <small-loading v-if="loading"></small-loading>
    </div>
  </div>
</template>
