<script setup lang="ts">

onMounted(() => {
  if (!localStorage.getItem("set")) {
    document.querySelector(".notRemoveAttr")?.setAttribute("data-gg", "hi");
  }
});
</script>

<template>
  <div>
    <div class="anyClass">
      <div class="anyClass2">
        <div class="anyClass3"></div>
        <div class="notRemoveAttr">
          <div class="anyClass4"></div>
        </div>
      </div>
    </div>
  </div>
</template>
