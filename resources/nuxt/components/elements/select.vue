<script setup lang="ts">
const props = defineProps({
  elements: {
    type: Array as any,
    required: true,
  },
  select: {
    type: [String, Array],
    required: false,
    default: null,
  },
  linear: {
    type: Boolean,
    required: false,
  },
  multi: {
    type: Boolean,
    required: false,
    default: false,
  },
});
const emit = defineEmits(["onSelect"]);

const itemArray = ref() as Ref<HTMLElement[]>;
let items: any[] = [];

if (props.multi) {
  items = JSON.parse(JSON.stringify(props.select));
}
function selectBox(item: any) {
  if (props.multi) {
    const ele = event?.currentTarget as HTMLElement;
    if (items.includes(item.value)) {
      ele.classList.remove("select");
      items = items.filter((e) => e !== item.value);
    } else {
      items.push(item.value);
      ele.classList.add("select");
    }

    emit("onSelect", items);
  } else {
    itemArray.value.forEach((element: HTMLElement) => {
      element.classList.remove("select");
    });
    emit("onSelect", item);
    (event?.currentTarget as HTMLElement).classList.add("select");
  }
}
</script>

<template>
  <div class="select-element">
    <ul class="elements" :class="{ linear: linear }">
      <li
        v-for="(item, i) in elements"
        ref="itemArray"
        :key="i"
        :class="{
          select: multi ? select.includes(item.value) : item.value === select,
        }"
        @click="selectBox(item)"
      >
        <span class="box-select"><i class="ri-check-line"></i></span
        >{{ item.title }}
      </li>
    </ul>
  </div>
</template>

//
<script lang="ts">
// import Vue from 'vue'
// export default Vue.extend({
// props : {
//   elements : {
//     type : Array<any>,
//     required : true
//   },
//   select : {
//     type : String,
//     required : false,
//     default : null
//   },
//   linear : {
//     type : Boolean,
//     required : false
//   }
// },
// methods : {
//   selectBox(item : any) {
//     (this.$el.querySelectorAll(".elements li") as unknown as HTMLElement[]).forEach((element : HTMLElement)=> {
//       element.classList.remove("select")
//     });

//     this.$emit("onSelect",item);

//     (event?.currentTarget as HTMLElement).classList.add("select");
//   }
// }
// })
//
</script>
