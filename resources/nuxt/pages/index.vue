<script setup>
const lang = "pages.index";
const localePath = useLocalePath();
const $auth = useAuth();

useAnimeScroll();
// const pwa = useNuxtApp()?.$pwa;

// document.addEventListener("click", function () {
//   console.log(pwa.showInstallPrompt)
//   console.log(await pwa.install());
  
// });

// console.log(useNuxtApp().$pwa.)
definePageMeta({
  offAuth: true,
});
</script>

<template>
  <div class="index-page container">
    <section class="first-sect row">
      <div class="rigth col-lg-6 col-md-12">
        <div class="title">
          <h1 class="ai" anime-name="rigth-in" next-anime>
            {{ $t(`${lang}.firstSect.title`) }}
          </h1>
          <img
            class="arrow ai"
            anime-name="rigth-in"
            anime-delay="200"
            next-anime
            src="~/assets/images/index/arrow.svg" />
          <span
            class="word ai"
            anime-name="rigth-in"
            anime-delay="500"
            next-anime>
            {{ $t(`${lang}.firstSect.smallWord`) }}
          </span>
        </div>
        <p class="ai" anime-name="rigth-in" anime-delay="700" next-anime>
          {{ $t(`${lang}.firstSect.comment`) }}
        </p>

        <div
          class="buttons ai"
          anime-name="rigth-in"
          anime-delay="800"
          next-anime>
          <NuxtLink class="register" :to="localePath('/dashboard/courses')">
            <template v-if="!$auth.loggedIn">
              {{ $t(`${lang}.firstSect.buttons.signup`) }}
            </template>
            <template v-else>
              {{ $t(`${lang}.firstSect.buttons.dashboard`) }}
            </template>
          </NuxtLink>
          <!-- <NuxtLink class="example" :to="localePath('/')">{{ $t(`${lang}.firstSect.buttons.example`) }}</NuxtLink> -->
        </div>
      </div>
      <div class="left col-lg-6 col-md-12 d-flex justify-content-end">
        <div class="image">
          <div
            class="back ai"
            next-anime
            anime-name="left-in"
            anime-delay="850"></div>
          <img
            class="rocket ai"
            anime-name="left-in"
            anime-delay="950"
            next-anime
            src="~/assets/images/index/rocket.png" />
          <!-- anime-after="200,991" -->
          <img
            class="cofe ai"
            anime-name="top-in"
            next-anime
            anime-delay="1050"
            src="~/assets/images/index/cofe.png" />
          <img
            class="aim ai"
            anime-name="bottom-in"
            next-anime
            anime-delay="1000"
            src="~/assets/images/index/aim.png" />
          <div
            class="pattren ai"
            anime-name="left-in-center"
            anime-delay="900"
            next-anime></div>
        </div>
      </div>
    </section>
    <div class="scroll-down ai" anime-name="bottom-in" anime-delay="1100">
      <div class="box"></div>
      <div class="line-doted">
        <span></span>
        <span></span>
        <span></span>
        <span></span>
      </div>
    </div>
    <section class="features">
      <div class="no-ads row feat">
        <div class="rigth col-lg-6 col-md-12 d-flex align-items-center">
          <div
            class="icon ai"
            anime-name="rigth-in"
            next-anime
            anime-before="200">
            <i class="ri-megaphone-line"></i>
          </div>
          <div class="text">
            <h1
              class="red ai"
              anime-name="rigth-in"
              anime-delay="50"
              next-anime>
              {{ $t(`${lang}.features.noAds.title`) }}
            </h1>
            <p class="text-no-ads ai" anime-name="rigth-in" anime-delay="100">
              {{ $t(`${lang}.features.noAds.comment`) }}
            </p>
          </div>
        </div>
        <div class="left col-lg-6 col-md-12 d-flex justify-content-lg-end">
          <div class="image group-anime" wait-anime="text-no-ads">
            <div
              class="pattren ai"
              anime-name="bottom-in-center"
              next-anime></div>
            <div
              class="back ai"
              anime-name="bottom-in"
              anime-delay="200"
              next-anime></div>
            <img
              class="img speaker ai"
              anime-name="left-in"
              anime-delay="300"
              next-anime
              src="~/assets/images/index/speaker.png" />
            <div
              class="ads-image-1 ai"
              anime-name="rigth-in"
              anime-delay="400"
              next-anime>
              <span
                class="app-img ai"
                anime-name="rigth-in"
                anime-delay="500"
                next-anime></span>
              <div
                class="text-app ai"
                anime-name="rigth-in"
                anime-delay="600"
                next-anime>
                <span class="app-title"></span>
                <div class="app-commint">
                  <span></span>
                  <span></span>
                  <span></span>
                </div>
              </div>
              <span
                class="btn-download ai"
                anime-name="rigth-in"
                anime-delay="700"
                next-anime>
                <i class="ri-arrow-down-line"></i>
              </span>
              <div
                class="blur-layer ai"
                anime-name="bottom-in"
                anime-delay="900"
                next-anime>
                <i class="ri-close-line"></i>
              </div>
            </div>
            <div
              class="ads-image-2 ai"
              anime-name="bottom-in"
              anime-delay="1000"
              next-anime>
              <div
                class="video ai"
                anime-name="bottom-in"
                anime-delay="1100"
                next-anime>
                <i
                  class="ri-movie-line ai"
                  anime-name="bottom-in"
                  anime-delay="1200"
                  next-anime></i>
              </div>
              <div
                class="blur-layer ai"
                anime-name="bottom-in"
                anime-delay="1500">
                <i class="ri-close-line"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="star-img ai" anime-name="bottom-in" anime-before="120">
        <img src="~/assets/images/index/star.png" />
        <img src="~/assets/images/index/star.png" />
      </div>

      <div class="feedbacks row feat">
        <div
          class="rigth col-lg-6 col-md-12 d-flex justify-content-start order">
          <div class="image">
            <div
              class="pattren ai"
              anime-name="bottom-in-center"
              next-anime></div>
            <div class="back ai" anime-name="bottom-in" next-anime></div>
            <img
              class="img hand-up ai"
              anime-name="left-in"
              anime-delay="300"
              next-anime
              src="~/assets/images/index/hand-up.png" />
            <img
              class="img hand-down ai"
              anime-name="rigth-in"
              anime-delay="400"
              next-anime
              src="~/assets/images/index/hand-down.png" />

            <div
              class="box excellent ai"
              anime-name="rigth-in"
              anime-delay="500"
              next-anime>
              <div
                class="mark ai"
                anime-name="rigth-in"
                anime-delay="600"
                next-anime>
                <i
                  class="ri-user-smile-line ai"
                  anime-name="rigth-in"
                  anime-delay="700"
                  next-anime></i>
              </div>
              <p>{{ $t(`${lang}.features.feedbacks.eval.0`) }}</p>
            </div>
            <div
              class="box very-good ai"
              anime-name="left-in"
              anime-delay="800"
              next-anime>
              <div
                class="mark ai"
                anime-name="left-in"
                anime-delay="900"
                next-anime>
                <i
                  class="ri-seedling-line ai"
                  anime-name="left-in"
                  anime-delay="1000"
                  next-anime></i>
              </div>
              <p>{{ $t(`${lang}.features.feedbacks.eval.1`) }}</p>
            </div>
            <div
              class="box good ai"
              anime-name="left-in"
              anime-delay="1100"
              next-anime>
              <div
                class="mark ai"
                anime-name="left-in"
                anime-delay="1200"
                next-anime>
                <i
                  class="ri-heart-pulse-line ai"
                  anime-name="left-in"
                  anime-delay="1300"
                  next-anime></i>
              </div>
              <p>{{ $t(`${lang}.features.feedbacks.eval.2`) }}</p>
            </div>
            <div
              class="box bad ai"
              anime-name="rigth-in"
              anime-delay="1400"
              next-anime>
              <div
                class="mark ai"
                anime-name="rigth-in"
                anime-delay="1500"
                next-anime>
                <i
                  class="ri-information-line ai"
                  anime-name="rigth-in"
                  anime-delay="1600"
                  next-anime></i>
              </div>
              <p>{{ $t(`${lang}.features.feedbacks.eval.3`) }}</p>
            </div>
            <div
              class="box vary-bad ai"
              anime-name="bottom-in"
              anime-delay="1700"
              next-anime>
              <div
                class="mark ai"
                anime-name="bottom-in"
                anime-delay="1800"
                next-anime>
                <i
                  class="ri-alert-line ai"
                  anime-name="bottom-in"
                  anime-delay="1900"></i>
              </div>
              <p>{{ $t(`${lang}.features.feedbacks.eval.4`) }}</p>
            </div>
          </div>
        </div>
        <div
          class="left col-lg-6 col-md-12 d-flex align-items-center justify-content-lg-end">
          <div
            class="icon black ai"
            anime-name="left-in"
            anime-before="200"
            next-anime>
            <i class="ri-bar-chart-box-line"></i>
          </div>
          <div class="text">
            <h1
              class="black ai"
              anime-name="left-in"
              anime-delay="50"
              next-anime>
              {{ $t(`${lang}.features.feedbacks.title`) }}
            </h1>
            <p class="ai" anime-name="left-in" anime-delay="100">
              {{ $t(`${lang}.features.feedbacks.comment`) }}
            </p>
          </div>
        </div>
      </div>
      <div class="free row feat">
        <div class="rigth col-lg-6 col-md-12 d-flex align-items-center">
          <div
            class="icon ai"
            anime-name="rigth-in"
            anime-before="200"
            next-anime>
            <i class="ri-price-tag-3-line"></i>
          </div>
          <div class="text">
            <h1
              class="red ai"
              anime-name="rigth-in"
              next-anime
              anime-delay="50">
              {{ $t(`${lang}.features.free.title`) }}
            </h1>
            <p class="ai" anime-name="rigth-in" anime-delay="100">
              {{ $t(`${lang}.features.free.comment`) }}
            </p>
          </div>
        </div>
        <div class="left col-lg-6 col-md-12 d-flex justify-content-lg-end">
          <div class="image">
            <div
              class="pattren ai"
              anime-name="bottom-in-center"
              next-anime></div>
            <div class="back ai" anime-name="bottom-in" next-anime></div>
            <img
              class="img dollar ai"
              anime-name="left-in"
              anime-delay="300"
              src="~/assets/images/index/dollar.png"
              next-anime />
            <img
              class="img monay ai"
              anime-name="rigth-in"
              anime-delay="500"
              src="~/assets/images/index/monay.png" />
          </div>
        </div>
      </div>
      <div class="multi row feat">
        <div
          class="rigth col-lg-6 col-md-12 d-flex justify-content-start order">
          <div class="image">
            <div
              class="pattren ai"
              anime-name="bottom-in-center"
              next-anime></div>
            <div class="back ai" anime-name="bottom-in" next-anime></div>
            <img
              class="img floder ai"
              anime-name="bottom-in"
              next-anime
              anime-delay="200"
              src="~/assets/images/index/floder.png" />

            <div
              class="box-course one ai"
              anime-name="left-in"
              next-anime
              anime-delay="600">
              <div class="background"></div>
              <div class="text">
                <div class="rigth">
                  <h2 class="name">
                    {{ $t(`${lang}.features.multi.image.0.title`) }}
                  </h2>
                  <p class="date">2023-05-11</p>
                  <p class="day-left">
                    {{ $t(`${lang}.features.multi.image.0.date`) }}
                  </p>
                </div>
                <div class="left">
                  <p class="rate">52%</p>
                </div>
              </div>
            </div>
            <div
              class="box-course two ai"
              anime-name="left-in"
              next-anime
              anime-delay="500">
              <div class="background"></div>
              <div class="text">
                <div class="rigth">
                  <h2 class="name">
                    {{ $t(`${lang}.features.multi.image.1.title`) }}
                  </h2>
                  <p class="date">2023-6-15</p>
                  <p class="day-left">
                    {{ $t(`${lang}.features.multi.image.1.date`) }}
                  </p>
                </div>
                <div class="left">
                  <p class="rate">77%</p>
                </div>
              </div>
            </div>
            <div
              class="box-course three ai"
              anime-name="left-in"
              next-anime
              anime-delay="400">
              <div class="background"></div>
              <div class="text">
                <div class="rigth">
                  <h2 class="name">
                    {{ $t(`${lang}.features.multi.image.2.title`) }}
                  </h2>
                  <p class="date">2023-02-17</p>
                  <p class="day-left">
                    {{ $t(`${lang}.features.multi.image.2.date`) }}
                  </p>
                </div>
                <div class="left">
                  <p class="rate">62%</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div
          class="left col-lg-6 col-md-12 d-flex align-items-center justify-content-lg-end">
          <div
            class="icon black ai"
            anime-name="left-in"
            anime-before="200"
            next-anime>
            <i class="ri-folder-line"></i>
          </div>
          <div class="text">
            <h1
              class="black ai"
              anime-name="left-in"
              next-anime
              anime-delay="50">
              {{ $t(`${lang}.features.multi.title`) }}
            </h1>
            <p class="ai" anime-name="left-in" anime-delay="100">
              {{ $t(`${lang}.features.multi.comment`) }}
            </p>
          </div>
        </div>
      </div>
      <div class="done-img ai" anime-name="bottom-in" anime-before="120">
        <img src="~/assets/images/index/done.png" />
        <img src="~/assets/images/index/done.png" />
      </div>
    </section>
    <section class="other-features">
      <h2 class="title ai" anime-name="bottom-in" anime-before="120">
        {{ $t(`${lang}.otherFeat.title`) }}
      </h2>

      <div class="image">
        <img
          class="start-medal ai"
          anime-name="top-in"
          next-anime
          src="~/assets/images/index/start-medal.png" />

        <div
          class="box-feat f6 ai"
          anime-name="rigth-in"
          next-anime
          anime-off-auto-run
          anime-delay="200">
          <div class="icon"><i class="ri-apps-2-line"></i></div>
          <p>{{ $t(`${lang}.otherFeat.elements.5`) }}</p>
        </div>

        <div
          class="box-feat f7 ai"
          anime-name="rigth-in"
          next-anime
          anime-off-auto-run
          anime-delay="300">
          <div class="icon"><i class="ri-share-line"></i></div>
          <p>{{ $t(`${lang}.otherFeat.elements.6`) }}</p>
        </div>

        <div
          class="box-feat f8 ai"
          anime-name="rigth-in"
          anime-off-auto-run
          next-anime
          anime-delay="400">
          <div class="icon"><i class="ri-moon-line"></i></div>
          <p>{{ $t(`${lang}.otherFeat.elements.7`) }}</p>
        </div>

        <div
          class="box-feat f9 ai"
          next-anime
          anime-name="rigth-in"
          anime-delay="500"
          anime-off-auto-run>
          <div class="icon"><i class="ri-sticky-note-line"></i></div>
          <p>{{ $t(`${lang}.otherFeat.elements.8`) }}</p>
        </div>

        <div
          class="box-feat f1 ai"
          anime-name="top-in"
          next-anime
          anime-off-auto-run
          anime-delay="600">
          <div class="icon"><i class="ri-home-2-line"></i></div>
          <p>{{ $t(`${lang}.otherFeat.elements.0`) }}</p>
        </div>
        <div
          class="box-feat f2 ai"
          anime-name="left-in"
          anime-off-auto-run
          next-anime
          anime-delay="700">
          <div class="icon"><i class="ri-earth-line"></i></div>
          <p>{{ $t(`${lang}.otherFeat.elements.1`) }}</p>
        </div>
        <div
          class="box-feat f3 ai"
          anime-name="left-in"
          next-anime
          anime-off-auto-run
          anime-delay="800">
          <div class="icon"><i class="ri-add-circle-line"></i></div>
          <p>{{ $t(`${lang}.otherFeat.elements.2`) }}</p>
        </div>
        <div
          class="box-feat f4 ai"
          anime-name="left-in"
          next-anime
          anime-off-auto-run
          anime-delay="900">
          <div class="icon"><i class="ri-arrow-down-line"></i></div>
          <p>{{ $t(`${lang}.otherFeat.elements.3`) }}</p>
        </div>
        <div
          class="box-feat f5 ai"
          anime-name="left-in"
          anime-delay="1000"
          anime-off-auto-run>
          <div class="icon"><i class="ri-bubble-chart-line"></i></div>
          <p>{{ $t(`${lang}.otherFeat.elements.4`) }}</p>
        </div>
      </div>
    </section>
    <section v-if="!$auth.loggedIn" class="register-now">
      <div
        class="body row ai"
        anime-name="top-in"
        next-anime
        anime-delay="200"
        anime-before="300">
        <div class="rigth col-lg-6 col-md-12 d-flex align-items-center">
          <div>
            <h2
              class="title ai"
              anime-name="rigth-in"
              next-anime
              anime-delay="500">
              {{ $t(`${lang}.register.title`) }}
            </h2>
            <p
              class="des ai"
              anime-name="rigth-in"
              next-anime
              anime-delay="600">
              {{ $t(`${lang}.register.comment`) }}
            </p>

            <NuxtLink
              :to="localePath(`${$app().FR_URL.juzr}/register`)"
              :external="true"
              class="ai main-btn"
              anime-name="rigth-in"
              next-anime
              anime-delay="900">
              {{ $t(`${lang}.register.title`) }}
            </NuxtLink>
            <!-- <div class="email-box"> -->

            <!-- <label
                class="ai"
                anime-name="rigth-in"
                next-anime
                anime-delay="700"
                >{{ $t(`gl.form.email`) }}</label
              >
              <div
                class="input ai"
                anime-name="rigth-in"
                next-anime
                anime-delay="800"
              >
                <input type="text" placeholder="email@xxxxx.xxx" />

              </div> -->
            <!-- </div> -->
          </div>
        </div>
        <div class="left col-lg-6 col-md-12 d-flex justify-content-end">
          <img
            class="at ai"
            anime-name="left-in"
            anime-delay="1200"
            src="~/assets/images/index/at.png" />
        </div>
      </div>
    </section>
  </div>
</template>
