

let $anime : any = null;
let $i18n : any= null;
let variables : any = {};

type MsgOption = {
  text: string | string[];
  type?: string;
  time?: number;
  wait?: number;
  btns?: { t: () => void; f?: () => void };
  force?: boolean;
};

class Msg {
  // Base
  static msgBoxsArray: Msg[] = [];
  private boxMsgs: HTMLElement;
  private boxsMsg: NodeListOf<HTMLDivElement>;
  private htmlMsg!: HTMLElement;

  // User control
  private text: string | string[]; // msg: Message text
  private type: string | undefined; // type: error,ok
  private time: number | undefined = 5000; // time: Time To Remove Message After...
  private btns: { t: () => void; f?: () => void } | undefined; // btns: if you need buttons => on click do something
  private wait: number | undefined = 300; // wait: Wait Time Before show Next Message
  private force: boolean = false;

  // Info Element
  private isRemove: boolean = false;
  // static id: number = 0;
  static wait: number = 0;
  // static removeLen : number = 0;
  // id: number = Msg.id;

  // event
  private onDestroyCallBack: (() => void) | null = null;

  constructor(
    { text, type, time, btns, wait, force }: MsgOption = { text: "" }
  ) {
    // Msg.id++;

    this.text = text;
    this.type = type;
    if (btns) {
      this.time = undefined;
      this.btns = btns;
    }

    if (time) {
      this.time = time;
    }
    if (wait) {
      this.wait = wait;
    }

    if (force) {
      this.force = force;
    }
    Msg.wait += this.wait!;

    this.boxMsgs = document.querySelector(".msg") as HTMLElement;
    if (!this.boxMsgs) {
      this.boxMsgs = $help().toHtml("<div class='msg'></div>");
      document.body.append(this.boxMsgs);
    }
    this.boxsMsg = this.boxMsgs.querySelectorAll(".box");

    // const lastBox = this.boxsMsg[this.boxsMsg.length - 1];

    // if (
    //   this.boxMsgs.clientHeight + window.innerHeight / 1.7 >=
    //   window.innerHeight
    // ) {
    //   lastBox.remove();
    // }

    this.createMsg();
  }

  private createMsg() {
    switch (this.type) {
      case "error": {
        this.htmlMsg = $help().toHtml(`<div class="box box-js-active error">
          <i class="ri-close-line"></i>
          <p>${this.text}</p>
        </div>`);
        break;
      }
      case "ok": {
        this.htmlMsg = $help().toHtml(`<div class="box box-js-active ok">
          <i class="ri-check-line"></i>
        <p>${this.text}</p>`);
        break;
      }
      case "sure": {
        this.htmlMsg = $help().toHtml(`<div class="box box-js-active sure">
          <i class="ri-error-warning-line"></i>
        <p>${this.text}</p>`);
        break;
      }

      default: {
        this.htmlMsg = $help().toHtml(`<div class="box  box-js-active">
          <i class="ri-notification-2-line"></i>
          <p>${this.text}</p>
        </div>`);
      }
    }

    if (this.btns) {
      this.addBtns();
    }

    this.show();

    if (!this.btns || (this.btns && this.time)) {
      setTimeout(() => {
        if (!this.isRemove) {
          this.remove();
        }
      }, this.time! + (!this.force ? Msg.wait! : 0));
    }
  }

  private addBtns() {
    // const t = this;
    const buttons = this.htmlMsg!.appendChild(
      $help().toHtml(`<div class="buttons"><div></div></div>`)
    ).querySelector("div");

    if (this.btns?.t) {
      const btnTrue = buttons!.appendChild(
        $help().toHtml(
          `<button class="true"><i class="ri-check-line"></i>${variables.yes}</button>`
        )
      );

      const clickTrue = async () => {
        btnTrue.removeEventListener("click", clickTrue);
        this.htmlMsg!.classList.add("disabled");
        await this.btns!.t();
        this.remove();
      };

      btnTrue.addEventListener("click", clickTrue);

      const btnFalse = buttons!.appendChild(
        $help().toHtml(
          `<button class="false"><i class="ri-close-line"></i>${variables.no}</button>`
        )
      );

      const clickFalse = async () => {
        btnFalse.removeEventListener("click", clickFalse);
        this.htmlMsg!.classList.add("disabled");
        if (this.btns?.f) {
          await this.btns!.f();
        }
        this.remove();
      };

      btnFalse.addEventListener("click", clickFalse);
    }
  }

  private remove() {
    
    // const t = this;
    this.isRemove = true;

    $anime({
      targets: this.htmlMsg,
      opacity: [1, 0],
      easing: "easeInOutCirc",
      height: 0,
      paddingTop: 0,
      filter: "blur(5px)",
      paddingBottom: 0,
      marginTop: 0,
      duration: 300,

      complete: () => {
      
        this.htmlMsg!.remove();
        Msg.msgBoxsArray = Msg.msgBoxsArray.filter((e) => e !== this);
        Msg.wait -= this.wait!;
        if (this.onDestroyCallBack) {
          this.onDestroyCallBack();
        }
      },
    });
  }

  private async show() {
    await new Promise((resolve) => {
      setTimeout(
        () => {
          if (this.boxMsgs.clientHeight >= window.innerHeight / 1.7) {
            let i = 0;
            let heightRemove = 0;

            // eslint-disable-next-line no-constant-condition
            while (true) {
              heightRemove += Msg.msgBoxsArray[i].htmlMsg!.clientHeight;

              if (
                this.boxMsgs.clientHeight - heightRemove <
                window.innerHeight / 1.7
              ) {
                if (!Msg.msgBoxsArray[i].isRemove) {
                  Msg.msgBoxsArray[i].remove();
                
                }

                break;
              } else {
                if (!Msg.msgBoxsArray[i].isRemove) {
                  Msg.msgBoxsArray[i].remove();
                 
                }

                i++;
              }
            }
          }

          this.boxsMsg = this.boxMsgs.querySelectorAll(".box");

          this.htmlMsg!.classList.add("visible");

          this.boxMsgs.prepend(this.htmlMsg!);
          Msg.msgBoxsArray.push(this);
        
                    

          this.boxsMsg = this.boxMsgs.querySelectorAll(".box");

          // const nuxtDir = systemStore ? systemStore?.$store?.getters?.lang?.dir : "rtl";
          $anime({
            targets: this.htmlMsg,
            opacity: [0, 1],

            translateX: variables.dir === "rtl" ? [150, 0] : [-150, 0],
            filter: ["blur(5px)", "blur(0px)"],
            // last Change   duration : 300,
            duration: 300,
            easing: "easeInOutCirc",
          });

       
          if (Msg.msgBoxsArray.length >= 2) {
            $anime({
              targets: Msg.msgBoxsArray[Msg.msgBoxsArray.length - 2].htmlMsg,
              // Msg.msgBoxsArray[this.boxsMsg.length - 2].htmlMsg
              marginTop: [
                -Msg?.msgBoxsArray[Msg.msgBoxsArray.length - 1]?.htmlMsg
                  ?.clientHeight,
                15,
              ],
              // last Change   duration : 300,
              duration: 300,
              easing: "easeInOutCirc",
            });
          }

          resolve(true);
          // last Change Msg.wait! = Msg.wait!-300
        },
        !this.force ? Msg.wait! - 300 : 0
      );

      // Msg.msgBoxsArray!.map((e)=> {
      //   return e.wait!;
      // }).reduce((e1,e2) => {
      //   return e1!+e2!;
      // })-this.wait!
    });
  }

  onDestroy(callback: () => void) {
    this.onDestroyCallBack = callback;
    return this;
  }
}

export const $msg = (MsgOption: MsgOption) => {
  const nuxt = useNuxtApp();
  $anime = nuxt.$anime;
  $i18n = nuxt.$i18n;

 
   variables = {
    yes: $i18n?.t("gl.yes"),
    no: $i18n?.t("gl.no"),
    dir: $i18n?.localeProperties.value.dir,
  };

  return new Msg(MsgOption);
};
