
<script setup lang="ts">
definePageMeta({
  middleware : (to) => {
    const localePath = useLocalePath();
    return navigateTo(localePath(`/course/${to.params.id}/stats`))
  }
});
</script>
