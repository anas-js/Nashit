<script setup lang="ts">
import Quill from "quill";
import hljs from "highlight.js";
import "quill/dist/quill.snow.css";
import "highlight.js/scss/atom-one-dark.scss";

const lang = "components.noteBook";
const editor = ref();
const emit = defineEmits(["write", "done"]);
const props = defineProps({
  setText: {
    type: String,
    reqired: false,
    default: null,
  },
  disabled : {
    type: Boolean,
    reqired: false,
    default: false,
  }
});
const i18n = useI18n();
const toolbar = [
  [{ header: [1, 2, 3, 4, 5, 6, false] }],
  [{ size: ["small", false, "large", "huge"] }],
  [{ font: [] }],

  ["bold", "italic", "underline", "strike"], // toggled buttons
  ["blockquote", "code-block", "code"],

  // [{ header: 1 }, { header: 2 }],
  [{ list: "ordered" }, { list: "bullet" }],
  [{ indent: "+1" }, { indent: "-1" }], // outdent/indent
  [{ direction: "rtl" }], // text direction

  // custom dropdown

  [{ color: [] }, { background: [] }], // dropdown with defaults from theme
  [{ align: [] }],

  ["clean"],
];

const editorSettings = {
  placeholder: $t("components.noteBook.placeholder"),
  //!!!!!!! scrollingContainer: "#quill-container",
  modules: {
    syntax: { hljs },
    toolbar: toolbar,
  },
  theme: "snow",
  // disable: true
};

const icons = Quill.import("ui/icons") as any;

icons.bold = '<i class="ri-bold"></i>';
icons.code = '<i class="ri-command-line"></i>';
icons.blockquote = '<i class="ri-double-quotes-r"></i>';
icons["code-block"] = '<i class="ri-braces-line"></i>';

icons.italic = '<i class="ri-italic"></i>';
icons.underline = '<i class="ri-underline"></i>';
icons.strike = '<i class="ri-strikethrough"></i>';
icons.list.ordered = '<i class="ri-list-ordered"></i>';
icons.list.bullet = '<i class="ri-list-check"></i>';

icons.indent["-1"] = '<i class="ri-contract-right-line"></i>';
icons.indent["+1"] = '<i class="ri-contract-left-line"></i>';
icons.direction[""] = '<i class="ri-text-direction-r"></i>';
icons.direction.rtl = '<i class="ri-text-direction-l"></i>';
icons.align[""] = '<i class="ri-align-right"></i>';
icons.align.right = '<i class="ri-align-left"></i>';

icons.color = '<i class="ri-font-color"></i>';
icons.background = '<i class="ri-brush-2-line"></i>';

icons.align.center = '<i class="ri-align-center"></i>';
icons.align.justify = '<i class="ri-align-justify"></i>';
icons.clean = '<i class="ri-format-clear"></i>';

// function write() {
//   if (this.canSend) {
//     this.funSend = null;

//     this.shows.loadingSave = true;
//     (this.funSend as any) = () => {
//       if (!navigator.onLine) {
//         this.$emit("onOfflineSave", {
//           item: this.item,
//           text: this.contnet,
//         });
//       } else {
//         axios
//           .post("/" + this.to, {
//             id: this.item.id,
//             text: this.contnet,
//             // @ts-ignore
//             returnSmallText: !!this.itemProperties?.smallText,
//           })
//           .then((res) => {
//             this.shows.loadingSave = false;
//             this.send = null;
//             this.funSend = null;

//             if ((this.itemProperties as any)?.smallText) {
//               (this.itemProperties as any).smallText = res.data.smallText;
//             }
//             // @ts-ignore
//             this.itemProperties.noteText = this.contnet;

//             // switch (this.to) {
//             //   case 'lesson': {

//             //     break;
//             //   }
//             //   case 'borad': {
//             //     // @ts-ignore
//             //     this.lesson.noteText = this.contnet;
//             //     break;
//             //   }
//             // }

//             // this.$emit("returnSmallText","نص تجربة");

//             // if(res.data.smallText) {
//             //   this.$emit("returnSmallText",res.data.SmallText);
//             // }

//           })
//           .catch((e) => {
//             this.shows.loadingSave = false;
//             this.send = null;
//             this.funSend = null;
//             this.canSend = false;

//             // @ts-ignore
//             const editor = this.$refs.editor!?.quill;
//             editor.blur();

//             // document.body.click();
//             // @ts-ignore
//             this.contnet = this.itemProperties?.noteText;
//             setTimeout(() => {
//               this.canSend = true;
//             }, 100);

//             window.removeEventListener("beforeunload", this.eventList!);

//             // eslint-disable-next-line no-new
//             Msg({
//               text: String($t(`${lang}.cant`)),
//               type: "error",
//             });
//           });
//       }
//     };

//   }
// }
const write = ref(false);
const done = ref();
let timeout: any = null;

let quill: Quill;

let silent = false;
const setHtml = (text: string) => {
    // text = "<p>1222 2 dd q q 1 1                </p>"
    silent = true;
    quill.root.innerHTML = text;
    // console.log(quill.clipboard.dangerouslyPasteHTML("<p>1222 2 dd q q 1 1                </p>"));
    // quill.setContents(quill.clipboard.convert({ html: text }), "silent");
    // quill.setSelection('');
    // quill.focus();
    if (!text && i18n.localeProperties.value.dir === "ltr") {
      quill.format("align", "right");
      quill.format("direction", "rtl");
    }
    
  };

  let mount = false;
onMounted(() => {
  if(mount) {
    return;
  }

  mount = true;
  if (!document.head.querySelector("style[langstyle]")) {
    const langStyle = document.createElement("style");
    langStyle.setAttribute("langstyle", "true");
    langStyle.innerHTML = `
    .ql-snow .ql-picker.ql-header .ql-picker-item::before,
  .ql-snow .ql-picker.ql-header .ql-picker-label::before,
  .ql-snow .ql-picker.ql-size .ql-picker-item::before,
  .ql-snow .ql-picker.ql-size .ql-picker-label::before,
  .ql-snow .ql-picker.ql-font .ql-picker-item::before,
  .ql-snow .ql-picker.ql-font .ql-picker-label::before {
    content: '${$t(`${lang}.labels.normal`)}';
  }

 .ql-snow .ql-picker.ql-header .ql-picker-item[data-value="1"]::before, .ql-snow .ql-picker.ql-header .ql-picker-label[data-value="1"]::before {
	 content: '${$t(`${lang}.labels.title`)} 1';
}
 .ql-snow .ql-picker.ql-header .ql-picker-item[data-value="2"]::before, .ql-snow .ql-picker.ql-header .ql-picker-label[data-value="2"]::before {
	 content: '${$t(`${lang}.labels.title`)} 2';
}
 .ql-snow .ql-picker.ql-header .ql-picker-item[data-value="3"]::before, .ql-snow .ql-picker.ql-header .ql-picker-label[data-value="3"]::before {
	 content: '${$t(`${lang}.labels.title`)} 3';
}
 .ql-snow .ql-picker.ql-header .ql-picker-item[data-value="4"]::before, .ql-snow .ql-picker.ql-header .ql-picker-label[data-value="4"]::before {
	 content: '${$t(`${lang}.labels.title`)} 4';
}
 .ql-snow .ql-picker.ql-header .ql-picker-item[data-value="5"]::before, .ql-snow .ql-picker.ql-header .ql-picker-label[data-value="5"]::before {
	 content: '${$t(`${lang}.labels.title`)} 5';
}
 .ql-snow .ql-picker.ql-header .ql-picker-item[data-value="6"]::before, .ql-snow .ql-picker.ql-header .ql-picker-label[data-value="6"]::before {
	 content: '${$t(`${lang}.labels.title`)} 6';
}
 .ql-snow .ql-picker.ql-size .ql-picker-item[data-value='huge']::before, .ql-snow .ql-picker.ql-size .ql-picker-label[data-value='huge']::before {
	 content: '${$t(`${lang}.labels.big`)}';
}
 .ql-snow .ql-picker.ql-size .ql-picker-item[data-value='large']::before, .ql-snow .ql-picker.ql-size .ql-picker-label[data-value='large']::before {
	 content: '${$t(`${lang}.labels.medium`)}';
}
 .ql-snow .ql-picker.ql-size .ql-picker-item[data-value='small']::before, .ql-snow .ql-picker.ql-size .ql-picker-label[data-value='small']::before {
	 content: '${$t(`${lang}.labels.small`)}';
}

    `;

    document.head.append(langStyle);
  }

   quill = new Quill(editor.value, editorSettings);
  
  if(props.disabled) {
    quill.disable();
  }
  





  // setHtml(props.setText);

  // watch(
  //   () => props.setText,
  //   (newText) => {
  //     setHtml(newText);
  //   }
  // );

  let event: any = null;

  // quill.on("text-change",(e) => {
    
    
  // });

  quill.on("text-change", (e) => {
    if(silent) {
      silent = false;
      return;
    }
    if (!write.value) {
      emit("write");
      write.value = true;
    }
    // remvoe last event before run
    clearTimeout(timeout);
    window.removeEventListener("beforeunload", event);

    // add new event
    function mybefore(e: Event) {
      e.preventDefault();
      e.returnValue = true;
    }
    event = mybefore;
    window.addEventListener("beforeunload", event);

    done.value = () => {
      // emit("done", quill.getSemanticHTML());
      const copy = editor.value
        .querySelector(".ql-editor")
        ?.cloneNode(true) as HTMLElement;
      copy.querySelectorAll("select")?.forEach((e) => e.remove());

      
    

      emit("done", copy?.innerHTML);
      window.removeEventListener("beforeunload", event);
      write.value = false;
    };

    timeout = setTimeout(done.value, 1000);
  });
});

function doneNow() {
  if (write.value) {
    done.value();
    clearTimeout(timeout);
  }
}
defineExpose({
  write,
  doneNow,
  setHtml
});

//   mixins: [functions],
//   },
//   data() {
//     return {
//       lang: 'components.noteBook',
//       contnet: '',
//       send: null,
//       funSend: null,
//       canSend: false,
//       eventList: null,
//       itemProperties: null,
//       shows: {
//         loadingSave: false,
//       },

//     };
//   },
//   watch: {
//     item(les) {
//       if (!Object.keys(les).length) {
//         return;
//       }

//       this.itemProperties = les;
//       const lang = this.$store.getters.lang;

//       if (lang.dir === 'ltr') {
//         const editor = this.$refs?.editor!.quill;
//         editor.format('direction', 'rtl');
//         this.canSend = false;
//         editor.format('align', 'right');
//         this.canSend = false;
//       }

//       this.setContnet(les.noteText);
//     },
//   },
//   methods: {
//     setContnet(text: string) {

//        this.contnet = text;

//       setTimeout(() => {
//         this.canSend = true;
//       }, 100);

//     },

//     close() {
//       if (this.funSend) {
//         (this.funSend as any)();
//         clearTimeout(this.send as any);
//       }
//       this.canSend = false;
//     },
//   },
</script>

<template>
  <div class="note-book quilljs">
    <div v-if="write" class="save-changes">
      <small-loading></small-loading>{{ $t(`gl.loading.wait`) }}
    </div>
    <div ref="editor"></div>
  </div>
</template>

<style lang="scss"></style>
