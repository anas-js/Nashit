export const onScrollActive = ({
  from,
  to,
  callback,
  target,
  fromWidth,
  toWidth
}: {
  from: number;
  to: number;
  callback: (scrollValue: number, value?: number) => void;
  target?: number;
  fromWidth?: number;
  toWidth? : number
}) => {
  const handel = () => {
    if (window.scrollY >= from && window.scrollY <= to) {

      if ((fromWidth && fromWidth < window.innerWidth) || (toWidth && toWidth > window.innerWidth)) {
        return;
      }
      const counter = window.scrollY - from;
      if (target) {
        const value = (target / (to - from)) * counter;
        callback(value, counter);
      } else {
        callback(counter);
      }
    }
  };

  window.addEventListener("scroll", handel);
  onBeforeUnmount(() => {
    window.removeEventListener("scroll", handel);
  });
};
