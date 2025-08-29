import {
  defineConfig,
  minimalPreset as preset,
  type Preset,
} from "@vite-pwa/assets-generator/config";

export default defineConfig({
  preset: {
    transparent: {
      sizes: [64,144, 192, 512],
      favicons: [[48, "favicon.ico"]],
    },
    maskable: {
      sizes: [512],
    },
    apple: {
      sizes: [180],
    }
  } as Preset,
  images: [
    "public/images/pwa/logo.svg",
    // 'public-dev/logo.svg'
  ],
});
