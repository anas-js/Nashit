// eslint-disable-next-line @typescript-eslint/no-invalid-void-type
export const $sleep = async (time = 3000,callback : () => void | Promise<any> = () => {}) => {
    await new Promise((resolve) => {
        setTimeout(async () => {
          await callback();
          resolve(true);
        }, time);
      });
}