export default defineNuxtPlugin(async (_) => {
  await $sync();

  window.addEventListener("online", async () => {
    await $sync();
  });

});
